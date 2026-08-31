<x-layout title="Стать продавцом">
    <div class="wrap" style="padding:48px 24px 64px;max-width:600px;">
        <div style="font-size:28px;font-weight:900;margin-bottom:8px;">Продавайте на {{ $businessSettings->site_name }}</div>
        <div style="font-size:14px;color:var(--text-faint);margin-bottom:28px;">Заполните данные о магазине &mdash; заявка поступит на рассмотрение администратору.</div>

        <form method="POST" action="{{ route('seller.become.store') }}" style="border:1px solid var(--border);background:var(--surface);border-radius:16px;padding:28px;display:flex;flex-direction:column;gap:16px;">
            @csrf
            <div>
                <label class="label">Название магазина</label>
                <input required class="input" name="store_name" value="{{ old('store_name') }}" placeholder="Например: PaperCo Store">
                @error('store_name')<div style="color:var(--danger);font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="label">Телефон</label>
                <input required class="input" name="phone" value="{{ old('phone') }}" placeholder="+993 65 123456">
            </div>
            <div>
                <label class="label">Адрес / город</label>
                <input class="input" name="store_address" value="{{ old('store_address') }}" placeholder="Ашхабад, ул. Магтымгулы 12">
            </div>
            <div>
                <label class="label">О магазине</label>
                <textarea name="store_description" class="input" style="height:90px;padding:12px 14px;resize:none;" placeholder="Расскажите, что вы продаёте">{{ old('store_description') }}</textarea>
            </div>
            <button type="submit" class="btn-accent" style="height:50px;font-size:15px;margin-top:8px;">Отправить заявку</button>
        </form>
    </div>
</x-layout>
