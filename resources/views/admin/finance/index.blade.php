@php
    $typeLabels = ['sale' => 'Продажа', 'refund' => 'Возврат', 'payout' => 'Выплата'];
    $typeColors = ['sale' => 'success', 'refund' => 'danger', 'payout' => 'accent'];
    $sortLabels = ['latest' => 'Сначала новые', 'oldest' => 'Сначала старые', 'amount_desc' => 'Сумма: по убыванию', 'amount_asc' => 'Сумма: по возрастанию'];
    $hasFilters = collect(request()->only(['search', 'seller_id', 'type', 'date_from', 'date_to']))->filter(fn ($v) => $v !== null && $v !== '')->count() > 0;
@endphp
<x-dashboard-layout title="Финансы" active="finance">
<style>
.finance-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px}
.finance-stats .metric-value{font-size:20px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.finance-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
@media(max-width:1200px){.finance-stats{grid-template-columns:repeat(3,1fr)}}
@media(max-width:700px){.finance-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.finance-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Финансы маркетплейса</div><div class="page-subtitle">Комиссии, оборот и начисления продавцам</div></div>
    <div class="toolbar">
        <a href="{{ route('admin.payments.index') }}" class="btn-ghost"><x-icon name="credit-card" :size="15"/>Платежи</a>
        <a href="{{ route('admin.payouts.index') }}" class="btn-accent"><x-icon name="wallet" :size="15"/>Выплаты</a>
    </div>
</div>

<div class="finance-stats">
    @foreach([
        ['Оборот', number_format($stats['gross'], 0, '', ' ').' '.$businessSettings->currency, 'cash', 'accent', 'Продажи за всё время', route('admin.finance.index', ['type' => 'sale'])],
        ['Комиссия платформы', number_format($stats['commission'], 0, '', ' ').' '.$businessSettings->currency, 'wallet', 'warning', 'Доход '.$businessSettings->site_name, route('admin.finance.index', ['type' => 'sale', 'sort' => 'amount_desc'])],
        ['Начислено продавцам', number_format($stats['sellerNet'], 0, '', ' ').' '.$businessSettings->currency, 'trending-up', 'success', 'Чистая выручка продавцов', route('admin.payouts.index')],
        ['Возвращено', number_format($stats['refunded'], 0, '', ' ').' '.$businessSettings->currency, 'refresh', 'danger', 'Отменённые заказы', route('admin.finance.index', ['type' => 'refund'])],
        ['Ожидает выплаты', number_format($stats['pendingPayouts'], 0, '', ' ').' '.$businessSettings->currency, 'credit-card', 'accent', 'В очереди на выплату', route('admin.payouts.index', ['status' => 'pending'])],
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
            <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Описание операции или магазин">
        </div>
        <div class="filter-field">
            <label>Продавец</label>
            <select class="admin-input" name="seller_id">
                <option value="">Все продавцы</option>
                @foreach($sellers as $s)
                    <option value="{{ $s->id }}" {{ (int) request('seller_id') === $s->id ? 'selected' : '' }}>{{ $s->store_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Операция</label>
            <select class="admin-input" name="type">
                <option value="">Все операции</option>
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
                    <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>{{ $option }} операций</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-foot">
        @if($hasFilters)<a href="{{ route('admin.finance.index') }}" class="btn-ghost">Сбросить</a>@endif
        <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $transactions->firstItem() ?? 0 }}–{{ $transactions->lastItem() ?? 0 }} из {{ number_format($transactions->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Дата</th><th>Продавец</th><th>Операция</th><th>Описание</th><th>Оборот</th><th>Комиссия</th><th>Итого</th></tr></thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>{{ $t->created_at->format('d.m.Y') }}<div class="entity-meta">{{ $t->created_at->format('H:i') }}</div></td>
                <td><div class="entity-cell">
                    <div class="avatar">{{ Str::of($t->seller?->store_name ?: '—')->substr(0, 2)->upper() }}</div>
                    <div class="entity-name">{{ $t->seller?->store_name ?? '—' }}</div>
                </div></td>
                <td><span class="badge" style="background:var(--{{ $typeColors[$t->type] ?? 'accent' }}-soft);color:var(--{{ $typeColors[$t->type] ?? 'accent' }})">{{ $typeLabels[$t->type] ?? $t->type }}</span></td>
                <td>{{ $t->description }}</td>
                <td>{{ number_format($t->gross_amount, 0, '', ' ') }}</td>
                <td>{{ number_format($t->commission_amount, 0, '', ' ') }}</td>
                <td style="font-weight:700;color:{{ $t->net_amount < 0 ? 'var(--danger)' : 'var(--success)' }}">{{ number_format($t->net_amount, 0, '', ' ') }}</td>
            </tr>
            @empty<tr><td colspan="7" class="empty-state">Операций не найдено. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $transactions->links() }}</div>
</div>
</x-dashboard-layout>
