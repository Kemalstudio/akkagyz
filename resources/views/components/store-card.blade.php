@props(['seller'])
@php($hue = crc32($seller->store_slug ?? $seller->store_name) % 360)
<a href="{{ route('stores.show', $seller->store_slug) }}" class="pcard store-card {{ $seller->is_vip ? 'is-vip' : '' }}">
    @if($seller->is_vip)
        <span class="badge" style="position:absolute;top:14px;right:14px;background:linear-gradient(100deg, oklch(0.78 0.16 85), oklch(0.68 0.17 60));color:#2a1a00;"><x-icon name="star" :size="11" />VIP</span>
    @endif
    <div class="store-card-avatar" style="background:oklch(0.93 0.07 {{ $hue }});color:oklch(0.4 0.15 {{ $hue }});{{ $seller->is_vip ? '' : 'border-color:oklch(0.82 0.09 '.$hue.');' }}">
        {{ Str::of($seller->store_name)->substr(0, 2)->upper() }}
    </div>
    <div style="font-size:17px;font-weight:700;color:var(--text);letter-spacing:-.01em">{{ $seller->store_name }}</div>
    <div style="font-size:13px;color:var(--text-faint);margin-top:4px;min-height:36px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $seller->store_description ?: 'Продавец на '.$businessSettings->site_name }}</div>
    <div style="display:flex;align-items:center;gap:14px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border);flex-wrap:wrap;">
        <span style="display:flex;align-items:center;gap:5px;font-size:12.5px;color:var(--text-muted);font-weight:700;"><x-icon name="package" :size="13" />{{ $seller->products_count }} товаров</span>
        @if($seller->store_rating)
            <span style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:var(--text-muted);font-weight:700;">
                <x-star-rating :rating="$seller->store_rating" :size="12" :gap="1" />
                {{ $seller->store_rating }}
            </span>
        @endif
    </div>
</a>
<style>
.store-card{padding:24px;position:relative;min-height:220px;}
.store-card.is-vip{border-color:var(--warning);}
.store-card-avatar{width:58px;height:58px;border-radius:17px;border:2px solid transparent;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;margin-bottom:16px;}
.store-card.is-vip .store-card-avatar{border-color:var(--warning);box-shadow:0 0 0 3px var(--warning-soft);}
</style>
