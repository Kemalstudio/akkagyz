<x-layout title="Корзина">
    <div class="wrap" style="padding:32px 24px 64px;">
        <div style="font-size:26px;font-weight:900;margin-bottom:24px;">Корзина <span style="color:var(--text-faint);font-weight:700;font-size:18px;">&middot; {{ $items->count() }} товара</span></div>

        @if($items->isEmpty())
            <div style="padding:60px 0;text-align:center;">
                <div style="color:var(--text-faint);font-size:15px;font-weight:600;margin-bottom:16px;">Ваша корзина пуста</div>
                <a href="{{ route('catalog') }}" class="btn-accent" style="display:inline-flex;">Перейти в каталог</a>
            </div>
        @else
        <div style="display:grid;grid-template-columns:1fr 380px;gap:28px;align-items:start;">
            <div style="display:flex;flex-direction:column;gap:16px;">
                @foreach($items as $item)
                    @php($itemUrl = route($item->product->seller_id ? 'marketplace.products.show' : 'products.show', $item->product->slug))
                    @php($itemImage = $item->product->images->first())
                    <div style="border:1px solid var(--border);background:var(--surface);border-radius:16px;padding:18px;display:flex;gap:16px;align-items:center;">
                        <a href="{{ $itemUrl }}" style="width:88px;height:88px;border-radius:12px;background:var(--bg-elevated) {{ $itemImage ? "url('{$itemImage->url}') center/contain no-repeat" : '' }};display:flex;align-items:center;justify-content:center;color:var(--text-faint);flex-shrink:0;">
                            @unless($itemImage)
                                <x-icon name="image" :size="34" />
                            @endunless
                        </a>
                        <div style="flex:1;">
                            <a href="{{ $itemUrl }}" style="font-size:15px;font-weight:700;color:var(--text);">{{ $item->product->name }}</a>
                            <div style="font-size:12px;color:var(--text-faint);margin-top:4px;font-weight:600;">{{ $item->product->seller->store_name ?? '' }}</div>
                            <form method="POST" action="{{ route('cart.destroy', $item) }}" style="margin-top:8px;">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none;border:none;padding:0;font-size:12px;font-weight:700;color:var(--danger);display:inline-flex;align-items:center;gap:5px;">
                                    <x-icon name="trash" :size="13" /> Удалить
                                </button>
                            </form>
                        </div>
                        <form method="POST" action="{{ route('cart.update', $item) }}" style="display:flex;align-items:center;border:1px solid var(--border);border-radius:10px;height:40px;flex-shrink:0;">
                            @csrf @method('PATCH')
                            <button type="submit" name="quantity" value="{{ max(0, $item->quantity - 1) }}" style="width:36px;height:100%;background:none;border:none;color:var(--text);display:flex;align-items:center;justify-content:center;"><x-icon name="minus" :size="13" /></button>
                            <span style="width:30px;text-align:center;font-weight:800;font-size:14px;">{{ $item->quantity }}</span>
                            <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" style="width:36px;height:100%;background:none;border:none;color:var(--text);display:flex;align-items:center;justify-content:center;"><x-icon name="plus" :size="13" /></button>
                        </form>
                        <div style="width:110px;text-align:right;font-size:17px;font-weight:900;flex-shrink:0;">{{ number_format($item->product->price * $item->quantity, 0, '', ' ') }} TMT</div>
                    </div>
                @endforeach

                <a href="{{ route('catalog') }}" style="font-size:14px;font-weight:800;display:flex;align-items:center;gap:6px;width:fit-content;"><x-icon name="chevron-left" :size="15" />Продолжить покупки</a>
            </div>

            <div style="position:sticky;top:24px;border-radius:16px;background:var(--surface);border:1px solid var(--border);padding:24px;">
                <div style="font-size:18px;font-weight:900;margin-bottom:16px;">Итого</div>
                <div style="display:flex;justify-content:space-between;font-size:14px;padding:9px 0;color:var(--text-muted);font-weight:600;"><span>Товары</span><span style="color:var(--text);">{{ number_format($subtotal + $discount, 0, '', ' ') }} TMT</span></div>
                @if($discount > 0)
                    <div style="display:flex;justify-content:space-between;font-size:14px;padding:9px 0;color:var(--text-muted);font-weight:600;"><span>Скидка</span><span style="color:var(--danger);">&minus;{{ number_format($discount, 0, '', ' ') }} TMT</span></div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:14px;padding:9px 0;color:var(--text-muted);font-weight:600;"><span>Доставка</span><span style="color:var(--success);">Бесплатно</span></div>
                <div style="border-top:1px solid var(--border);margin-top:8px;padding-top:16px;display:flex;justify-content:space-between;align-items:baseline;">
                    <span style="font-size:16px;font-weight:800;">К оплате</span>
                    <span style="font-size:24px;font-weight:900;">{{ number_format($total, 0, '', ' ') }} TMT</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn-accent" style="width:100%;margin-top:20px;height:52px;font-size:15px;">
                    Оформить заказ <x-icon name="arrow-right" :size="16" />
                </a>
            </div>
        </div>
        @endif
    </div>
</x-layout>
