<x-dashboard-layout title="Дашборд" active="dashboard">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:28px;">
        <div>
            <div style="font-size:24px;font-weight:900;">Добро пожаловать, {{ auth()->user()->store_name ?? auth()->user()->name }}</div>
            <div style="font-size:13px;color:var(--text-faint);margin-top:2px;">Обзор вашего магазина на {{ $businessSettings->site_name }}</div>
        </div>
        <a href="{{ route('seller.products.create') }}" class="btn-accent"><x-icon name="plus" :size="15" />Добавить товар</a>
    </div>

    @if(auth()->user()->store_status !== 'approved')
        <div class="alert alert-{{ auth()->user()->store_status === 'rejected' ? 'danger' : 'success' }}" style="margin-bottom:20px;">
            @if(auth()->user()->store_status === 'rejected')
                Заявка на регистрацию магазина отклонена администратором.
            @else
                Ваша заявка на рассмотрении у администратора. Товары можно добавлять уже сейчас &mdash; они опубликуются после подтверждения магазина.
            @endif
        </div>
    @endif

    <div class="rgrid-4" style="margin-bottom:20px;">
        <div class="stat">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;"><span style="font-size:12px;color:var(--text-faint);font-weight:700;">Выручка</span><div style="width:32px;height:32px;border-radius:9px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;"><x-icon name="cash" :size="16" /></div></div>
            <div style="font-size:26px;font-weight:900;margin-top:10px;">{{ number_format($stats['revenue'], 0, '', ' ') }} TMT</div>
        </div>
        <div class="stat">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;"><span style="font-size:12px;color:var(--text-faint);font-weight:700;">Заказы</span><div style="width:32px;height:32px;border-radius:9px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;"><x-icon name="cart" :size="16" /></div></div>
            <div style="font-size:26px;font-weight:900;margin-top:10px;">{{ $stats['orders'] }}</div>
        </div>
        <div class="stat">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;"><span style="font-size:12px;color:var(--text-faint);font-weight:700;">Товары</span><div style="width:32px;height:32px;border-radius:9px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;"><x-icon name="package" :size="16" /></div></div>
            <div style="font-size:26px;font-weight:900;margin-top:10px;">{{ $stats['products'] }}</div>
        </div>
        <div class="stat">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;"><span style="font-size:12px;color:var(--text-faint);font-weight:700;">Рейтинг</span><div style="width:32px;height:32px;border-radius:9px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;"><x-icon name="star" :size="16" /></div></div>
            <div style="font-size:26px;font-weight:900;margin-top:10px;">{{ $stats['rating'] ?: '—' }}</div>
        </div>
    </div>

    <div class="rgrid-2">
        <div class="card" style="padding:24px;">
            <div style="font-size:15px;font-weight:800;margin-bottom:16px;">Топ товары</div>
            @forelse($topProducts as $p)
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;">
                    <div style="width:36px;height:36px;border-radius:8px;background:var(--bg-elevated);flex-shrink:0;display:flex;align-items:center;justify-content:center;color:var(--text-faint);"><x-icon name="image" :size="16" /></div>
                    <div style="flex:1;"><div style="font-size:13px;font-weight:700;">{{ $p->name }}</div><div style="font-size:11px;color:var(--text-faint);">{{ $p->sales_count }} продаж</div></div>
                    <div style="font-size:13px;font-weight:800;">{{ number_format($p->price * $p->sales_count, 0, '', ' ') }} TMT</div>
                </div>
            @empty
                <div style="color:var(--text-faint);font-size:13px;">Пока нет данных</div>
            @endforelse
        </div>
        <div class="card" style="padding:24px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;"><span style="font-size:15px;font-weight:800;">Последние заказы</span><a href="{{ route('seller.orders.index') }}" style="font-size:13px;font-weight:800;">Все заказы &rarr;</a></div>
            @forelse($recentOrders as $oi)
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;">
                    <div style="flex:1;"><div style="font-size:13px;font-weight:700;">{{ $oi->product_name }}</div><div style="font-size:11px;color:var(--text-faint);">{{ $oi->order->customerName() }}</div></div>
                    <div style="font-size:13px;font-weight:800;">{{ number_format($oi->price * $oi->quantity, 0, '', ' ') }} TMT</div>
                </div>
            @empty
                <div style="color:var(--text-faint);font-size:13px;">Пока нет заказов</div>
            @endforelse
        </div>
    </div>
</x-dashboard-layout>
