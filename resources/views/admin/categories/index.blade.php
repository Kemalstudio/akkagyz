@php
    $hasFilters = collect(request()->only(['search', 'level', 'parent_id']))->filter(fn ($v) => $v !== null && $v !== '')->count() > 0;
@endphp
<x-dashboard-layout title="Категории" active="categories">
<style>
.categories-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px}
.categories-stats .metric-value{font-size:22px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.categories-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
@media(max-width:1200px){.categories-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.categories-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Категории</div><div class="page-subtitle">Структура каталога и порядок отображения</div></div>
    <a href="{{ route('admin.categories.create') }}" class="btn-accent"><x-icon name="plus" :size="15"/>Новая категория</a>
</div>

<div class="categories-stats">
    @foreach([
        ['Всего категорий', number_format($overview['total'], 0, '', ' '), 'folder', 'accent', 'Основные и подкатегории', route('admin.categories.index')],
        ['Основные категории', number_format($overview['top'], 0, '', ' '), 'grid', 'accent', 'Верхний уровень каталога', route('admin.categories.index', ['level' => 'top'])],
        ['Подкатегории', number_format($overview['sub'], 0, '', ' '), 'list', 'accent', 'Вложены в основные', route('admin.categories.index', ['level' => 'sub'])],
        ['Без товаров', number_format($overview['empty'], 0, '', ' '), 'box', 'warning', 'Пустые категории', route('admin.categories.index', ['empty' => 1])],
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

@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<form method="GET" class="card filter-card">
    <div class="filter-grid">
        <div class="filter-field" style="grid-column:span 2">
            <label>Поиск</label>
            <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Название категории">
        </div>
        <div class="filter-field">
            <label>Уровень</label>
            <select class="admin-input" name="level">
                <option value="">Все уровни</option>
                <option value="top" {{ request('level') === 'top' ? 'selected' : '' }}>Только основные</option>
                <option value="sub" {{ request('level') === 'sub' ? 'selected' : '' }}>Только подкатегории</option>
            </select>
        </div>
        <div class="filter-field">
            <label>Родительская категория</label>
            <select class="admin-input" name="parent_id">
                <option value="">Все родители</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ (int) request('parent_id') === $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>На странице</label>
            <select class="admin-input" name="per_page">
                @foreach([20, 50, 100] as $option)
                    <option value="{{ $option }}" {{ request('per_page', 20) == $option ? 'selected' : '' }}>{{ $option }} категорий</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-foot">
        @if($hasFilters)<a href="{{ route('admin.categories.index') }}" class="btn-ghost">Сбросить</a>@endif
        <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $categories->firstItem() ?? 0 }}–{{ $categories->lastItem() ?? 0 }} из {{ number_format($categories->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Категория</th><th>Родитель</th><th>Товары</th><th>Подкатегории</th><th>Порядок</th><th></th></tr></thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td><div class="entity-cell">
                    <span class="entity-icon"><x-icon :name="$category->icon ?: 'folder'" :size="17"/></span>
                    <div><div class="entity-name">{{ $category->name }}</div><div class="entity-meta">/{{ $category->slug }}</div></div>
                </div></td>
                <td>{{ $category->parent?->name ?: 'Основная' }}</td>
                <td>@if($category->products_count)<span class="badge" style="background:var(--success-soft);color:var(--success)">{{ $category->products_count }}</span>@else<span class="badge" style="background:var(--warning-soft);color:var(--warning)">0</span>@endif</td>
                <td>{{ $category->children_count }}</td>
                <td>{{ $category->sort_order }}</td>
                <td><div class="row-actions">
                    <a href="{{ route('admin.categories.attributes.index', $category) }}" class="action-btn"><x-icon name="list" :size="14"/>Характеристики</a>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="action-btn"><x-icon name="pen" :size="14"/>Изменить</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Удалить категорию «{{ $category->name }}»?')">@csrf @method('DELETE')<button type="submit" class="action-btn danger"><x-icon name="trash" :size="14"/></button></form>
                </div></td>
            </tr>
            @empty<tr><td colspan="6" class="empty-state">Категории не найдены. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $categories->links() }}</div>
</div>
</x-dashboard-layout>
