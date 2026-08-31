@props(['variant' => 'main', 'cartCount' => 0, 'wishlistCount' => 0])
@php
    $homeRoute = $variant === 'marketplace' ? 'marketplace.home' : 'home';
    $catalogRoute = $variant === 'marketplace' ? 'marketplace.catalog' : 'catalog';
    $catalogActive = request()->routeIs($catalogRoute)
        || request()->routeIs($variant === 'marketplace' ? 'marketplace.products.show' : 'products.show');
    $profileActive = request()->routeIs('profile.*') || request()->routeIs('login') || request()->routeIs('register');
@endphp
<nav class="mobile-nav" aria-label="Мобильное меню">
    <a href="{{ route($homeRoute) }}" class="mobile-nav-item {{ request()->routeIs($homeRoute) ? 'active' : '' }}">
        <x-icon name="home" :size="21" />
        <span>Главная</span>
    </a>
    <a href="{{ route($catalogRoute) }}" class="mobile-nav-item {{ $catalogActive ? 'active' : '' }}">
        <x-icon name="grid" :size="21" />
        <span>Каталог</span>
    </a>
    <a href="{{ route('wishlist.index') }}" class="mobile-nav-item {{ request()->routeIs('wishlist.*') ? 'active' : '' }}">
        <span class="mobile-nav-icon-wrap">
            <x-icon name="favorite" :size="21" />
            @if($wishlistCount > 0)<span class="mobile-nav-badge">{{ $wishlistCount > 99 ? '99+' : $wishlistCount }}</span>@endif
        </span>
        <span>Избранное</span>
    </a>
    <a href="{{ route('cart.index') }}" class="mobile-nav-item {{ request()->routeIs('cart.*') ? 'active' : '' }}">
        <span class="mobile-nav-icon-wrap">
            <x-icon name="cart" :size="21" />
            @if($cartCount > 0)<span class="mobile-nav-badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>@endif
        </span>
        <span>Корзина</span>
    </a>
    <a href="{{ auth()->check() ? route('profile.edit') : route('login') }}" class="mobile-nav-item {{ $profileActive ? 'active' : '' }}">
        <x-icon name="user" :size="21" />
        <span>{{ auth()->check() ? 'Профиль' : 'Войти' }}</span>
    </a>
</nav>

<style>
    .mobile-nav{display:none}
    @media(max-width:760px){
        .mobile-nav{position:fixed;left:0;right:0;bottom:0;z-index:70;display:grid;grid-template-columns:repeat(5,1fr);background:color-mix(in oklch,var(--bg-elevated) 96%,transparent);border-top:1px solid var(--border);padding:6px 2px calc(6px + env(safe-area-inset-bottom));box-shadow:0 -10px 26px rgba(15,30,65,.09);backdrop-filter:blur(14px)}
        body{padding-bottom:calc(60px + env(safe-area-inset-bottom))}
        .wishlist-toast{bottom:calc(68px + env(safe-area-inset-bottom))}
    }
    .mobile-nav-item{display:flex;flex-direction:column;align-items:center;gap:3px;padding:6px 2px;border-radius:12px;color:var(--text-faint);font-size:9.5px;font-weight:800;line-height:1;transition:color .15s ease}
    .mobile-nav-item.active{color:var(--accent)}
    .mobile-nav-icon-wrap{position:relative;display:flex}
    .mobile-nav-badge{position:absolute;top:-5px;right:-9px;min-width:15px;height:15px;padding:0 3px;border-radius:8px;background:var(--danger);color:#fff;font-size:9px;font-weight:800;display:flex;align-items:center;justify-content:center;line-height:1}
</style>
