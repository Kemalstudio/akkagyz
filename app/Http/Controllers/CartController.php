<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Support\GuestCart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = GuestCart::scope(CartItem::query(), $request->user())->with(['product.seller', 'product.category', 'product.images'])->get();

        $subtotal = $items->sum(fn ($item) => $item->product->price * $item->quantity);
        $compareSubtotal = $items->sum(fn ($item) => ($item->product->compare_price ?? $item->product->price) * $item->quantity);
        $discount = max(0, $compareSubtotal - $subtotal);

        return view('storefront.cart', [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $subtotal,
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $product->loadMissing('seller');

        if (! $product->isPurchasable()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Товара нет в наличии.'], 422)
                : back()->with('error', 'Товара нет в наличии.');
        }

        $owner = $request->user()
            ? ['user_id' => $request->user()->id]
            : ['guest_token' => GuestCart::token()];

        $item = CartItem::firstOrNew($owner + ['product_id' => $product->id]);
        $current = $item->exists ? $item->quantity : 0;
        $quantity = $request->input('action', 'increment') === 'decrement' ? $current - 1 : $current + 1;

        if ($quantity > $product->stock) {
            return $request->expectsJson()
                ? response()->json(['message' => 'В наличии только '.$product->stock.' шт.'], 422)
                : back()->with('error', 'Недостаточно товара на складе.');
        }

        if ($quantity < 1) {
            if ($item->exists) {
                $item->delete();
            }
            $quantity = 0;
        } else {
            $item->quantity = $quantity;
            $item->save();
        }

        if ($request->expectsJson()) {
            $items = GuestCart::scope(CartItem::query(), $request->user())->with(['product.images'])->latest()->get();
            $subtotal = $items->sum(fn ($cartItem) => $cartItem->product->price * $cartItem->quantity);

            return response()->json([
                'quantity' => $quantity,
                'count' => $items->sum('quantity'),
                'subtotal' => $subtotal,
                'drawer' => view('storefront.partials.cart-drawer-items', compact('items', 'subtotal'))->render(),
                'message' => $quantity ? 'Товар в корзине' : 'Товар удалён из корзины',
            ]);
        }

        return back()->with('status', 'Товар добавлен в корзину.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeOwner($request, $cartItem);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);
        $quantity = $data['quantity'];

        $cartItem->loadMissing('product.seller');
        if (! $cartItem->product?->isPurchasable()) {
            return back()->with('error', 'Этот товар больше недоступен для заказа.');
        }
        if ($quantity > $cartItem->product->stock) {
            return back()->with('error', 'В наличии только '.$cartItem->product->stock.' шт.');
        }

        if ($quantity < 1) {
            $cartItem->delete();
        } else {
            $cartItem->update(['quantity' => $quantity]);
        }

        return back();
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        $this->authorizeOwner($request, $cartItem);

        $cartItem->delete();

        return back()->with('status', 'Товар удалён из корзины.');
    }

    public function clear(Request $request)
    {
        GuestCart::scope(CartItem::query(), $request->user())->delete();

        return redirect()->route('cart.index')->with('status', 'Корзина очищена.');
    }

    private function authorizeOwner(Request $request, CartItem $cartItem): void
    {
        $owns = $request->user()
            ? $cartItem->user_id === $request->user()->id
            : ($cartItem->guest_token !== null && hash_equals($cartItem->guest_token, GuestCart::token(false) ?? ''));

        abort_unless($owns, 403);
    }
}
