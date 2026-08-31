@php
    $statusColors = ['pending' => 'warning', 'confirmed' => 'accent', 'processing' => 'accent', 'shipped' => 'accent', 'delivered' => 'success', 'cancelled' => 'danger'];
    $paymentColors = ['unpaid' => 'warning', 'pending' => 'warning', 'paid' => 'success', 'failed' => 'danger', 'refunded' => 'accent'];
    $sortLabels = ['latest' => 'Сначала новые', 'oldest' => 'Сначала старые', 'total_desc' => 'Сначала дорогие', 'total_asc' => 'Сначала недорогие'];
    $activeFilters = collect(request()->only(['search', 'status', 'payment_status', 'delivery_method', 'date_from', 'date_to']))->filter(fn ($v) => $v !== null && $v !== '')->count();
    $hasFilters = $activeFilters > 0 || (request('sort') && request('sort') !== 'latest');
@endphp
<x-dashboard-layout title="Заказы" active="orders">
<style>
.orders-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.orders-stats .metric-value{font-size:22px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.orders-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
.order-status-form{display:flex;align-items:center;gap:6px}
.order-status-select{height:32px;padding:0 8px;border-radius:8px;border:1px solid transparent;font-size:11px;font-weight:800;font-family:var(--font)}
@media(max-width:1200px){.orders-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.orders-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Центр обработки заказов</div><div class="page-subtitle">Контролируйте оплату, комплектацию и доставку заказов</div></div>
    <a class="btn-ghost" href="{{ request()->fullUrl() }}"><x-icon name="refresh" :size="15" />Обновить</a>
</div>

<div class="orders-stats">
    @foreach([
        ['Всего заказов', number_format($overview['total'], 0, '', ' '), 'cart', 'accent', 'За всё время', route('admin.orders')],
        ['Новые сегодня', number_format($overview['today'], 0, '', ' '), 'calendar', 'warning', now()->format('d.m.Y'), route('admin.orders', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()])],
        ['Сейчас в работе', number_format($overview['active'], 0, '', ' '), 'package', 'accent', 'До завершения', route('admin.orders', ['status' => 'active'])],
        ['Выручка доставленных', number_format($overview['revenue'], 0, '', ' ').' '.$businessSettings->currency, 'trending-up', 'success', 'Успешные заказы', route('admin.orders', ['status' => 'delivered'])],
    ] as $metric)
    <a href="{{ $metric[5] }}" class="stat stat-link">
        <div style="display:flex;justify-content:space-between;align-items:start">
            <div><div class="kicker">{{ $metric[0] }}</div><div class="metric-value">{{ $metric[1] }}</div></div>
            <div class="stat-icon" style="background:var(--{{ $metric[3] }}-soft);color:var(--{{ $metric[3] }})"><x-icon :name="$metric[2]" :size="17" /></div>
        </div>
        <div class="metric-note">{{ $metric[4] }}<x-icon name="chevron-right" :size="12" class="stat-link-arrow"/></div>
    </a>
    @endforeach
</div>

<form method="GET" class="card filter-card">
    <div class="kicker" style="margin-bottom:14px">Расширенный фильтр</div>
    <div class="filter-grid">
        <div class="filter-field" style="grid-column:span 2">
            <label>Поиск</label>
            <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Номер, имя, телефон, email или город">
        </div>
        <div class="filter-field">
            <label>Статус</label>
            <select class="admin-input" name="status">
                <option value="">Все статусы</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>В работе</option>
                @foreach($statusLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }} ({{ $statusCounts->get($val, 0) }})</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Оплата</label>
            <select class="admin-input" name="payment_status">
                <option value="">Любой статус</option>
                @foreach($paymentLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('payment_status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Получение</label>
            <select class="admin-input" name="delivery_method">
                <option value="">Любой способ</option>
                <option value="courier" {{ request('delivery_method') === 'courier' ? 'selected' : '' }}>Курьер</option>
                <option value="pickup" {{ request('delivery_method') === 'pickup' ? 'selected' : '' }}>Самовывоз</option>
            </select>
        </div>
        <div class="filter-field">
            <label>Дата от</label>
            <input class="admin-input" type="date" name="date_from" value="{{ request('date_from') }}">
        </div>
        <div class="filter-field">
            <label>Дата до</label>
            <input class="admin-input" type="date" name="date_to" value="{{ request('date_to') }}">
        </div>
        <div class="filter-field">
            <label>Сортировка</label>
            <select class="admin-input" name="sort">
                @foreach($sortLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('sort', 'latest') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>На странице</label>
            <select class="admin-input" name="per_page">
                @foreach([15, 30, 50] as $option)
                    <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>{{ $option }} заказов</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-foot">
        @if($hasFilters)<a href="{{ route('admin.orders') }}" class="btn-ghost">Сбросить</a>@endif
        <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} из {{ number_format($orders->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Заказ</th><th>Покупатель</th><th>Состав</th><th>Оплата</th><th>Сумма</th><th>Статус</th><th>Дата</th><th></th></tr></thead>
        <tbody>
            @forelse($orders as $order)
            @php
                $customerInitials = Str::of($order->customerName())->trim()->explode(' ')->filter()->take(2)->map(fn ($part) => Str::substr($part, 0, 1))->implode('');
            @endphp
            <tr>
                <td>
                    <a class="entity-name" href="{{ route('admin.orders.show', $order) }}">{{ $order->number }}</a>
                    <div class="entity-meta"><x-icon :name="$order->delivery_method === 'courier' ? 'truck' : 'store'" :size="11"/> {{ $order->delivery_method === 'courier' ? 'Курьер' : 'Самовывоз' }}@if($order->tracking_number) · {{ $order->tracking_number }}@endif</div>
                </td>
                <td><div class="entity-cell">
                    <div class="avatar">@if($order->user?->avatar_url)<img src="{{ $order->user->avatar_url }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:10px">@else{{ Str::upper($customerInitials ?: 'Г') }}@endif</div>
                    <div><div class="entity-name">{{ $order->customerName() }}</div><div class="entity-meta">{{ $order->city ?: 'Город не указан' }}</div></div>
                </div></td>
                <td>
                    <div style="font-weight:700">{{ $order->items_count }} {{ trans_choice('товар|товара|товаров', $order->items_count) }}</div>
                    <div class="entity-meta">{{ $order->phone ?: 'Телефон не указан' }}</div>
                </td>
                <td>
                    <span class="badge" style="background:var(--{{ $paymentColors[$order->payment_status] ?? 'accent' }}-soft);color:var(--{{ $paymentColors[$order->payment_status] ?? 'accent' }})">{{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}</span>
                    <div class="entity-meta">{{ $order->payment_method === 'cash' ? 'Наличные' : Str::headline($order->payment_method) }}</div>
                </td>
                <td style="font-weight:700;white-space:nowrap">{{ number_format($order->total, 0, '', ' ') }} {{ $businessSettings->currency }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="order-status-form" onsubmit="return orderStatusConfirm(this,'{{ $order->status }}','{{ $order->number }}')">
                        @csrf @method('PATCH')
                        <select name="status" class="order-status-select" data-status-picker style="background:var(--{{ $statusColors[$order->status] ?? 'accent' }}-soft);color:var(--{{ $statusColors[$order->status] ?? 'accent' }})">
                            @foreach($statusLabels as $value => $label)
                                <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }} {{ ($order->status === 'cancelled' && $value !== 'cancelled') ? 'disabled' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="action-btn" title="Сохранить статус"><x-icon name="check" :size="13"/></button>
                    </form>
                </td>
                <td><div style="font-weight:700">{{ $order->created_at->format('d.m.Y') }}</div><div class="entity-meta">{{ $order->created_at->format('H:i') }} · {{ $order->created_at->diffForHumans() }}</div></td>
                <td><div class="row-actions">
                    <a href="{{ route('admin.orders.show', $order) }}" class="action-btn" title="Открыть заказ"><x-icon name="eye" :size="13"/></a>
                    <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="action-btn" title="Печать"><x-icon name="printer" :size="13"/></a>
                </div></td>
            </tr>
            @empty<tr><td colspan="8" class="empty-state">Заказы не найдены. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $orders->links() }}</div>
</div>

<script>
function orderStatusConfirm(form, current, number) {
    var value = form.status.value;
    if (value === current) return true;
    if (value === 'cancelled') return confirm('Отменить заказ ' + number + '? Остатки товаров будут восстановлены.');
    if (value === 'delivered') return confirm('Подтвердить доставку заказа ' + number + '?');
    return true;
}
</script>
</x-dashboard-layout>
