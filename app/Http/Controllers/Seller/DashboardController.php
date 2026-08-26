<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $seller = $request->user();

        $products = $seller->products();

        $stats = [
            'revenue' => OrderItem::where('seller_id', $seller->id)->sum(\Illuminate\Support\Facades\DB::raw('price * quantity')),
            'orders' => OrderItem::where('seller_id', $seller->id)->distinct('order_id')->count('order_id'),
            'products' => $products->count(),
            'rating' => round($products->avg('rating_avg') ?? 0, 1),
        ];

        $topProducts = $seller->products()->orderByDesc('sales_count')->limit(3)->get();

        $recentOrders = OrderItem::with(['order.user'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('seller.dashboard', compact('stats', 'topProducts', 'recentOrders'));
    }
}
