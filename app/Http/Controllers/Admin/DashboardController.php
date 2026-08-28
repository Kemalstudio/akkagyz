<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = now();
        // "Выручка" отражает только фактически полученные деньги (заказ доставлен —
        // при наложенном платеже деньги получены при вручении), как и учёт в MarketplaceFinanceService.
        $thisMonthRevenue = (int) Order::whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])->where('status', 'delivered')->sum('total');
        $lastMonthRevenue = (int) Order::whereBetween('created_at', [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth()])->where('status', 'delivered')->sum('total');

        $stats = [
            'revenue' => (int) Order::where('status', 'delivered')->sum('total'),
            'orders' => Order::count(),
            'sellers' => User::where('role', 'seller')->where('store_status', 'approved')->count(),
            'pendingSellers' => User::where('role', 'seller')->where('store_status', 'pending')->count(),
            'customers' => User::where('role', 'customer')->count(),
            'pendingProducts' => Product::where('status', 'pending')->count(),
            'thisMonthRevenue' => $thisMonthRevenue,
            'revenueGrowth' => $lastMonthRevenue > 0 ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : ($thisMonthRevenue > 0 ? 100 : 0),
            'averageOrder' => (int) round(Order::where('status', '!=', 'cancelled')->avg('total') ?? 0),
            'todayOrders' => Order::whereDate('created_at', $now->toDateString())->count(),
        ];

        $ordersForChart = Order::where('created_at', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->get(['total', 'created_at', 'status']);
        $revenueChart = collect(range(11, 0))->map(function ($monthsAgo) use ($now, $ordersForChart) {
            $month = $now->copy()->subMonths($monthsAgo);
            $orders = $ordersForChart->filter(fn ($order) => $order->created_at->isSameMonth($month));

            return [
                'label' => Carbon::parse($month)->locale('ru')->translatedFormat('M'),
                'revenue' => (int) $orders->where('status', 'delivered')->sum('total'),
                'orders' => $orders->count(),
            ];
        })->values();

        $orderStatuses = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')->pluck('total', 'status');

        $categoryShare = Product::selectRaw('category_id, sum(price * sales_count) as revenue')
            ->with('category')
            ->groupBy('category_id')
            ->orderByDesc('revenue')
            ->limit(4)
            ->get();

        $totalCategoryRevenue = max(1, $categoryShare->sum('revenue'));

        $pendingSellers = User::where('role', 'seller')->where('store_status', 'pending')->latest()->limit(5)->get();
        $pendingProducts = Product::with('seller')->where('status', 'pending')->latest()->limit(5)->get();
        $recentUsers = User::latest()->limit(6)->get();
        $recentOrders = Order::with('user')->latest()->limit(6)->get();
        $topProducts = Product::with('category')->orderByDesc('sales_count')->limit(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'revenueChart', 'orderStatuses', 'categoryShare', 'totalCategoryRevenue',
            'pendingSellers', 'pendingProducts', 'recentUsers', 'recentOrders', 'topProducts'
        ));
    }
}
