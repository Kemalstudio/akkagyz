<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewSellerApplication;
use App\Models\BusinessSetting;
use App\Support\GuestCart;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'in:customer,seller'],
            'store_name' => ['required_if:role,seller', 'nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $role = $request->input('role', 'customer');
        $isSeller = $role === 'seller';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'store_name' => $isSeller ? $request->input('store_name') : null,
            'store_slug' => $isSeller ? User::uniqueStoreSlug($request->input('store_name')) : null,
            'store_status' => $isSeller ? 'pending' : null,
            'phone' => $request->input('phone'),
        ]);

        event(new Registered($user));

        Auth::login($user);
        GuestCart::mergeInto($user);

        if ($isSeller) {
            if (BusinessSetting::current()->seller_notifications) {
                User::admins()->get()->each(fn ($admin) => $admin->notify(new NewSellerApplication($user)));
            }

            return redirect()->route('seller.dashboard')->with('status', 'Добро пожаловать! Ваша заявка продавца на рассмотрении у администратора.');
        }

        return redirect()->route('dashboard');
    }
}
