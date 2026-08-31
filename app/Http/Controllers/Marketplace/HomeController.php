<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $storeQuery = User::approvedSellers()->where('is_blocked', false);
        $storesTotal = (clone $storeQuery)->count();

        $stores = (clone $storeQuery)
            ->withCount(['products' => fn ($query) => $query
                ->active()
                ->whereNull('archived_at')])
            ->orderByDesc('is_vip')
            ->orderByDesc('store_views')
            ->limit(12)
            ->get();

        $productQuery = Product::query()->customerVisible()->marketplace();
        $productRelations = ['images', 'category', 'seller'];

        return view('marketplace.home', [
            'banners' => Banner::active()->forMarketplace()->orderBy('sort_order')->get(),
            'quickCategories' => Category::topLevel()->orderBy('sort_order')->get(),
            'stores' => $stores,
            'storesTotal' => $storesTotal,
            'stats' => [
                'stores' => $storesTotal,
                'products' => (clone $productQuery)->count(),
            ],
            'vipProducts' => (clone $productQuery)->vip()->with($productRelations)->latest()->limit(4)->get(),
            'popular' => (clone $productQuery)->with($productRelations)->orderByDesc('sales_count')->limit(4)->get(),
            'newest' => (clone $productQuery)->with($productRelations)->latest()->limit(4)->get(),
        ]);
    }
}
