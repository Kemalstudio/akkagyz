@php
    $statusLabels = ['pending' => 'На рассмотрении', 'approved' => 'Одобрен', 'rejected' => 'Отклонён'];
    $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
    $hasFilters = collect(request()->only(['search', 'status', 'blocked']))->filter(fn ($v) => $v !== null && $v !== '')->count() > 0;
@endphp
<x-dashboard-layout title="Продавцы" active="sellers">
<style>
.sellers-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px}
.sellers-stats .metric-value{font-size:22px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.sellers-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.chip-check{display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:var(--text-muted);cursor:pointer}
.chip-check input{accent-color:var(--danger)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
.reports-card{padding:22px;margin-bottom:20px}
.report-row{display:flex;align-items:center;gap:12px;padding:12px 0}
.report-row+.report-row{border-top:1px solid var(--border)}
@media(max-width:1200px){.sellers-stats{grid-template-columns:repeat(3,1fr)}}
@media(max-width:700px){.sellers-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.sellers-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Продавцы</div><div class="page-subtitle">Проверяйте заявки, магазины и жалобы покупателей</div></div>
</div>

<div class="sellers-stats">
    @foreach([
        ['Всего продавцов', number_format($overview['total'], 0, '', ' '), 'store', 'accent', 'Все магазины на платформе', route('admin.sellers')],
        ['Одобрены', number_format($overview['approved'], 0, '', ' '), 'check', 'success', 'Работают на витрине', route('admin.sellers', ['status' => 'approved'])],
        ['На рассмотрении', number_format($overview['pending'], 0, '', ' '), 'package', 'warning', 'Ждут проверки заявки', route('admin.sellers', ['status' => 'pending'])],
        ['Отклонены', number_format($overview['rejected'], 0, '', ' '), 'x', 'danger', 'Заявка не прошла', route('admin.sellers', ['status' => 'rejected'])],
        ['Заблокированы', number_format($overview['blocked'], 0, '', ' '), 'lock', 'danger', 'Доступ ограничен', route('admin.sellers', ['blocked' => 1])],
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

@if($reports->isNotEmpty())
<div class="card reports-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
        <span style="font-size:15px;font-weight:800">Жалобы на магазины</span>
        <span class="badge" style="background:var(--danger-soft);color:var(--danger)">{{ $reports->count() }} новых</span>
    </div>
    @foreach($reports as $report)
        <div class="report-row">
            <div style="flex:1;min-width:0">
                <div style="font-size:13px;font-weight:700">{{ $report->reason }} &mdash; {{ $report->seller->store_name }}</div>
                <div class="entity-meta" style="margin-top:2px">{{ $report->comment ?: 'Без комментария' }} &middot; от {{ $report->user->name }}</div>
            </div>
            <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">@csrf<button type="submit" class="action-btn">Рассмотрено</button></form>
        </div>
    @endforeach
</div>
@endif

<form method="GET" class="card filter-card">
    <div class="filter-grid">
        <div class="filter-field" style="grid-column:span 2">
            <label>Поиск</label>
            <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Магазин, владелец или email">
        </div>
        <div class="filter-field">
            <label>Статус заявки</label>
            <select class="admin-input" name="status">
                <option value="">Все статусы</option>
                @foreach($statusLabels as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>На странице</label>
            <select class="admin-input" name="per_page">
                @foreach([15, 30, 50] as $option)
                    <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>{{ $option }} продавцов</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-foot">
        <label class="chip-check"><input type="checkbox" name="blocked" value="1" {{ request()->boolean('blocked') ? 'checked' : '' }}><x-icon name="lock" :size="13" />Только заблокированные</label>
        <div style="display:flex;gap:10px">
            @if($hasFilters)<a href="{{ route('admin.sellers') }}" class="btn-ghost">Сбросить</a>@endif
            <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
        </div>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $sellers->firstItem() ?? 0 }}–{{ $sellers->lastItem() ?? 0 }} из {{ number_format($sellers->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Магазин</th><th>Владелец</th><th>Товаров</th><th>Статус</th><th></th></tr></thead>
        <tbody>
            @forelse($sellers as $seller)
            <tr>
                <td><div class="entity-cell">
                    <div class="avatar">{{ Str::of($seller->store_name ?? $seller->name)->substr(0, 2)->upper() }}</div>
                    <div>
                        <div class="entity-name">{{ $seller->store_name ?? '—' }}</div>
                        @if($seller->is_blocked)<div class="entity-meta" style="color:var(--danger)">Заблокирован</div>@endif
                    </div>
                </div></td>
                <td>{{ $seller->name }}<div class="entity-meta">{{ $seller->email }}</div></td>
                <td>{{ $seller->products_count }}</td>
                <td><span class="badge" style="background:var(--{{ $statusColors[$seller->store_status] ?? 'warning' }}-soft);color:var(--{{ $statusColors[$seller->store_status] ?? 'warning' }})">{{ $statusLabels[$seller->store_status] ?? '—' }}</span></td>
                <td><div class="row-actions">
                    @if($seller->store_slug)<a href="{{ route('stores.show', $seller->store_slug) }}" target="_blank" class="action-btn" title="Открыть магазин"><x-icon name="arrow-right" :size="13"/></a>@endif
                    @if($seller->store_status !== 'approved')
                        <form method="POST" action="{{ route('admin.sellers.approve', $seller) }}" onsubmit="return confirm('Одобрить продавца «{{ $seller->store_name ?? $seller->name }}»?');">@csrf<button type="submit" class="action-btn" style="border-color:transparent;background:var(--success-soft);color:var(--success)"><x-icon name="check" :size="13"/>Одобрить</button></form>
                    @endif
                    @if($seller->store_status !== 'rejected')
                        <form method="POST" action="{{ route('admin.sellers.reject', $seller) }}" onsubmit="return confirm('Отклонить заявку «{{ $seller->store_name ?? $seller->name }}»?');">@csrf<button type="submit" class="action-btn" style="border-color:transparent;background:var(--danger-soft);color:var(--danger)"><x-icon name="x" :size="13"/>Отклонить</button></form>
                    @endif
                    <form method="POST" action="{{ route('admin.sellers.block', $seller) }}" onsubmit="return confirm('{{ $seller->is_blocked ? 'Разблокировать' : 'Заблокировать' }} продавца «{{ $seller->store_name ?? $seller->name }}»?');">@csrf<button type="submit" class="action-btn {{ $seller->is_blocked ? '' : 'danger' }}">{{ $seller->is_blocked ? 'Разблокировать' : 'Заблокировать' }}</button></form>
                </div></td>
            </tr>
            @empty<tr><td colspan="5" class="empty-state">Продавцы не найдены. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $sellers->links() }}</div>
</div>
</x-dashboard-layout>
