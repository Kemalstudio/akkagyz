@if($promo)
@php
    $discountLabel = $promo->type === 'percent' ? $promo->value.'%' : number_format($promo->value, 0, '', ' ').' TMT';
@endphp
<div id="ak-promo-banner" style="position:relative;background:linear-gradient(100deg, var(--accent-strong), var(--accent));color:var(--accent-text);">
    <div class="wrap" style="padding:9px 44px 9px 24px;display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;position:relative;text-align:center;">
        <x-icon name="gift" :size="16" style="flex-shrink:0;opacity:.9;" />
        <span style="font-size:13px;font-weight:700;line-height:1.3;">
            Промокод <b style="font-weight:900;letter-spacing:.02em;">{{ $promo->code }}</b>
            &mdash; скидка {{ $discountLabel }} на заказ{{ $promo->minimum_amount > 0 ? ' от '.number_format($promo->minimum_amount, 0, '', ' ').' TMT' : '' }}
        </span>
        <button type="button" id="ak-promo-copy" data-code="{{ $promo->code }}" style="background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);color:inherit;font-size:11.5px;font-weight:800;padding:4px 11px;border-radius:20px;flex-shrink:0;cursor:pointer;transition:background .15s;">
            Скопировать код
        </button>
        <button type="button" id="ak-promo-banner-close" aria-label="Скрыть" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:24px;height:24px;border-radius:50%;background:rgba(255,255,255,.12);border:none;color:inherit;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
            <x-icon name="x" :size="13" />
        </button>
    </div>
</div>
<script>
(function(){
    var banner = document.getElementById('ak-promo-banner');
    if (!banner) return;
    var version = '{{ $promo->id }}-{{ $promo->updated_at->timestamp }}';
    var seenKey = 'ak-promo-banner-dismissed';
    try {
        if (localStorage.getItem(seenKey) === version) { banner.remove(); return; }
    } catch (e) {}

    document.getElementById('ak-promo-banner-close').addEventListener('click', function(){
        try { localStorage.setItem(seenKey, version); } catch (e) {}
        banner.remove();
    });

    var copyBtn = document.getElementById('ak-promo-copy');
    copyBtn.addEventListener('click', function(){
        var code = copyBtn.getAttribute('data-code');
        var done = function(){
            var original = copyBtn.textContent;
            copyBtn.textContent = 'Скопировано!';
            setTimeout(function(){ copyBtn.textContent = original; }, 1800);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(done).catch(done);
        } else {
            done();
        }
    });
})();
</script>
@endif
