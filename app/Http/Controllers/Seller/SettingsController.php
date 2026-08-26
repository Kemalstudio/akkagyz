<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit(Request $request)
    {
        return view('seller.settings', ['seller' => $request->user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'store_name' => ['required', 'string', 'max:120'],
            'store_description' => ['nullable', 'string', 'max:1000'],
            'store_address' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Настройки магазина сохранены.');
    }

    public function activateVip(Request $request)
    {
        $request->user()->update(['is_vip' => true, 'vip_activated_at' => now()]);

        return back()->with('status', 'VIP-статус активирован! Ваш магазин теперь показывается первым.');
    }
}
