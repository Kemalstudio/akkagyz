@props(['product', 'showSeller' => false])
@php
    $inWishlist = auth()->check() && auth()->user()->wishlistItems()->where('product_id', $product->id)->exists();
    $inCompare = auth()->check() && auth()->user()->compareItems()->where('product_id', $product->id)->exists();
    $productUrl = route($product->seller_id ? 'marketplace.products.show' : 'products.show', $product->slug);
    $cardImage = $product->images->first();
    $cartQuantity = auth()->check() ? (int) (auth()->user()->cartItems()->where('product_id', $product->id)->value('quantity') ?? 0) : 0;
@endphp
<div class="pcard">
    <a href="{{ $productUrl }}" class="pcard-img" aria-label="{{ $product->name }}" @if($cardImage) style="background-image:url('{{ $cardImage->url }}');background-size:contain;background-repeat:no-repeat;background-position:center;" @endif>
        @unless($cardImage)
            <x-icon name="image" :size="36" />
        @endunless

        <div style="position:absolute;top:10px;left:10px;display:flex;flex-direction:column;gap:6px;">
            @if($product->is_vip)
                <span class="badge" style="background:linear-gradient(100deg, oklch(0.78 0.16 85), oklch(0.68 0.17 60));color:#2a1a00;"><x-icon name="star" :size="10" />VIP</span>
            @endif
            @if($product->discount_percent)
                <span class="badge" style="background:var(--danger);color:white;">&minus;{{ $product->discount_percent }}%</span>
            @elseif($product->created_at?->gt(now()->subDays(21)))
                <span class="badge" style="background:var(--success);color:var(--accent-text);">Новинка</span>
            @endif
        </div>

        @if(!$product->in_stock)
            <span class="badge" style="position:absolute;bottom:0;left:0;right:0;background:oklch(0.17 0.014 264 / 0.85);color:var(--text-faint);text-align:center;border-radius:0;padding:7px;font-size:11.5px;">Нет в наличии</span>
        @endif
    </a>

    <div style="position:absolute;top:10px;right:10px;display:flex;flex-direction:column;gap:6px;">
        @auth
            <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="wishlist-toggle-form">@csrf
                <button type="submit" class="pcard-quick wishlist-toggle {{ $inWishlist ? 'on' : '' }}" aria-pressed="{{ $inWishlist ? 'true' : 'false' }}" title="{{ $inWishlist ? 'Удалить из избранного' : 'В избранное' }}">
                    <x-icon name="favorite" :size="17" />
                </button>
            </form>
            <button onclick="event.preventDefault();document.getElementById('cmp-{{ $product->id }}').submit();" class="pcard-quick {{ $inCompare ? 'on' : '' }}" title="Сравнить">
                <x-icon name="compare" :size="15" />
            </button>
            <form id="cmp-{{ $product->id }}" method="POST" action="{{ route('compare.toggle', $product) }}" style="display:none;">@csrf</form>
        @else
            <button type="button" onclick="akOpenAuthGate('wishlist')" class="pcard-quick" title="В избранное"><x-icon name="heart" :size="15" /></button>
            <button type="button" onclick="akOpenAuthGate('compare')" class="pcard-quick" title="Сравнить"><x-icon name="compare" :size="15" /></button>
        @endauth
    </div>

    <div style="padding:14px;display:flex;flex-direction:column;gap:5px;flex:1;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:6px;">
            <span style="font-size:10px;color:var(--text-faint);font-weight:600;text-transform:uppercase;letter-spacing:.06em;">{{ $product->category?->name }}</span>
            @if($product->in_stock)
                <span style="display:flex;align-items:center;gap:4px;font-size:10.5px;color:var(--success);font-weight:700;flex-shrink:0;">
                    <span style="width:6px;height:6px;border-radius:50%;background:var(--success);"></span>В наличии
                </span>
            @endif
        </div>

        <a href="{{ $productUrl }}" style="font-size:14px;font-weight:600;line-height:1.4;min-height:40px;color:var(--text);display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $product->name }}</a>

        <div style="display:flex;align-items:center;gap:8px;min-height:20px;">
            @if($product->rating_count > 0)
                <span class="rating-chip">
                    <x-star-rating :rating="$product->rating_avg" :size="11" :gap="1" />
                    {{ number_format($product->rating_avg, 1) }}
                </span>
                <span style="color:var(--text-faint);font-size:11.5px;font-weight:600;">{{ $product->rating_count }} {{ $product->rating_count == 1 ? 'отзыв' : 'отзывов' }}</span>
            @endif
        </div>

        @if($showSeller && $product->seller)
            <div style="display:flex;align-items:center;gap:5px;color:var(--text-faint);font-size:11.5px;font-weight:600;">
                <x-icon name="store" :size="11" />
                <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $product->seller->store_name ?? $product->seller->name }}</span>
            </div>
        @endif

        <div style="margin-top:auto;display:flex;align-items:baseline;gap:8px;padding-top:4px;">
            <div style="font-size:19px;font-weight:700;letter-spacing:-.02em;{{ $product->compare_price ? 'color:var(--danger);' : '' }}">{{ number_format($product->price, 0, '', ' ') }} TMT</div>
            @if($product->compare_price)
                <div style="font-size:12.5px;color:var(--text-faint);text-decoration:line-through;">{{ number_format($product->compare_price, 0, '', ' ') }} TMT</div>
            @endif
        </div>

        @if($product->in_stock)
            @auth
            <form method="POST" action="{{ route('cart.add', $product) }}" class="cart-ajax-form" data-product-id="{{ $product->id }}">
                @csrf
                <div class="cart-card-state">
                    @if($cartQuantity > 0)
                        <div class="cart-qty-control"><button type="submit" data-cart-action="decrement"><x-icon name="minus" :size="14"/></button><span><small>В корзине</small><b>{{ $cartQuantity }}</b></span><button type="submit" data-cart-action="increment"><x-icon name="plus" :size="14"/></button></div>
                    @else
                        <button type="submit" data-cart-action="increment" class="btn-accent cart-add-button"><x-icon name="cart" :size="16"/><span>В корзину</span></button>
                    @endif
                </div>
            </form>
            @else<button type="button" onclick="akOpenAuthGate('cart')" class="btn-accent" style="width:100%;margin-top:6px"><x-icon name="cart" :size="16"/>В корзину</button>@endauth
        @else
            <button disabled class="btn-accent" style="width:100%;margin-top:6px;background:var(--surface-hover);color:var(--text-faint);cursor:not-allowed;">Нет в наличии</button>
        @endif
    </div>
</div>
