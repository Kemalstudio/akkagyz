<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Магазины' }} — {{ $businessSettings->site_name }}</title>
    <meta name="description" content="Маркетплейс проверенных независимых магазинов Туркменистана на {{ $businessSettings->site_name }}.">
    @include('partials.theme-init')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap">
    <style>
        @include('partials.design-system')
        .mp-logo-wrap{display:flex;align-items:center;gap:10px;}
        .mp-badge{font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.04em;padding:4px 9px;border-radius:7px;background:var(--warning-soft);color:var(--warning);}
        .mp-back-link{display:flex;align-items:center;gap:5px;font-size:12.5px;font-weight:700;color:var(--text-faint);flex-shrink:0;}
        .mp-back-link:hover{color:var(--text);}
        .header-stores-btn{display:flex;align-items:center;gap:7px;padding:0 16px;height:36px;border-radius:9px;background:var(--surface-hover);color:var(--text);font-weight:800;font-size:14px;flex-shrink:0;white-space:nowrap;border:1px solid var(--border);transition:border-color .15s ease,background .15s ease;}
        .header-stores-btn:hover{background:var(--surface);border-color:var(--border-strong);color:var(--text);}
        .header-sell-link{display:flex;align-items:center;gap:6px;padding:0 14px;height:36px;border-radius:9px;color:var(--warning);font-weight:800;font-size:13px;white-space:nowrap;flex-shrink:0;background:var(--warning-soft);}
        .search-suggest-label{font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:var(--text-faint);font-weight:800;padding:10px 14px 6px;}
        .search-suggest-cat{display:block;padding:8px 14px;font-size:13.5px;font-weight:700;color:var(--text);}
        .search-suggest-cat:hover{background:var(--surface-hover);color:var(--accent);}
        .search-suggest-item{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:9px 14px;font-size:13.5px;font-weight:600;color:var(--text);}
        .search-suggest-item:hover{background:var(--surface-hover);}
        .search-suggest-item .ss-price{color:var(--accent);font-weight:800;flex-shrink:0;}
        .search-suggest-all{display:block;padding:11px 14px;font-size:13px;font-weight:800;color:var(--accent);border-top:1px solid var(--border);text-align:center;}
        .search-suggest-empty{padding:16px 14px;font-size:13px;color:var(--text-faint);text-align:center;}
        .mp-actions{display:flex;align-items:center;gap:5px;margin-left:auto;flex-shrink:0}.mp-actions>.icon-btn{border:1px solid var(--border);border-radius:12px;background:var(--surface);transition:.18s}.mp-actions>.icon-btn:hover{transform:translateY(-2px);box-shadow:0 9px 20px rgba(15,28,64,.1)}.mp-actions>.icon-btn[title="Избранное"]{color:#e44876}.mp-actions>.icon-btn[title="Сравнение"]{color:#3867d6}
        .mp-header{position:relative;z-index:30;background:var(--bg-elevated);border-bottom:1px solid var(--border)}
        .mp-header-inner{display:flex;align-items:center;gap:14px;padding:14px 24px}
        .mp-search{position:relative;flex:1;max-width:460px}
        .mp-footer-grid{display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:32px;padding:48px 24px 32px}
        .mp-footer-bottom{display:flex;justify-content:space-between;padding:18px 24px;color:var(--text-faint);font-size:13px}
        .skip-link{position:fixed;z-index:9999;top:10px;left:10px;padding:10px 14px;border-radius:9px;background:var(--accent);color:#fff;font-size:13px;font-weight:900;transform:translateY(-150%)}
        .skip-link:focus{transform:translateY(0)}
        @media(max-width:1180px){.header-stores-btn,.header-sell-link{display:none}.mp-search{max-width:none}}
        @media(max-width:780px){.mp-header-inner{flex-wrap:wrap;padding:11px 16px}.mp-search{order:5;flex:1 0 100%;max-width:none}.mp-actions{margin-left:auto}.mp-actions>div[style*="width:1px"]{display:none}.mp-footer-grid{grid-template-columns:repeat(2,1fr);padding:38px 18px 26px}.mp-footer-bottom{padding:16px 18px}}
        @media(max-width:560px){.mp-badge{display:none}.mp-actions>div[style*="position:relative"] button span{display:none}.mp-actions>div[style*="position:relative"] button{padding-right:6px}.mp-footer-grid{grid-template-columns:1fr}.mp-footer-bottom{display:block}.mp-back-link{font-size:11px}}
        @media(max-width:760px){.mp-actions>a[title="Избранное"],.mp-actions>a[title="Корзина"]{display:none}}
    </style>
</head>
<body>

<a class="skip-link" href="#main-content">Перейти к содержимому</a>

<div style="background:var(--bg-elevated);border-bottom:1px solid var(--border);">
    <div class="wrap" style="padding:6px 24px;">
        <a href="{{ route('home') }}" class="mp-back-link"><x-icon name="chevron-left" :size="13" /> Основной магазин {{ $businessSettings->site_name }}</a>
    </div>
</div>

<header class="mp-header">
    <div class="wrap mp-header-inner">
        <a href="{{ route('marketplace.home') }}" class="mp-logo-wrap" style="flex-shrink:0;">
            <x-logo :height="34" />
            <span class="mp-badge">Магазины</span>
        </a>

        <x-catalog-menu :categories="$navCategories" :route-name="'marketplace.catalog'" />

        <form action="{{ route('marketplace.catalog') }}" method="GET" class="mp-search" autocomplete="off" role="search" aria-label="Поиск по маркетплейсу">
            <div style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--text-faint);display:flex;pointer-events:none;">
                <x-icon name="search" :size="18" />
            </div>
            <input id="ak-search-input" type="text" name="q" value="{{ request('q') }}" placeholder="Искать товары продавцов" class="input" style="padding-left:44px;">
            <div id="search-suggest" class="dropdown-panel" style="position:absolute;left:0;right:0;top:52px;background:var(--surface);border:1px solid var(--border);border-radius:14px;z-index:80;overflow:hidden;box-shadow:0 24px 48px rgba(0,0,0,.28);"></div>
        </form>

        <a href="{{ route('stores.index') }}" class="header-stores-btn">
            <x-icon name="store" :size="15" /> Все магазины
        </a>

        @if(!auth()->check() || auth()->user()->isCustomer())
            <a href="{{ auth()->check() ? route('seller.become') : route('register') }}" class="header-sell-link">
                <x-icon name="store" :size="15" /> Стать продавцом
            </a>
        @endif

        <x-language-switcher/><div class="mp-actions">
            <x-theme-toggle />
            @auth
                <x-notification-bell :notifications="$notifications" :unread-count="$unreadCount" />
            @endauth
            <a href="{{ route('wishlist.index') }}" class="icon-btn" title="Избранное">
                <x-icon name="favorite" :size="19" />
                @auth
                    <span class="count" data-wishlist-count @if(($wishlistCount ?? 0) < 1) hidden @endif>{{ $wishlistCount ?? 0 }}</span>
                @endauth
            </a>
            <a href="{{ route('cart.index') }}" class="icon-btn" title="Корзина">
                <x-icon name="cart" :size="19" />
                <span class="count" data-cart-count @if(($cartCount ?? 0) < 1) hidden @endif>{{ $cartCount ?? 0 }}</span>
            </a>
            <div style="width:1px;height:26px;background:var(--border);margin:0 8px;"></div>

            @auth
                <div style="position:relative;">
                    <button onclick="akToggleMenu(event,'acct-menu')" style="display:flex;align-items:center;gap:10px;padding:6px 12px 6px 6px;border-radius:10px;background:var(--surface);border:1px solid var(--border);color:var(--text);transition:border-color .15s ease;">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;">
                            <x-icon name="user" :size="17" />
                        </div>
                        <span style="font-size:13px;font-weight:700;">{{ Str::limit(auth()->user()->name, 16) }}</span>
                    </button>
                    <div id="acct-menu" class="dropdown-panel" style="position:absolute;right:0;top:52px;background:var(--surface);border:1px solid var(--border);border-radius:12px;min-width:200px;padding:8px;z-index:40;box-shadow:0 20px 40px rgba(0,0,0,.22);">
                        @if(auth()->user()->isSeller())
                            <a href="{{ route('seller.dashboard') }}" style="display:block;padding:10px 12px;border-radius:8px;color:var(--text);font-size:13px;font-weight:700;">Кабинет продавца</a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" style="display:block;padding:10px 12px;border-radius:8px;color:var(--text);font-size:13px;font-weight:700;">Админ-панель</a>
                        @endif
                        <a href="{{ route('orders.index') }}" style="display:block;padding:10px 12px;border-radius:8px;color:var(--text);font-size:13px;font-weight:700;">Мои заказы</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%;text-align:left;padding:10px 12px;border-radius:8px;background:none;border:none;color:var(--danger);font-size:13px;font-weight:700;">Выйти</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-ghost">Войти</a>
                <a href="{{ route('register') }}" class="btn-accent">Регистрация</a>
            @endauth
        </div>
    </div>
</header>

<x-promo-banner />

@include('partials.search-script', ['suggestUrl' => route('marketplace.search.suggest'), 'catalogUrl' => route('marketplace.catalog'), 'productBaseUrl' => '/marketplace/products'])

@if(session('status'))
    <div class="wrap" style="padding-top:20px;"><div class="alert alert-success">{{ session('status') }}</div></div>
@endif
@if(session('error'))
    <div class="wrap" style="padding-top:20px;"><div class="alert alert-danger">{{ session('error') }}</div></div>
@endif

<main id="main-content">
    {{ $slot }}
</main>

<footer style="border-top:1px solid var(--border);background:var(--bg-elevated);margin-top:56px;">
    <div class="wrap mp-footer-grid">
        <div>
            <div class="mp-logo-wrap"><x-logo :height="30" /><span class="mp-badge">Магазины</span></div>
            <div style="font-size:13px;color:var(--text-faint);margin-top:14px;max-width:260px;line-height:1.5;">Витрина независимых продавцов на {{ $businessSettings->site_name }} — товары от проверенных магазинов Туркменистана.</div>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="font-weight:800;font-size:13px;color:var(--text-faint);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Покупателям</div>
            <a href="{{ route('marketplace.catalog') }}" style="color:var(--text-muted);font-size:14px;">Каталог витрины</a>
            <a href="{{ route('stores.index') }}" style="color:var(--text-muted);font-size:14px;">Все магазины</a>
            <a href="{{ route('cart.index') }}" style="color:var(--text-muted);font-size:14px;">Корзина</a>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="font-weight:800;font-size:13px;color:var(--text-faint);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">Продавцам</div>
            <a href="{{ route('seller.become') }}" style="color:var(--text-muted);font-size:14px;">Начать продавать</a>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="font-weight:800;font-size:13px;color:var(--text-faint);text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px;">{{ $businessSettings->site_name }}</div>
            <a href="{{ route('home') }}" style="color:var(--text-muted);font-size:14px;">Основной магазин</a>
        </div>
    </div>
    <div style="border-top:1px solid var(--border);">
        <div class="wrap mp-footer-bottom">
            <span>&copy; {{ date('Y') }} {{ $businessSettings->site_name }}. Все права защищены.</span>
        </div>
    </div>
</footer>

<x-mobile-nav variant="marketplace" :cart-count="$cartCount" :wishlist-count="$wishlistCount" />

@include('partials.menu-script')
@include('partials.wishlist-script')
@include('partials.cart-drawer')
@include('partials.cart-script')
</body>
</html>
