<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Notifications\NewOrderForSeller;
use App\Notifications\OrderPlaced;
use App\Services\Payments\PaymentGatewayResolver;
use App\Services\PromoCodeService;
use App\Support\GuestCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    /** Surcharge for express checkout from the cart drawer (skips the full form, reuses the customer's last delivery details). */
    private const EXPRESS_FEE = 30;

    public function index(Request $request)
    {
        $items = GuestCart::scope(CartItem::query(), $request->user())->with(['product.images', 'product.seller'])->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $subtotal = $items->sum(fn ($item) => $item->product->price * $item->quantity);
        $compareSubtotal = $items->sum(fn ($item) => ($item->product->compare_price ?? $item->product->price) * $item->quantity);
        $discount = max(0, $compareSubtotal - $subtotal);

        return view('storefront.checkout', [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $subtotal,
        ]);
    }

    public function store(Request $request, PromoCodeService $promos, PaymentGatewayResolver $gateways)
    {
        $data = $request->validate([
            'idempotency_key' => ['nullable', 'uuid'],
            'name' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'required_if:delivery_method,courier', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^[0-9+()\s-]{6,40}$/'],
            'delivery_method' => ['required', 'in:courier,pickup'],
            'payment_method' => ['required', 'in:cash'],
            'promo_code' => ['nullable', 'string', 'max:50'],
        ]);
        $idempotencyKey = $data['idempotency_key'] ?? Str::uuid()->toString();
        unset($data['idempotency_key']);
        if ($data['delivery_method'] === 'pickup') {
            $data['address'] = 'Самовывоз';
        }

        if (! $request->user() && ($data['promo_code'] ?? null)) {
            return back()->withInput()->with('error', 'Промокоды доступны только зарегистрированным пользователям. Войдите в аккаунт, чтобы применить промокод.');
        }

        $existing = $this->existingOrder($request, $idempotencyKey);
        if ($existing) {
            return $this->redirectToOrder($request, $existing)->with('status', 'Заказ уже был оформлен. Номер заказа: '.$existing->number);
        }

        $items = GuestCart::scope(CartItem::query(), $request->user())->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $order = DB::transaction(function () use ($request, $data, $promos, $idempotencyKey, $gateways) {
            if ($request->user()) {
                $existing = $request->user()->orders()
                    ->where('idempotency_key', $idempotencyKey)
                    ->lockForUpdate()
                    ->first();
                if ($existing) {
                    return $existing;
                }
            }

            $cartItems = GuestCart::scope(CartItem::query(), $request->user())
                ->orderBy('product_id')
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Корзина пуста.']);
            }

            $products = Product::query()
                ->with('seller')
                ->whereKey($cartItems->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $rows = $cartItems->map(function (CartItem $item) use ($products) {
                $product = $products->get($item->product_id);
                if (! $product?->isPurchasable()) {
                    throw ValidationException::withMessages(['cart' => 'Один из товаров больше недоступен.']);
                }
                if ($item->quantity > $product->stock) {
                    throw ValidationException::withMessages(['cart' => "Недостаточно товара «{$product->name}» на складе."]);
                }

                return [$item, $product];
            });

            $subtotal = $rows->sum(fn (array $row) => $row[1]->price * $row[0]->quantity);
            $compareSubtotal = $rows->sum(fn (array $row) => ($row[1]->compare_price ?? $row[1]->price) * $row[0]->quantity);
            $discount = max(0, $compareSubtotal - $subtotal);
            [$promo, $promoDiscount] = $request->user()
                ? $promos->calculate($data['promo_code'] ?? null, $request->user(), $cartItems, $subtotal)
                : [null, 0];
            unset($data['promo_code']);

            $order = Order::create([
                'number' => 'AK-'.strtoupper(Str::random(6)),
                'user_id' => $request->user()?->id,
                'status' => 'pending',
                'subtotal' => $compareSubtotal,
                'discount' => $discount + $promoDiscount,
                'total' => $subtotal - $promoDiscount,
                'promo_code_id' => $promo?->id, 'promo_code' => $promo?->code,
                'idempotency_key' => $idempotencyKey,
                ...$data,
            ]);

            foreach ($rows as [$item, $product]) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'seller_id' => $product->seller_id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item->quantity,
                    'status' => 'pending',
                ]);

                $product->decrement('stock', $item->quantity);
                $product->increment('sales_count', $item->quantity);

                if ($product->seller) {
                    if (BusinessSetting::current()->seller_notifications) {
                        $product->seller->notify((new NewOrderForSeller($orderItem))->afterCommit());
                    }
                }
            }

            $gateways->resolve($order->payment_method)->charge($order);
            GuestCart::scope(CartItem::query(), $request->user())->delete();
            if ($promo) {
                $promo->usages()->create(['user_id' => $request->user()->id, 'order_id' => $order->id, 'discount' => $promoDiscount]);
            }

            return $order;
        });

        if ($order->wasRecentlyCreated && BusinessSetting::current()->order_notifications) {
            $request->user()?->notify((new OrderPlaced($order))->afterCommit());
        }

        if (! $request->user() && $order->wasRecentlyCreated) {
            $request->session()->put("guest_checkout_orders.{$idempotencyKey}", $order->id);
        }

        $message = $order->wasRecentlyCreated
            ? 'Заказ оформлен! Номер заказа: '.$order->number
            : 'Заказ уже был оформлен. Номер заказа: '.$order->number;

        return $this->redirectToOrder($request, $order)->with('status', $message);
    }

    public function expressStore(Request $request, PaymentGatewayResolver $gateways)
    {
        $user = $request->user();
        $lastOrder = $user->orders()->latest()->first();

        $data = [
            'name' => $user->name,
            'city' => $lastOrder?->city,
            'address' => $lastOrder?->address,
            'phone' => $lastOrder?->phone ?: $user->phone,
            'delivery_method' => $lastOrder?->delivery_method ?: 'courier',
            'payment_method' => 'cash',
        ];

        if (! $data['phone'] || ! $data['city'] || ($data['delivery_method'] === 'courier' && ! $data['address'])) {
            return response()->json([
                'message' => 'Быстрый заказ пока недоступен: оформите один заказ обычным способом, чтобы сохранить адрес доставки.',
            ], 422);
        }

        $items = GuestCart::scope(CartItem::query(), $user)->with('product')->get();
        if ($items->isEmpty()) {
            return response()->json(['message' => 'Корзина пуста.'], 422);
        }

        $idempotencyKey = Str::uuid()->toString();

        try {
            $order = DB::transaction(function () use ($request, $data, $gateways, $idempotencyKey, $user) {
                $cartItems = GuestCart::scope(CartItem::query(), $user)
                    ->orderBy('product_id')
                    ->lockForUpdate()
                    ->get();

                if ($cartItems->isEmpty()) {
                    throw ValidationException::withMessages(['cart' => 'Корзина пуста.']);
                }

                $products = Product::query()
                    ->with('seller')
                    ->whereKey($cartItems->pluck('product_id'))
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $rows = $cartItems->map(function (CartItem $item) use ($products) {
                    $product = $products->get($item->product_id);
                    if (! $product?->isPurchasable()) {
                        throw ValidationException::withMessages(['cart' => 'Один из товаров больше недоступен.']);
                    }
                    if ($item->quantity > $product->stock) {
                        throw ValidationException::withMessages(['cart' => "Недостаточно товара «{$product->name}» на складе."]);
                    }

                    return [$item, $product];
                });

                $subtotal = $rows->sum(fn (array $row) => $row[1]->price * $row[0]->quantity);
                $compareSubtotal = $rows->sum(fn (array $row) => ($row[1]->compare_price ?? $row[1]->price) * $row[0]->quantity);
                $discount = max(0, $compareSubtotal - $subtotal);

                $order = Order::create([
                    'number' => 'AK-'.strtoupper(Str::random(6)),
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'subtotal' => $compareSubtotal,
                    'discount' => $discount,
                    'express_fee' => self::EXPRESS_FEE,
                    'total' => $subtotal + self::EXPRESS_FEE,
                    'idempotency_key' => $idempotencyKey,
                    ...$data,
                ]);

                foreach ($rows as [$item, $product]) {
                    $orderItem = OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'seller_id' => $product->seller_id,
                        'product_name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $item->quantity,
                        'status' => 'pending',
                    ]);

                    $product->decrement('stock', $item->quantity);
                    $product->increment('sales_count', $item->quantity);

                    if ($product->seller && BusinessSetting::current()->seller_notifications) {
                        $product->seller->notify((new NewOrderForSeller($orderItem))->afterCommit());
                    }
                }

                $gateways->resolve($order->payment_method)->charge($order);
                GuestCart::scope(CartItem::query(), $user)->delete();

                return $order;
            });
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->validator->errors()->first()], 422);
        }

        if (BusinessSetting::current()->order_notifications) {
            $user->notify((new OrderPlaced($order))->afterCommit());
        }

        return response()->json([
            'redirect' => route('orders.show', $order),
            'message' => 'Заказ оформлен! Номер заказа: '.$order->number,
        ]);
    }

    public function previewPromo(Request $request, PromoCodeService $promos)
    {
        $data = $request->validate([
            'promo_code' => ['required', 'string', 'max:50'],
        ]);
        $items = $request->user()->cartItems()->with('product')->get();
        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['promo_code' => 'Корзина пуста.']);
        }

        $subtotal = $items->sum(fn ($item) => $item->product->price * $item->quantity);
        [$promo, $discount] = $promos->calculate($data['promo_code'], $request->user(), $items, $subtotal);

        return response()->json([
            'code' => $promo->code,
            'discount' => $discount,
            'total' => max(0, $subtotal - $discount),
            'message' => 'Промокод применён.',
        ]);
    }

    private function redirectToOrder(Request $request, Order $order)
    {
        return $request->user()
            ? redirect()->route('orders.show', $order)
            : redirect()->route('orders.show.guest', ['order' => $order->id, 'token' => $order->access_token]);
    }

    private function existingOrder(Request $request, string $idempotencyKey): ?Order
    {
        if ($request->user()) {
            return $request->user()->orders()->where('idempotency_key', $idempotencyKey)->first();
        }

        $orderId = $request->session()->get("guest_checkout_orders.{$idempotencyKey}");

        return $orderId
            ? Order::whereKey($orderId)->whereNull('user_id')->where('idempotency_key', $idempotencyKey)->first()
            : null;
    }
}
