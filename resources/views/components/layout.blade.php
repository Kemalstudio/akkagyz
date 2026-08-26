<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' — '.$businessSettings->site_name : $businessSettings->site_name.' — Маркетплейс' }}</title>
    @include('partials.theme-init')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap">
    <style>
        @include('partials.design-system')
        .site-header{position:sticky;top:0;z-index:50;background:color-mix(in oklch,var(--bg-elevated) 92%,transparent);border-bottom:1px solid var(--border);backdrop-filter:blur(18px);box-shadow:0 8px 30px rgba(0,0,0,.05)}
        .header-service{border-bottom:1px solid color-mix(in oklch,var(--border) 65%,transparent)}.header-service-inner{height:32px;display:flex;align-items:center;justify-content:space-between;gap:20px;font-size:11px;color:var(--text-faint)}.header-service-links{display:flex;align-items:center;gap:20px}.header-service a{color:var(--text-faint);transition:color .15s}.header-service a:hover{color:var(--text)}
        .header-main{min-height:74px;padding:12px 24px;display:flex;align-items:center;gap:12px}.header-logo{display:flex;align-items:center;flex-shrink:0;padding-right:5px}.header-search{flex:1;max-width:600px;position:relative}.header-search .input{height:44px;border-radius:12px;background:var(--bg);border-color:transparent;padding-left:44px;padding-right:76px}.header-search .input:focus{background:var(--surface);border-color:var(--accent)}.search-submit{position:absolute;right:5px;top:5px;height:34px;padding:0 13px;border:0;border-radius:9px;background:var(--accent);color:white;font-size:11px;font-weight:700}
        .header-stores-btn{display:flex;align-items:center;gap:7px;padding:0 14px;height:38px;border-radius:10px;background:transparent;color:var(--text-muted);font-weight:600;font-size:13px;flex-shrink:0;white-space:nowrap;border:1px solid var(--border);transition:border-color .15s ease,background .15s ease;}
        .header-stores-btn:hover{background:var(--surface);border-color:var(--border-strong);color:var(--text);}
        .search-suggest-label{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--text-faint);font-weight:800;padding:10px 14px 6px;}
        .search-suggest-cat{display:block;padding:8px 14px;font-size:13.5px;font-weight:700;color:var(--text);}
        .search-suggest-cat:hover{background:var(--surface-hover);color:var(--accent);}
        .search-suggest-item{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:9px 14px;font-size:13.5px;font-weight:600;color:var(--text);}
        .search-suggest-item:hover{background:var(--surface-hover);}
        .search-suggest-item .ss-price{color:var(--accent);font-weight:800;flex-shrink:0;}
        .search-suggest-all{display:block;padding:11px 14px;font-size:13px;font-weight:800;color:var(--accent);border-top:1px solid var(--border);text-align:center;}
        .search-suggest-empty{padding:16px 14px;font-size:13px;color:var(--text-faint);text-align:center;}
        .header-actions{display:flex;align-items:center;gap:3px;margin-left:auto;flex-shrink:0}.header-actions .icon-btn{width:40px;height:40px}.header-divider{width:1px;height:24px;background:var(--border);margin:0 7px}.account-btn{display:flex;align-items:center;gap:9px;padding:4px 10px 4px 4px;border-radius:12px;background:transparent;border:1px solid transparent;color:var(--text);transition:.15s}.account-btn:hover{background:var(--surface);border-color:var(--border)}.account-avatar{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--accent),#7c6cf2);color:white;display:grid;place-items:center;font-size:11px;font-weight:700}.mobile-only{display:none}
        @media(max-width:1080px){.header-main{flex-wrap:wrap}.header-search{order:5;max-width:none;flex-basis:100%}.site-header{position:relative}.header-service-links a:nth-child(2){display:none}}
        @media(max-width:720px){.header-service{display:none}.header-main{min-height:64px;padding:10px 14px;gap:8px}.header-logo img{max-width:122px!important;height:30px!important}.header-stores-btn{display:none}.header-actions .theme-toggle,.header-actions>a[title="Сравнение"]{display:none}.header-divider{display:none}.account-name{display:none}.account-btn{padding:3px}.header-search .input{height:42px;font-size:13px}.catalog-trigger-btn{padding:0 12px!important;font-size:12px!important}.header-actions .icon-btn{width:37px;height:37px}.desktop-auth-register{display:none}.mobile-only{display:flex}}
        .header-actions .icon-btn{border-radius:12px;transition:.18s;border:1px solid transparent}.header-actions .icon-btn svg,.header-actions .theme-toggle svg{stroke-width:1.8}.header-actions .icon-btn:hover{color:var(--accent);background:var(--accent-soft);border-color:color-mix(in oklch,var(--accent) 20%,transparent);transform:translateY(-1px);box-shadow:0 7px 16px rgba(20,42,100,.09)}.header-actions .icon-btn[title="Избранное"]{color:#e44876}.header-actions .icon-btn[title="Сравнение"]{color:#3867d6}
        .contact-wrap{position:relative}.contact-trigger{height:25px;padding:0 9px;display:flex;align-items:center;gap:6px;border:1px solid color-mix(in oklch,var(--accent) 28%,var(--border));border-radius:8px;background:var(--accent-soft);color:var(--accent);font:inherit;font-weight:700;cursor:pointer;transition:.18s}.contact-trigger:hover{background:color-mix(in oklch,var(--accent) 15%,transparent);transform:translateY(-1px)}.contact-trigger .contact-chevron{transition:transform .2s}.contact-wrap:has(.show) .contact-chevron{transform:rotate(180deg)}.contact-menu{position:absolute;right:0;top:31px;width:310px;padding:10px;background:var(--surface);border:1px solid var(--border);border-radius:15px;box-shadow:0 22px 55px rgba(8,15,35,.2);z-index:100}.contact-head{padding:10px 10px 12px;border-bottom:1px solid var(--border)}.contact-title{font-size:14px;font-weight:700;color:var(--text)}.contact-copy{font-size:11px;color:var(--text-faint);margin-top:3px;line-height:1.4}.contact-list{display:grid;gap:4px;padding-top:7px}.contact-row{display:flex!important;align-items:center;gap:10px;padding:10px;border-radius:10px;color:var(--text)!important}.contact-row:hover{background:var(--surface-hover)}.contact-icon{width:32px;height:32px;display:grid;place-items:center;flex:0 0 auto;border-radius:9px;background:var(--accent-soft);color:var(--accent)}.contact-label{display:block;font-size:10px;color:var(--text-faint);margin-bottom:2px}.contact-value{display:block;font-size:12px;font-weight:600;color:var(--text);overflow-wrap:anywhere}.contact-empty{padding:12px;font-size:12px;line-height:1.5;color:var(--text-faint)}
    </style>
</head>
<body>

<x-promo-banner />
<x-promo-popup />
<x-auth-gate />

@php($business = \App\Models\BusinessSetting::current())
<header class="site-header">
    <div class="header-service"><div class="wrap header-service-inner"><div style="display:flex;align-items:center;gap:8px"><span style="width:6px;height:6px;border-radius:50%;background:var(--success)"></span>Доставка по всему Туркменистану</div><div class="header-service-links"><a href="{{ route('marketplace.home') }}" style="display:flex;align-items:center;gap:5px;color:var(--accent);font-weight:700"><x-icon name="store" :size="12"/>Магазины</a><a href="{{ route('orders.track') }}">Отследить заказ</a><a href="{{ route('seller.become') }}">Стать продавцом</a><x-contact-menu :business="$business"/></div></div></div>
    <div class="wrap header-main">
        <a href="{{ route('home') }}" class="header-logo">
            <x-logo :height="38" />
        </a>

        <x-catalog-menu :categories="$navCategories" />

        <form action="{{ route('catalog') }}" method="GET" class="header-search" autocomplete="off">
            <div style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--text-faint);display:flex;pointer-events:none;">
                <x-icon name="search" :size="18" />
            </div>
            <input id="ak-search-input" type="text" name="q" value="{{ request('q') }}" placeholder="Искать товары, бренды, категории" class="input" style="padding-left:44px;">
            <button class="search-submit" type="submit">Найти</button>
            <div id="search-suggest" class="dropdown-panel" style="position:absolute;left:0;right:0;top:52px;background:var(--surface);border:1px solid var(--border);border-radius:14px;z-index:80;overflow:hidden;box-shadow:0 24px 48px rgba(0,0,0,.28);"></div>
        </form>

        <x-language-switcher/><div class="header-actions">
            <x-theme-toggle />
            @auth
                <x-notification-bell :notifications="$notifications" :unread-count="$unreadCount" />
            @endauth
            <a href="{{ route('compare.index') }}" class="icon-btn" title="Сравнение">
                <x-icon name="compare" :size="19" />
                @auth
                    @if(($compareCount ?? 0) > 0)<span class="count">{{ $compareCount }}</span>@endif
                @endauth
            </a>
            <a href="{{ route('wishlist.index') }}" class="icon-btn" title="Избранное">
                <x-icon name="favorite" :size="19" />
                @auth
                    <span class="count" data-wishlist-count @if(($wishlistCount ?? 0) < 1) hidden @endif>{{ $wishlistCount ?? 0 }}</span>
                @endauth
            </a>
            <a href="{{ route('cart.index') }}" class="icon-btn" title="Корзина">
                <x-icon name="cart" :size="19" />
                @auth
                    <span class="count" data-cart-count @if(($cartCount ?? 0) < 1) hidden @endif>{{ $cartCount ?? 0 }}</span>
                @endauth
            </a>
            <div class="header-divider"></div>

            @auth
                <div style="position:relative;">
                    <button onclick="akToggleMenu(event,'acct-menu')" class="account-btn">
                        <div class="account-avatar" style="overflow:hidden">@if(auth()->user()->avatar_url)<img src="{{ auth()->user()->avatar_url }}" style="width:100%;height:100%;object-fit:cover" alt="">@else{{ Str::of(auth()->user()->name)->substr(0,2)->upper() }}@endif</div>
                        <span class="account-name" style="font-size:12px;font-weight:600;">{{ Str::limit(auth()->user()->name, 16) }}</span><x-icon class="account-name" name="chevron-down" :size="12" />
                    </button>
                    <div id="acct-menu" class="dropdown-panel" style="position:absolute;right:0;top:52px;background:var(--surface);border:1px solid var(--border);border-radius:12px;min-width:200px;padding:8px;z-index:40;box-shadow:0 20px 40px rgba(0,0,0,.22);">
                        @if(auth()->user()->isSeller())
                            <a href="{{ route('seller.dashboard') }}" style="display:block;padding:10px 12px;border-radius:8px;color:var(--text);font-size:13px;font-weight:700;">Кабинет продавца</a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" style="display:block;padding:10px 12px;border-radius:8px;color:var(--text);font-size:13px;font-weight:700;">Админ-панель</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" style="display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:8px;color:var(--text);font-size:13px;font-weight:600;"><x-icon name="user" :size="14"/>Мой профиль</a>
                        <a href="{{ route('orders.index') }}" style="display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:8px;color:var(--text);font-size:13px;font-weight:600;"><x-icon name="package" :size="14"/>Мои заказы</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%;text-align:left;padding:10px 12px;border-radius:8px;background:none;border:none;color:var(--danger);font-size:13px;font-weight:700;">Выйти</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-ghost">Войти</a>
                <a href="{{ route('register') }}" class="btn-accent desktop-auth-register">Регистрация</a>
            @endauth
        </div>
    </div>
</header>

@include('partials.search-script', ['suggestUrl' => route('search.suggest'), 'catalogUrl' => route('catalog'), 'productBaseUrl' => '/products'])

@if(session('status'))
    <div class="wrap" style="padding-top:20px;"><div class="alert alert-success">{{ session('status') }}</div></div>
@endif
@if(session('error'))
    <div class="wrap" style="padding-top:20px;"><div class="alert alert-danger">{{ session('error') }}</div></div>
@endif

<main>
    {{ $slot }}
</main>

<footer style="border-top:1px solid var(--border);background:var(--bg-elevated);margin-top:56px;">
    <div class="wrap" style="padding:48px 24px 32px;display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:32px;">
        <div>
            <x-logo :height="32" />
            <div style="font-size:13px;color:var(--text-faint);margin-top:14px;max-width:260px;line-height:1.5;">Магазин канцтоваров, упаковки и товаров для творчества от AK KAGYZ. Собственный ассортимент, быстрая доставка по Туркменистану.</div>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="font-weight:800;font-size:13px;color:var(--text-faint);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Покупателям</div>
            <a href="{{ route('catalog') }}" style="color:var(--text-muted);font-size:14px;">Каталог товаров</a>
            <a href="{{ route('cart.index') }}" style="color:var(--text-muted);font-size:14px;">Корзина</a>
            <a href="{{ route('wishlist.index') }}" style="color:var(--text-muted);font-size:14px;">Избранное</a>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="font-weight:800;font-size:13px;color:var(--text-faint);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Магазины</div>
            <a href="{{ route('marketplace.home') }}" style="color:var(--text-muted);font-size:14px;">Витрина продавцов</a>
            <a href="{{ route('seller.become') }}" style="color:var(--text-muted);font-size:14px;">Начать продавать</a>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="font-weight:800;font-size:13px;color:var(--text-faint);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Компания</div>
            <span style="color:var(--text-muted);font-size:14px;">О нас</span>
            <span style="color:var(--text-muted);font-size:14px;">Поддержка</span>
        </div>
    </div>
    <div style="border-top:1px solid var(--border);">
        <div class="wrap" style="padding:18px 24px;display:flex;justify-content:space-between;color:var(--text-faint);font-size:13px;">
            <span>&copy; {{ date('Y') }} AK KAGYZ. Все права защищены.</span>
        </div>
    </div>
</footer>

@include('partials.menu-script')
@include('partials.wishlist-script')
@include('partials.cart-drawer')
@include('partials.cart-script')
</body>
</html>
