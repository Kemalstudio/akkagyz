@php
    $routeName = $routeName ?? 'catalog';
    $homeRouteName = $homeRouteName ?? 'home';
    $inWishlist = auth()->check() && auth()->user()->wishlistItems()->where('product_id', $product->id)->exists();
    $inCompare = auth()->check() && auth()->user()->compareItems()->where('product_id', $product->id)->exists();
    $userReview = auth()->check() ? $product->reviews()->where('user_id', auth()->id())->first() : null;
    $mainImage = $product->images->first();
    $cartQuantity = auth()->check() ? (int) (auth()->user()->cartItems()->where('product_id', $product->id)->value('quantity') ?? 0) : 0;
@endphp
<style>
.reviews-section{padding:54px 24px 18px}.reviews-heading{display:flex;align-items:end;justify-content:space-between;gap:18px;margin-bottom:20px}.reviews-kicker{font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--accent);font-weight:700}.reviews-heading h2{font-size:25px;font-weight:700;letter-spacing:-.025em;margin:5px 0 0}.reviews-heading h2 span{color:var(--text-faint);font-weight:500}.reviews-top-rating{display:flex;align-items:center;gap:6px;padding:8px 11px;border-radius:10px;background:var(--warning-soft);color:var(--warning);font-size:14px;font-weight:700}.reviews-top-rating small{font-weight:500;color:var(--text-faint)}.reviews-overview{display:grid;grid-template-columns:250px minmax(0,650px);gap:16px;align-items:stretch}.rating-summary,.review-editor,.review-login-card{border:1px solid var(--border);border-radius:18px;background:var(--surface)}.rating-summary{padding:23px;text-align:center}.rating-big{font-size:43px;font-weight:700;letter-spacing:-.05em;margin-bottom:5px}.rating-summary-copy{font-size:11px;color:var(--text-faint);margin-top:9px}.verified-note{display:flex;align-items:center;text-align:left;gap:7px;margin-top:18px;padding-top:15px;border-top:1px solid var(--border);font-size:10px;line-height:1.4;color:var(--text-faint)}.verified-note svg{color:var(--success);flex-shrink:0}.review-editor{padding:20px}.review-editor-head{display:flex;align-items:center;justify-content:space-between;gap:14px}.review-editor-head strong,.review-login-card strong{display:block;font-size:15px}.review-editor-head>div>span{display:block;font-size:10px;color:var(--text-faint);margin-top:3px}.review-user-avatar,.review-avatar{display:grid;place-items:center;overflow:hidden;background:var(--accent-soft);color:var(--accent);font-size:11px;font-weight:700;flex-shrink:0}.review-user-avatar{width:38px;height:38px;border-radius:11px}.review-user-avatar img,.review-avatar img{width:100%;height:100%;object-fit:cover}.rating-question{font-size:10px;color:var(--text-faint);margin-top:14px}.rating-picker{display:flex;flex-direction:row-reverse;justify-content:flex-end;gap:4px;margin:5px 0 12px}.rating-picker input{position:absolute;opacity:0}.rating-picker label{color:var(--border-strong);cursor:pointer;display:flex;transition:.15s}.rating-picker label:hover,.rating-picker label:hover~label,.rating-picker input:checked~label{color:var(--warning);transform:translateY(-1px)}.review-editor>textarea{width:100%;min-height:90px;padding:12px 13px;border:1px solid var(--border);border-radius:11px;background:var(--bg);color:var(--text);font:inherit;font-size:12px;line-height:1.55;resize:vertical;outline:none}.review-editor>textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}.review-editor-footer{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:10px}.review-editor-footer>span{font-size:9px;color:var(--text-faint)}.review-login-card{padding:23px;display:grid;grid-template-columns:47px 1fr auto;align-items:center;gap:14px}.review-login-icon{width:47px;height:47px;border-radius:13px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center}.review-login-card p{font-size:11px;line-height:1.5;color:var(--text-faint);margin:5px 0 0}.review-list-head{display:flex;align-items:center;justify-content:space-between;margin:34px 0 13px;max-width:916px}.review-list-head>div strong{font-size:16px}.review-list-head>div span{font-size:10px;color:var(--text-faint);margin-left:8px}.reviews-list{max-width:916px;display:grid;gap:11px}.review-item{display:flex;gap:13px;padding:18px;border:1px solid var(--border);border-radius:16px;background:var(--surface);transition:.18s}.review-item:hover{border-color:var(--border-strong);box-shadow:0 8px 20px rgba(0,0,0,.05)}.review-avatar{width:42px;height:42px;border-radius:12px}.review-item-body{min-width:0;flex:1}.review-meta{display:flex;align-items:center;gap:8px;flex-wrap:wrap}.review-meta strong{font-size:13px}.review-meta time{margin-left:auto;font-size:10px;color:var(--text-faint)}.verified-buyer{display:flex;align-items:center;gap:3px;padding:3px 5px;border-radius:6px;background:var(--success-soft);color:var(--success);font-size:8px;font-weight:700}.review-stars{display:flex;align-items:center;gap:7px;margin-top:5px}.review-stars b{font-size:10px;color:var(--text-faint)}.review-item-body>p{font-size:13px;color:var(--text-muted);line-height:1.65;margin:10px 0 0}.review-no-copy{font-size:11px;color:var(--text-faint);margin-top:9px;font-style:italic}.reviews-empty{text-align:center;padding:42px 22px;border:1px dashed var(--border);border-radius:17px;background:var(--surface)}.reviews-empty>span{width:52px;height:52px;border-radius:15px;margin:0 auto 12px;background:var(--warning-soft);color:var(--warning);display:grid;place-items:center}.reviews-empty strong{display:block;font-size:15px}.reviews-empty p{font-size:11px;color:var(--text-faint);margin:5px 0 0}@media(max-width:760px){.reviews-overview{grid-template-columns:1fr}.review-login-card{grid-template-columns:47px 1fr}.review-login-card .btn-accent{grid-column:1/-1}.review-meta time{margin-left:0}}@media(max-width:480px){.reviews-section{padding-left:14px;padding-right:14px}.review-item{padding:14px}.review-editor-footer{align-items:flex-end}.review-editor-footer .btn-accent{font-size:11px;padding:0 12px}}
</style>
<div class="wrap" style="padding:18px 24px 8px;display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-faint);font-weight:600;">
    <a href="{{ route($homeRouteName) }}" style="color:var(--text-faint);">Главная</a><span>/</span>
    @if($product->category)
        <a href="{{ route($routeName, ['category' => $product->category->slug]) }}" style="color:var(--text-faint);">{{ $product->category->name }}</a><span>/</span>
    @endif
    <span style="color:var(--text);">{{ $product->name }}</span>
</div>

<div class="wrap product-detail-grid" style="padding:12px 24px 8px;display:grid;grid-template-columns:1fr 1fr;gap:48px;">
    <div style="display:flex;flex-direction:column;gap:12px;position:relative;">
        <div id="product-main-image" class="product-gallery-main">
            @if($mainImage)<img id="gallery-main-img" src="{{ $mainImage->url }}" alt="{{ $product->name }}">@else<x-icon name="image" :size="90" />@endif
            <div style="position:absolute;top:16px;left:16px;display:flex;flex-direction:column;gap:6px;">
                @if($product->is_vip)
                    <span class="badge" style="background:linear-gradient(100deg, oklch(0.78 0.16 85), oklch(0.68 0.17 60));color:#2a1a00;"><x-icon name="star" :size="11" />VIP</span>
                @endif
                @if($product->discount_percent)
                    <span class="badge" style="background:var(--danger);color:white;">&minus;{{ $product->discount_percent }}%</span>
                @endif
            </div>
            @if($product->images->count()>1)<button type="button" class="gallery-nav gallery-prev" onclick="akGalleryMove(-1)"><x-icon name="chevron-left" :size="20"/></button><button type="button" class="gallery-nav gallery-next" onclick="akGalleryMove(1)"><x-icon name="chevron-right" :size="20"/></button><span class="gallery-count"><span id="gallery-index">1</span> / {{ $product->images->count() }}</span>@endif
            @if($mainImage)<div class="gallery-zoom-lens" id="gallery-zoom-lens"></div>@endif
        </div>
        @if($mainImage)<div class="gallery-zoom-pane" id="gallery-zoom-pane"></div>@endif
        @if($product->images->count() > 1)
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                @foreach($product->images as $img)
                    <button type="button" class="gallery-thumb {{ $loop->first?'active':'' }}" data-index="{{ $loop->index }}" onclick="akGalleryGo({{ $loop->index }})"><img src="{{ $img->url }}" alt="" loading="lazy"></button>
                @endforeach
            </div>
        @endif
    </div>

    <div style="display:flex;flex-direction:column;">
        <h1 style="font-size:30px;font-weight:700;letter-spacing:-.02em;line-height:1.25;margin:6px 0 0;">{{ $product->name }}</h1>
        <div style="display:flex;align-items:center;gap:12px;margin-top:12px;">
            @if($product->rating_count > 0)
                <span class="rating-chip" style="font-size:13px;padding:5px 10px 5px 8px;">
                    <x-star-rating :rating="$product->rating_avg" :size="14" />
                    {{ number_format($product->rating_avg, 1) }}
                </span>
                <a href="#reviews" style="font-size:13px;color:var(--text-faint);font-weight:600;">{{ $product->rating_count }} отзывов</a>
                <span style="color:var(--text-faint);">&middot;</span>
            @endif
            <span style="font-size:13px;color:{{ $product->in_stock ? 'var(--success)' : 'var(--text-faint)' }};font-weight:700;">{{ $product->in_stock ? 'В наличии' : 'Нет в наличии' }}</span>
        </div>

        <div style="display:flex;align-items:baseline;gap:12px;margin-top:20px;">
            <div style="font-size:34px;font-weight:700;letter-spacing:-.03em;">{{ number_format($product->price, 0, '', ' ') }} TMT</div>
            @if($product->compare_price)
                <div style="font-size:17px;color:var(--text-faint);text-decoration:line-through;">{{ number_format($product->compare_price, 0, '', ' ') }} TMT</div>
                <div class="badge" style="background:var(--danger-soft);color:var(--danger);">Экономия {{ number_format($product->compare_price - $product->price, 0, '', ' ') }} TMT</div>
            @endif
        </div>

        @if($product->description)
            <p style="font-size:14px;color:var(--text-muted);line-height:1.7;margin-top:20px;">{{ $product->description }}</p>
        @endif

        <div class="product-facts"><div><span>Артикул</span><strong>AK-{{ str_pad($product->id,6,'0',STR_PAD_LEFT) }}</strong></div><div><span>Категория</span><strong>{{ $product->category?->name ?: '—' }}</strong></div><div><span>Продано</span><strong>{{ $product->sales_count }} шт.</strong></div><div><span>На складе</span><strong>{{ $product->stock }} шт.</strong></div></div>

        @if($product->attributeValues->isNotEmpty())
            @php($specs = $product->attributeValues->sortBy(fn($item) => $item->attribute->sort_order))
            <div style="margin-top:24px;">
                <div style="font-size:15px;font-weight:800;margin-bottom:12px;">Характеристики</div>
                <div style="border:1px solid var(--border);border-radius:13px;overflow:hidden;">
                    @foreach($specs as $item)
                        <div style="display:flex;justify-content:space-between;gap:14px;padding:11px 15px;font-size:13px;{{ !$loop->last ? 'border-bottom:1px solid var(--border);' : '' }}">
                            <span style="color:var(--text-faint);">{{ $item->attribute->name }}</span>
                            <span style="font-weight:700;text-align:right;">{{ $item->value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div style="display:flex;align-items:center;gap:14px;margin-top:20px;">
            @auth
                @if($product->in_stock)
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="cart-ajax-form cart-ajax-large" data-product-id="{{ $product->id }}" style="flex:1;">
                        @csrf
                        <div class="cart-card-state" style="height:52px">
                            @if($cartQuantity > 0)
                                <div class="cart-qty-control" style="height:52px"><button type="submit" data-cart-action="decrement"><x-icon name="minus" :size="15"/></button><span><small>Товар в корзине</small><b>{{ $cartQuantity }}</b></span><button type="submit" data-cart-action="increment"><x-icon name="plus" :size="15"/></button></div>
                            @else
                                <button type="submit" data-cart-action="increment" class="btn-accent cart-add-button" style="height:52px;font-size:15px"><x-icon name="cart" :size="18"/><span>Добавить в корзину</span></button>
                            @endif
                        </div>
                    </form>
                @else
                    <button disabled class="btn-accent" style="flex:1;height:52px;font-size:15px;background:var(--surface-hover);color:var(--text-faint);">Нет в наличии</button>
                @endif
                <form method="POST" action="{{ route('wishlist.toggle', $product) }}" class="wishlist-toggle-form" style="height:52px">
                    @csrf
                    <button type="submit" class="icon-btn wishlist-toggle {{ $inWishlist ? 'on' : '' }}" style="width:52px;height:52px;border:1px solid var(--border);background:var(--surface)" aria-pressed="{{ $inWishlist ? 'true' : 'false' }}" title="{{ $inWishlist ? 'Удалить из избранного' : 'В избранное' }}">
                        <x-icon name="favorite" :size="19" />
                    </button>
                </form>
                <form method="POST" action="{{ route('compare.toggle', $product) }}">
                    @csrf
                    <button type="submit" class="icon-btn" style="width:52px;height:52px;border:1px solid var(--border);background:var(--surface);{{ $inCompare ? 'color:var(--accent);' : '' }}" title="Сравнить">
                        <x-icon name="compare" :size="19" />
                    </button>
                </form>
            @else
                <button type="button" onclick="akOpenAuthGate('cart')" class="btn-accent" style="flex:1;height:52px;font-size:15px;"><x-icon name="cart" :size="18" /> Добавить в корзину</button><button type="button" onclick="akOpenAuthGate('wishlist')" class="icon-btn" style="width:52px;height:52px;border:1px solid var(--border);background:var(--surface)"><x-icon name="heart" :size="19"/></button><button type="button" onclick="akOpenAuthGate('compare')" class="icon-btn" style="width:52px;height:52px;border:1px solid var(--border);background:var(--surface)"><x-icon name="compare" :size="19"/></button>
            @endauth
        </div>

        <div style="display:flex;gap:12px;margin-top:24px;">
            <div style="flex:1;display:flex;gap:10px;align-items:flex-start;padding:14px;border-radius:12px;background:var(--surface);border:1px solid var(--border);">
                <span style="color:var(--accent);flex-shrink:0;"><x-icon name="truck" :size="20" /></span>
                <div><div style="font-size:13px;font-weight:800;">Быстрая доставка</div><div style="font-size:12px;color:var(--text-faint);margin-top:2px;">По Туркменистану, 1&ndash;3 дня</div></div>
            </div>
            <div style="flex:1;display:flex;gap:10px;align-items:flex-start;padding:14px;border-radius:12px;background:var(--surface);border:1px solid var(--border);">
                <span style="color:var(--accent);flex-shrink:0;"><x-icon name="shield" :size="20" /></span>
                <div><div style="font-size:13px;font-weight:800;">Возврат 14 дней</div><div style="font-size:12px;color:var(--text-faint);margin-top:2px;">Если товар не подошёл</div></div>
            </div>
        </div>

        @if($product->seller)
            <a href="{{ $product->seller->store_slug ? route('stores.show', $product->seller->store_slug) : '#' }}" style="margin-top:20px;padding:16px;border-radius:14px;background:var(--bg-elevated);border:1px solid var(--border);display:flex;align-items:center;gap:14px;">
                <div style="width:44px;height:44px;border-radius:50%;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:900;flex-shrink:0;">
                    {{ Str::of($product->seller->store_name ?? $product->seller->name)->substr(0, 2)->upper() }}
                </div>
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:800;color:var(--text);display:flex;align-items:center;gap:6px;">Продавец: {{ $product->seller->store_name ?? $product->seller->name }} @if($product->seller->is_vip)<span style="color:var(--warning);"><x-icon name="star" :size="13" /></span>@endif</div>
                    <div style="font-size:12px;color:var(--text-faint);font-weight:700;margin-top:2px;">На площадке с {{ $product->seller->created_at->format('Y') }} года</div>
                </div>
                <x-icon name="chevron-right" :size="16" style="color:var(--text-faint);flex-shrink:0;" />
            </a>
        @endif
    </div>
</div>

<section class="wrap reviews-section" id="reviews">
    <div class="reviews-heading"><div><span class="reviews-kicker">Мнение покупателей</span><h2>Отзывы о товаре <span>{{ $product->rating_count }}</span></h2></div>@if($product->rating_count)<div class="reviews-top-rating"><x-icon name="star" :size="16"/>{{ number_format($product->rating_avg,1) }} <small>из 5</small></div>@endif</div>
    <div class="reviews-overview">
        <aside class="rating-summary"><div class="rating-big">{{ $product->rating_count ? number_format($product->rating_avg,1) : '—' }}</div><x-star-rating :rating="$product->rating_avg" :size="18" :gap="3"/><div class="rating-summary-copy">На основании {{ $product->rating_count }} {{ $product->rating_count===1?'отзыва':'отзывов' }}</div><div class="verified-note"><x-icon name="shield" :size="15"/>Отзывы могут оставлять зарегистрированные покупатели</div></aside>
        @auth
        <form method="POST" action="{{ route('products.reviews.store',$product) }}" class="review-editor">@csrf<div class="review-editor-head"><div><strong>{{ $userReview?'Изменить ваш отзыв':'Оставить отзыв' }}</strong><span>Оцените товар и поделитесь впечатлениями</span></div><span class="review-user-avatar">@if(auth()->user()->avatar_url)<img src="{{ auth()->user()->avatar_url }}" alt="">@else{{ Str::of(auth()->user()->name)->substr(0,2)->upper() }}@endif</span></div><div class="rating-question">Ваша оценка</div><div class="rating-picker">@for($i=5;$i>=1;$i--)<input id="product-rating-{{ $i }}" type="radio" name="rating" value="{{ $i }}" @checked(($userReview?->rating??0)===$i) required><label for="product-rating-{{ $i }}" title="{{ $i }} из 5"><x-icon name="star" :size="25"/></label>@endfor</div><textarea name="comment" maxlength="2000" placeholder="Что вам понравилось? Расскажите о качестве, упаковке и использовании товара.">{{ $userReview->comment??'' }}</textarea><div class="review-editor-footer"><span><span id="review-char-count">{{ mb_strlen($userReview->comment??'') }}</span>/2000</span><button type="submit" class="btn-accent"><x-icon name="arrow-right" :size="15"/>{{ $userReview?'Сохранить отзыв':'Опубликовать отзыв' }}</button></div></form>
        @else
        <div class="review-login-card"><span class="review-login-icon"><x-icon name="pen" :size="22"/></span><div><strong>Поделитесь своим мнением</strong><p>Войдите в аккаунт, чтобы поставить оценку и написать отзыв о товаре.</p></div><button type="button" class="btn-accent" onclick="akOpenAuthGate('review')">Войти и оставить отзыв</button></div>
        @endauth
    </div>
    <div class="review-list-head"><div><strong>Отзывы покупателей</strong><span>Сначала новые</span></div>@if($reviews->isNotEmpty())<span class="badge">{{ $reviews->count() }} показано</span>@endif</div>
    <div class="reviews-list">
        @forelse($reviews as $review)
            <article class="review-item">
                <div class="review-avatar">
                    @if($review->user->avatar_url)<img src="{{ $review->user->avatar_url }}" style="width:100%;height:100%;object-fit:cover" alt="">@else{{ Str::of($review->user->name)->substr(0, 2)->upper() }}@endif
                </div>
                <div class="review-item-body"><div class="review-meta"><strong>{{ $review->user->name }}</strong><span class="verified-buyer"><x-icon name="check" :size="10"/>Покупатель</span><time>{{ $review->created_at->format('d.m.Y') }}</time></div><div class="review-stars"><x-star-rating :rating="$review->rating" :size="13" :gap="2"/><b>{{ $review->rating }}.0</b></div>@if($review->comment)<p>{{ $review->comment }}</p>@else<div class="review-no-copy">Оценка без комментария</div>@endif
                    @if($review->replies->isNotEmpty())<div class="review-replies">@foreach($review->replies as $reply)<div class="review-reply"><div class="reply-avatar">@if($reply->user->avatar_url)<img src="{{ $reply->user->avatar_url }}" alt="">@else{{ Str::of($reply->user->name)->substr(0,2)->upper() }}@endif</div><div style="flex:1"><div style="display:flex;gap:7px;align-items:center"><strong>{{ $reply->user->name }}</strong>@if($reply->user->isAdmin())<span class="admin-reply-badge">AK KAGYZ</span>@endif<span style="font-size:10px;color:var(--text-faint)">{{ $reply->created_at->diffForHumans() }}</span></div><div style="font-size:13px;color:var(--text-muted);margin-top:4px;line-height:1.55">{{ $reply->message }}</div></div>@auth @if(auth()->user()->isAdmin()||auth()->id()===$reply->user_id)<form method="POST" action="{{ route('reviews.replies.destroy',$reply) }}">@csrf @method('DELETE')<button class="reply-delete" title="Удалить">×</button></form>@endif @endauth</div>@endforeach</div>@endif
                    @auth<div class="reply-wrap"><button type="button" class="reply-toggle" onclick="this.nextElementSibling.classList.toggle('show')">Ответить</button><form class="reply-form" method="POST" action="{{ route('reviews.replies.store',$review) }}">@csrf<textarea name="message" maxlength="1500" required placeholder="Напишите ответ..."></textarea><button class="btn-accent" style="height:34px;font-size:11px">Опубликовать</button></form></div>@endauth
                </div>
            </article>
        @empty
            <div class="reviews-empty"><span><x-icon name="star-outline" :size="25"/></span><strong>У этого товара пока нет отзывов</strong><p>Станьте первым покупателем, который поделится впечатлениями.</p></div>
        @endforelse
    </div>
</section>
<script>document.querySelector('.review-editor>textarea')?.addEventListener('input',function(){const counter=document.getElementById('review-char-count');if(counter)counter.textContent=this.value.length})</script>

@if($related->isNotEmpty())
<div class="wrap" style="padding:24px 24px 56px;">
    <div style="font-size:22px;font-weight:700;margin-bottom:20px;">Похожие товары</div>
    <div class="related-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
        @foreach($related as $item)
            <x-product-card :product="$item" />
        @endforeach
    </div>
</div>
@endif
<style>.product-gallery-main{position:relative;aspect-ratio:1;background:var(--surface);border:1px solid var(--border);border-radius:22px;display:flex;align-items:center;justify-content:center;color:var(--text-faint);overflow:hidden;cursor:zoom-in}.product-gallery-main>img{width:100%;height:100%;object-fit:contain}.gallery-zoom-lens{position:absolute;top:0;left:0;border-radius:9px;border:1.5px solid var(--accent);background:color-mix(in oklch,var(--accent) 14%,transparent);box-shadow:0 0 0 2000px rgba(10,12,18,.14);pointer-events:none;opacity:0;visibility:hidden;transition:opacity .15s ease;z-index:4}.gallery-zoom-lens.active{opacity:1;visibility:visible}.gallery-zoom-pane{position:absolute;top:0;left:calc(100% + 24px);width:100%;aspect-ratio:1;border-radius:22px;border:1px solid var(--border);background-color:var(--surface);background-repeat:no-repeat;box-shadow:0 30px 70px rgba(10,12,18,.28);opacity:0;visibility:hidden;pointer-events:none;transition:opacity .18s ease;z-index:45}.gallery-zoom-pane.active{opacity:1;visibility:visible}.gallery-nav{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:12px;border:1px solid var(--border);background:color-mix(in oklch,var(--surface) 84%,transparent);backdrop-filter:blur(10px);color:var(--text);display:grid;place-items:center;z-index:3}.gallery-prev{left:14px}.gallery-next{right:14px}.gallery-count{position:absolute;right:14px;bottom:14px;padding:5px 9px;border-radius:8px;background:rgba(10,12,18,.65);color:white;font-size:10px;backdrop-filter:blur(7px)}.gallery-thumb{width:68px;height:68px;border-radius:11px;border:1px solid var(--border);background:var(--surface);padding:3px;overflow:hidden;transition:.18s}.gallery-thumb img{width:100%;height:100%;object-fit:contain}.gallery-thumb.active{border-color:var(--accent);box-shadow:0 0 0 2px var(--accent-soft)}.product-facts{display:grid;grid-template-columns:repeat(3,1fr);gap:1px;margin-top:20px;border:1px solid var(--border);border-radius:13px;overflow:hidden;background:var(--border)}.product-facts div{background:var(--surface);padding:11px 13px}.product-facts span{display:block;font-size:9px;color:var(--text-faint);text-transform:uppercase;letter-spacing:.06em}.product-facts strong{display:block;font-size:12px;font-weight:600;margin-top:3px}.review-replies{margin-top:14px;padding-left:15px;border-left:2px solid var(--accent-soft);display:grid;gap:10px}.review-reply{display:flex;gap:9px;padding:10px 12px;background:var(--bg-elevated);border-radius:10px}.reply-avatar{width:28px;height:28px;border-radius:8px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center;font-size:9px;font-weight:700;overflow:hidden;flex-shrink:0}.reply-avatar img{width:100%;height:100%;object-fit:cover}.review-reply strong{font-size:11px;font-weight:700}.admin-reply-badge{font-size:8px;padding:2px 5px;border-radius:5px;background:var(--accent);color:white;font-weight:700}.reply-delete{border:0;background:none;color:var(--text-faint);font-size:17px;padding:0}.reply-wrap{margin-top:10px}.reply-toggle{border:0;background:none;color:var(--accent);font-size:11px;font-weight:600;padding:0}.reply-form{display:none;align-items:flex-end;gap:8px;margin-top:9px}.reply-form.show{display:flex}.reply-form textarea{flex:1;min-height:52px;resize:vertical;border:1px solid var(--border);border-radius:9px;background:var(--bg);color:var(--text);padding:9px;font:inherit;font-size:12px;outline:none}.reply-form textarea:focus{border-color:var(--accent)}@media(max-width:850px){.product-detail-grid{grid-template-columns:1fr!important;gap:26px!important}.gallery-zoom-pane,.gallery-zoom-lens{display:none!important}}@media(max-width:950px){.related-grid{grid-template-columns:repeat(2,1fr)!important}}@media(max-width:520px){.related-grid{grid-template-columns:1fr!important}.product-detail-grid{padding-left:14px!important;padding-right:14px!important}.product-facts{grid-template-columns:repeat(2,1fr)}.reply-form{flex-direction:column;align-items:stretch}}</style>
@if($product->images->isNotEmpty())<script>(function(){
    const images=@json($product->images->pluck('url')->values());
    let current=0;
    const main=document.getElementById('gallery-main-img'),thumbs=document.querySelectorAll('.gallery-thumb'),index=document.getElementById('gallery-index');
    const area=document.getElementById('product-main-image'),lens=document.getElementById('gallery-zoom-lens'),pane=document.getElementById('gallery-zoom-pane');
    const ZOOM=2.4;

    function paneImage(){ if(pane) pane.style.backgroundImage="url('"+images[current]+"')"; }

    window.akGalleryGo=function(i){
        current=(i+images.length)%images.length;
        main.src=images[current];
        if(index)index.textContent=current+1;
        thumbs.forEach((t,n)=>t.classList.toggle('active',n===current));
        paneImage();
    };
    window.akGalleryMove=function(step){window.akGalleryGo(current+step)};

    function canZoom(){
        return !!(area && lens && pane) && window.matchMedia('(hover:hover) and (pointer:fine) and (min-width:851px)').matches;
    }

    area?.addEventListener('mouseenter',()=>{
        if(!canZoom()) return;
        paneImage();
        lens.classList.add('active');
        pane.classList.add('active');
    });

    area?.addEventListener('mousemove',e=>{
        if(!canZoom()) return;
        const r=area.getBoundingClientRect();

        // The <img> uses object-fit:contain, so it may be letterboxed inside
        // the square box. Map the cursor onto the actual rendered picture
        // rect (not the box) so the lens and the zoomed pane agree on the
        // same point of the image.
        const naturalW=main.naturalWidth||r.width, naturalH=main.naturalHeight||r.height;
        const contentScale=Math.min(r.width/naturalW, r.height/naturalH);
        const renderedW=naturalW*contentScale, renderedH=naturalH*contentScale;
        const offsetX=(r.width-renderedW)/2, offsetY=(r.height-renderedH)/2;

        // Zoom just enough to always fill the pane, even if the picture is
        // heavily letterboxed.
        const effectiveZoom=Math.max(ZOOM, r.width/renderedW, r.height/renderedH);

        const lensW=renderedW/effectiveZoom, lensH=renderedH/effectiveZoom;
        let ix=e.clientX-r.left-offsetX-lensW/2;
        let iy=e.clientY-r.top-offsetY-lensH/2;
        ix=Math.max(0,Math.min(ix,renderedW-lensW));
        iy=Math.max(0,Math.min(iy,renderedH-lensH));

        lens.style.width=lensW+'px';
        lens.style.height=lensH+'px';
        lens.style.transform='translate('+(offsetX+ix)+'px,'+(offsetY+iy)+'px)';

        const fx=(renderedW-lensW)>0 ? ix/(renderedW-lensW) : 0.5;
        const fy=(renderedH-lensH)>0 ? iy/(renderedH-lensH) : 0.5;
        const scaledW=renderedW*effectiveZoom, scaledH=renderedH*effectiveZoom;
        pane.style.backgroundSize=scaledW+'px '+scaledH+'px';
        pane.style.backgroundPosition=(-fx*(scaledW-r.width))+'px '+(-fy*(scaledH-r.height))+'px';
    });

    area?.addEventListener('mouseleave',()=>{
        lens?.classList.remove('active');
        pane?.classList.remove('active');
    });
})();</script>@endif
