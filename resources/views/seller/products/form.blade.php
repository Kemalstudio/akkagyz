<x-dashboard-layout :title="$product ? 'Изменить товар' : 'Добавить товар'" active="products">
    <style>.seller-upload-zone{aspect-ratio:1/1;max-width:220px;border:2px dashed var(--border-strong);border-radius:14px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--text-faint);cursor:pointer;position:relative;overflow:hidden;transition:.2s;}.seller-upload-zone:hover{border-color:var(--accent);background:var(--accent-soft);}.seller-upload-zone input{position:absolute;inset:0;opacity:0;cursor:pointer;}.seller-preview-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-top:10px;max-width:220px;}.seller-preview-grid img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:10px;border:1px solid var(--border);}</style>
    <div style="font-size:24px;font-weight:900;margin-bottom:4px;">{{ $product ? 'Изменить товар' : 'Добавить новый товар' }}</div>
    <div style="font-size:13px;color:var(--text-faint);margin-bottom:24px;">{{ $product ? '' : 'Товар появится на витрине после проверки администратором' }}</div>

    <form method="POST" action="{{ $product ? route('seller.products.update', $product) : route('seller.products.store') }}" enctype="multipart/form-data" class="card" style="padding:24px;">
        @csrf
        @if($product) @method('PUT') @endif

        <div class="rgrid-sidebar">
            <div>
                <label class="seller-upload-zone" id="seller-upload-zone">
                    <input type="file" name="images[]" id="seller-product-images" accept="image/*" multiple>
                    <span id="seller-upload-copy">
                        <x-icon name="upload" :size="30" />
                        <span style="display:block;font-size:12px;font-weight:700;text-align:center;margin-top:6px;">Фото товара<br>до 5 изображений</span>
                    </span>
                </label>
                <div class="seller-preview-grid" id="seller-image-preview">
                    @foreach($product?->images ?? [] as $image)
                        <img src="{{ $image->url }}" alt="">
                    @endforeach
                </div>
                @if($product?->images?->isNotEmpty())
                    <div style="font-size:10px;color:var(--text-faint);margin-top:8px;max-width:220px;">При загрузке новых файлов текущие фото будут заменены.</div>
                @endif
                @error('images.*')<div style="color:var(--danger);font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            </div>
            <div class="rgrid-2">
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
                <div>
                    <label class="label">Состояние товара</label>
                    @php($condition = old('condition', $product->condition ?? 'new'))
                    <div style="display:flex;gap:8px;">
                        <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;height:44px;border-radius:10px;border:1px solid var(--border-strong);cursor:pointer;font-size:13px;font-weight:700;{{ $condition === 'new' ? 'background:var(--accent-soft);border-color:var(--accent);color:var(--accent);' : '' }}">
                            <input type="radio" name="condition" value="new" {{ $condition === 'new' ? 'checked' : '' }} style="accent-color:var(--accent);"> Новый
                        </label>
                        <label style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;height:44px;border-radius:10px;border:1px solid var(--border-strong);cursor:pointer;font-size:13px;font-weight:700;{{ $condition === 'used' ? 'background:var(--accent-soft);border-color:var(--accent);color:var(--accent);' : '' }}">
                            <input type="radio" name="condition" value="used" {{ $condition === 'used' ? 'checked' : '' }} style="accent-color:var(--accent);"> Б/У
                        </label>
                    </div>
                </div>
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
    <script>
    document.getElementById('seller-product-images').addEventListener('change', function () {
        const box = document.getElementById('seller-image-preview');
        box.innerHTML = '';
        [...this.files].slice(0, 5).forEach(file => {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            box.appendChild(img);
        });
    });
    </script>
</x-dashboard-layout>
