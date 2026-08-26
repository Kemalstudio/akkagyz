@php
    $statusLabels = ['pending' => 'Ожидает', 'processing' => 'В обработке', 'shipped' => 'В пути', 'delivered' => 'Доставлен', 'cancelled' => 'Отменён'];
    $statusColors = ['pending' => 'warning', 'processing' => 'accent', 'shipped' => 'accent', 'delivered' => 'success', 'cancelled' => 'danger'];
    $deliveryLabels = ['courier' => 'Курьером', 'pickup' => 'Самовывоз'];
    $paymentLabels = ['kaspi' => 'Kaspi Pay', 'card' => 'Банковская карта', 'cash' => 'Наличными'];
@endphp
<x-layout title="Заказ {{ $order->number }}">
    <div class="wrap" style="padding:32px 24px 64px;max-width:900px;">
        <a href="{{ route('orders.index') }}" style="font-size:13px;font-weight:700;color:var(--text-faint);display:flex;align-items:center;gap:6px;margin-bottom:16px;"><x-icon name="chevron-left" :size="14" />Мои заказы</a>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <div style="font-size:24px;font-weight:900;">Заказ {{ $order->number }}</div>
            <span class="badge" style="background:var(--{{ $statusColors[$order->status] }}-soft);color:var(--{{ $statusColors[$order->status] }});font-size:13px;padding:8px 14px;">{{ $statusLabels[$order->status] }}</span>
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
    </div>
</x-layout>
