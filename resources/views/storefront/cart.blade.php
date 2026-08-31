@php
    $currency = \App\Models\BusinessSetting::current()->currency ?: 'TMT';
    $units = $items->sum('quantity');
    $hasUnavailable = $items->contains(fn ($item) => ! $item->product?->isPurchasable());
@endphp
<x-layout title="Корзина">
    <style>
        .cart-page{max-width:1240px;padding:34px 24px 72px}.cart-head{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:24px}.cart-title{margin:0;font-size:32px;line-height:1.1;letter-spacing:-.04em}.cart-subtitle{margin-top:7px;color:var(--text-faint);font-size:13px}.cart-layout{display:grid;grid-template-columns:minmax(0,1fr) 370px;gap:26px;align-items:start}.cart-list{display:grid;gap:12px}.cart-item{display:grid;grid-template-columns:116px minmax(0,1fr) auto;gap:18px;align-items:center;padding:18px;border:1px solid var(--border);border-radius:20px;background:var(--surface);transition:border-color .18s,box-shadow .18s}.cart-item:hover{border-color:var(--border-strong);box-shadow:0 14px 34px rgba(14,30,60,.07)}.cart-photo{width:116px;height:116px;border-radius:16px;border:1px solid var(--border);background:var(--bg-elevated);display:grid;place-items:center;overflow:hidden;color:var(--text-faint)}.cart-photo img{width:100%;height:100%;object-fit:contain}.cart-category{font-size:10px;color:var(--text-faint);font-weight:800;text-transform:uppercase;letter-spacing:.08em}.cart-name{display:block;margin-top:5px;color:var(--text);font-size:15px;line-height:1.4;font-weight:800}.cart-seller,.cart-stock{display:flex;align-items:center;gap:5px;margin-top:7px;color:var(--text-faint);font-size:11px}.cart-stock{color:var(--success);font-weight:700}.cart-stock.low{color:var(--warning)}.cart-actions{display:flex;align-items:center;gap:14px;margin-top:15px}.cart-quantity{height:42px;display:grid;grid-template-columns:40px 42px 40px;align-items:center;border:1px solid var(--border);border-radius:12px;overflow:hidden;background:var(--bg)}.cart-quantity button{height:100%;border:0;background:transparent;color:var(--text);display:grid;place-items:center;cursor:pointer}.cart-quantity button:hover:not(:disabled){background:var(--accent-soft);color:var(--accent)}.cart-quantity button:disabled{opacity:.35;cursor:not-allowed}.cart-quantity strong{text-align:center;font-size:14px}.cart-remove{border:0;background:none;color:var(--text-faint);font-size:11px;font-weight:700;display:flex;align-items:center;gap:5px;cursor:pointer}.cart-remove:hover{color:var(--danger)}.cart-price{text-align:right;min-width:120px}.cart-price strong{display:block;font-size:20px;letter-spacing:-.025em}.cart-price span{display:block;margin-top:5px;font-size:11px;color:var(--text-faint)}.cart-price s{display:block;margin-top:3px;font-size:11px;color:var(--text-faint)}.cart-summary{position:sticky;top:24px;padding:24px;border:1px solid var(--border);border-radius:22px;background:var(--surface);box-shadow:0 18px 45px rgba(14,30,60,.08)}.cart-summary h2{margin:0 0 14px;font-size:19px}.cart-line{display:flex;justify-content:space-between;gap:18px;padding:9px 0;color:var(--text-muted);font-size:13px}.cart-line strong{color:var(--text)}.cart-total{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;margin-top:10px;padding-top:17px;border-top:1px solid var(--border)}.cart-total span{font-size:14px;font-weight:800}.cart-total strong{font-size:25px;letter-spacing:-.04em}.cart-checkout{width:100%;height:54px;margin-top:20px;border-radius:14px}.cart-benefits{display:grid;gap:10px;margin-top:18px;padding-top:17px;border-top:1px solid var(--border)}.cart-benefit{display:flex;align-items:center;gap:9px;color:var(--text-faint);font-size:11px;line-height:1.4}.cart-benefit svg{color:var(--success);flex:0 0 auto}.cart-empty{max-width:600px;margin:30px auto 0;padding:54px 30px;text-align:center;border:1px dashed var(--border-strong);border-radius:24px;background:linear-gradient(145deg,var(--surface),var(--accent-soft))}.cart-empty-icon{width:78px;height:78px;margin:0 auto 18px;border-radius:24px;background:var(--surface);color:var(--accent);display:grid;place-items:center;box-shadow:0 14px 32px rgba(32,85,190,.13)}.cart-empty h2{font-size:24px;margin:0}.cart-empty p{color:var(--text-faint);font-size:13px;line-height:1.6;margin:8px 0 22px}.cart-warning{display:flex;gap:10px;align-items:flex-start;margin-bottom:16px;padding:13px 15px;border-radius:13px;background:var(--danger-soft);color:var(--danger);font-size:12px;line-height:1.5}.cart-toolbar{display:flex;align-items:center;gap:12px}.cart-clear{border:0;background:none;color:var(--text-faint);font-size:12px;font-weight:700;cursor:pointer}.cart-clear:hover{color:var(--danger)}
        @media(max-width:920px){.cart-layout{grid-template-columns:1fr}.cart-summary{position:static}.cart-head{align-items:flex-start}.cart-toolbar{flex-direction:column;align-items:flex-end}}
        @media(max-width:620px){.cart-page{padding:24px 14px 52px}.cart-title{font-size:27px}.cart-item{grid-template-columns:86px minmax(0,1fr);gap:13px;padding:14px}.cart-photo{width:86px;height:96px}.cart-price{grid-column:1/-1;display:flex;align-items:center;justify-content:space-between;text-align:left;min-width:0;padding-top:12px;border-top:1px solid var(--border)}.cart-price span{margin:0}.cart-actions{gap:8px;justify-content:space-between}.cart-summary{padding:20px}.cart-head{display:block}.cart-toolbar{margin-top:14px;align-items:flex-start}}
    </style>

    <div class="wrap cart-page">
        <div class="cart-head">
            <div>
                <h1 class="cart-title">Корзина</h1>
                <div class="cart-subtitle">{{ $units }} шт. в {{ $items->count() }} позициях</div>
            </div>
            @if($items->isNotEmpty())
                <div class="cart-toolbar">
                    <a href="{{ route('catalog') }}" style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:800;"><x-icon name="chevron-left" :size="14" /> Продолжить покупки</a>
                    <form method="POST" action="{{ route('cart.clear') }}" onsubmit="return confirm('Очистить всю корзину?')">
                        @csrf @method('DELETE')
                        <button class="cart-clear" type="submit">Очистить корзину</button>
                    </form>
                </div>
            @endif
        </div>

        @if($items->isEmpty())
            <div class="cart-empty">
                <div class="cart-empty-icon"><x-icon name="cart" :size="34" /></div>
                <h2>Корзина пока пуста</h2>
                <p>Добавьте нужные товары из каталога — они появятся здесь, и вы сможете оформить заказ за пару минут.</p>
                <a href="{{ route('catalog') }}" class="btn-accent" style="display:inline-flex;height:48px;border-radius:13px;">Перейти в каталог <x-icon name="arrow-right" :size="16" /></a>
            </div>
        @else
            <div class="cart-layout">
                <div class="cart-list">
                    @foreach($items as $item)
                        @php
                            $product = $item->product;
                            $itemUrl = route($product->seller_id ? 'marketplace.products.show' : 'products.show', $product->slug);
                            $image = $product->images->first();
                            $lineTotal = $product->price * $item->quantity;
                            $oldLineTotal = ($product->compare_price ?? $product->price) * $item->quantity;
                        @endphp
                        <article class="cart-item">
                            <a class="cart-photo" href="{{ $itemUrl }}" aria-label="{{ $product->name }}">
                                @if($image)<img src="{{ $image->url }}" alt="{{ $product->name }}" loading="lazy">@else<x-icon name="image" :size="30" />@endif
                            </a>
                            <div>
                                <div class="cart-category">{{ $product->category?->name ?: 'Товар' }}</div>
                                <a class="cart-name" href="{{ $itemUrl }}">{{ $product->name }}</a>
                                @if($product->seller)<div class="cart-seller"><x-icon name="store" :size="12" />{{ $product->seller->store_name ?? $product->seller->name }}</div>@endif
                                <div class="cart-stock {{ $product->stock <= 5 ? 'low' : '' }}">
                                    <span style="width:6px;height:6px;border-radius:50%;background:currentColor"></span>
                                    {{ $product->stock <= 5 ? 'Осталось '.$product->stock.' шт.' : 'В наличии' }}
                                </div>
                                <div class="cart-actions">
                                    <form method="POST" action="{{ route('cart.update', $item) }}" class="cart-quantity" aria-label="Количество товара">
                                        @csrf @method('PATCH')
                                        <button type="submit" name="quantity" value="{{ max(0, $item->quantity - 1) }}" aria-label="Уменьшить количество"><x-icon name="minus" :size="14" /></button>
                                        <strong aria-live="polite">{{ $item->quantity }}</strong>
                                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" aria-label="Увеличить количество" {{ $item->quantity >= $product->stock ? 'disabled' : '' }}><x-icon name="plus" :size="14" /></button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="cart-remove"><x-icon name="trash" :size="13" /> Удалить</button>
                                    </form>
                                </div>
                            </div>
                            <div class="cart-price">
                                <strong>{{ number_format($lineTotal, 0, '', ' ') }} {{ $currency }}</strong>
                                <span>{{ number_format($product->price, 0, '', ' ') }} {{ $currency }} / шт.</span>
                                @if($oldLineTotal > $lineTotal)<s>{{ number_format($oldLineTotal, 0, '', ' ') }} {{ $currency }}</s>@endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="cart-summary" aria-label="Итоги корзины">
                    <h2>Ваш заказ</h2>
                    @if($hasUnavailable)
                        <div class="cart-warning"><x-icon name="x" :size="17" />В корзине есть недоступный товар. Удалите его перед оформлением.</div>
                    @endif
                    <div class="cart-line"><span>Товары ({{ $units }} шт.)</span><strong>{{ number_format($subtotal + $discount, 0, '', ' ') }} {{ $currency }}</strong></div>
                    @if($discount > 0)<div class="cart-line"><span>Скидка на товары</span><strong style="color:var(--success)">&minus;{{ number_format($discount, 0, '', ' ') }} {{ $currency }}</strong></div>@endif
                    <div class="cart-line"><span>Доставка</span><strong style="color:var(--success)">Бесплатно</strong></div>
                    <div class="cart-total"><span>Итого</span><strong>{{ number_format($total, 0, '', ' ') }} {{ $currency }}</strong></div>
                    @if($hasUnavailable)
                        <button class="btn-accent cart-checkout" disabled style="opacity:.55;cursor:not-allowed">Оформление недоступно</button>
                    @else
                        <a href="{{ route('checkout.index') }}" class="btn-accent cart-checkout">Перейти к оформлению <x-icon name="arrow-right" :size="16" /></a>
                    @endif
                    <div class="cart-benefits">
                        <div class="cart-benefit"><x-icon name="shield" :size="15" />Защищённое оформление и проверка заказа</div>
                        <div class="cart-benefit"><x-icon name="truck" :size="15" />Доставка по Туркменистану</div>
                        <div class="cart-benefit"><x-icon name="cash" :size="15" />Оплата наличными при получении</div>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</x-layout>
