<x-dashboard-layout title="Настройки магазина" active="settings">
    <div style="font-size:24px;font-weight:900;margin-bottom:24px;">Настройки магазина</div>

    <div style="display:grid;grid-template-columns:1.3fr 1fr;gap:20px;align-items:start;">
        <form method="POST" action="{{ route('seller.settings.update') }}" class="card" style="padding:24px;display:flex;flex-direction:column;gap:16px;">
            @csrf @method('PATCH')
            <div><label class="label">Название магазина</label><input required class="input" name="store_name" value="{{ old('store_name', $seller->store_name) }}"></div>
            <div><label class="label">Телефон</label><input required class="input" name="phone" value="{{ old('phone', $seller->phone) }}"></div>
            <div><label class="label">Адрес / город</label><input class="input" name="store_address" value="{{ old('store_address', $seller->store_address) }}" placeholder="Ашхабад, ул. Магтымгулы 12"></div>
            <div><label class="label">О магазине</label><textarea name="store_description" class="input" style="height:110px;padding:12px 14px;resize:none;">{{ old('store_description', $seller->store_description) }}</textarea></div>
            <div style="font-size:12px;color:var(--text-faint);">Публичная страница магазина: <a href="{{ route('stores.show', $seller->store_slug) }}" target="_blank">akkagyz.kz/marketplace/stores/{{ $seller->store_slug }}</a></div>
            <button type="submit" class="btn-accent" style="width:fit-content;">Сохранить</button>
        </form>

        <div style="display:flex;flex-direction:column;gap:20px;">
            <div class="card" style="padding:24px;">
                @if($seller->is_vip)
                    <div class="badge" style="background:var(--warning-soft);color:var(--warning);margin-bottom:10px;"><x-icon name="star" :size="12" />VIP-магазин</div>
                    <div style="font-size:13px;color:var(--text-muted);">Ваш магазин показывается первым в каталоге и в списке магазинов с {{ $seller->vip_activated_at?->format('d.m.Y') }}.</div>
                @else
                    <div style="font-size:15px;font-weight:800;margin-bottom:6px;">Стать VIP-магазином</div>
                    <div style="font-size:13px;color:var(--text-muted);margin-bottom:16px;">VIP-магазины показываются первыми в каталоге продавцов и получают приоритет в результатах поиска.</div>
                    <form method="POST" action="{{ route('seller.vip.activate') }}">
                        @csrf
                        <button type="submit" class="btn-accent" style="width:100%;"><x-icon name="star" :size="15" />Оплатить VIP</button>
                    </form>
                @endif
            </div>

            <div class="card" style="padding:24px;">
                <div style="font-size:15px;font-weight:800;margin-bottom:12px;">Статистика магазина</div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 0;border-bottom:1px solid var(--border);"><span style="color:var(--text-faint);">Просмотров страницы</span><span style="font-weight:800;">{{ $seller->store_views }}</span></div>
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 0;"><span style="color:var(--text-faint);">Рейтинг магазина</span><span style="font-weight:800;">{{ $seller->store_rating ?: '—' }}</span></div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
