@forelse($items as $item)
    @php($image = $item->product->images->first())
    <div class="cart-drawer-item">
        <a class="cart-drawer-image" href="{{ route($item->product->seller_id ? 'marketplace.products.show' : 'products.show', $item->product->slug) }}" @if($image) style="background-image:url('{{ $image->url }}')" @endif>@unless($image)<x-icon name="image" :size="22"/>@endunless</a>
        <div><a class="cart-drawer-name" href="{{ route($item->product->seller_id ? 'marketplace.products.show' : 'products.show', $item->product->slug) }}">{{ $item->product->name }}</a><div class="cart-drawer-qty">{{ $item->quantity }} шт. × {{ number_format($item->product->price,0,'',' ') }} TMT</div></div>
        <div class="cart-drawer-price">{{ number_format($item->quantity * $item->product->price,0,'',' ') }} TMT</div>
    </div>
@empty
    <div class="cart-drawer-empty"><div style="width:56px;height:56px;margin:0 auto 14px;border-radius:18px;background:var(--bg);display:grid;place-items:center"><x-icon name="cart" :size="25"/></div><strong style="display:block;color:var(--text);margin-bottom:5px">Корзина пуста</strong><span style="font-size:12px">Добавьте товары из каталога</span></div>
@endforelse
