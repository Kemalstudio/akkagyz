<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Notifications\NewOrderForSeller;
use App\Notifications\OrderPlaced;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\PromoCodeService;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->cartItems()->with('product')->get();

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

    public function store(Request $request, PromoCodeService $promos)
    {
        $data = $request->validate([
            'city' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'delivery_method' => ['required', 'in:courier,pickup'],
            'payment_method' => ['required', 'in:cash'],
            'promo_code' => ['nullable','string','max:50'],
        ]);

        $items = $request->user()->cartItems()->with('product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index');
        }

        foreach ($items as $item) {
            if ($item->quantity > $item->product->stock) {
                return back()->with('error', "Недостаточно товара «{$item->product->name}» на складе.");
            }
        }

        $order = DB::transaction(function () use ($request, $data, $items, $promos) {
            $subtotal = $items->sum(fn ($item) => $item->product->price * $item->quantity);
            $compareSubtotal = $items->sum(fn ($item) => ($item->product->compare_price ?? $item->product->price) * $item->quantity);
            $discount = max(0, $compareSubtotal - $subtotal);
            [$promo,$promoDiscount]=$promos->calculate($data['promo_code']??null,$request->user(),$items,$subtotal);unset($data['promo_code']);

            $order = Order::create([
                'number' => 'AK-'.strtoupper(Str::random(6)),
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount+$promoDiscount,
                'total' => $subtotal-$promoDiscount,
                'promo_code_id'=>$promo?->id,'promo_code'=>$promo?->code,
                ...$data,
            ]);

            foreach ($items as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'seller_id' => $item->product->seller_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'status' => 'pending',
                ]);

                $item->product->decrement('stock', $item->quantity);
                $item->product->increment('sales_count', $item->quantity);

                if ($item->product->seller) {
                    if (BusinessSetting::current()->order_notifications) {
                        $item->product->seller->notify(new NewOrderForSeller($orderItem));
                    }
                }
            }

            $request->user()->cartItems()->delete();
            if($promo)$promo->usages()->create(['user_id'=>$request->user()->id,'order_id'=>$order->id,'discount'=>$promoDiscount]);

            return $order;
        });

        if (BusinessSetting::current()->order_notifications) {
            $request->user()->notify(new OrderPlaced($order));
        }

        return redirect()->route('orders.show', $order)->with('status', 'Заказ оформлен! Номер заказа: '.$order->number);
    }
}
