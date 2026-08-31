@php
    $statusLabels = ['draft' => 'Черновик', 'pending' => 'На модерации', 'active' => 'Активен', 'rejected' => 'Отклонён'];
    $statusColors = ['draft' => 'text-faint', 'pending' => 'warning', 'active' => 'success', 'rejected' => 'danger'];
@endphp
<x-dashboard-layout title="Товары" active="products">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
        <div style="font-size:24px;font-weight:900;">Мои товары</div>
        <a href="{{ route('seller.products.create') }}" class="btn-accent"><x-icon name="plus" :size="15" />Добавить товар</a>
    </div>

    <div class="card" style="padding:24px;">
        @if($products->isEmpty())
            <div style="color:var(--text-faint);font-size:14px;text-align:center;padding:40px 0;">У вас пока нет товаров.</div>
        @else
        <div class="admin-table-wrap"><table class="admin-table">
            <thead><tr><th>Товар</th><th>Цена</th><th>Остаток</th><th>Продано</th><th>Статус</th><th></th></tr></thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td style="display:flex;align-items:center;gap:10px;"><div style="width:36px;height:36px;border-radius:8px;background:var(--bg-elevated);flex-shrink:0;"></div><span style="font-weight:700;">{{ $product->name }}</span></td>
                    <td style="font-weight:700;">{{ number_format($product->price, 0, '', ' ') }} TMT</td>
                    <td>{{ $product->stock }} шт</td>
                    <td>{{ $product->sales_count }}</td>
                    <td><span class="badge" style="background:var(--{{ $statusColors[$product->status] === 'text-faint' ? 'border' : $statusColors[$product->status].'-soft' }});color:var(--{{ $statusColors[$product->status] }});">{{ $statusLabels[$product->status] }}</span></td>
                    <td style="display:flex;gap:12px;">
                        <a href="{{ route('seller.products.edit', $product) }}" style="font-weight:800;font-size:12px;">Изменить</a>
                        <form method="POST" action="{{ route('seller.products.destroy', $product) }}" onsubmit="return confirm('Удалить товар?');">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none;border:none;padding:0;font-weight:800;font-size:12px;color:var(--danger);">Удалить</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table></div>
        <div style="margin-top:20px;">{{ $products->links() }}</div>
        @endif
    </div>
</x-dashboard-layout>
