<x-guest-layout>
    <div style="text-align:center;margin-bottom:24px"><div style="width:58px;height:58px;border-radius:18px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center;margin:0 auto 16px"><x-icon name="lock" :size="25"/></div><div style="font-size:23px;font-weight:700">Подтвердите вход</div><p style="font-size:13px;line-height:1.6;color:var(--text-faint);margin:8px 0 0">Мы отправили одноразовый код на вашу электронную почту</p></div>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('otp.verify') }}">@csrf<label class="label" for="code">Код подтверждения</label><input id="code" class="input" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="8" autofocus required placeholder="Введите код" style="height:56px;text-align:center;font-size:22px;letter-spacing:.3em"><button class="btn-accent" style="width:100%;height:48px;margin-top:16px">Продолжить</button></form>
    <form method="POST" action="{{ route('otp.resend') }}" style="text-align:center;margin-top:18px">@csrf<button style="border:0;background:none;color:var(--accent);font-size:12px;font-weight:600">Отправить код ещё раз</button></form>
</x-guest-layout>
