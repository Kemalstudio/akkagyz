@php
    $currency = \App\Models\BusinessSetting::current()->currency ?: 'TMT';
    $statusLabels = \App\Models\Order::STATUS_LABELS;
    $statusColors = ['pending' => 'warning', 'processing' => 'accent', 'shipped' => 'accent', 'delivered' => 'success', 'cancelled' => 'danger'];
    $deliveryLabels = ['courier' => 'Курьерская доставка', 'pickup' => 'Самовывоз'];
    $paymentLabels = ['cash' => 'Наличными при получении', 'card' => 'Банковская карта', 'kaspi' => 'Kaspi Pay'];
    $paymentStatusLabels = ['unpaid' => 'Ожидает оплаты', 'paid' => 'Оплачен', 'refunded' => 'Возвращён'];
    $steps = ['pending' => ['Принят', 'Заказ зарегистрирован'], 'processing' => ['В обработке', 'Собираем товары'], 'shipped' => ['В пути', 'Передан в доставку'], 'delivered' => ['Доставлен', 'Заказ получен']];
    $stepKeys = array_keys($steps);
    $currentStep = array_search($order->status, $stepKeys, true);
    $canCancel = in_array($order->status, ['pending', 'processing'], true);
    $statusColor = $statusColors[$order->status] ?? 'accent';
    $summarySubtotal = (int) $order->subtotal - (int) $order->discount + (int) $order->express_fee === (int) $order->total
        ? (int) $order->subtotal
        : (int) $order->total + (int) $order->discount - (int) $order->express_fee;
@endphp
<x-layout title="Заказ {{ $order->number }}">
    <style>
        .order-page{max-width:1160px;padding:28px 24px 72px}.order-back{display:inline-flex;align-items:center;gap:6px;margin-bottom:18px;color:var(--text-faint);font-size:12px;font-weight:800}.order-hero{display:flex;align-items:flex-start;justify-content:space-between;gap:24px;margin-bottom:22px}.order-hero h1{margin:0;font-size:29px;letter-spacing:-.04em}.order-hero-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:8px;color:var(--text-faint);font-size:11px}.order-status{display:inline-flex;align-items:center;gap:7px;padding:9px 13px;border-radius:999px;font-size:11px;font-weight:900}.order-status:before{content:'';width:7px;height:7px;border-radius:50%;background:currentColor}.order-tools{display:flex;gap:8px}.order-tool{height:38px;padding:0 12px;border:1px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text-muted);display:flex;align-items:center;gap:6px;font-size:10px;font-weight:800;cursor:pointer}.order-tool:hover{border-color:var(--accent);color:var(--accent)}.order-progress-card{padding:25px 24px;border:1px solid var(--border);border-radius:21px;background:var(--surface);margin-bottom:20px}.order-progress{display:grid;grid-template-columns:repeat(4,1fr)}.order-step{position:relative;text-align:center}.order-step:before{content:'';position:absolute;top:18px;right:50%;left:-50%;height:3px;background:var(--border)}.order-step:first-child:before{display:none}.order-step.done:before{background:var(--accent)}.order-dot{position:relative;z-index:1;width:38px;height:38px;margin:auto;border:2px solid var(--border);border-radius:50%;background:var(--bg);color:var(--text-faint);display:grid;place-items:center}.order-step.done .order-dot{border-color:var(--accent);background:var(--accent);color:#fff;box-shadow:0 7px 17px color-mix(in srgb,var(--accent) 25%,transparent)}.order-step strong{display:block;margin-top:9px;font-size:11px}.order-step small{display:block;margin-top:3px;color:var(--text-faint);font-size:9px}.order-cancelled{display:flex;align-items:center;gap:13px;padding:4px;color:var(--danger)}.order-cancelled-icon{width:42px;height:42px;border-radius:13px;background:var(--danger-soft);display:grid;place-items:center}.order-cancelled strong{display:block;font-size:14px}.order-cancelled span{display:block;margin-top:3px;color:var(--text-faint);font-size:10px}.tracking-panel{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-top:22px;padding-top:18px;border-top:1px solid var(--border)}.tracking-panel span{display:block;color:var(--text-faint);font-size:9px;text-transform:uppercase;letter-spacing:.08em}.tracking-panel strong{display:block;margin-top:4px;font-size:14px}.order-layout{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:20px;align-items:start}.order-main{display:grid;gap:20px}.order-panel{border:1px solid var(--border);border-radius:20px;background:var(--surface);overflow:hidden}.order-panel-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid var(--border)}.order-panel-head h2{margin:0;font-size:16px}.order-panel-head span{color:var(--text-faint);font-size:10px}.order-items{padding:3px 20px}.order-item{display:grid;grid-template-columns:68px minmax(0,1fr) auto;gap:14px;align-items:center;padding:16px 0;border-bottom:1px solid var(--border)}.order-item:last-child{border-bottom:0}.order-item-img{width:68px;height:68px;border:1px solid var(--border);border-radius:14px;background:var(--bg);display:grid;place-items:center;overflow:hidden;color:var(--text-faint)}.order-item-img img{width:100%;height:100%;object-fit:contain}.order-item-name{color:var(--text);font-size:13px;font-weight:800;line-height:1.4}.order-item-meta{margin-top:5px;color:var(--text-faint);font-size:10px}.order-item-price{text-align:right;font-size:14px;font-weight:900;white-space:nowrap}.order-item-price small{display:block;margin-top:4px;color:var(--text-faint);font-size:9px;font-weight:600}.order-info-grid{display:grid;grid-template-columns:1fr 1fr}.order-info{padding:20px}.order-info+ .order-info{border-left:1px solid var(--border)}.order-info-title{display:flex;align-items:center;gap:8px;margin-bottom:12px;font-size:12px;font-weight:900}.order-info-title svg{color:var(--accent)}.order-info p{margin:0;color:var(--text-muted);font-size:11px;line-height:1.75}.order-side{position:sticky;top:24px;display:grid;gap:14px}.order-summary{padding:21px;border:1px solid var(--border);border-radius:20px;background:var(--surface);box-shadow:0 16px 40px rgba(14,30,60,.07)}.order-summary h2{margin:0 0 13px;font-size:17px}.order-line{display:flex;justify-content:space-between;gap:16px;padding:8px 0;color:var(--text-muted);font-size:11.5px}.order-line strong{color:var(--text)}.order-total{display:flex;align-items:flex-end;justify-content:space-between;gap:15px;margin-top:8px;padding-top:15px;border-top:1px solid var(--border)}.order-total span{font-size:13px;font-weight:900}.order-total strong{font-size:23px;letter-spacing:-.04em}.order-payment-status{display:flex;align-items:center;gap:7px;margin-top:14px;padding:10px 11px;border-radius:11px;background:var(--bg);color:var(--text-faint);font-size:10px}.order-actions{display:grid;gap:9px}.order-actions .btn-accent,.order-actions .btn-ghost{width:100%;height:46px;border-radius:12px}.cancel-details{border:1px solid var(--border);border-radius:14px;background:var(--surface);overflow:hidden}.cancel-details summary{padding:13px 15px;color:var(--danger);font-size:11px;font-weight:800;cursor:pointer;list-style:none}.cancel-details summary::-webkit-details-marker{display:none}.cancel-form{padding:0 14px 14px}.cancel-form select{width:100%;height:40px;margin-bottom:9px;padding:0 10px;border:1px solid var(--border);border-radius:10px;background:var(--bg);color:var(--text);font:inherit;font-size:10px}.cancel-form button{width:100%;height:40px;border:0;border-radius:10px;background:var(--danger);color:#fff;font-weight:800;cursor:pointer}.history-list{padding:8px 20px 18px}.history-item{position:relative;padding:12px 0 3px 23px}.history-item:before{content:'';position:absolute;left:3px;top:18px;width:8px;height:8px;border-radius:50%;background:var(--accent)}.history-item:after{content:'';position:absolute;left:6px;top:28px;bottom:-10px;width:2px;background:var(--border)}.history-item:last-child:after{display:none}.history-item strong{font-size:11px}.history-item span{display:block;margin-top:3px;color:var(--text-faint);font-size:9.5px}.history-item p{margin:5px 0 0;color:var(--text-muted);font-size:10px}
        @media(max-width:900px){.order-layout{grid-template-columns:1fr}.order-side{position:static}.order-hero{flex-direction:column}.order-tools{align-self:stretch}.order-tool{flex:1;justify-content:center}}
        @media(max-width:620px){.order-page{padding:22px 14px 54px}.order-hero h1{font-size:24px}.order-progress{grid-template-columns:1fr}.order-step{display:grid;grid-template-columns:38px 1fr;text-align:left;column-gap:12px;padding-bottom:18px}.order-step:before{width:3px;height:auto;left:18px;right:auto;top:-18px;bottom:38px}.order-step strong,.order-step small{grid-column:2;margin-top:0}.order-step small{margin-top:-15px}.order-progress-card{padding:20px}.order-item{grid-template-columns:56px 1fr}.order-item-img{width:56px;height:60px}.order-item-price{grid-column:2;text-align:left}.order-info-grid{grid-template-columns:1fr}.order-info+ .order-info{border-left:0;border-top:1px solid var(--border)}.order-tools{flex-wrap:wrap}}
        @media print{.site-header,footer,.order-back,.order-tools,.order-actions,.cancel-details{display:none!important}.order-page{padding:0}.order-layout{grid-template-columns:1fr 300px}.order-side{position:static}}
    </style>

    <div class="wrap order-page">
        <a class="order-back" href="{{ route('orders.index') }}"><x-icon name="chevron-left" :size="14" />Все заказы</a>
        <header class="order-hero">
            <div><h1>Заказ {{ $order->number }}</h1><div class="order-hero-meta"><time datetime="{{ $order->created_at->toIso8601String() }}">Оформлен {{ $order->created_at->format('d.m.Y в H:i') }}</time><span>·</span><span>{{ $order->items->sum('quantity') }} шт.</span><span class="order-status" style="background:var(--{{ $statusColor }}-soft);color:var(--{{ $statusColor }})">{{ $statusLabels[$order->status] ?? $order->status }}</span></div></div>
            <div class="order-tools"><button type="button" class="order-tool" data-copy="{{ $order->number }}"><x-icon name="copy" :size="14" />Копировать номер</button><button type="button" class="order-tool" onclick="window.print()"><x-icon name="printer" :size="14" />Печать</button></div>
        </header>

        <section class="order-progress-card" aria-label="Статус заказа">
            @if($order->status === 'cancelled')
                <div class="order-cancelled"><span class="order-cancelled-icon"><x-icon name="x" :size="19" /></span><div><strong>Заказ отменён</strong><span>{{ $order->cancelled_at ? $order->cancelled_at->format('d.m.Y в H:i') : 'Заказ больше не обрабатывается' }}</span></div></div>
            @else
                <div class="order-progress">
                    @foreach($steps as $key => [$label, $copy])
                        @php
                            $index = array_search($key, $stepKeys, true);
                        @endphp
                        <div class="order-step {{ $currentStep !== false && $index <= $currentStep ? 'done' : '' }}"><span class="order-dot"><x-icon name="check" :size="15" /></span><strong>{{ $label }}</strong><small>{{ $copy }}</small></div>
                    @endforeach
                </div>
            @endif
            @if($order->tracking_number)
                <div class="tracking-panel"><div><span>Трек-номер</span><strong>{{ $order->tracking_number }}</strong></div><button type="button" class="order-tool" data-copy="{{ $order->tracking_number }}"><x-icon name="copy" :size="13" />Копировать</button></div>
            @endif
        </section>

        <div class="order-layout">
            <div class="order-main">
                <section class="order-panel">
                    <div class="order-panel-head"><h2>Состав заказа</h2><span>{{ $order->items->count() }} позиций</span></div>
                    <div class="order-items">
                        @foreach($order->items as $item)
                            @php
                                $image = $item->product?->images->first();
                                $productUrl = $item->product ? route($item->product->seller_id ? 'marketplace.products.show' : 'products.show', $item->product->slug) : null;
                            @endphp
                            <div class="order-item">
                                <div class="order-item-img">@if($image)<img src="{{ $image->url }}" alt="{{ $item->product_name }}">@else<x-icon name="image" :size="21" />@endif</div>
                                <div>@if($productUrl)<a class="order-item-name" href="{{ $productUrl }}">{{ $item->product_name }}</a>@else<div class="order-item-name">{{ $item->product_name }}</div>@endif<div class="order-item-meta">{{ $item->quantity }} шт. × {{ number_format($item->price, 0, '', ' ') }} {{ $currency }}@if($item->seller) · {{ $item->seller->store_name ?? $item->seller->name }}@endif</div></div>
                                <div class="order-item-price">{{ number_format($item->price * $item->quantity, 0, '', ' ') }} {{ $currency }}<small>{{ $statusLabels[$item->status] ?? $item->status }}</small></div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="order-panel">
                    <div class="order-info-grid">
                        <div class="order-info"><div class="order-info-title"><x-icon name="truck" :size="16" />Получение</div><p><strong style="color:var(--text)">{{ $deliveryLabels[$order->delivery_method] ?? $order->delivery_method }}</strong><br>{{ $order->city }}@if($order->delivery_method !== 'pickup'), {{ $order->address }}@endif<br>{{ $order->phone }}</p></div>
                        <div class="order-info"><div class="order-info-title"><x-icon name="cash" :size="16" />Оплата</div><p><strong style="color:var(--text)">{{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}</strong><br>{{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}</p></div>
                    </div>
                </section>

                @if($order->statusHistories->isNotEmpty())
                    <section class="order-panel"><div class="order-panel-head"><h2>История заказа</h2></div><div class="history-list">@foreach($order->statusHistories->sortByDesc('created_at') as $history)<div class="history-item"><strong>{{ $statusLabels[$history->to_status] ?? $history->to_status }}</strong><span>{{ $history->created_at->format('d.m.Y, H:i') }}</span>@if($history->comment)<p>{{ $history->comment }}</p>@endif</div>@endforeach</div></section>
                @endif
            </div>

            <aside class="order-side">
                <div class="order-summary">
                    <h2>Итого</h2>
                    <div class="order-line"><span>Товары</span><strong>{{ number_format($summarySubtotal, 0, '', ' ') }} {{ $currency }}</strong></div>
                    @if($order->discount > 0)<div class="order-line"><span>Скидка</span><strong style="color:var(--success)">&minus;{{ number_format($order->discount, 0, '', ' ') }} {{ $currency }}</strong></div>@endif
                    @if($order->express_fee > 0)<div class="order-line"><span>Быстрый заказ</span><strong>+{{ number_format($order->express_fee, 0, '', ' ') }} {{ $currency }}</strong></div>@endif
                    <div class="order-line"><span>Доставка</span><strong style="color:var(--success)">Бесплатно</strong></div>
                    <div class="order-total"><span>К оплате</span><strong>{{ number_format($order->total, 0, '', ' ') }} {{ $currency }}</strong></div>
                    <div class="order-payment-status"><x-icon name="cash" :size="14" />{{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}</div>
                </div>
                <div class="order-actions">
                    <form method="POST" action="{{ route('orders.repeat', $order) }}">@csrf<button type="submit" class="btn-accent"><x-icon name="refresh" :size="15" />Повторить заказ</button></form>
                    <a href="{{ route('catalog') }}" class="btn-ghost" style="display:flex;align-items:center;justify-content:center;gap:7px">Продолжить покупки</a>
                </div>
                @if($canCancel)
                    <details class="cancel-details"><summary>Отменить заказ</summary><form class="cancel-form" method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Подтвердить отмену заказа?')">@csrf @method('PATCH')<select name="reason" aria-label="Причина отмены"><option value="">Причина не указана</option><option>Передумал(а)</option><option>Ошибка в адресе или контактах</option><option>Хочу изменить состав заказа</option><option>Нашёл(ла) другой товар</option></select><button type="submit">Подтвердить отмену</button></form></details>
                @endif
            </aside>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-copy]').forEach(button => button.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(button.dataset.copy);
                const original = button.innerHTML;
                button.textContent = 'Скопировано';
                setTimeout(() => button.innerHTML = original, 1400);
            } catch (_) {}
        }));
    </script>
</x-layout>
