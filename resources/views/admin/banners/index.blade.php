<x-dashboard-layout title="Баннеры" active="banners">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
        <div>
            <div style="font-size:24px;font-weight:900;">Баннеры на главной</div>
            <div style="font-size:13px;color:var(--text-faint);margin-top:2px;">Перетаскивайте карточки, чтобы изменить порядок показа в слайдере</div>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="btn-accent"><x-icon name="plus" :size="15" />Добавить баннер</a>
    </div>

    <div id="banner-list" style="display:flex;flex-direction:column;gap:14px;">
        @forelse($banners as $banner)
            <div class="banner-row" data-id="{{ $banner->id }}" draggable="true" style="display:flex;align-items:center;gap:16px;padding:16px;border-radius:16px;background:var(--surface);border:1px solid var(--border);cursor:grab;{{ !$banner->is_active ? 'opacity:.55;' : '' }}">
                <div style="color:var(--text-faint);flex-shrink:0;" title="Перетащите для сортировки">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="9" cy="6" r="1.6"/><circle cx="15" cy="6" r="1.6"/><circle cx="9" cy="12" r="1.6"/><circle cx="15" cy="12" r="1.6"/><circle cx="9" cy="18" r="1.6"/><circle cx="15" cy="18" r="1.6"/></svg>
                </div>
                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" loading="lazy" style="width:140px;height:64px;object-fit:cover;border-radius:10px;flex-shrink:0;">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:14px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $banner->title ?: 'Без наложения текста' }}{{ !$banner->show_overlay ? ' · изображение' : '' }}</div>
                    <div style="font-size:12px;color:var(--text-faint);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $banner->subtitle }}</div>
                </div>
                <span class="badge" style="background:var(--accent-soft);color:var(--accent);flex-shrink:0;">{{ ['main' => 'Главная', 'marketplace' => 'Маркетплейс', 'both' => 'Везде'][$banner->placement] ?? $banner->placement }}</span>
                <span class="badge" style="background:{{ $banner->is_active ? 'var(--success-soft)' : 'var(--surface-hover)' }};color:{{ $banner->is_active ? 'var(--success)' : 'var(--text-faint)' }};flex-shrink:0;">{{ $banner->is_active ? 'Активен' : 'Скрыт' }}</span>
                <div style="display:flex;gap:8px;flex-shrink:0;">
                    <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">@csrf<button type="submit" style="height:34px;padding:0 12px;border-radius:8px;font-weight:800;font-size:12px;border:1px solid var(--border);background:none;color:var(--text);">{{ $banner->is_active ? 'Скрыть' : 'Показать' }}</button></form>
                    <a href="{{ route('admin.banners.edit', $banner) }}" style="height:34px;padding:0 12px;border-radius:8px;font-weight:800;font-size:12px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;">Изменить</a>
                    <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Удалить баннер?');">@csrf @method('DELETE')<button type="submit" style="height:34px;padding:0 12px;border-radius:8px;font-weight:800;font-size:12px;border:none;background:var(--danger-soft);color:var(--danger);">Удалить</button></form>
                </div>
            </div>
        @empty
            <div class="card" style="padding:40px;text-align:center;color:var(--text-faint);font-size:14px;">Баннеров пока нет.</div>
        @endforelse
    </div>

    <script>
    (function(){
        var list = document.getElementById('banner-list');
        var dragEl = null;
        list.addEventListener('dragstart', function(e){
            dragEl = e.target.closest('.banner-row');
            dragEl.style.opacity = '.4';
        });
        list.addEventListener('dragend', function(){
            if (dragEl) dragEl.style.opacity = '';
            dragEl = null;
            saveOrder();
        });
        list.addEventListener('dragover', function(e){
            e.preventDefault();
            var target = e.target.closest('.banner-row');
            if (!target || target === dragEl) return;
            var rect = target.getBoundingClientRect();
            var after = (e.clientY - rect.top) > rect.height / 2;
            list.insertBefore(dragEl, after ? target.nextSibling : target);
        });
        function saveOrder(){
            var ids = Array.from(list.querySelectorAll('.banner-row')).map(function(el){ return el.dataset.id; });
            fetch("{{ route('admin.banners.reorder') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({order: ids})
            });
        }
    })();
    </script>
</x-dashboard-layout>
