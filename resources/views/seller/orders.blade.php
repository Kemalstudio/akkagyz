@php
    $statusLabels = ['pending' => 'Ожидает отправки', 'shipped' => 'В пути', 'delivered' => 'Доставлен'];
    $statusColors = ['pending' => 'warning', 'shipped' => 'accent', 'delivered' => 'success'];
@endphp
<x-dashboard-layout title="Заказы" active="orders">
    <div style="font-size:24px;font-weight:900;margin-bottom:24px;">Заказы с моими товарами</div>

    <div class="card" style="padding:24px;">
        @if($orderItems->isEmpty())
            <div style="color:var(--text-faint);font-size:14px;text-align:center;padding:40px 0;">Заказов пока нет.</div>
        @else
        <table style="width:100%;border-collapse:collapse;">
            <thead><tr><th>Заказ</th><th>Покупатель</th><th>Товар</th><th>Сумма</th><th>Статус</th></tr></thead>
            <tbody>
                @foreach($orderItems as $item)
                <tr>
                    <td style="font-weight:800;">#{{ $item->order->number }}</td>
                    <td>{{ $item->order->user->name }}</td>
                    <td>{{ $item->product_name }} &times;{{ $item->quantity }}</td>
                    <td style="font-weight:700;">{{ number_format($item->price * $item->quantity, 0, '', ' ') }} TMT</td>
                    <td>
                        <form method="POST" action="{{ route('seller.orders.update', $item) }}">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()" style="background:var(--{{ $statusColors[$item->status] }}-soft);color:var(--{{ $statusColors[$item->status] }});border:none;border-radius:20px;padding:4px 10px;font-size:11px;font-weight:800;font-family:var(--font);">
                                @foreach($statusLabels as $val => $label)
                                    <option value="{{ $val }}" {{ $item->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:20px;">{{ $orderItems->links() }}</div>
        @endif
    </div>
</x-dashboard-layout>
