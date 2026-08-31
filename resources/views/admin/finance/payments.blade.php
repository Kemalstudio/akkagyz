@php
    $statusLabels = ['succeeded' => 'Успешно', 'refunded' => 'Возвращён', 'pending' => 'Ожидает', 'failed' => 'Ошибка'];
    $statusColors = ['succeeded' => 'success', 'refunded' => 'accent', 'pending' => 'warning', 'failed' => 'danger'];
    $typeLabels = ['payment' => 'Оплата', 'refund' => 'Возврат'];
    $sortLabels = ['latest' => 'Сначала новые', 'oldest' => 'Сначала старые', 'amount_desc' => 'Сумма: по убыванию', 'amount_asc' => 'Сумма: по возрастанию'];
    $hasFilters = collect(request()->only(['search', 'status', 'type', 'date_from', 'date_to']))->filter(fn ($v) => $v !== null && $v !== '')->count() > 0;
@endphp
<x-dashboard-layout title="Платежи" active="payments">
<style>
.payments-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px}
.payments-stats .metric-value{font-size:20px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.payments-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
@media(max-width:1200px){.payments-stats{grid-template-columns:repeat(3,1fr)}}
@media(max-width:700px){.payments-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.payments-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Платёжные операции</div><div class="page-subtitle">Единый журнал подтверждений и возвратов</div></div>
    <a href="{{ route('admin.finance.index') }}" class="btn-ghost"><x-icon name="chart" :size="15"/>Финансы</a>
</div>

<div class="payments-stats">
    @foreach([
        ['Всего платежей', number_format($overview['total'], 0, '', ' '), 'credit-card', 'accent', 'Все операции', route('admin.payments.index')],
        ['Успешно оплачено', number_format($overview['succeeded'], 0, '', ' ').' TMT', 'check', 'success', 'Подтверждённые платежи', route('admin.payments.index', ['status' => 'succeeded'])],
        ['Ожидают', number_format($overview['pending'], 0, '', ' '), 'package', 'warning', 'Требуют подтверждения', route('admin.payments.index', ['status' => 'pending'])],
        ['Ошибки', number_format($overview['failed'], 0, '', ' '), 'x', 'danger', 'Не прошли оплату', route('admin.payments.index', ['status' => 'failed'])],
        ['Возвращено', number_format($overview['refunded'], 0, '', ' ').' TMT', 'refresh', 'accent', 'Оформленные возвраты', route('admin.payments.index', ['status' => 'refunded'])],
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
    <div class="filter-grid">
        <div class="filter-field" style="grid-column:span 2">
            <label>Поиск</label>
            <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Номер заказа">
        </div>
        <div class="filter-field">
            <label>Статус</label>
            <select class="admin-input" name="status">
                <option value="">Все статусы</option>
                @foreach($statusLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Тип</label>
            <select class="admin-input" name="type">
                <option value="">Все типы</option>
                @foreach($typeLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
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
                    <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>{{ $option }} платежей</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-foot">
        @if($hasFilters)<a href="{{ route('admin.payments.index') }}" class="btn-ghost">Сбросить</a>@endif
        <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }} из {{ number_format($payments->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Дата</th><th>Заказ</th><th>Покупатель</th><th>Провайдер</th><th>Тип</th><th>Статус</th><th>Сумма</th></tr></thead>
        <tbody>
            @forelse($payments as $p)
            <tr>
                <td>{{ $p->created_at->format('d.m.Y') }}<div class="entity-meta">{{ $p->created_at->format('H:i') }}</div></td>
                <td><a class="entity-name" href="{{ route('admin.orders.show', $p->order) }}">{{ $p->order->number }}</a></td>
                <td>{{ $p->order->customerName() }}</td>
                <td>{{ $p->provider ?: '—' }}</td>
                <td>{{ $typeLabels[$p->type] ?? ($p->type ?: '—') }}</td>
                <td><span class="badge" style="background:var(--{{ $statusColors[$p->status] ?? 'accent' }}-soft);color:var(--{{ $statusColors[$p->status] ?? 'accent' }})">{{ $statusLabels[$p->status] ?? $p->status }}</span></td>
                <td style="font-weight:700">{{ number_format($p->amount, 0, '', ' ') }} {{ $p->currency }}</td>
            </tr>
            @empty<tr><td colspan="7" class="empty-state">Платежи не найдены. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $payments->links() }}</div>
</div>
</x-dashboard-layout>
