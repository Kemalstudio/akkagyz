@forelse($items as $item)
    @php($image = $item->product->images->first())
    @php($itemUrl = route($item->product->seller_id ? 'marketplace.products.show' : 'products.show', $item->product->slug))
    <div class="cart-drawer-item">
        <a class="cart-drawer-image" href="{{ $itemUrl }}" @if($image) style="background-image:url('{{ $image->url }}')" @endif>@unless($image)<x-icon name="image" :size="22"/>@endunless</a>
        <div class="cart-drawer-item-body">
            <a class="cart-drawer-name" href="{{ $itemUrl }}">{{ $item->product->name }}</a>
            <div class="cart-drawer-qty">{{ number_format($item->product->price,0,'',' ') }} TMT / шт.</div>
            <div class="cart-drawer-item-bottom">
                <form method="POST" action="{{ route('cart.add', $item->product) }}" class="cart-ajax-form cart-drawer-stepper" data-product-id="{{ $item->product->id }}">
                    @csrf
                    <button type="submit" data-cart-action="decrement" aria-label="Уменьшить количество"><x-icon name="minus" :size="12"/></button>
                    <span>{{ $item->quantity }}</span>
                    <button type="submit" data-cart-action="increment" aria-label="Увеличить количество" {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}><x-icon name="plus" :size="12"/></button>
                </form>
                <form method="POST" action="{{ route('cart.destroy', $item) }}" class="cart-ajax-form" data-product-id="{{ $item->product->id }}">
                    @csrf @method('DELETE')
                    <button type="submit" data-cart-action="remove" class="cart-drawer-remove" aria-label="Удалить товар"><x-icon name="trash" :size="13"/></button>
                </form>
            </div>
        </div>
        <div class="cart-drawer-price">{{ number_format($item->quantity * $item->product->price,0,'',' ') }} TMT</div>
    </div>
@empty
    <div class="cart-drawer-empty"><div style="width:56px;height:56px;margin:0 auto 14px;border-radius:18px;background:var(--bg);display:grid;place-items:center"><x-icon name="cart" :size="25"/></div><strong style="display:block;color:var(--text);margin-bottom:5px">Корзина пуста</strong><span style="font-size:12px">Добавьте товары из каталога</span></div>
@endforelse
