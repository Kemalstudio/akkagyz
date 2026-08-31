@php
    $currency = \App\Models\BusinessSetting::current()->currency ?: 'TMT';
    $statusLabels = \App\Models\Order::STATUS_LABELS;
    $statusColors = ['pending' => 'warning', 'processing' => 'accent', 'shipped' => 'accent', 'delivered' => 'success', 'cancelled' => 'danger'];
    $tabs = [null => 'Все', 'pending' => 'Ожидают', 'processing' => 'В обработке', 'shipped' => 'В пути', 'delivered' => 'Доставлены', 'cancelled' => 'Отменены'];
@endphp
<x-layout title="Мои заказы">
    <style>
        .orders-page{max-width:1160px;padding:34px 24px 72px}.orders-head{display:flex;justify-content:space-between;align-items:flex-end;gap:24px}.orders-head h1{margin:0;font-size:32px;letter-spacing:-.04em}.orders-head p{margin:7px 0 0;color:var(--text-faint);font-size:13px}.orders-stats{display:flex;gap:9px}.orders-stat{min-width:100px;padding:11px 14px;border:1px solid var(--border);border-radius:14px;background:var(--surface)}.orders-stat strong{display:block;font-size:18px}.orders-stat span{display:block;margin-top:2px;color:var(--text-faint);font-size:9px;text-transform:uppercase;letter-spacing:.06em}.orders-toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;margin:25px 0 18px}.orders-tabs{display:flex;gap:7px;overflow:auto;padding-bottom:2px}.orders-tab{flex:0 0 auto;padding:9px 13px;border:1px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text-muted);font-size:11px;font-weight:800}.orders-tab.active{border-color:var(--accent);background:var(--accent);color:#fff}.orders-search{display:flex;min-width:260px}.orders-search .input{height:40px;border-radius:11px 0 0 11px;background:var(--surface)}.orders-search button{width:42px;border:0;border-radius:0 11px 11px 0;background:var(--accent);color:#fff;display:grid;place-items:center;cursor:pointer}.orders-list{display:grid;gap:13px}.order-card{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:20px;padding:20px;border:1px solid var(--border);border-radius:20px;background:var(--surface);color:var(--text);transition:.2s}.order-card:hover{transform:translateY(-2px);border-color:var(--border-strong);box-shadow:0 15px 36px rgba(13,30,60,.08)}.order-top{display:flex;align-items:center;gap:11px;flex-wrap:wrap}.order-number{font-size:16px;font-weight:900}.order-date{color:var(--text-faint);font-size:11px}.order-badge{padding:6px 9px;border-radius:999px;font-size:9.5px;font-weight:900}.order-content{display:flex;align-items:center;gap:16px;margin-top:15px}.order-images{display:flex;padding-left:7px}.order-image{width:54px;height:54px;margin-left:-7px;border:3px solid var(--surface);border-radius:14px;background:var(--bg);display:grid;place-items:center;overflow:hidden;color:var(--text-faint)}.order-image img{width:100%;height:100%;object-fit:contain}.order-more{font-size:10px;font-weight:900}.order-copy{color:var(--text-faint);font-size:11px;line-height:1.55}.order-side{text-align:right;display:flex;flex-direction:column;align-items:flex-end;justify-content:space-between}.order-side strong{font-size:20px;letter-spacing:-.03em}.order-open{display:flex;align-items:center;gap:5px;color:var(--accent);font-size:10.5px;font-weight:900}.orders-empty{padding:58px 25px;text-align:center;border:1px dashed var(--border-strong);border-radius:22px;background:var(--surface)}.orders-empty-icon{width:68px;height:68px;margin:0 auto 16px;border-radius:20px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center}.orders-empty h2{margin:0;font-size:21px}.orders-empty p{margin:7px 0 20px;color:var(--text-faint);font-size:12px}
        @media(max-width:800px){.orders-head{align-items:flex-start;flex-direction:column}.orders-toolbar{align-items:stretch;flex-direction:column}.orders-search{min-width:0;width:100%}}
        @media(max-width:560px){.orders-page{padding:24px 14px 54px}.orders-head h1{font-size:27px}.orders-stats{width:100%;overflow:auto}.orders-stat{min-width:92px}.order-card{grid-template-columns:1fr;padding:16px}.order-side{padding-top:13px;border-top:1px solid var(--border);flex-direction:row;align-items:center}.order-content{align-items:flex-start}.order-image{width:46px;height:46px}.order-copy{font-size:10px}}
    </style>

    <div class="wrap orders-page">
        <header class="orders-head">
            <div><h1>Мои заказы</h1><p>Следите за доставкой, повторяйте покупки и открывайте детали заказа.</p></div>
            <div class="orders-stats">
                <div class="orders-stat"><strong>{{ $stats['all'] }}</strong><span>всего</span></div>
                <div class="orders-stat"><strong>{{ $stats['active'] }}</strong><span>в работе</span></div>
                <div class="orders-stat"><strong>{{ $stats['delivered'] }}</strong><span>доставлено</span></div>
            </div>
        </header>

        <div class="orders-toolbar">
            <nav class="orders-tabs" aria-label="Фильтр по статусу">
                @foreach($tabs as $status => $label)
                    <a class="orders-tab {{ request('status') === $status || ($status === null && !request()->filled('status')) ? 'active' : '' }}" href="{{ route('orders.index', array_filter(['status' => $status, 'q' => request('q')])) }}">{{ $label }}</a>
                @endforeach
            </nav>
            <form class="orders-search" method="GET" action="{{ route('orders.index') }}">
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <input class="input" name="q" value="{{ request('q') }}" placeholder="Номер заказа" aria-label="Поиск по номеру заказа">
                <button type="submit" aria-label="Найти"><x-icon name="search" :size="16" /></button>
            </form>
        </div>

        @if($orders->isEmpty())
            <div class="orders-empty">
                <div class="orders-empty-icon"><x-icon name="package" :size="30" /></div>
                <h2>{{ request()->hasAny(['status', 'q']) ? 'Заказы не найдены' : 'У вас пока нет заказов' }}</h2>
                <p>{{ request()->hasAny(['status', 'q']) ? 'Измените фильтр или сбросьте поиск.' : 'Выберите товары в каталоге и оформите первый заказ.' }}</p>
                <a href="{{ request()->hasAny(['status', 'q']) ? route('orders.index') : route('catalog') }}" class="btn-accent" style="display:inline-flex;height:46px;">{{ request()->hasAny(['status', 'q']) ? 'Показать все' : 'Перейти в каталог' }}</a>
            </div>
        @else
            <div class="orders-list">
                @foreach($orders as $order)
                    @php
                        $color = $statusColors[$order->status] ?? 'accent';
                        $unitsCount = $order->items->sum('quantity');
                    @endphp
                    <a class="order-card" href="{{ route('orders.show', $order) }}">
                        <div>
                            <div class="order-top"><span class="order-number">Заказ {{ $order->number }}</span><span class="order-badge" style="background:var(--{{ $color }}-soft);color:var(--{{ $color }})">{{ $statusLabels[$order->status] ?? $order->status }}</span><time class="order-date" datetime="{{ $order->created_at->toIso8601String() }}">{{ $order->created_at->format('d.m.Y, H:i') }}</time></div>
                            <div class="order-content">
                                <div class="order-images">
                                    @foreach($order->items->take(3) as $item)
                                        @php($image = $item->product?->images->first())
                                        <span class="order-image">@if($image)<img src="{{ $image->url }}" alt="" loading="lazy">@else<x-icon name="image" :size="18" />@endif</span>
                                    @endforeach
                                    @if($order->items->count() > 3)<span class="order-image order-more">+{{ $order->items->count() - 3 }}</span>@endif
                                </div>
                                <div class="order-copy">{{ $unitsCount }} шт. · {{ $order->items->count() }} позиций<br>{{ $order->delivery_method === 'pickup' ? 'Самовывоз' : 'Курьерская доставка' }}</div>
                            </div>
                        </div>
                        <div class="order-side"><strong>{{ number_format($order->total, 0, '', ' ') }} {{ $currency }}</strong><span class="order-open">Подробнее <x-icon name="chevron-right" :size="13" /></span></div>
                    </a>
                @endforeach
            </div>
            <div style="margin-top:24px">{{ $orders->links() }}</div>
        @endif
    </div>
</x-layout>
