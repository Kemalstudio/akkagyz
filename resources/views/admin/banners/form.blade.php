<x-dashboard-layout :title="$banner ? 'Изменить баннер' : 'Добавить баннер'" active="banners">
    <div style="font-size:24px;font-weight:900;margin-bottom:24px;">{{ $banner ? 'Изменить баннер' : 'Добавить баннер' }}</div>

    <form method="POST" action="{{ $banner ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" enctype="multipart/form-data" class="card" style="padding:24px;max-width:640px;">
        @csrf
        @if($banner) @method('PUT') @endif

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div>
                <label class="label">Изображение (широкий формат, рекомендуется ~1800&times;790)</label>
                @if($banner)
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" style="width:100%;height:160px;object-fit:cover;border-radius:12px;margin-bottom:10px;">
                @endif
                <input type="file" name="image" accept="image/*" class="input" style="padding:10px 14px;height:auto;" {{ $banner ? '' : 'required' }}>
                @error('image')<div style="color:var(--danger);font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            </div>

            @php($placement = old('placement', $banner->placement ?? 'main'))
            <div>
                <label class="label">Место показа</label>
                <select name="placement" class="input" style="font-family:var(--font);">
                    <option value="main" {{ $placement === 'main' ? 'selected' : '' }}>Только главная страница</option>
                    <option value="marketplace" {{ $placement === 'marketplace' ? 'selected' : '' }}>Только маркетплейс</option>
                    <option value="both" {{ $placement === 'both' ? 'selected' : '' }}>Везде</option>
                </select>
                @error('placement')<div style="color:var(--danger);font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            </div>

            @php($showOverlay = old('show_overlay', $banner->show_overlay ?? true))
            <label style="display:flex;align-items:flex-start;gap:10px;padding:14px 16px;border-radius:12px;background:var(--surface-hover);border:1px solid var(--border);cursor:pointer;">
                <input type="checkbox" id="show-overlay-toggle" name="show_overlay" value="1" {{ $showOverlay ? 'checked' : '' }} style="width:18px;height:18px;margin-top:1px;accent-color:var(--accent);">
                <span>
                    <span style="display:block;font-weight:800;font-size:13.5px;">Наложить заголовок и кнопку поверх изображения</span>
                    <span style="display:block;font-size:12px;color:var(--text-faint);margin-top:2px;">Выключите, если изображение уже готовое (со своим текстом и надписями) — оно покажется как есть, без затемнения и дублирующего текста.</span>
                </span>
            </label>

            <div id="overlay-text-fields" style="display:flex;flex-direction:column;gap:16px;">
                <div><label class="label">Заголовок</label><input class="input" name="title" value="{{ old('title', $banner->title ?? '') }}" placeholder="Например: Сезонная распродажа канцтоваров"></div>
                <div><label class="label">Подзаголовок</label><textarea name="subtitle" class="input" style="height:70px;padding:12px 14px;resize:none;" placeholder="Короткое описание акции">{{ old('subtitle', $banner->subtitle ?? '') }}</textarea></div>
                <div class="rgrid-2">
                    <div><label class="label">Текст кнопки</label><input class="input" name="button_text" value="{{ old('button_text', $banner->button_text ?? '') }}" placeholder="Смотреть акции"></div>
                    <div><label class="label">Ссылка кнопки</label><input class="input" name="link_url" value="{{ old('link_url', $banner->link_url ?? '') }}" placeholder="/catalog"></div>
                </div>
            </div>
        </div>

        <script>
        (function () {
            var toggle = document.getElementById('show-overlay-toggle');
            var fields = document.getElementById('overlay-text-fields');
            var titleInput = fields.querySelector('input[name="title"]');
            function sync() {
                var on = toggle.checked;
                fields.style.opacity = on ? '1' : '.5';
                titleInput.required = on;
            }
            toggle.addEventListener('change', sync);
            sync();
        })();
        </script>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
            <a href="{{ route('admin.banners.index') }}" class="btn-ghost">Отмена</a>
            <button type="submit" class="btn-accent">{{ $banner ? 'Сохранить' : 'Добавить баннер' }}</button>
        </div>
    </form>
</x-dashboard-layout>
