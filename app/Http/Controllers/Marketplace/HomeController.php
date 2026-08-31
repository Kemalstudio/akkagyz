<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $productQuery = Product::query()->customerVisible()->marketplace();
        $productRelations = ['images', 'category', 'seller'];

        return view('marketplace.home', [
            'banners' => Banner::active()->forMarketplace()->orderBy('sort_order')->get(),
            'quickCategories' => Category::topLevel()->orderBy('sort_order')->get(),
            'popular' => (clone $productQuery)->with($productRelations)->orderByDesc('sales_count')->limit(4)->get(),
            'newest' => (clone $productQuery)->with($productRelations)->latest()->limit(4)->get(),
        ]);
    }
}
