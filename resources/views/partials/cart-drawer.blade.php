<div class="cart-drawer-backdrop" data-cart-close></div>
<aside class="cart-drawer" id="cart-drawer" aria-hidden="true">
    <div class="cart-drawer-head"><div class="cart-drawer-title"><x-icon name="cart" :size="21"/>Ваша корзина</div><button class="cart-drawer-close" type="button" data-cart-close aria-label="Закрыть"><x-icon name="x" :size="18"/></button></div>
    <div class="cart-drawer-body" data-cart-drawer-body><div class="cart-drawer-empty">Добавьте товар, чтобы увидеть корзину</div></div>
    <div class="cart-drawer-foot">
        <div class="cart-drawer-total"><span>Итого</span><strong data-cart-subtotal>0 TMT</strong></div>
        <div class="cart-drawer-actions"><a href="{{ route('cart.index') }}" class="btn-ghost">Открыть корзину</a><a href="{{ route('checkout.index') }}" class="btn-accent">Оформить <x-icon name="arrow-right" :size="15"/></a></div>
        @auth
            <button type="button" id="cart-express-btn" class="cart-drawer-express" data-express-url="{{ route('checkout.express') }}">
                <span class="cart-drawer-express-label"><x-icon name="zap" :size="15"/> Быстрый заказ</span>
                <span class="cart-drawer-express-fee">+30 TMT</span>
            </button>
        @endauth
    </div>
</aside>
