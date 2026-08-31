<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StoreReport;
use App\Models\StoreReview;
use App\Models\User;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $base = fn () => User::approvedSellers()
            ->where('is_blocked', false)
            ->withCount(['products' => fn ($query) => $query
                ->active()
                ->whereNull('archived_at')]);

        $stats = [
            'stores' => (clone $base())->count(),
            'products' => Product::customerVisible()->marketplace()->count(),
        ];

        if ($request->filled('q')) {
            $sellers = $base()
                ->where('store_name', 'like', '%'.$request->string('q').'%')
                ->orderByDesc('is_vip')
                ->orderByDesc('store_views')
                ->paginate(12)
                ->withQueryString();

            return view('storefront.stores.index', [
                'sellers' => $sellers,
                'vipSellers' => collect(),
                'searching' => true,
                'stats' => $stats,
            ]);
        }

        $vipSellers = $base()->where('is_vip', true)->orderByDesc('store_views')->get();
        $sellers = $base()->where('is_vip', false)->orderByDesc('store_views')->paginate(12)->withQueryString();

        return view('storefront.stores.index', [
            'sellers' => $sellers,
            'vipSellers' => $vipSellers,
            'searching' => false,
            'stats' => $stats,
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $seller = User::approvedSellers()
            ->where('is_blocked', false)
            ->where('store_slug', $slug)
            ->firstOrFail();
        $seller->increment('store_views');

        $products = $seller->products()
            ->customerVisible()
            ->with(['category', 'seller', 'images'])
            ->latest()
            ->paginate(12);
        $reviews = $seller->storeReviews()->with('user')->latest()->limit(10)->get();
        $userReview = $request->user()
            ? $seller->storeReviews()->where('user_id', $request->user()->id)->first()
            : null;

        return view('storefront.stores.show', compact('seller', 'products', 'reviews', 'userReview'));
    }

    public function storeReview(Request $request, string $slug)
    {
        $seller = User::approvedSellers()
            ->where('is_blocked', false)
            ->where('store_slug', $slug)
            ->firstOrFail();

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        StoreReview::updateOrCreate(
            ['seller_id' => $seller->id, 'user_id' => $request->user()->id],
            $data
        );

        return back()->with('status', 'Спасибо за отзыв о магазине!');
    }

    public function storeReport(Request $request, string $slug)
    {
        $seller = User::approvedSellers()
            ->where('is_blocked', false)
            ->where('store_slug', $slug)
            ->firstOrFail();

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        StoreReport::create([
            'seller_id' => $seller->id,
            'user_id' => $request->user()->id,
            ...$data,
        ]);

        return back()->with('status', 'Жалоба отправлена администрации. Спасибо!');
    }
}
