@php
    $methodColors = ['GET' => 'accent', 'POST' => 'success', 'PUT' => 'warning', 'PATCH' => 'warning', 'DELETE' => 'danger'];
    $hasFilters = collect(request()->only(['user_id', 'method', 'date_from', 'date_to', 'search', 'errors_only']))->filter(fn ($v) => $v !== null && $v !== '')->count() > 0;
@endphp
<x-dashboard-layout title="Системный журнал" active="audit">
<style>
.audit-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.audit-stats .metric-value{font-size:22px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.audit-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.chip-check{display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:var(--text-muted);cursor:pointer}
.chip-check input{accent-color:var(--danger)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
.audit-route{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:11px;color:var(--text-muted)}
@media(max-width:1200px){.audit-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.audit-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Журнал действий</div><div class="page-subtitle">Кто, когда и откуда изменял данные</div></div>
    <a href="{{ route('admin.admins.index') }}" class="btn-ghost"><x-icon name="shield" :size="15"/>Администраторы</a>
</div>

<div class="audit-stats">
    @foreach([
        ['Всего записей', number_format($overview['total'], 0, '', ' '), 'list', 'accent', 'За всё время', route('admin.audit.index')],
        ['Сегодня', number_format($overview['today'], 0, '', ' '), 'calendar', 'accent', now()->format('d.m.Y'), route('admin.audit.index', ['date_from' => now()->toDateString(), 'date_to' => now()->toDateString()])],
        ['Активных сотрудников', number_format($overview['admins'], 0, '', ' '), 'users', 'success', 'Выполняли действия', route('admin.admins.index')],
        ['Ошибки', number_format($overview['errors'], 0, '', ' '), 'x', 'danger', 'Ответы с кодом 400+', route('admin.audit.index', ['errors_only' => 1])],
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
            <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Действие, маршрут или IP">
        </div>
        <div class="filter-field">
            <label>Сотрудник</label>
            <select class="admin-input" name="user_id">
                <option value="">Все сотрудники</option>
                @foreach($admins as $a)
                    <option value="{{ $a->id }}" {{ (int) request('user_id') === $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Метод</label>
            <select class="admin-input" name="method">
                <option value="">Все методы</option>
                @foreach(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $m)
                    <option value="{{ $m }}" {{ request('method') === $m ? 'selected' : '' }}>{{ $m }}</option>
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
            <label>На странице</label>
            <select class="admin-input" name="per_page">
                @foreach([30, 50, 100] as $option)
                    <option value="{{ $option }}" {{ request('per_page', 30) == $option ? 'selected' : '' }}>{{ $option }} записей</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-foot">
        <label class="chip-check"><input type="checkbox" name="errors_only" value="1" {{ request()->boolean('errors_only') ? 'checked' : '' }}><x-icon name="x" :size="13" />Только ошибки</label>
        <div style="display:flex;gap:10px">
            @if($hasFilters)<a href="{{ route('admin.audit.index') }}" class="btn-ghost">Сбросить</a>@endif
            <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
        </div>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }} из {{ number_format($logs->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Время</th><th>Сотрудник</th><th>Метод</th><th>Действие</th><th>Маршрут</th><th>IP</th><th>Статус</th></tr></thead>
        <tbody>
            @forelse($logs as $l)
            <tr>
                <td>{{ $l->created_at->format('d.m.Y') }}<div class="entity-meta">{{ $l->created_at->format('H:i:s') }}</div></td>
                <td>{{ $l->user?->name ?? 'Система' }}</td>
                <td><span class="badge" style="background:var(--{{ $methodColors[$l->method] ?? 'accent' }}-soft);color:var(--{{ $methodColors[$l->method] ?? 'accent' }})">{{ $l->method }}</span></td>
                <td>{{ $l->action }}</td>
                <td class="audit-route">{{ $l->route }}</td>
                <td>{{ $l->ip_address }}</td>
                <td><span class="badge" style="background:var(--{{ $l->response_status >= 400 ? 'danger' : 'success' }}-soft);color:var(--{{ $l->response_status >= 400 ? 'danger' : 'success' }})">{{ $l->response_status }}</span></td>
            </tr>
            @empty<tr><td colspan="7" class="empty-state">Записи не найдены. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $logs->links() }}</div>
</div>
</x-dashboard-layout>
