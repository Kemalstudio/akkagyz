@if($popup)
<div id="ak-promo-overlay" style="display:none;position:fixed;inset:0;background:oklch(0.1 0.02 264 / 0);backdrop-filter:blur(0px);z-index:200;align-items:center;justify-content:center;transition:background .35s cubic-bezier(.16,1,.3,1),backdrop-filter .35s cubic-bezier(.16,1,.3,1);">
    <div id="ak-promo-card" style="position:relative;width:min(420px, 90vw);opacity:0;transform:translateY(18px) scale(.95);transition:opacity .35s cubic-bezier(.16,1,.3,1),transform .35s cubic-bezier(.16,1,.3,1);">
        <button id="ak-promo-close" type="button" aria-label="Закрыть" style="position:absolute;top:-14px;right:-14px;width:36px;height:36px;border-radius:50%;background:var(--accent);color:var(--accent-text);border:3px solid var(--bg);display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transform:scale(.5) rotate(-45deg);transition:opacity .25s cubic-bezier(.16,1,.3,1),transform .25s cubic-bezier(.16,1,.3,1),visibility .25s;z-index:2;cursor:pointer;">
            <x-icon name="x" :size="16" />
        </button>
        <a href="{{ $popup->link_url ?: '#' }}" id="ak-promo-link" style="display:block;border-radius:18px;overflow:hidden;background:var(--surface);border:1px solid var(--border);box-shadow:0 32px 64px rgba(0,0,0,.4);">
            <div style="position:relative;">
                <img src="{{ $popup->image_url }}" alt="{{ $popup->title }}" style="width:100%;display:block;">
                @if($popup->badge_text)
                    <span class="badge" style="position:absolute;top:14px;left:14px;background:var(--danger);color:#fff;font-size:12px;padding:6px 12px;">{{ $popup->badge_text }}</span>
                @endif
            </div>
            @if($popup->title)
                <div style="padding:16px 18px;font-size:15px;font-weight:800;color:var(--text);">{{ $popup->title }}</div>
            @endif
        </a>
    </div>
</div>

<script>
(function(){
    var popup = document.getElementById('ak-promo-overlay');
    if (!popup) return;
    var version = '{{ $popup->id }}-{{ $popup->updated_at->timestamp }}';
    var seenKey = 'ak-popup-seen';
    try {
        if (localStorage.getItem(seenKey) === version) return;
    } catch (e) {}

    var card = document.getElementById('ak-promo-card');
    var closeBtn = document.getElementById('ak-promo-close');
    var canClose = false;
    var scrollY = 0;

    scrollY = window.scrollY || 0;
    document.body.style.overflow = 'hidden';
    popup.style.display = 'flex';
    requestAnimationFrame(function(){
        requestAnimationFrame(function(){
            popup.style.background = 'oklch(0.1 0.02 264 / 0.7)';
            popup.style.backdropFilter = 'blur(4px)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0) scale(1)';
        });
    });

    setTimeout(function(){
        canClose = true;
        closeBtn.style.opacity = '1';
        closeBtn.style.visibility = 'visible';
        closeBtn.style.transform = 'scale(1) rotate(0deg)';
    }, 1000);

    function closePopup(e){
        if (e) e.preventDefault();
        if (!canClose) return;
        try { localStorage.setItem(seenKey, version); } catch (err) {}
        popup.style.background = 'oklch(0.1 0.02 264 / 0)';
        popup.style.backdropFilter = 'blur(0px)';
        card.style.opacity = '0';
        card.style.transform = 'translateY(18px) scale(.95)';
        document.body.style.overflow = '';
        setTimeout(function(){ popup.style.display = 'none'; }, 320);
    }

    closeBtn.addEventListener('click', closePopup);
    popup.addEventListener('click', function(e){ if (e.target === popup) closePopup(e); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closePopup(); });
})();
</script>
@endif
