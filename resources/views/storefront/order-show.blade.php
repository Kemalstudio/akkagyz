@php
    $statusLabels = \App\Models\Order::STATUS_LABELS;
    $statusColors = ['pending' => 'warning', 'processing' => 'accent', 'shipped' => 'accent', 'delivered' => 'success', 'cancelled' => 'danger'];
    $deliveryLabels = ['courier' => 'Курьером', 'pickup' => 'Самовывоз'];
    $paymentLabels = ['kaspi' => 'Kaspi Pay', 'card' => 'Банковская карта', 'cash' => 'Наличными'];
    $steps = ['pending' => 'Принят', 'processing' => 'В обработке', 'shipped' => 'В пути', 'delivered' => 'Доставлен'];
    $stepOrder = array_keys($steps);
    $currentStep = array_search($order->status, $stepOrder, true);
    $canCancel = in_array($order->status, ['pending', 'processing'], true);
@endphp
<x-layout title="Заказ {{ $order->number }}">
    <style>
        .order-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:0;padding:22px 6px}
        .order-step{position:relative;text-align:center;padding:0 6px}
        .order-step:before{content:'';position:absolute;height:3px;background:var(--border);left:-50%;right:50%;top:16px}
        .order-step:first-child:before{display:none}
        .order-step.done:before{background:var(--accent)}
        .order-step-dot{position:relative;z-index:1;margin:auto;width:34px;height:34px;border-radius:50%;display:grid;place-items:center;background:var(--bg);border:2px solid var(--border);color:var(--text-faint)}
        .order-step.done .order-step-dot{background:var(--accent);border-color:var(--accent);color:#fff}
        .order-step span{display:block;font-size:11px;font-weight:700;margin-top:8px;color:var(--text-faint)}
        .order-step.done span{color:var(--text)}
        @media(max-width:600px){.order-steps{grid-template-columns:1fr}.order-step{text-align:left;display:grid;grid-template-columns:34px 1fr;column-gap:12px;padding:0 0 16px}.order-step:before{width:3px;height:auto;left:16px;top:-16px;bottom:34px}.order-step span{grid-column:2;margin-top:0}}
    </style>
    <div class="wrap" style="padding:32px 24px 64px;max-width:900px;">
        <a href="{{ route('orders.index') }}" style="font-size:13px;font-weight:700;color:var(--text-faint);display:flex;align-items:center;gap:6px;margin-bottom:16px;"><x-icon name="chevron-left" :size="14" />Мои заказы</a>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
            <div style="font-size:24px;font-weight:900;">Заказ {{ $order->number }}</div>
            <span class="badge" style="background:var(--{{ $statusColors[$order->status] }}-soft);color:var(--{{ $statusColors[$order->status] }});font-size:13px;padding:8px 14px;">{{ $statusLabels[$order->status] }}</span>
        </div>

        @if(session('status'))<div style="background:var(--success-soft);color:var(--success);padding:12px 16px;border-radius:12px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('status') }}</div>@endif
        @if(session('error'))<div style="background:var(--danger-soft);color:var(--danger);padding:12px 16px;border-radius:12px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('error') }}</div>@endif

        <div style="border:1px solid var(--border);background:var(--surface);border-radius:16px;margin-bottom:20px;overflow:hidden;">
            @if($order->status === 'cancelled')
                <div style="padding:16px 20px;display:flex;align-items:center;gap:10px;color:var(--danger);font-size:13px;font-weight:700;"><x-icon name="x" :size="18" />Этот заказ отменён{{ $order->cancelled_at ? ' '.$order->cancelled_at->format('d.m.Y в H:i') : '' }}.</div>
            @else
                <div class="order-steps">
                    @foreach($stepOrder as $index => $key)
                        <div class="order-step {{ $currentStep !== false && $index <= $currentStep ? 'done' : '' }}">
                            <div class="order-step-dot"><x-icon name="check" :size="15" /></div>
                            <span>{{ $steps[$key] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
            @if($order->tracking_number)
                <div style="padding:12px 20px;border-top:1px solid var(--border);font-size:12px;color:var(--text-faint);">Трек-номер: <strong style="color:var(--text);">{{ $order->tracking_number }}</strong></div>
            @endif
        </div>

        <div style="border:1px solid var(--border);background:var(--surface);border-radius:16px;padding:24px;margin-bottom:20px;">
            @foreach($order->items as $item)
                <div style="display:flex;gap:14px;align-items:center;padding:12px 0;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                    <div style="width:56px;height:56px;border-radius:10px;background:var(--bg-elevated);display:flex;align-items:center;justify-content:center;color:var(--text-faint);flex-shrink:0;"><x-icon name="image" :size="22" /></div>
                    <div style="flex:1;">
                        <div style="font-size:14px;font-weight:700;">{{ $item->product_name }}</div>
                        <div style="font-size:12px;color:var(--text-faint);margin-top:2px;">{{ $item->quantity }} шт &times; {{ number_format($item->price, 0, '', ' ') }} TMT</div>
                    </div>
                    <div style="font-size:16px;font-weight:800;">{{ number_format($item->price * $item->quantity, 0, '', ' ') }} TMT</div>
                </div>
            @endforeach
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div style="border:1px solid var(--border);background:var(--surface);border-radius:16px;padding:20px;">
                <div style="font-size:14px;font-weight:800;margin-bottom:12px;">Доставка</div>
                <div style="font-size:13px;color:var(--text-muted);line-height:1.8;">
                    {{ $deliveryLabels[$order->delivery_method] }}<br>
                    {{ $order->city }}, {{ $order->address }}<br>
                    {{ $order->phone }}
                </div>
            </div>
            <div style="border:1px solid var(--border);background:var(--surface);border-radius:16px;padding:20px;">
                <div style="font-size:14px;font-weight:800;margin-bottom:12px;">Оплата и итого</div>
                <div style="font-size:13px;color:var(--text-muted);line-height:1.8;">
                    {{ $paymentLabels[$order->payment_method] }}<br>
                    Товары: {{ number_format($order->subtotal, 0, '', ' ') }} TMT<br>
                    @if($order->discount > 0)Скидка: &minus;{{ number_format($order->discount, 0, '', ' ') }} TMT<br>@endif
                    <span style="color:var(--text);font-weight:800;">Итого: {{ number_format($order->total, 0, '', ' ') }} TMT</span>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px;margin-top:24px;flex-wrap:wrap;">
            @if($canCancel)
                <form method="POST" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Отменить этот заказ?');">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-ghost" style="display:flex;align-items:center;gap:8px;color:var(--danger);"><x-icon name="x" :size="16" />Отменить заказ</button>
                </form>
            @endif
            <form method="POST" action="{{ route('orders.repeat', $order) }}">
                @csrf
                <button type="submit" class="btn-ghost" style="display:flex;align-items:center;gap:8px;"><x-icon name="refresh" :size="16" />Повторить заказ</button>
            </form>
        </div>
    </div>
</x-layout>
