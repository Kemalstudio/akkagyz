@php
    $statusLabels = ['draft' => 'Черновик', 'pending' => 'На модерации', 'active' => 'Активен', 'rejected' => 'Отклонён'];
    $statusColors = ['draft' => 'warning', 'pending' => 'warning', 'active' => 'success', 'rejected' => 'danger'];
@endphp
<x-dashboard-layout title="Товары" active="products">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div><div class="page-title">Товары</div><div class="page-subtitle">Каталог, остатки и модерация публикаций</div></div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <form method="GET" class="toolbar">
                <input class="admin-input" name="search" value="{{ request('search') }}" placeholder="Название или бренд">
                <select name="status" onchange="this.form.submit()" style="height:40px;padding:0 14px;border-radius:10px;background:var(--surface);border:1px solid var(--border);color:var(--text);font-size:13px;font-weight:700;font-family:var(--font);">
                    <option value="">Все статусы</option>
                    @foreach($statusLabels as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <label style="display:flex;align-items:center;gap:6px;height:40px;padding:0 14px;border-radius:10px;background:var(--surface);border:1px solid var(--border);font-size:13px;font-weight:700;cursor:pointer;">
                    <input type="checkbox" name="vip" value="1" onchange="this.form.submit()" {{ request()->boolean('vip') ? 'checked' : '' }} style="accent-color:var(--warning);">
                    Только VIP
                </label>
            </form>
            <a href="{{ route('admin.products.create') }}" class="btn-accent"><x-icon name="plus" :size="15" /> Добавить товар</a>
        </div>
    </div>

    <div class="card table-card">
        <div class="admin-table-wrap"><table class="admin-table">
            <thead><tr><th>Товар</th><th>Продавец</th><th>Цена</th><th>Статус</th><th></th></tr></thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td><div class="entity-cell">
                        <div style="width:42px;height:42px;border-radius:10px;background:var(--surface-hover);overflow:hidden;display:grid;place-items:center;color:var(--text-faint);flex-shrink:0">@if($product->images->first())<img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover">@else<x-icon name="image" :size="17"/>@endif</div>
                        <div><div class="entity-name">{{ $product->name }}</div><div class="entity-meta">{{ $product->brand ?: $product->category?->name }}</div></div>
                        @if($product->is_vip)
                            <span class="badge" style="background:var(--warning-soft);color:var(--warning);"><x-icon name="star" :size="10" />VIP</span>
                        @endif
                    </div></td>
                    <td>{{ $product->seller->store_name ?? $product->seller->name ?? 'AK KAGYZ (свой товар)' }}</td>
                    <td style="font-weight:700;">{{ number_format($product->price, 0, '', ' ') }} TMT</td>
                    <td><span class="badge" style="background:var(--{{ $statusColors[$product->status] }}-soft);color:var(--{{ $statusColors[$product->status] }});">{{ $statusLabels[$product->status] }}</span></td>
                    <td><div class="row-actions">
                        @if($product->status === 'active')<a href="{{ route($product->seller_id ? 'marketplace.products.show' : 'products.show',$product->slug) }}" target="_blank" class="action-btn" title="Открыть на витрине"><x-icon name="arrow-right" :size="13"/></a>@endif
                        @if($product->seller_id === null)
                            <a href="{{ route('admin.products.edit', $product) }}" class="action-btn"><x-icon name="pen" :size="13"/>Изменить</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Удалить товар?');">@csrf @method('DELETE')<button type="submit" class="action-btn danger"><x-icon name="trash" :size="13"/></button></form>
                        @else
                            @if($product->status !== 'active')
                                <form method="POST" action="{{ route('admin.products.approve', $product) }}">@csrf<button type="submit" style="height:32px;padding:0 12px;border-radius:8px;font-weight:800;font-size:12px;border:none;background:var(--success-soft);color:var(--success);">Опубликовать</button></form>
                            @endif
                            @if($product->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.products.reject', $product) }}">@csrf<button type="submit" style="height:32px;padding:0 12px;border-radius:8px;font-weight:800;font-size:12px;border:none;background:var(--danger-soft);color:var(--danger);">Отклонить</button></form>
                            @endif
                        @endif
                    </div></td>
                </tr>
                @empty<tr><td colspan="5" class="empty-state">Товары не найдены. Измените параметры поиска.</td></tr>@endforelse
            </tbody>
        </table></div>
        <div class="pagination-wrap">{{ $products->links() }}</div>
    </div>
</x-dashboard-layout>
