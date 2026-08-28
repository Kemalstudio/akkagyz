<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreReport;
use App\Models\User;
use App\Notifications\SellerApplicationReviewed;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array($request->integer('per_page'), [15, 30, 50], true)
            ? $request->integer('per_page')
            : 15;

        $base = User::where('role', 'seller');
        $overview = [
            'total' => (clone $base)->count(),
            'approved' => (clone $base)->where('store_status', 'approved')->count(),
            'pending' => (clone $base)->where('store_status', 'pending')->count(),
            'rejected' => (clone $base)->where('store_status', 'rejected')->count(),
            'blocked' => (clone $base)->where('is_blocked', true)->count(),
        ];

        $sellers = $base->withCount('products')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search').'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('store_name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('store_status', $request->string('status')))
            ->when($request->boolean('blocked'), fn ($q) => $q->where('is_blocked', true))
            ->latest()->paginate($perPage)->withQueryString();
        $reports = StoreReport::with(['seller', 'user'])->where('status', 'pending')->latest()->limit(10)->get();

        return view('admin.sellers', compact('sellers', 'reports', 'overview'));
    }

    public function resolveReport(StoreReport $report)
    {
        $report->update(['status' => 'reviewed']);

        return back()->with('status', 'Жалоба отмечена как рассмотренная.');
    }

    public function approve(User $seller)
    {
        $seller->update(['store_status' => 'approved', 'store_approved_at' => now()]);
        $seller->notify(new SellerApplicationReviewed(true));

        return back()->with('status', "Продавец «{$seller->store_name}» одобрен.");
    }

    public function reject(User $seller)
    {
        $seller->update(['store_status' => 'rejected']);
        $seller->notify(new SellerApplicationReviewed(false));

        return back()->with('status', "Заявка «{$seller->store_name}» отклонена.");
    }

    public function block(User $seller)
    {
        $seller->update(['is_blocked' => ! $seller->is_blocked]);

        return back()->with('status', $seller->is_blocked ? 'Продавец заблокирован.' : 'Продавец разблокирован.');
    }
}
