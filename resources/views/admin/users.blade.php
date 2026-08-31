@php
    $hasFilters = collect(request()->only(['search', 'status']))->filter(fn ($v) => $v !== null && $v !== '')->count() > 0;
@endphp
<x-dashboard-layout title="Пользователи" active="users">
<style>
.user-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.user-stats .metric-value{font-size:22px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.user-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
.user-avatar{width:42px;height:42px;border-radius:12px;overflow:hidden;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center;font-weight:700;flex-shrink:0}
.user-avatar img{width:100%;height:100%;object-fit:cover}
@media(max-width:1200px){.user-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.user-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Пользователи</div><div class="page-subtitle">Поиск, аналитика активности и управление доступом покупателей</div></div>
</div>

<div class="user-stats">
    @foreach([
        ['Всего пользователей', number_format($stats['total'], 0, '', ' '), 'users', 'accent', 'Все покупатели', route('admin.users')],
        ['Активные', number_format($stats['active'], 0, '', ' '), 'check', 'success', 'С доступом к аккаунту', route('admin.users', ['status' => 'active'])],
        ['Заблокированы', number_format($stats['blocked'], 0, '', ' '), 'lock', 'danger', 'Доступ ограничен', route('admin.users', ['status' => 'blocked'])],
        ['Новые за 30 дней', number_format($stats['new'], 0, '', ' '), 'trending-up', 'warning', 'Свежая регистрация', route('admin.users', ['new' => 1])],
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
            <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Имя, email или телефон">
        </div>
        <div class="filter-field">
            <label>Статус</label>
            <select class="admin-input" name="status">
                <option value="">Все статусы</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Активные</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Заблокированные</option>
            </select>
        </div>
    </div>
    <div class="filter-foot">
        @if($hasFilters)<a href="{{ route('admin.users') }}" class="btn-ghost">Сбросить</a>@endif
        <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} из {{ number_format($users->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Пользователь</th><th>Контакты</th><th>Заказы</th><th>Сумма покупок</th><th>Регистрация</th><th>Статус</th><th></th></tr></thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <td><div class="entity-cell">
                    <div class="user-avatar">@if($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="">@else{{ Str::of($user->name)->substr(0,2)->upper() }}@endif</div>
                    <div><div class="entity-name">{{ $user->name }}</div><div class="entity-meta">ID #{{ $user->id }}</div></div>
                </div></td>
                <td><div>{{ $user->email }}</div><div class="entity-meta">{{ $user->phone ?: 'Телефон не указан' }}</div></td>
                <td>{{ $user->orders_count }}</td>
                <td style="font-weight:700">{{ number_format($user->orders_sum_total ?? 0, 0, '', ' ') }} TMT</td>
                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                <td><span class="badge" style="background:var(--{{ $user->is_blocked ? 'danger' : 'success' }}-soft);color:var(--{{ $user->is_blocked ? 'danger' : 'success' }})">{{ $user->is_blocked ? 'Заблокирован' : 'Активен' }}</span></td>
                <td><div class="row-actions">
                    <form method="POST" action="{{ route('admin.users.block', $user) }}" onsubmit="return confirm('{{ $user->is_blocked ? 'Разблокировать' : 'Заблокировать' }} пользователя «{{ $user->name }}»?');">@csrf<button type="submit" class="action-btn {{ $user->is_blocked ? '' : 'danger' }}">{{ $user->is_blocked ? 'Разблокировать' : 'Заблокировать' }}</button></form>
                </div></td>
            </tr>
            @empty<tr><td colspan="7" class="empty-state">Пользователи не найдены. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $users->links() }}</div>
</div>
</x-dashboard-layout>
