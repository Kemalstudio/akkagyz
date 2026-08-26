<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->cartItems()->with(['product.seller', 'product.images'])->get();

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
        if (! $product->in_stock) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Товара нет в наличии.'], 422)
                : back()->with('error', 'Товара нет в наличии.');
        }

        $item = $request->user()->cartItems()->firstOrNew(['product_id' => $product->id]);
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
            $items = $request->user()->cartItems()->with(['product.images'])->latest()->get();
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

    public function update(Request $request, \App\Models\CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);

        $quantity = (int) $request->input('quantity', 1);

        if ($quantity < 1) {
            $cartItem->delete();
        } else {
            $cartItem->update(['quantity' => $quantity]);
        }

        return back();
    }

    public function destroy(Request $request, \App\Models\CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === $request->user()->id, 403);

        $cartItem->delete();

        return back()->with('status', 'Товар удалён из корзины.');
    }
}
