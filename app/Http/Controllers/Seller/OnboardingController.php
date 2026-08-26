<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewSellerApplication;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function create()
    {
        return view('seller.become');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:120'],
            'store_description' => ['nullable', 'string', 'max:1000'],
            'store_address' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
        ]);

        $request->user()->update([
            ...$data,
            'store_slug' => User::uniqueStoreSlug($data['store_name']),
            'role' => 'seller',
            'store_status' => 'pending',
        ]);

        if (BusinessSetting::current()->seller_notifications) {
            User::admins()->get()->each(fn ($admin) => $admin->notify(new NewSellerApplication($request->user())));
        }

        return redirect()->route('seller.dashboard')->with('status', 'Заявка отправлена! Ожидайте подтверждения от администратора.');
    }
}
