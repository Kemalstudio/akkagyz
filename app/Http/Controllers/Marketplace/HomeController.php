<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $stores = User::approvedSellers()
            ->withCount(['products' => fn ($q) => $q->active()])
            ->orderByDesc('is_vip')
            ->orderByDesc('store_views')
            ->limit(12)
            ->get();

        return view('marketplace.home', [
            'stores' => $stores,
            'storesTotal' => User::approvedSellers()->count(),
            'stats' => [
                'stores' => User::approvedSellers()->count(),
                'products' => Product::active()->marketplace()->count(),
            ],
            'vipProducts' => Product::active()->marketplace()->vip()->with('images')->orderByDesc('created_at')->limit(4)->get(),
            'popular' => Product::active()->marketplace()->with('images')->orderByDesc('sales_count')->limit(4)->get(),
            'newest' => Product::active()->marketplace()->with('images')->orderByDesc('created_at')->limit(4)->get(),
        ]);
    }
}
