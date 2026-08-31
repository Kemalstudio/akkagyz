@php
    $roleLabels = ['super_admin' => 'Главный администратор', 'order_manager' => 'Менеджер заказов', 'content_manager' => 'Контент-менеджер', 'moderator' => 'Модератор', 'support' => 'Поддержка', 'accountant' => 'Бухгалтер', 'marketer' => 'Маркетолог'];
    $permissionLabels = ['dashboard' => 'Обзор', 'orders' => 'Заказы', 'catalog' => 'Каталог', 'moderation' => 'Модерация', 'finance' => 'Финансы', 'marketing' => 'Маркетинг', 'support' => 'Поддержка', 'system' => 'Система'];
    $superCount = $admins->where('admin_role', 'super_admin')->count();
@endphp
<x-dashboard-layout title="Администраторы" active="admins">
<style>
.admins-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px}
.admins-stats .metric-value{font-size:22px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.admins-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.access-card{padding:20px;margin-bottom:20px}
.access-card h3{margin:0 0 16px;font-size:14px;font-weight:800}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:16px;padding-top:16px;border-top:1px solid var(--border)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
.perm-grid{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px}
.perm-chip{display:flex;align-items:center;gap:6px;padding:7px 12px;border:1px solid var(--border);border-radius:999px;background:var(--surface);font-size:12px;font-weight:700;color:var(--text-muted);cursor:pointer;transition:.15s}
.perm-chip:hover{border-color:var(--border-strong)}
.perm-chip:has(input:checked){background:var(--accent-soft);border-color:color-mix(in oklch,var(--accent) 40%,var(--border));color:var(--accent)}
.perm-chip input{accent-color:var(--accent)}
.perm-note{display:none;margin-top:12px;font-size:11px;font-weight:700;color:var(--text-faint);padding:9px 12px;border-radius:9px;background:var(--accent-soft);color:var(--accent)}
.admin-row-cell{min-width:320px}
@media(max-width:1200px){.admins-stats{grid-template-columns:1fr 1fr}}
@media(max-width:460px){.admins-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Команда и права</div><div class="page-subtitle">Разделение доступа сотрудников по ролям</div></div>
    <a href="{{ route('admin.audit.index') }}" class="btn-ghost"><x-icon name="list" :size="15"/>Журнал действий</a>
</div>

<div class="admins-stats">
    @foreach([
        ['Всего сотрудников', number_format($admins->count(), 0, '', ' '), 'users', 'accent', 'С доступом к панели'],
        ['Главных администраторов', number_format($superCount, 0, '', ' '), 'shield', 'danger', 'Полный доступ ко всем разделам'],
        ['С ограниченными правами', number_format($admins->count() - $superCount, 0, '', ' '), 'lock', 'warning', 'Доступ по выбранным разделам'],
    ] as $metric)
    <div class="stat">
        <div style="display:flex;justify-content:space-between;align-items:start">
            <div><div class="kicker">{{ $metric[0] }}</div><div class="metric-value">{{ $metric[1] }}</div></div>
            <div class="stat-icon" style="background:var(--{{ $metric[3] }}-soft);color:var(--{{ $metric[3] }})"><x-icon :name="$metric[2]" :size="17" /></div>
        </div>
        <div class="metric-note">{{ $metric[4] }}</div>
    </div>
    @endforeach
</div>

@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<form class="card access-card" method="POST" action="{{ route('admin.admins.store') }}">
    @csrf
    <h3>Добавить сотрудника</h3>
    <div class="filter-grid">
        <div class="filter-field"><label>Имя</label><input class="admin-input" name="name" required placeholder="Имя и фамилия"></div>
        <div class="filter-field"><label>Email</label><input class="admin-input" type="email" name="email" required placeholder="email@akkagyz.kz"></div>
        <div class="filter-field"><label>Пароль</label><input class="admin-input" type="password" name="password" required placeholder="Минимум 8 символов"></div>
        <div class="filter-field">
            <label>Роль</label>
            <select class="admin-input role-select" name="admin_role">
                @foreach($roleLabels as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="perm-grid">
        @foreach($permissions as $p)
            <label class="perm-chip"><input type="checkbox" name="permissions[]" value="{{ $p }}">{{ $permissionLabels[$p] ?? $p }}</label>
        @endforeach
    </div>
    <div class="perm-note">Главный администратор получает доступ ко всем разделам автоматически</div>
    <div class="filter-foot">
        <button type="submit" class="btn-accent"><x-icon name="plus" :size="14"/>Добавить сотрудника</button>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $admins->count() }} из {{ $admins->count() }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Сотрудник</th><th>Роль и права доступа</th><th></th></tr></thead>
        <tbody>
            @forelse($admins as $a)
            <tr>
                <td><div class="entity-cell">
                    <div class="avatar">{{ Str::of($a->name)->substr(0, 2)->upper() }}</div>
                    <div><div class="entity-name">{{ $a->name }}</div><div class="entity-meta">{{ $a->email }}</div></div>
                </div></td>
                <td class="admin-row-cell">
                    <form method="POST" action="{{ route('admin.admins.update', $a) }}" id="admin-form-{{ $a->id }}" onsubmit="return confirm('Сохранить права доступа для «{{ $a->name }}»?');">
                        @csrf @method('PATCH')
                        <select class="admin-input role-select" name="admin_role" style="width:100%">
                            @foreach($roleLabels as $value => $label)
                                <option value="{{ $value }}" {{ $a->admin_role === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="perm-grid">
                            @foreach($permissions as $p)
                                <label class="perm-chip"><input type="checkbox" name="permissions[]" value="{{ $p }}" {{ in_array($p, $a->admin_permissions ?? []) ? 'checked' : '' }}>{{ $permissionLabels[$p] ?? $p }}</label>
                            @endforeach
                        </div>
                        <div class="perm-note">Главный администратор получает доступ ко всем разделам автоматически</div>
                    </form>
                </td>
                <td><button type="submit" form="admin-form-{{ $a->id }}" class="btn-accent">Сохранить</button></td>
            </tr>
            @empty<tr><td colspan="3" class="empty-state">Сотрудники не найдены</td></tr>@endforelse
        </tbody>
    </table></div>
</div>

<script>
function toggleAdminPermissions(select) {
    var form = select.closest('form') || select.closest('.access-card');
    var grid = form.querySelector('.perm-grid');
    var note = form.querySelector('.perm-note');
    var isSuper = select.value === 'super_admin';
    grid.style.display = isSuper ? 'none' : 'flex';
    note.style.display = isSuper ? 'block' : 'none';
}
document.querySelectorAll('.role-select').forEach(function (select) {
    toggleAdminPermissions(select);
    select.addEventListener('change', function () { toggleAdminPermissions(select); });
});
</script>
</x-dashboard-layout>
