<x-dashboard-layout :title="$popup ? 'Изменить попап' : 'Добавить попап'" active="popups">
    <div style="font-size:24px;font-weight:900;margin-bottom:24px;">{{ $popup ? 'Изменить попап' : 'Добавить попап' }}</div>

    <form method="POST" action="{{ $popup ? route('admin.popups.update', $popup) : route('admin.popups.store') }}" enctype="multipart/form-data" class="card" style="padding:24px;max-width:560px;">
        @csrf
        @if($popup) @method('PUT') @endif

        <div style="display:flex;flex-direction:column;gap:16px;">
            <div>
                <label class="label">Изображение попапа</label>
                @if($popup)
                    <img src="{{ $popup->image_url }}" alt="{{ $popup->title }}" style="width:220px;border-radius:12px;margin-bottom:10px;display:block;">
                @endif
                <input type="file" name="image" accept="image/*" class="input" style="padding:10px 14px;height:auto;" {{ $popup ? '' : 'required' }}>
                @error('image')<div style="color:var(--danger);font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            </div>
            <div><label class="label">Заголовок (необязательно)</label><input class="input" name="title" value="{{ old('title', $popup->title ?? '') }}" placeholder="Например: Скидка 5% на новинки"></div>
            <div><label class="label">Текст бейджа (необязательно)</label><input class="input" name="badge_text" value="{{ old('badge_text', $popup->badge_text ?? '') }}" placeholder="Например: -5%"></div>
            <div><label class="label">Ссылка при клике</label><input class="input" name="link_url" value="{{ old('link_url', $popup->link_url ?? '') }}" placeholder="/catalog"></div>
            <label style="display:flex;align-items:center;gap:10px;font-size:14px;color:var(--text-muted);font-weight:600;cursor:pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $popup->is_active ?? true) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--accent);">
                Показывать посетителям (включение этого попапа выключит остальные)
            </label>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;">
            <a href="{{ route('admin.popups.index') }}" class="btn-ghost">Отмена</a>
            <button type="submit" class="btn-accent">{{ $popup ? 'Сохранить' : 'Добавить попап' }}</button>
        </div>
    </form>
</x-dashboard-layout>
