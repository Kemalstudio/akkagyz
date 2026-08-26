<x-dashboard-layout title="Характеристики категории" active="categories">
<div class="page-head"><div><a class="back-link" href="{{ route('admin.categories.index') }}">← Категории</a><div class="page-title">Характеристики: {{ $category->name }}</div><div class="page-subtitle">Название характеристики совпадает с названием фильтра в каталоге этой категории</div></div></div>
@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<div style="display:flex;flex-direction:column;gap:12px;">
    @forelse($attributes as $attribute)
        <form method="POST" action="{{ route('admin.categories.attributes.update',[$category,$attribute]) }}" style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:16px;background:var(--surface);border:1px solid var(--border);">
            @csrf @method('PUT')
            <div style="flex:1;min-width:0;">
                <label style="display:block;font-size:10px;color:var(--text-faint);margin-bottom:5px;">Название характеристики / фильтра</label>
                <input class="input" name="name" value="{{ $attribute->name }}" required>
            </div>
            <div style="width:120px;flex-shrink:0;">
                <label style="display:block;font-size:10px;color:var(--text-faint);margin-bottom:5px;">Порядок</label>
                <input class="input" type="number" min="0" name="sort_order" value="{{ $attribute->sort_order }}">
            </div>
            <span class="badge" style="flex-shrink:0;align-self:flex-end;margin-bottom:2px;">{{ $attribute->values_count }} {{ $attribute->values_count == 1 ? 'значение' : 'значений' }}</span>
            <div style="display:flex;gap:8px;flex-shrink:0;align-self:flex-end;">
                <button type="submit" style="height:38px;padding:0 14px;border-radius:9px;font-weight:800;font-size:12px;border:1px solid var(--border);background:none;color:var(--text);">Сохранить</button>
            </div>
        </form>
        <form method="POST" action="{{ route('admin.categories.attributes.destroy',[$category,$attribute]) }}" onsubmit="return confirm('Удалить характеристику «{{ $attribute->name }}»? Значения у всех товаров будут удалены, а фильтр исчезнет из каталога.')" style="margin-top:-6px;text-align:right;">
            @csrf @method('DELETE')
            <button type="submit" style="height:26px;padding:0 10px;border-radius:7px;font-weight:700;font-size:11px;border:none;background:none;color:var(--danger);">Удалить характеристику</button>
        </form>
    @empty
        <div class="card" style="padding:40px;text-align:center;color:var(--text-faint);font-size:14px;">У этой категории пока нет характеристик.</div>
    @endforelse
</div>

<div class="card form-card" style="margin-top:22px">
    <div style="font-size:14px;font-weight:700;margin-bottom:14px">Добавить характеристику</div>
    <form method="POST" action="{{ route('admin.categories.attributes.store',$category) }}">
        @csrf
        <div class="form-grid">
            <div class="field full">
                <label>Название характеристики</label>
                <input class="input" name="name" required placeholder="Например, Диагональ экрана">
                <small style="display:block;margin-top:6px;color:var(--text-faint)">Под этим же названием характеристика появится как фильтр в каталоге категории «{{ $category->name }}».</small>
            </div>
            <div class="field"><label>Порядок отображения</label><input class="input" type="number" min="0" name="sort_order" placeholder="По умолчанию — в конце"></div>
        </div>
        <div class="form-actions"><button class="btn-accent"><x-icon name="plus" :size="15"/>Добавить</button></div>
    </form>
</div>
</x-dashboard-layout>
