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
            <div><label class="label">Заголовок</label><input required class="input" name="title" value="{{ old('title', $banner->title ?? '') }}" placeholder="Например: Сезонная распродажа канцтоваров"></div>
            <div><label class="label">Подзаголовок</label><textarea name="subtitle" class="input" style="height:70px;padding:12px 14px;resize:none;" placeholder="Короткое описание акции">{{ old('subtitle', $banner->subtitle ?? '') }}</textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div><label class="label">Текст кнопки</label><input class="input" name="button_text" value="{{ old('button_text', $banner->button_text ?? '') }}" placeholder="Смотреть акции"></div>
                <div><label class="label">Ссылка кнопки</label><input class="input" name="link_url" value="{{ old('link_url', $banner->link_url ?? '') }}" placeholder="/catalog"></div>
            </div>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
            <a href="{{ route('admin.banners.index') }}" class="btn-ghost">Отмена</a>
            <button type="submit" class="btn-accent">{{ $banner ? 'Сохранить' : 'Добавить баннер' }}</button>
        </div>
    </form>
</x-dashboard-layout>
