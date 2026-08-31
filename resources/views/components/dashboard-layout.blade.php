@php($isAdmin = auth()->user()->isAdmin())
<!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{ $title }} — {{ $businessSettings->site_name }}</title>@include('partials.theme-init')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap">
<style>
@include('partials.design-system')
body{min-width:320px}.admin-shell{display:flex;min-height:100vh}.admin-sidebar{position:fixed;inset:0 auto 0 0;width:264px;display:flex;flex-direction:column;background:color-mix(in oklch,var(--bg-elevated) 92%,transparent);border-right:1px solid var(--border);backdrop-filter:blur(18px);z-index:30;overflow:hidden}.admin-sidebar-head{flex:0 0 auto;padding:22px 16px 12px}.admin-nav-scroll{flex:1 1 auto;min-height:0;overflow-y:auto;overflow-x:hidden;padding:2px 16px 14px;scrollbar-width:thin;scrollbar-color:var(--border-strong) transparent}.admin-nav-scroll::-webkit-scrollbar{width:6px}.admin-nav-scroll::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:6px}.admin-nav-scroll::-webkit-scrollbar-thumb:hover{background:var(--text-faint)}.admin-nav-scroll::-webkit-scrollbar-track{background:transparent}.admin-sidebar-foot{flex:0 0 auto;padding:14px 16px 18px;border-top:1px solid var(--border);background:color-mix(in oklch,var(--bg-elevated) 92%,transparent)}.admin-main{flex:1;min-width:0;margin-left:264px}.admin-topbar{height:72px;padding:0 28px;display:flex;align-items:center;gap:18px;position:sticky;top:0;background:color-mix(in oklch,var(--bg) 88%,transparent);border-bottom:1px solid var(--border);backdrop-filter:blur(18px);z-index:20}.admin-content{padding:28px 32px 48px;max-width:1600px;margin:auto}.brand-block{display:flex;align-items:center;justify-content:space-between;padding:0 8px 20px}.panel-label{font-size:10px;color:var(--text-faint);font-weight:900;letter-spacing:.12em;text-transform:uppercase;padding:0 14px;margin:8px 0}.nav-group{display:flex;flex-direction:column;gap:4px}.nav-divider{margin:16px 14px 8px;padding-top:14px;border-top:1px solid var(--border);display:flex;align-items:center;gap:6px;color:var(--text-faint);font-size:10px;font-weight:900;letter-spacing:.1em;text-transform:uppercase}.navlink{position:relative;display:flex;align-items:center;gap:12px;padding:10.5px 14px;border-radius:11px;font-size:13px;font-weight:800;color:var(--text-muted);transition:.16s}.navlink svg{flex:0 0 auto;opacity:.85}.navlink:hover{background:var(--surface);color:var(--text)}.navlink:hover svg{opacity:1}.navlink.on{background:linear-gradient(135deg,var(--accent-soft),transparent);color:var(--accent)}.navlink.on svg{opacity:1}.navlink.on:before{content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);width:3px;height:20px;border-radius:4px;background:var(--accent);box-shadow:0 0 12px var(--accent)}.header-search{flex:1;max-width:480px;display:flex;align-items:center;gap:8px;height:42px;padding:0 8px 0 14px;border:1px solid var(--border);border-radius:11px;background:var(--surface);color:var(--text-faint);transition:border-color .15s ease,box-shadow .15s ease,background .15s ease;font-family:var(--font)}.header-search:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}button.header-search:hover,button.header-search:focus{border-color:var(--border-strong);background:var(--surface-hover);outline:none}.header-search input{flex:1;min-width:0;height:100%;border:0;outline:0;background:transparent;color:var(--text);font-size:13px}.header-search select{height:30px;padding:0 6px;border:0;border-left:1px solid var(--border);border-radius:0;background:transparent;color:var(--text-muted);font-size:11px;font-weight:700;outline:0}.card,.stat{background:linear-gradient(145deg,var(--surface),color-mix(in oklch,var(--surface) 92%,var(--bg)));border:1px solid var(--border);border-radius:18px;box-shadow:0 1px 2px rgba(0,0,0,.04)}.stat{padding:20px;transition:.2s}.stat:hover{transform:translateY(-2px);border-color:var(--border-strong);box-shadow:0 12px 30px rgba(0,0,0,.1)}.stat-icon{width:38px;height:38px;border-radius:11px;display:flex;align-items:center;justify-content:center}.metric-note{display:flex;align-items:center;gap:4px}.stat-link{display:block;color:inherit;text-decoration:none;cursor:pointer}.stat-link:hover{color:inherit}.stat-link-arrow{opacity:0;transform:translateX(-3px);transition:opacity .15s,transform .15s;color:var(--accent);flex-shrink:0}.stat-link:hover .stat-link-arrow{opacity:1;transform:translateX(0)}.page-title{font-size:26px;font-weight:900;letter-spacing:-.02em}.page-subtitle{font-size:13px;color:var(--text-faint);margin-top:3px}.admin-table-wrap{width:100%;overflow:auto}.admin-table{width:100%;border-collapse:collapse;min-width:720px}th{font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--text-faint);font-weight:900;text-align:left;padding:0 16px 12px}td{padding:14px 16px;font-size:13px;border-top:1px solid var(--border)}tbody tr{transition:background .15s}tbody tr:hover{background:color-mix(in oklch,var(--surface-hover) 42%,transparent)}.avatar{width:34px;height:34px;border-radius:10px;background:var(--accent-soft);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:900;color:var(--accent);flex-shrink:0}.toolbar{display:flex;align-items:center;gap:10px;flex-wrap:wrap}.admin-input{height:40px;padding:0 13px;border:1px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text);outline:none;font-size:13px}.admin-input:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}.kicker{font-size:10px;color:var(--text-faint);font-weight:900;text-transform:uppercase;letter-spacing:.08em}.mobile-menu{display:none}.sidebar-scrim{display:none}.user-panel{display:flex;align-items:center;gap:10px;padding:10px;border-radius:13px;background:var(--surface);border:1px solid var(--border)}.status-dot{width:7px;height:7px;border-radius:50%;background:var(--success);box-shadow:0 0 0 4px var(--success-soft)}
@media(max-width:1050px){.admin-sidebar{transform:translateX(-102%);transition:transform .22s}.admin-sidebar.open{transform:none}.admin-main{margin-left:0}.mobile-menu{display:flex}.sidebar-scrim.show{display:block;position:fixed;inset:0;background:var(--scrim);z-index:25}.admin-content{padding:24px}.admin-topbar{padding:0 24px}}@media(max-width:700px){.admin-content{padding:18px 14px 36px}.admin-topbar{height:64px;padding:0 14px}.page-title{font-size:22px}.desktop-only{display:none!important}.card{border-radius:14px}.stat{padding:16px}}
.empty-state{text-align:center;padding:36px 18px!important;color:var(--text-faint);font-size:13px}
.admin-content{font-weight:500}.admin-content strong{font-weight:700}.admin-content .page-title{font-weight:700}.admin-content .page-subtitle{font-weight:500}.page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:24px;flex-wrap:wrap}.table-card{padding:8px 0 0}.pagination-wrap{padding:18px 22px}.search-box{height:40px;min-width:240px;display:flex;align-items:center;gap:9px;padding:0 12px;border:1px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text-faint)}.search-box:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}.search-box input{width:100%;border:0;outline:0;background:transparent;color:var(--text);font-size:13px}.entity-cell{display:flex;align-items:center;gap:11px}.entity-icon{width:36px;height:36px;border-radius:10px;display:grid;place-items:center;background:var(--accent-soft);color:var(--accent)}.entity-name{font-size:13px;font-weight:700;color:var(--text)}.entity-meta{font-size:10px;color:var(--text-faint);margin-top:2px}.row-actions{display:flex;align-items:center;justify-content:flex-end;gap:7px}.action-btn{height:32px;padding:0 10px;display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);border-radius:8px;background:transparent;color:var(--text-muted);font-size:11px;font-weight:600}.action-btn:hover{background:var(--surface-hover);color:var(--text)}.action-btn.danger{color:var(--danger)}.form-card{max-width:820px;padding:26px}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.field.full{grid-column:1/-1}.field label{display:block;font-size:12px;font-weight:600;color:var(--text-muted);margin-bottom:7px}.field small{display:block;color:var(--danger);font-size:11px;margin-top:5px}.form-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:24px;padding-top:20px;border-top:1px solid var(--border)}.back-link{display:inline-block;font-size:12px;color:var(--text-faint);margin-bottom:8px}@media(max-width:600px){.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.search-box{min-width:100%}}
.admin-content [style*="font-weight:900"]{font-weight:700!important}.admin-content [style*="font-weight:800"]{font-weight:600!important}.navlink{font-weight:600}.panel-label,.kicker,th{font-weight:600}.metric-value{font-weight:700!important}
.navlink-row{position:relative}
.nav-fav-btn{position:absolute;right:6px;top:50%;transform:translateY(-50%);width:26px;height:26px;border:0;background:transparent;color:var(--text-faint);border-radius:7px;display:flex;align-items:center;justify-content:center;cursor:pointer;opacity:0;transition:opacity .15s,color .15s,background .15s}
.navlink-row:hover .nav-fav-btn,.nav-fav-btn.is-fav{opacity:1}.nav-fav-btn:hover{background:var(--surface-hover);color:var(--text)}.nav-fav-btn.is-fav{color:var(--warning)}.nav-fav-btn.is-fav svg{fill:var(--warning)}
#nav-favorites{display:none;flex-direction:column;gap:4px}#nav-favorites.show{display:flex}
.nav-collapsible-toggle{width:100%;background:none;border:0;border-top:1px solid var(--border);cursor:pointer;font-family:var(--font)}
.nav-collapsible-toggle .chevron{margin-left:auto;transition:transform .18s;opacity:.7;flex-shrink:0}
.nav-collapsible.collapsed .nav-collapsible-toggle .chevron{transform:rotate(-90deg)}
.nav-collapsible-body{display:flex;flex-direction:column;gap:4px;overflow:hidden}
.nav-collapsible.collapsed .nav-collapsible-body{display:none}
.kbd-hint{display:inline-flex;align-items:center;gap:2px;padding:3px 7px;border-radius:6px;background:var(--bg-elevated);border:1px solid var(--border);color:var(--text-faint);font-size:10px;font-weight:800;font-family:var(--font);cursor:pointer;flex-shrink:0}
.quick-create{position:relative}
.quick-create-menu{position:absolute;right:0;top:48px;width:200px;background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:6px;z-index:70;box-shadow:0 24px 48px rgba(0,0,0,.28)}
.quick-create-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:9px;font-size:13px;font-weight:700;color:var(--text)}
.quick-create-item:hover{background:var(--surface-hover);color:var(--text)}
.quick-create-item svg{color:var(--text-faint);flex-shrink:0}
.cmdk-backdrop{position:fixed;inset:0;z-index:100;background:var(--scrim);backdrop-filter:blur(2px);display:flex;align-items:flex-start;justify-content:center;padding-top:12vh;opacity:0;visibility:hidden;transition:.16s ease}
.cmdk-backdrop.show{opacity:1;visibility:visible}
.cmdk-modal{width:min(560px,92vw);max-height:70vh;display:flex;flex-direction:column;background:var(--surface);border:1px solid var(--border-strong);border-radius:16px;box-shadow:0 30px 80px rgba(0,0,0,.35);transform:translateY(-10px);transition:transform .16s ease;overflow:hidden}
.cmdk-backdrop.show .cmdk-modal{transform:translateY(0)}
.cmdk-input-row{display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid var(--border);flex-shrink:0}
.cmdk-input-row svg{color:var(--text-faint);flex-shrink:0}
#cmdk-input{flex:1;min-width:0;border:0;outline:0;background:transparent;color:var(--text);font-size:15px;font-family:var(--font)}
.cmdk-results{overflow-y:auto;padding:8px}
.cmdk-group-label{padding:10px 10px 4px;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:.08em;color:var(--text-faint)}
.cmdk-item{display:flex;align-items:center;gap:11px;padding:10px;border-radius:10px;color:var(--text);cursor:pointer}
.cmdk-item svg{flex-shrink:0;color:var(--text-faint);opacity:.85}
.cmdk-item b{font-size:13px;font-weight:700;display:block}
.cmdk-item span{font-size:11px;color:var(--text-faint);display:block;margin-top:1px}
.cmdk-item.active,.cmdk-item:hover{background:var(--accent-soft)}
.cmdk-item.active svg,.cmdk-item:hover svg{color:var(--accent);opacity:1}
.cmdk-empty{padding:34px 16px;text-align:center;color:var(--text-faint);font-size:13px}
@media(max-width:700px){.cmdk-backdrop{padding-top:0;align-items:stretch}.cmdk-modal{width:100%;max-height:100%;border-radius:0}}
</style></head><body><div class="admin-shell"><div class="sidebar-scrim" id="sidebar-scrim"></div>
<aside class="admin-sidebar" id="admin-sidebar"><div class="admin-sidebar-head"><div class="brand-block"><a href="{{ route('home') }}"><x-logo :height="31" /></a><x-theme-toggle /></div><div class="panel-label">{{ $isAdmin ? 'Управление платформой' : 'Кабинет продавца' }}</div></div><nav class="nav-group admin-nav-scroll">
<div class="nav-group" id="nav-favorites"></div>
@if($isAdmin)
<div class="navlink-row" data-nav-key="dashboard"><a href="{{ route('admin.dashboard') }}" class="navlink {{ $active==='dashboard'?'on':'' }}"><x-icon name="dashboard" :size="18"/>Обзор</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="orders"><a href="{{ route('admin.orders') }}" class="navlink {{ $active==='orders'?'on':'' }}"><x-icon name="cart" :size="18"/>Заказы</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="products"><a href="{{ route('admin.products') }}" class="navlink {{ $active==='products'?'on':'' }}"><x-icon name="package" :size="18"/>Товары</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="sellers"><a href="{{ route('admin.sellers') }}" class="navlink {{ $active==='sellers'?'on':'' }}"><x-icon name="store" :size="18"/>Продавцы</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="users"><a href="{{ route('admin.users') }}" class="navlink {{ $active==='users'?'on':'' }}"><x-icon name="users" :size="18"/>Покупатели</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="nav-collapsible" data-nav-group="marketing">
<button type="button" class="nav-divider nav-collapsible-toggle"><x-icon name="tag" :size="12"/>Маркетинг<x-icon name="chevron-down" :size="12" class="chevron"/></button>
<div class="nav-collapsible-body">
<div class="navlink-row" data-nav-key="banners"><a href="{{ route('admin.banners.index') }}" class="navlink {{ $active==='banners'?'on':'' }}"><x-icon name="image" :size="18"/>Баннеры</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="popups"><a href="{{ route('admin.popups.index') }}" class="navlink {{ $active==='popups'?'on':'' }}"><x-icon name="bell" :size="18"/>Попап-реклама</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="promocodes"><a href="{{ route('admin.promocodes.index') }}" class="navlink {{ $active==='promocodes'?'on':'' }}"><x-icon name="tag" :size="18"/>Промокоды</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="categories"><a href="{{ route('admin.categories.index') }}" class="navlink {{ $active==='categories'?'on':'' }}"><x-icon name="folder" :size="18"/>Категории</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
</div></div>
<div class="nav-collapsible" data-nav-group="finance">
<button type="button" class="nav-divider nav-collapsible-toggle"><x-icon name="chart" :size="12"/>Финансы<x-icon name="chevron-down" :size="12" class="chevron"/></button>
<div class="nav-collapsible-body">
<div class="navlink-row" data-nav-key="finance"><a href="{{ route('admin.finance.index') }}" class="navlink {{ $active==='finance'?'on':'' }}"><x-icon name="chart" :size="18"/>Финансы</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="payments"><a href="{{ route('admin.payments.index') }}" class="navlink {{ $active==='payments'?'on':'' }}"><x-icon name="credit-card" :size="18"/>Платежи</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="payouts"><a href="{{ route('admin.payouts.index') }}" class="navlink {{ $active==='payouts'?'on':'' }}"><x-icon name="wallet" :size="18"/>Выплаты</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
</div></div>
<div class="nav-collapsible" data-nav-group="operations">
<button type="button" class="nav-divider nav-collapsible-toggle"><x-icon name="box" :size="12"/>Операции<x-icon name="chevron-down" :size="12" class="chevron"/></button>
<div class="nav-collapsible-body">
<div class="navlink-row" data-nav-key="inventory"><a href="{{ route('admin.inventory.index') }}" class="navlink {{ $active==='inventory'?'on':'' }}"><x-icon name="box" :size="18"/>Склад</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="returns"><a href="{{ route('admin.returns.index') }}" class="navlink {{ $active==='returns'?'on':'' }}"><x-icon name="refresh" :size="18"/>Возвраты</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="support"><a href="{{ route('admin.support.index') }}" class="navlink {{ $active==='support'?'on':'' }}"><x-icon name="headphones" :size="18"/>Поддержка</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="reviews"><a href="{{ route('admin.reviews.index') }}" class="navlink {{ $active==='reviews'?'on':'' }}"><x-icon name="star" :size="18"/>Отзывы</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
</div></div>
<div class="nav-collapsible" data-nav-group="system">
<button type="button" class="nav-divider nav-collapsible-toggle"><x-icon name="settings" :size="12"/>Система<x-icon name="chevron-down" :size="12" class="chevron"/></button>
<div class="nav-collapsible-body">
<div class="navlink-row" data-nav-key="admins"><a href="{{ route('admin.admins.index') }}" class="navlink {{ $active==='admins'?'on':'' }}"><x-icon name="shield" :size="18"/>Администраторы</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="audit"><a href="{{ route('admin.audit.index') }}" class="navlink {{ $active==='audit'?'on':'' }}"><x-icon name="list" :size="18"/>Журнал действий</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="settings"><a href="{{ route('admin.settings.edit') }}" class="navlink {{ $active==='settings'?'on':'' }}"><x-icon name="settings" :size="18"/>Бизнес-настройки</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="marketplace"><a href="{{ route('admin.marketplace') }}" class="navlink {{ $active==='marketplace'?'on':'' }}"><x-icon name="grid" :size="18"/>Управление маркетплейсом</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="tools"><a href="{{ route('admin.tools') }}" class="navlink {{ $active==='tools'?'on':'' }}"><x-icon name="briefcase" :size="18"/>Центр управления</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
</div></div>
@else
<div class="navlink-row" data-nav-key="dashboard"><a href="{{ route('seller.dashboard') }}" class="navlink {{ $active==='dashboard'?'on':'' }}"><x-icon name="dashboard" :size="18"/>Обзор</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="products"><a href="{{ route('seller.products.index') }}" class="navlink {{ $active==='products'?'on':'' }}"><x-icon name="package" :size="18"/>Товары</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="orders"><a href="{{ route('seller.orders.index') }}" class="navlink {{ $active==='orders'?'on':'' }}"><x-icon name="cart" :size="18"/>Заказы</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
<div class="navlink-row" data-nav-key="settings"><a href="{{ route('seller.settings.edit') }}" class="navlink {{ $active==='settings'?'on':'' }}"><x-icon name="settings" :size="18"/>Настройки магазина</a><button type="button" class="nav-fav-btn" title="Добавить в избранное"><x-icon name="star" :size="14"/></button></div>
@endif</nav><div class="admin-sidebar-foot"><a href="{{ route('home') }}" class="btn-ghost">← На витрину</a><div class="user-panel" style="margin-top:10px"><div class="avatar">{{ Str::of(auth()->user()->name)->substr(0,2)->upper() }}</div><div style="min-width:0;flex:1"><div style="font-size:12px;font-weight:900;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth()->user()->name }}</div><div style="font-size:10px;color:var(--text-faint)">{{ $isAdmin?'Администратор':'Продавец' }}</div></div><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" title="Выйти" style="border:0;background:none;color:var(--danger);padding:5px"><x-icon name="arrow-right" :size="17"/></button></form></div></div></aside>
<main class="admin-main"><header class="admin-topbar"><div style="display:flex;align-items:center;gap:12px;flex:0 0 auto"><button class="icon-btn mobile-menu" id="sidebar-toggle" aria-label="Открыть меню"><x-icon name="list" :size="20"/></button><div><div class="kicker desktop-only">{{ $businessSettings->site_name }} / {{ $isAdmin?'Админ-панель':'Продавец' }}</div><div style="font-size:15px;font-weight:900;white-space:nowrap">{{ $title }}</div></div></div>
<button type="button" id="command-palette-trigger" class="header-search" style="cursor:pointer"><x-icon name="search" :size="16"/><span style="flex:1;min-width:0;text-align:left;color:var(--text-faint);font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">Поиск и переход по разделам…</span><span class="kbd-hint desktop-only">Ctrl K</span></button>
<div style="display:flex;align-items:center;gap:10px;flex:0 0 auto">
@if($isAdmin)
<div class="quick-create" id="quick-create">
    <button type="button" onclick="akToggleMenu(event,'quick-create-menu')" class="btn-accent" style="height:38px;padding:0 14px"><x-icon name="plus" :size="15"/><span class="desktop-only">Создать</span></button>
    <div id="quick-create-menu" class="dropdown-panel quick-create-menu">
        <a href="{{ route('admin.products.create') }}" class="quick-create-item"><x-icon name="package" :size="15"/>Товар</a>
        <a href="{{ route('admin.categories.create') }}" class="quick-create-item"><x-icon name="folder" :size="15"/>Категория</a>
        <a href="{{ route('admin.banners.create') }}" class="quick-create-item"><x-icon name="image" :size="15"/>Баннер</a>
        <a href="{{ route('admin.popups.create') }}" class="quick-create-item"><x-icon name="bell" :size="15"/>Popup</a>
        <a href="{{ route('admin.promocodes.index') }}" class="quick-create-item"><x-icon name="tag" :size="15"/>Промокод</a>
    </div>
</div>
@endif
<span class="desktop-only" style="font-size:12px;color:var(--text-faint)">Система работает</span><span class="status-dot desktop-only"></span><x-notification-bell :notifications="$notifications" :unread-count="$unreadCount"/></div></header><div class="admin-content">@if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif{{ $slot }}</div></main></div>
<div class="cmdk-backdrop" id="cmdk-backdrop">
    <div class="cmdk-modal" role="dialog" aria-modal="true" aria-label="Быстрый поиск">
        <div class="cmdk-input-row"><x-icon name="search" :size="18"/><input type="text" id="cmdk-input" placeholder="Поиск разделов, товаров, заказов, покупателей…" autocomplete="off"><span class="kbd-hint" id="cmdk-close">Esc</span></div>
        <div class="cmdk-results" id="cmdk-results"></div>
    </div>
</div>
<script>const sidebar=document.getElementById('admin-sidebar'),scrim=document.getElementById('sidebar-scrim');function toggleSidebar(){sidebar.classList.toggle('open');scrim.classList.toggle('show')}document.getElementById('sidebar-toggle')?.addEventListener('click',toggleSidebar);scrim?.addEventListener('click',toggleSidebar)</script>
<script>
(function () {
    var FAV_KEY = 'admin_nav_favorites';
    var COLLAPSE_KEY = 'admin_nav_collapsed';
    var navScroll = document.querySelector('.admin-nav-scroll');
    var favGroup = document.getElementById('nav-favorites');
    if (!navScroll) return;

    function readList(key) {
        try { return JSON.parse(localStorage.getItem(key) || '[]'); } catch (e) { return []; }
    }
    function writeList(key, list) {
        try { localStorage.setItem(key, JSON.stringify(list)); } catch (e) {}
    }

    function syncFavButtons() {
        var favorites = readList(FAV_KEY);
        navScroll.querySelectorAll('.navlink-row[data-nav-key] > .nav-fav-btn').forEach(function (btn) {
            var key = btn.closest('.navlink-row').dataset.navKey;
            var isFav = favorites.indexOf(key) !== -1;
            btn.classList.toggle('is-fav', isFav);
            btn.title = isFav ? 'Убрать из избранного' : 'Добавить в избранное';
        });
    }

    function renderFavorites() {
        if (!favGroup) return;
        var favorites = readList(FAV_KEY);
        favGroup.innerHTML = '';
        if (!favorites.length) { favGroup.classList.remove('show'); return; }
        var label = document.createElement('div');
        label.className = 'panel-label';
        label.textContent = 'Избранное';
        favGroup.appendChild(label);
        favorites.forEach(function (key) {
            var original = navScroll.querySelector('.navlink-row[data-nav-key="' + key + '"] > .navlink');
            if (!original) return;
            var row = document.createElement('div');
            row.className = 'navlink-row';
            row.appendChild(original.cloneNode(true));
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'nav-fav-btn is-fav';
            btn.title = 'Убрать из избранного';
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>';
            row.appendChild(btn);
            favGroup.appendChild(row);
        });
        favGroup.classList.add('show');
    }

    function toggleFavorite(key) {
        var favorites = readList(FAV_KEY);
        var idx = favorites.indexOf(key);
        if (idx === -1) { favorites.push(key); } else { favorites.splice(idx, 1); }
        writeList(FAV_KEY, favorites);
        syncFavButtons();
        renderFavorites();
    }

    document.body.addEventListener('click', function (e) {
        var btn = e.target.closest('.nav-fav-btn');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        var row = btn.closest('.navlink-row');
        if (row && row.dataset.navKey) toggleFavorite(row.dataset.navKey);
    });

    syncFavButtons();
    renderFavorites();

    var collapsedGroups = readList(COLLAPSE_KEY);
    document.querySelectorAll('.nav-collapsible').forEach(function (group) {
        var key = group.dataset.navGroup;
        if (collapsedGroups.indexOf(key) !== -1) group.classList.add('collapsed');
        var toggle = group.querySelector('.nav-collapsible-toggle');
        toggle.addEventListener('click', function () {
            group.classList.toggle('collapsed');
            var list = readList(COLLAPSE_KEY);
            var idx = list.indexOf(key);
            var isCollapsed = group.classList.contains('collapsed');
            if (isCollapsed && idx === -1) list.push(key);
            if (!isCollapsed && idx !== -1) list.splice(idx, 1);
            writeList(COLLAPSE_KEY, list);
        });
    });
})();
</script>
<script>
(function () {
    var trigger = document.getElementById('command-palette-trigger');
    var backdrop = document.getElementById('cmdk-backdrop');
    var input = document.getElementById('cmdk-input');
    var results = document.getElementById('cmdk-results');
    var closeBtn = document.getElementById('cmdk-close');
    if (!backdrop || !trigger) return;

    var searchUrl = @json($isAdmin ? route('admin.search') : null);
    var dotIcon = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/></svg>';

    var pages = Array.from(document.querySelectorAll('.navlink-row[data-nav-key] > .navlink')).map(function (a) {
        var icon = a.querySelector('svg');
        return { title: a.textContent.trim(), url: a.getAttribute('href'), iconHtml: icon ? icon.outerHTML : dotIcon };
    });

    var activeIndex = -1;
    var currentItems = [];
    var fetchTimer = null;

    function escapeHtml(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
    function itemHtml(iconHtml, title, subtitle, url) {
        return '<div class="cmdk-item" data-url="' + url + '">' + iconHtml + '<div><b>' + title + '</b>' + (subtitle ? '<span>' + subtitle + '</span>' : '') + '</div></div>';
    }
    function renderItems(html) {
        results.innerHTML = html || '<div class="cmdk-empty">Ничего не найдено</div>';
        currentItems = Array.from(results.querySelectorAll('.cmdk-item'));
        activeIndex = currentItems.length ? 0 : -1;
        highlight();
    }
    function highlight() {
        currentItems.forEach(function (el, i) { el.classList.toggle('active', i === activeIndex); });
        if (activeIndex >= 0) currentItems[activeIndex].scrollIntoView({ block: 'nearest' });
    }
    function staticHtml(query) {
        var q = query.trim().toLowerCase();
        var matches = pages.filter(function (p) { return !q || p.title.toLowerCase().indexOf(q) !== -1; });
        return matches.length ? '<div class="cmdk-group-label">Разделы</div>' + matches.map(function (p) { return itemHtml(p.iconHtml, escapeHtml(p.title), null, p.url); }).join('') : '';
    }

    function open() {
        backdrop.classList.add('show');
        input.value = '';
        renderItems(staticHtml('') || '<div class="cmdk-empty">Начните вводить, чтобы найти раздел, товар, заказ или покупателя</div>');
        setTimeout(function () { input.focus(); }, 10);
    }
    function close() { backdrop.classList.remove('show'); }

    input.addEventListener('input', function () {
        var query = input.value;
        var html = staticHtml(query);
        clearTimeout(fetchTimer);
        if (!query.trim() || !searchUrl) { renderItems(html); return; }
        renderItems(html);
        fetchTimer = setTimeout(function () {
            fetch(searchUrl + '?palette=1&q=' + encodeURIComponent(query))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    var extra = '';
                    ['products', 'orders', 'users'].forEach(function (group) {
                        var label = { products: 'Товары', orders: 'Заказы', users: 'Покупатели' }[group];
                        if (data[group] && data[group].length) {
                            extra += '<div class="cmdk-group-label">' + label + '</div>' + data[group].map(function (item) {
                                return itemHtml(dotIcon, escapeHtml(item.title), escapeHtml(item.subtitle), item.url);
                            }).join('');
                        }
                    });
                    if (input.value === query) renderItems(html + extra);
                })
                .catch(function () {});
        }, 250);
    });

    results.addEventListener('click', function (e) {
        var item = e.target.closest('.cmdk-item');
        if (item) window.location.href = item.dataset.url;
    });

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            backdrop.classList.contains('show') ? close() : open();
            return;
        }
        if (!backdrop.classList.contains('show')) return;
        if (e.key === 'Escape') { close(); }
        else if (e.key === 'ArrowDown') { e.preventDefault(); if (currentItems.length) { activeIndex = Math.min(activeIndex + 1, currentItems.length - 1); highlight(); } }
        else if (e.key === 'ArrowUp') { e.preventDefault(); if (currentItems.length) { activeIndex = Math.max(activeIndex - 1, 0); highlight(); } }
        else if (e.key === 'Enter' && activeIndex >= 0) { window.location.href = currentItems[activeIndex].dataset.url; }
    });

    trigger.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    backdrop.addEventListener('click', function (e) { if (e.target === backdrop) close(); });
})();
</script>
@include('partials.menu-script')</body></html>
