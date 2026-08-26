<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->wishlistItems()->with(['product.category', 'product.images'])->get();

        return view('storefront.wishlist', ['items' => $items]);
    }

    public function toggle(Request $request, Product $product)
    {
        $existing = $request->user()->wishlistItems()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Удалено из избранного.';
            $active = false;
        } else {
            $request->user()->wishlistItems()->create(['product_id' => $product->id]);
            $message = 'Добавлено в избранное.';
            $active = true;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'active' => $active,
                'count' => $request->user()->wishlistItems()->count(),
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }

    public function clear(Request $request)
    {
        $request->user()->wishlistItems()->delete();
        return back()->with('status', 'Избранное очищено.');
    }
}
