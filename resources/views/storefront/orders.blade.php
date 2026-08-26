@php
    $statusLabels = ['pending' => 'Ожидает', 'processing' => 'В обработке', 'shipped' => 'В пути', 'delivered' => 'Доставлен', 'cancelled' => 'Отменён'];
    $statusColors = ['pending' => 'warning', 'processing' => 'accent', 'shipped' => 'accent', 'delivered' => 'success', 'cancelled' => 'danger'];
@endphp
<x-layout title="Мои заказы">
    <div class="wrap" style="padding:32px 24px 64px;">
        <div style="font-size:26px;font-weight:900;margin-bottom:24px;">Мои заказы</div>

        @if($orders->isEmpty())
            <div style="padding:60px 0;text-align:center;">
                <div style="color:var(--text-faint);font-size:15px;font-weight:600;margin-bottom:16px;">У вас пока нет заказов</div>
                <a href="{{ route('catalog') }}" class="btn-accent" style="display:inline-flex;">Перейти в каталог</a>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:16px;">
                @foreach($orders as $order)
                    <a href="{{ route('orders.show', $order) }}" style="display:flex;align-items:center;gap:20px;padding:20px;border-radius:16px;background:var(--surface);border:1px solid var(--border);">
                        <div>
                            <div style="font-size:15px;font-weight:800;">Заказ {{ $order->number }}</div>
                            <div style="font-size:12px;color:var(--text-faint);margin-top:2px;">{{ $order->created_at->format('d.m.Y H:i') }} &middot; {{ $order->items->count() }} товара</div>
                        </div>
                        <span class="badge" style="background:var(--{{ $statusColors[$order->status] }}-soft);color:var(--{{ $statusColors[$order->status] }});margin-left:auto;">{{ $statusLabels[$order->status] }}</span>
                        <div style="font-size:18px;font-weight:900;">{{ number_format($order->total, 0, '', ' ') }} TMT</div>
                    </a>
                @endforeach
            </div>
            <div style="margin-top:24px;">{{ $orders->links() }}</div>
        @endif
    </div>
</x-layout>
