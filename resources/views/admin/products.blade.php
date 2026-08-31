@php
    $statusLabels = ['draft' => 'Черновик', 'pending' => 'На модерации', 'active' => 'Активен', 'rejected' => 'Отклонён'];
    $statusColors = ['draft' => 'warning', 'pending' => 'warning', 'active' => 'success', 'rejected' => 'danger'];
    $sourceLabels = ['own' => 'Свои товары', 'marketplace' => 'Товары продавцов'];
    $stockLabels = ['in' => 'В наличии', 'low' => 'Заканчивается', 'out' => 'Нет в наличии', 'issues' => 'Мало или нет в наличии'];
    $sortLabels = ['latest' => 'Сначала новые', 'oldest' => 'Сначала старые', 'price_desc' => 'Цена: по убыванию', 'price_asc' => 'Цена: по возрастанию', 'stock_asc' => 'Остаток: по возрастанию', 'sales_desc' => 'По продажам'];
    $activeFilters = collect(request()->only(['search', 'status', 'category_id', 'seller_id', 'source', 'stock', 'vip', 'price_from', 'price_to']))->filter(fn ($v) => $v !== null && $v !== '')->count();
    $hasFilters = $activeFilters > 0 || (request('sort') && request('sort') !== 'latest');
@endphp
<x-dashboard-layout title="Товары" active="products">
<style>
.products-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:20px}
.products-stats .metric-value{font-size:22px;font-weight:900;letter-spacing:-.02em;margin-top:10px}
.products-stats .metric-note{font-size:11px;color:var(--text-faint);font-weight:700;margin-top:4px}
.filter-card{padding:18px 20px;margin-bottom:20px}
.filter-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(168px,1fr));gap:12px}
.filter-field label{display:block;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.06em;color:var(--text-faint);margin-bottom:6px}
.filter-field .admin-input{width:100%}
.filter-foot{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
.chip-check{display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:var(--text-muted);cursor:pointer}
.chip-check input{accent-color:var(--warning)}
.price-compare{font-size:11px;color:var(--text-faint);text-decoration:line-through;margin-left:6px}
.seller-name{font-size:13px;font-weight:700}
.results-note{padding:14px 22px 0;font-size:12px;color:var(--text-faint);font-weight:700}
@media(max-width:1200px){.products-stats{grid-template-columns:repeat(3,1fr)}}
@media(max-width:700px){.products-stats{grid-template-columns:repeat(2,1fr)}}
@media(max-width:460px){.products-stats{grid-template-columns:1fr}}
</style>

<div class="page-head">
    <div><div class="page-title">Товары</div><div class="page-subtitle">Каталог, остатки и модерация публикаций</div></div>
    <div class="toolbar">
        <a href="{{ route('admin.inventory.index') }}" class="btn-ghost"><x-icon name="box" :size="15" />Склад</a>
        <a href="{{ route('admin.products.create') }}" class="btn-accent"><x-icon name="plus" :size="15" /> Добавить товар</a>
    </div>
</div>

<div class="products-stats">
    @foreach([
        ['Всего товаров', number_format($overview['total'], 0, '', ' '), 'grid', 'accent', 'Во всём каталоге', route('admin.products')],
        ['Активные', number_format($overview['active'], 0, '', ' '), 'check', 'success', 'Опубликованы на витрине', route('admin.products', ['status' => 'active'])],
        ['На модерации', number_format($overview['pending'], 0, '', ' '), 'package', 'warning', 'Ждут проверки', route('admin.products', ['status' => 'pending'])],
        ['Проблемы с остатком', number_format($overview['stock_issues'], 0, '', ' '), 'box', 'danger', 'Мало или нет в наличии', route('admin.products', ['stock' => 'issues'])],
        ['Стоимость склада', number_format($overview['inventory_value'], 0, '', ' ').' TMT', 'cash', 'accent', 'Цена × остаток', route('admin.inventory.index')],
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
            <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Название, SKU, штрихкод, продавец">
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
            <label>Источник</label>
            <select class="admin-input" name="source">
                <option value="">Все источники</option>
                @foreach($sourceLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('source') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Остаток</label>
            <select class="admin-input" name="stock">
                <option value="">Любой остаток</option>
                @foreach($stockLabels as $val => $label)
                    <option value="{{ $val }}" {{ request('stock') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Категория</label>
            <select class="admin-input" name="category_id">
                <option value="">Все категории</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (int) request('category_id') === $category->id ? 'selected' : '' }}>{{ $category->parent ? '— ' : '' }}{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Продавец</label>
            <select class="admin-input" name="seller_id">
                <option value="">Все продавцы</option>
                @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}" {{ (int) request('seller_id') === $seller->id ? 'selected' : '' }}>{{ $seller->store_name ?: $seller->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="filter-field">
            <label>Цена от</label>
            <input class="admin-input" type="number" min="0" name="price_from" value="{{ request('price_from') }}" placeholder="0">
        </div>
        <div class="filter-field">
            <label>Цена до</label>
            <input class="admin-input" type="number" min="0" name="price_to" value="{{ request('price_to') }}" placeholder="∞">
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
                    <option value="{{ $option }}" {{ request('per_page', 15) == $option ? 'selected' : '' }}>{{ $option }} товаров</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="filter-foot">
        <label class="chip-check"><input type="checkbox" name="vip" value="1" {{ request()->boolean('vip') ? 'checked' : '' }}><x-icon name="star" :size="13" />Только VIP</label>
        <div style="display:flex;gap:10px">
            @if($hasFilters)<a href="{{ route('admin.products') }}" class="btn-ghost">Сбросить</a>@endif
            <button type="submit" class="btn-accent"><x-icon name="filter" :size="14" />Применить</button>
        </div>
    </div>
</form>

<div class="card table-card">
    <div class="results-note">Показано {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} из {{ number_format($products->total(), 0, '', ' ') }}</div>
    <div class="admin-table-wrap"><table class="admin-table">
        <thead><tr><th>Товар</th><th>Категория</th><th>Продавец</th><th>Цена</th><th>Остаток</th><th>Статус</th><th></th></tr></thead>
        <tbody>
            @forelse($products as $product)
            <tr>
                <td><div class="entity-cell">
                    <div style="width:42px;height:42px;border-radius:10px;background:var(--surface-hover);overflow:hidden;display:grid;place-items:center;color:var(--text-faint);flex-shrink:0">@if($product->images->first())<img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover">@else<x-icon name="image" :size="17"/>@endif</div>
                    <div style="min-width:0">
                        <div class="entity-name">{{ $product->name }}</div>
                        <div class="entity-meta">{{ $product->sku ? 'SKU '.$product->sku : 'Без SKU' }}@if($product->variants_count) · {{ $product->variants_count }} {{ trans_choice('вариация|вариации|вариаций', $product->variants_count) }}@endif</div>
                    </div>
                    @if($product->is_vip)
                        <span class="badge" style="background:var(--warning-soft);color:var(--warning);flex-shrink:0"><x-icon name="star" :size="10" />VIP</span>
                    @endif
                </div></td>
                <td>{{ $product->category?->name ?? '—' }}</td>
                <td>
                    <div class="seller-name">{{ $product->seller->store_name ?? $product->seller->name ?? $businessSettings->site_name }}</div>
                    <div class="entity-meta">{{ $product->seller_id ? 'Продавец' : 'Свой товар' }}</div>
                </td>
                <td>
                    <div style="font-weight:700">{{ number_format($product->price, 0, '', ' ') }} TMT</div>
                    @if($product->discount_percent)
                        <div><span class="price-compare">{{ number_format($product->compare_price, 0, '', ' ') }}</span> <span class="badge" style="background:var(--danger-soft);color:var(--danger);padding:2px 5px;font-size:10px">-{{ $product->discount_percent }}%</span></div>
                    @endif
                </td>
                <td>
                    @if($product->stock <= 0)
                        <span class="badge" style="background:var(--danger-soft);color:var(--danger)">Нет в наличии</span>
                    @elseif($product->stock <= $product->low_stock_threshold)
                        <span class="badge" style="background:var(--warning-soft);color:var(--warning)">Осталось {{ $product->stock }}</span>
                    @else
                        <span class="badge" style="background:var(--success-soft);color:var(--success)">{{ $product->stock }} шт.</span>
                    @endif
                </td>
                <td><span class="badge" style="background:var(--{{ $statusColors[$product->status] }}-soft);color:var(--{{ $statusColors[$product->status] }});">{{ $statusLabels[$product->status] }}</span></td>
                <td><div class="row-actions">
                    @if($product->status === 'active')<a href="{{ route($product->seller_id ? 'marketplace.products.show' : 'products.show',$product->slug) }}" target="_blank" class="action-btn" title="Открыть на витрине"><x-icon name="arrow-right" :size="13"/></a>@endif
                    @if($product->seller_id === null)
                        <a href="{{ route('admin.products.edit', $product) }}" class="action-btn"><x-icon name="pen" :size="13"/>Изменить</a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Удалить товар «{{ $product->name }}»?');">@csrf @method('DELETE')<button type="submit" class="action-btn danger"><x-icon name="trash" :size="13"/></button></form>
                    @else
                        @if($product->status !== 'active')
                            <form method="POST" action="{{ route('admin.products.approve', $product) }}" onsubmit="return confirm('Опубликовать товар «{{ $product->name }}»?');"><button type="submit" class="action-btn" style="border-color:transparent;background:var(--success-soft);color:var(--success);"><x-icon name="check" :size="13"/>Опубликовать</button></form>
                        @endif
                        @if($product->status !== 'rejected')
                            <form method="POST" action="{{ route('admin.products.reject', $product) }}" class="reject-form" data-name="{{ $product->name }}">@csrf<input type="hidden" name="rejection_reason"><button type="submit" class="action-btn" style="border-color:transparent;background:var(--danger-soft);color:var(--danger);"><x-icon name="x" :size="13"/>Отклонить</button></form>
                        @endif
                    @endif
                </div></td>
            </tr>
            @empty<tr><td colspan="7" class="empty-state">Товары не найдены. Измените параметры поиска.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="pagination-wrap">{{ $products->links() }}</div>
</div>

<script>
document.querySelectorAll('.reject-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        var reason = prompt('Причина отклонения товара «' + form.dataset.name + '»:', 'Товар не прошёл модерацию.');
        if (reason === null) { e.preventDefault(); return; }
        form.querySelector('input[name="rejection_reason"]').value = reason;
    });
});
</script>
</x-dashboard-layout>
