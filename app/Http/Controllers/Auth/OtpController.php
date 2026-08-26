<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
class OtpController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if(!$request->session()->has('otp.user_id')) return redirect()->route('login');
        return view('auth.otp',['expiresAt'=>$request->session()->get('otp.expires_at')]);
    }
    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code'=>['required','digits_between:4,8']]);
        $attempts=(int)$request->session()->get('otp.attempts',0);
        if($attempts>=5) { $this->clear($request); return redirect()->route('login')->withErrors(['email'=>'Слишком много попыток. Войдите снова.']); }
        if(now()->timestamp>(int)$request->session()->get('otp.expires_at',0)) { $this->clear($request); return redirect()->route('login')->withErrors(['email'=>'Срок действия кода истёк. Войдите снова.']); }
        if(!Hash::check($request->code,(string)$request->session()->get('otp.code'))) { $request->session()->put('otp.attempts',$attempts+1); return back()->withErrors(['code'=>'Неверный код подтверждения.']); }
        $user=User::find($request->session()->get('otp.user_id')); $remember=(bool)$request->session()->get('otp.remember'); $destination=$request->session()->pull('otp.destination',route('home')); $this->clear($request);
        if(!$user) return redirect()->route('login'); Auth::login($user,$remember); $request->session()->regenerate();
        return redirect()->intended($destination);
    }
    public function resend(Request $request): RedirectResponse
    {
        $user=User::find($request->session()->get('otp.user_id')); if(!$user) return redirect()->route('login');
        self::issue($request,$user); return back()->with('status','Новый код отправлен.');
    }
    public static function issue(Request $request, User $user): void
    {
        $settings=BusinessSetting::current(); $length=max(4,min(8,(int)$settings->otp_length)); $code=str_pad((string)random_int(0,(10**$length)-1),$length,'0',STR_PAD_LEFT);
        $request->session()->put(['otp.user_id'=>$user->id,'otp.code'=>Hash::make($code),'otp.expires_at'=>now()->addMinutes($settings->otp_ttl)->timestamp,'otp.attempts'=>0]);
        Mail::raw("Ваш код подтверждения: {$code}\n\nКод действует {$settings->otp_ttl} минут.",fn($mail)=>$mail->to($user->email)->subject('Код подтверждения — '.$settings->site_name));
    }
    private function clear(Request $request): void { $request->session()->forget(['otp.user_id','otp.code','otp.expires_at','otp.attempts','otp.remember','otp.destination']); }
}
