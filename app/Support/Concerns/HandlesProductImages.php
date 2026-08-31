<?php

namespace App\Support\Concerns;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesProductImages
{
    private function storeProductImages(Request $request, Product $product): void
    {
        foreach ($request->file('images', []) as $index => $image) {
            $product->images()->create(['path' => $image->store('products', 'public'), 'sort_order' => $index]);
        }
    }

    private function replaceProductImages(Request $request, Product $product): void
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
        $this->storeProductImages($request, $product);
    }
}
