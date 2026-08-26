<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()->compareItems()->with(['product.category', 'product.images', 'product.seller'])->get();

        return view('storefront.compare', ['items' => $items]);
    }

    public function toggle(Request $request, Product $product)
    {
        $existing = $request->user()->compareItems()->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();

            return back()->with('status', 'Убрано из сравнения.');
        }

        if ($request->user()->compareItems()->count() >= 4) {
            return back()->with('error', 'Можно сравнить не более 4 товаров одновременно.');
        }

        $request->user()->compareItems()->create(['product_id' => $product->id]);

        return back()->with('status', 'Добавлено к сравнению.');
    }

    public function clear(Request $request)
    {
        $request->user()->compareItems()->delete();
        return back()->with('status', 'Список сравнения очищен.');
    }
}
