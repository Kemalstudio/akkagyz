<x-dashboard-layout :title="$product ? 'Изменить товар' : 'Добавить товар'" active="products">
    <div style="font-size:24px;font-weight:900;margin-bottom:4px;">{{ $product ? 'Изменить товар' : 'Добавить новый товар' }}</div>
    <div style="font-size:13px;color:var(--text-faint);margin-bottom:24px;">{{ $product ? '' : 'Товар появится на витрине после проверки администратором' }}</div>

    <form method="POST" action="{{ $product ? route('seller.products.update', $product) : route('seller.products.store') }}" class="card" style="padding:24px;">
        @csrf
        @if($product) @method('PUT') @endif

        <div style="display:grid;grid-template-columns:220px 1fr;gap:24px;">
            <div>
                <div style="aspect-ratio:1/1;border:2px dashed var(--border-strong);border-radius:14px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--text-faint);">
                    <x-icon name="upload" :size="30" />
                    <span style="font-size:12px;font-weight:700;text-align:center;">Фото товара<br>(скоро)</span>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div style="grid-column:1/-1;"><label class="label">Название товара</label><input required class="input" name="name" value="{{ old('name', $product->name ?? '') }}" placeholder="Например: Набор ручек Premium 12 цветов"></div>
                <div>
                    <label class="label">Категория</label>
                    <select required name="category_id" class="input" style="font-family:var(--font);">
                        <option value="">Выберите категорию</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="label">Цена, TMT</label><input required type="number" min="0" class="input" name="price" value="{{ old('price', $product->price ?? '') }}" placeholder="0"></div>
                <div><label class="label">Старая цена (для скидки), TMT</label><input type="number" min="0" class="input" name="compare_price" value="{{ old('compare_price', $product->compare_price ?? '') }}" placeholder="Необязательно"></div>
                <div><label class="label">Количество на складе</label><input required type="number" min="0" class="input" name="stock" value="{{ old('stock', $product->stock ?? '') }}" placeholder="0"></div>
                <div style="grid-column:1/-1;"><label class="label">Описание</label><textarea name="description" class="input" style="height:90px;padding:12px 14px;resize:none;" placeholder="Расскажите о товаре подробнее">{{ old('description', $product->description ?? '') }}</textarea></div>

                <div style="grid-column:1/-1;"><x-attribute-fields :categories="$categories" :product="$product" /></div>

                <div style="grid-column:1/-1;">
                    @if(auth()->user()->is_vip)
                        <label style="display:flex;align-items:center;gap:10px;padding:14px 16px;border-radius:12px;background:var(--warning-soft);border:1px solid var(--warning);cursor:pointer;">
                            <input type="checkbox" name="is_vip" value="1" {{ old('is_vip', $product->is_vip ?? false) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--warning);">
                            <span style="display:flex;align-items:center;gap:6px;font-weight:800;font-size:13.5px;color:var(--text);"><x-icon name="star" :size="14" style="color:var(--warning);" />Отметить как VIP-товар</span>
                            <span style="font-size:12px;color:var(--text-faint);margin-left:auto;">Показывается в приоритетных VIP-подборках</span>
                        </label>
                    @else
                        <div style="display:flex;align-items:center;gap:10px;padding:14px 16px;border-radius:12px;background:var(--surface-hover);border:1px dashed var(--border-strong);">
                            <x-icon name="star-outline" :size="16" style="color:var(--text-faint);flex-shrink:0;" />
                            <span style="font-size:13px;color:var(--text-faint);">VIP-товары доступны только VIP-магазинам. <a href="{{ route('seller.settings.edit') }}" style="font-weight:800;">Активировать VIP</a></span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" style="margin-top:16px;">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
            <a href="{{ route('seller.products.index') }}" class="btn-ghost">Отмена</a>
            <button type="submit" class="btn-accent">{{ $product ? 'Сохранить' : 'Опубликовать товар' }}</button>
        </div>
    </form>
</x-dashboard-layout>
