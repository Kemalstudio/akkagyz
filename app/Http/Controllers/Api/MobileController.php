<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\BusinessSetting;
use App\Models\MobileApiToken;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\MobilePasswordResetCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class MobileController extends Controller
{
    public function config(): JsonResponse
    {
        $settings = BusinessSetting::current();
        return response()->json(['data' => [
            'site_name' => $settings->site_name ?: 'AK KAGYZ', 'tagline' => $settings->tagline,
            'description' => $settings->store_description, 'logo_url' => $settings->logo_url,
            'primary_color' => $settings->primary_color ?: '#285ED6', 'currency' => $settings->currency ?: 'TMT',
            'phone' => $settings->contact_phone_visible ? $settings->phone : null, 'email' => $settings->email,
            'address' => $settings->address, 'support_hours' => $settings->support_hours,
            'socials' => ['instagram'=>$settings->instagram_url,'telegram'=>$settings->telegram_url,'whatsapp'=>$settings->whatsapp_url],
            'locales' => $settings->enabled_locales ?: ['ru','tk','en'], 'default_locale' => $settings->default_locale ?: 'ru',
        ]]);
    }

    public function products(Request $request): JsonResponse
    {
        $query = Product::active()->with(['category', 'images', 'seller']);
        $query->when($request->filled('q'), fn ($q) => $q->where(fn ($inner) => $inner
            ->where('name', 'like', '%'.$request->string('q').'%')
            ->orWhere('brand', 'like', '%'.$request->string('q').'%')));
        $query->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')));
        $query->when($request->filled('brand'), fn ($q) => $q->where('brand', $request->string('brand')));
        $query->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', $request->integer('min_price')));
        $query->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', $request->integer('max_price')));
        $query->when($request->boolean('in_stock'), fn ($q) => $q->where('stock', '>', 0));
        match ($request->string('sort')->toString()) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating_avg'),
            'popular' => $query->orderByDesc('sales_count'),
            default => $query->latest(),
        };
        $products = $query->paginate(min(1000, max(1, $request->integer('per_page', 30))));

        return response()->json([
            'data' => collect($products->items())->map(fn ($product) => $this->productData($product)),
            'meta' => ['current_page' => $products->currentPage(), 'last_page' => $products->lastPage(), 'total' => $products->total()],
        ]);
    }

    public function categories(): JsonResponse
    {
        // Note: `limit()` inside an eager-load closure applies to the whole
        // underlying query, not per parent — it must not be used here, or
        // most categories end up with no preview image at all.
        $categories = Category::withCount(['products' => fn ($q) => $q->active()])
            ->with(['products' => fn ($q) => $q->active()->with('images')->select('id', 'category_id'), 'children'])
            ->orderBy('sort_order')->get();

        return response()->json(['data' => $categories->map(fn ($category) => [
            'id' => $category->id, 'parent_id' => $category->parent_id, 'name' => $category->name,
            'slug' => $category->slug, 'icon' => $category->icon, 'products_count' => $category->products_count,
            'image_url' => $category->products->first()?->images->first()?->url,
        ])]);
    }

    public function filters(Request $request): JsonResponse
    {
        $query = Product::active();
        $query->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->integer('category_id')));

        return response()->json(['data' => [
            'brands' => (clone $query)->whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand')->pluck('brand'),
            'min_price' => (int) ((clone $query)->min('price') ?? 0),
            'max_price' => (int) ((clone $query)->max('price') ?? 0),
        ]]);
    }

    public function product(Product $product): JsonResponse
    {
        abort_unless($product->status === 'active', 404);
        $product->load(['category', 'images', 'seller', 'reviews'=>fn($q)=>$q->published()->with(['user','replies.user'])]);

        return response()->json(['data' => $this->productData($product) + ['reviews' => $product->reviews->map(fn ($review) => [
            'id' => $review->id, 'rating' => $review->rating, 'comment' => $review->comment,
            'author' => $review->user->name, 'created_at' => $review->created_at->toIso8601String(),
            'replies' => $review->replies->map(fn ($reply) => ['author' => $reply->user->name, 'comment' => $reply->comment]),
        ])]]);
    }

    public function review(Request $request, Product $product): JsonResponse
    {
        $verified = OrderItem::where('product_id',$product->id)->where('status','delivered')->whereHas('order',fn($q)=>$q->where('user_id',$request->user()->id))->exists();
        abort_unless($verified,403,'Отзыв можно оставить только после доставки товара.');
        $data = $request->validate(['rating' => ['required', 'integer', 'between:1,5'], 'comment' => ['required', 'string', 'min:3', 'max:2000']]);
        $review = $product->reviews()->updateOrCreate(['user_id' => $request->user()->id], $data+['is_verified_purchase'=>true,'status'=>'published']);
        $published=$product->reviews()->published();$product->update(['rating_avg' => round($published->avg('rating')??0, 2), 'rating_count' => $published->count()]);

        return response()->json(['message' => 'Отзыв сохранён.', 'review_id' => $review->id], 201);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = User::create($data + ['role' => 'customer']);

        return $this->tokenResponse($user, 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        $user = User::where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password) || $user->is_blocked) {
            return response()->json(['message' => 'Неверный email или пароль.'], 422);
        }

        return $this->tokenResponse($user);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $user = User::where('email', $data['email'])->first();
        // Always respond the same way whether or not the email exists, so the
        // endpoint can't be used to check which emails are registered.
        if ($user) {
            $code = (string) random_int(100000, 999999);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                ['token' => Hash::make($code), 'created_at' => now()]
            );
            $user->notify(new MobilePasswordResetCode($code));
        }

        return response()->json(['message' => 'Если такой email зарегистрирован, мы отправили код подтверждения.']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $row = DB::table('password_reset_tokens')->where('email', $data['email'])->first();
        if (! $row || ! Hash::check($data['code'], $row->token) || now()->diffInMinutes($row->created_at) > 15) {
            return response()->json(['message' => 'Код неверен или истёк.'], 422);
        }
        $user = User::where('email', $data['email'])->firstOrFail();
        $user->update(['password' => $data['password']]);
        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();
        MobileApiToken::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Пароль успешно изменён. Войдите с новым паролем.']);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = $request->user();
        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Текущий пароль указан неверно.'], 422);
        }
        $user->update(['password' => $data['password']]);
        $currentTokenId = $request->attributes->get('mobile_token')->id;
        MobileApiToken::where('user_id', $user->id)->where('id', '!=', $currentTokenId)->delete();

        return response()->json(['message' => 'Пароль обновлён.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->userData($request->user())]);
    }

    public function updateMe(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:190', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
        $user->update($data);

        return response()->json([
            'message' => 'Профиль обновлён.',
            'data' => $this->userData($user->fresh()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->attributes->get('mobile_token')?->delete();

        return response()->json(['message' => 'Вы вышли из аккаунта.']);
    }

    public function cart(Request $request): JsonResponse
    {
        return response()->json($this->cartData($request));
    }

    public function cartUpdate(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate(['action' => ['required', 'in:increment,decrement']]);
        $item = $request->user()->cartItems()->firstOrNew(['product_id' => $product->id]);
        $current = $item->exists ? $item->quantity : 0;
        $quantity = $data['action'] === 'increment' ? $current + 1 : $current - 1;
        if ($quantity > $product->stock) {
            return response()->json(['message' => 'Недостаточно товара на складе.'], 422);
        }
        if ($quantity < 1) {
            if ($item->exists) {
                $item->delete();
            }
        } else {
            $item->quantity = $quantity;
            $item->save();
        }

        return response()->json($this->cartData($request));
    }

    public function wishlist(Request $request): JsonResponse
    {
        $items = $request->user()->wishlistItems()->with(['product.category', 'product.images', 'product.seller'])->latest()->get();

        return response()->json(['data' => $items->map(fn ($item) => $this->productData($item->product))]);
    }

    public function wishlistToggle(Request $request, Product $product): JsonResponse
    {
        $item = $request->user()->wishlistItems()->where('product_id', $product->id)->first();
        if ($item) {
            $item->delete();
        } else {
            $request->user()->wishlistItems()->create(['product_id' => $product->id]);
        }

        return response()->json(['active' => ! $item, 'count' => $request->user()->wishlistItems()->count()]);
    }

    public function orders(Request $request): JsonResponse
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(20);

        return response()->json([
            'data' => collect($orders->items())->map(fn ($order) => [
                'id' => $order->id, 'number' => $order->number, 'status' => $order->status,
                'total' => $order->total, 'items_count' => $order->items->sum('quantity'),
                'created_at' => $order->created_at->toIso8601String(),
            ]),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function checkout(Request $request, \App\Services\PromoCodeService $promos): JsonResponse
    {
        $data = $request->validate([
            'idempotency_key' => ['sometimes', 'uuid'],
            'city' => ['required', 'string', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'delivery_method' => ['required', 'in:courier,pickup'],
            'payment_method' => ['required', 'in:card,cash'],
            'promo_code' => ['nullable','string','max:50'],
        ]);
        $data['idempotency_key'] ??= Str::uuid()->toString();
        $existing = $request->user()->orders()
            ->where('idempotency_key', $data['idempotency_key'])
            ->first();
        if ($existing) {
            return response()->json([
                'message' => 'Заказ уже был оформлен.',
                'data' => $this->orderData($existing->load('items.product.images')),
            ]);
        }

        $cart = $request->user()->cartItems()->with('product')->get();
        if ($cart->isEmpty()) {
            return response()->json(['message' => 'Корзина пуста.'], 422);
        }

        $order = DB::transaction(function () use ($request, $data, $cart, $promos) {
            $rows = [];
            foreach ($cart as $item) {
                $product = Product::query()->lockForUpdate()->find($item->product_id);
                if (! $product || $product->status !== 'active') {
                    abort(422, 'Один из товаров больше недоступен.');
                }
                if ($item->quantity > $product->stock) {
                    abort(422, "Недостаточно товара «{$product->name}» на складе.");
                }
                $rows[] = [$item, $product];
            }

            $subtotal = collect($rows)->sum(fn ($row) => $row[0]->quantity * $row[1]->price);
            $compareSubtotal = collect($rows)->sum(fn ($row) => $row[0]->quantity * ($row[1]->compare_price ?? $row[1]->price));
            [$promo,$promoDiscount]=$promos->calculate($data['promo_code']??null,$request->user(),$cart,$subtotal);unset($data['promo_code']);
            $order = Order::create([
                'number' => 'AK-'.strtoupper(Str::random(8)),
                'user_id' => $request->user()->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => max(0, $compareSubtotal - $subtotal)+$promoDiscount,
                'total' => $subtotal-$promoDiscount,
                'promo_code_id'=>$promo?->id,'promo_code'=>$promo?->code,
                ...$data,
            ]);

            foreach ($rows as [$item, $product]) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'seller_id' => $product->seller_id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $item->quantity,
                    'status' => 'pending',
                ]);
                $product->decrement('stock', $item->quantity);
                $product->increment('sales_count', $item->quantity);
            }
            $request->user()->cartItems()->delete();
            if($promo)$promo->usages()->create(['user_id'=>$request->user()->id,'order_id'=>$order->id,'discount'=>$promoDiscount]);

            return $order->load('items.product.images');
        });

        return response()->json([
            'message' => 'Заказ успешно оформлен.',
            'data' => $this->orderData($order),
        ], 201);
    }

    public function order(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        return response()->json(['data' => $this->orderData($order->load('items.product.images'))]);
    }

    public function cancelOrder(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        if (! in_array($order->status, ['pending', 'processing'], true)) {
            return response()->json(['message' => 'Этот заказ уже нельзя отменить.'], 422);
        }

        DB::transaction(function () use ($order) {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();
            $lockedOrder->load('items');
            if (! in_array($lockedOrder->status, ['pending', 'processing'], true)) {
                abort(422, 'Этот заказ уже нельзя отменить.');
            }
            foreach ($lockedOrder->items as $item) {
                Product::query()->whereKey($item->product_id)->increment('stock', $item->quantity);
                Product::query()->whereKey($item->product_id)->decrement('sales_count', $item->quantity);
                $item->update(['status' => 'cancelled']);
            }
            $lockedOrder->update(['status' => 'cancelled']);
        });

        return response()->json(['message' => 'Заказ отменён.', 'data' => $this->orderData($order->fresh('items.product.images'))]);
    }

    public function repeatOrder(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        $order->load('items.product');
        $added = 0;
        foreach ($order->items as $item) {
            if ($item->product && $item->product->status === 'active' && $item->product->stock > 0) {
                $request->user()->cartItems()->updateOrCreate(
                    ['product_id' => $item->product_id],
                    ['quantity' => min($item->quantity, $item->product->stock)]
                );
                $added++;
            }
        }

        return response()->json($this->cartData($request) + ['message' => $added ? 'Товары добавлены в корзину.' : 'Доступных товаров для повтора нет.']);
    }

    public function trackOrder(Request $request): JsonResponse
    {
        $data = $request->validate(['number' => ['required', 'string', 'max:30'], 'phone' => ['required', 'string', 'max:40']]);
        $normalizedPhone = preg_replace('/\D+/', '', $data['phone']);
        $order = Order::with('items')->whereRaw('UPPER(number) = ?', [strtoupper(trim($data['number']))])->first();
        if (! $order || preg_replace('/\D+/', '', $order->phone) !== $normalizedPhone) {
            return response()->json(['message' => 'Заказ с таким номером и телефоном не найден.'], 404);
        }

        return response()->json(['data' => $this->orderData($order)]);
    }

    private function cartData(Request $request): array
    {
        $items = $request->user()->cartItems()->with(['product.category', 'product.images', 'product.seller'])->get();

        return ['data' => $items->map(fn ($item) => ['quantity' => $item->quantity, 'product' => $this->productData($item->product)]), 'count' => $items->sum('quantity'), 'subtotal' => $items->sum(fn ($item) => $item->quantity * $item->product->price)];
    }

    private function tokenResponse(User $user, int $status = 200): JsonResponse
    {
        $plain = Str::random(64);
        MobileApiToken::create(['user_id' => $user->id, 'name' => 'flutter', 'token_hash' => hash('sha256', $plain), 'expires_at' => now()->addDays(90)]);

        return response()->json(['token' => $plain, 'token_type' => 'Bearer', 'expires_in' => 7776000, 'user' => $this->userData($user)], $status);
    }

    private function orderData(Order $order): array
    {
        $order->loadMissing('items.product.images');

        return [
            'id' => $order->id,
            'number' => $order->number,
            'status' => $order->status,
            'subtotal' => $order->subtotal,
            'discount' => $order->discount,
            'total' => $order->total,
            'city' => $order->city,
            'address' => $order->address,
            'phone' => $order->phone,
            'delivery_method' => $order->delivery_method,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'created_at' => $order->created_at->toIso8601String(),
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'status' => $item->status,
                'image_url' => $item->product?->images->first()?->url,
            ]),
        ];
    }

    private function userData(User $user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone, 'avatar_url' => $user->avatar_url, 'role' => $user->role];
    }

    private function productData(Product $product): array
    {
        return ['id' => $product->id, 'name' => $product->name, 'slug' => $product->slug, 'category_id' => $product->category_id, 'category' => $product->category?->name, 'brand' => $product->brand, 'description' => $product->description, 'price' => $product->price, 'compare_price' => $product->compare_price, 'stock' => $product->stock, 'rating' => (float) $product->rating_avg, 'rating_count' => $product->rating_count, 'image_url' => $product->images->first()?->url, 'images' => $product->images->pluck('url')->filter()->values(), 'seller' => $product->seller?->store_name];
    }
}
