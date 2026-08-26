@props(['notifications', 'unreadCount'])
<div style="position:relative;">
    <button type="button" onclick="akToggleMenu(event,'notif-menu')" class="icon-btn" title="Уведомления">
        <x-icon name="bell" :size="19" />
        @if($unreadCount > 0)<span class="count">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>@endif
    </button>
    <div id="notif-menu" class="dropdown-panel" style="position:absolute;right:0;top:52px;background:var(--surface);border:1px solid var(--border);border-radius:14px;width:360px;z-index:70;box-shadow:0 24px 48px rgba(0,0,0,.28);overflow:hidden;">
        <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:14px;font-weight:900;">Уведомления</span>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.readAll') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;padding:0;font-size:12px;font-weight:700;color:var(--accent);">Прочитать все</button>
                </form>
            @endif
        </div>
        <div style="max-height:380px;overflow-y:auto;">
            @forelse($notifications as $n)
                <a href="{{ route('notifications.open', $n->id) }}" style="display:flex;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border);{{ $n->read_at ? '' : 'background:var(--accent-soft);' }}">
                    <div style="width:34px;height:34px;border-radius:10px;background:var(--bg-elevated);color:var(--accent);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <x-icon :name="$n->data['icon'] ?? 'bell'" :size="16" />
                    </div>
                    <div style="min-width:0;">
                        <div style="font-size:13px;font-weight:800;color:var(--text);">{{ $n->data['title'] ?? '' }}</div>
                        <div style="font-size:12px;color:var(--text-muted);margin-top:2px;line-height:1.4;">{{ $n->data['message'] ?? '' }}</div>
                        <div style="font-size:11px;color:var(--text-faint);margin-top:4px;">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @empty
                <div style="padding:32px 16px;text-align:center;color:var(--text-faint);font-size:13px;">Пока нет уведомлений</div>
            @endforelse
        </div>
    </div>
</div>
