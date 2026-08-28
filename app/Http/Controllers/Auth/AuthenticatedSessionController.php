<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\GuestCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\BusinessSetting;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        GuestCart::mergeInto($user);
        $destination = match (true) {
            $user->isAdmin() => route('admin.dashboard'),
            $user->isSeller() => route('seller.dashboard'),
            default => route('dashboard'),
        };

        $settings = BusinessSetting::current();
        if ($settings->otp_enabled && $settings->otp_channel === 'email' && ! $user->isAdmin()) {
            $request->session()->put(['otp.remember'=>$request->boolean('remember'),'otp.destination'=>$destination]);
            Auth::logout();
            try {
                OtpController::issue($request, $user);
            } catch (\Throwable $exception) {
                report($exception);
                $request->session()->forget(['otp.user_id','otp.code','otp.expires_at','otp.attempts','otp.remember','otp.destination']);
                return back()->withErrors(['email'=>'Не удалось отправить OTP-код. Обратитесь к администратору.']);
            }
            return redirect()->route('otp.show');
        }

        return redirect()->intended($destination);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
