@php
    $checkMeta = [
        'Товары без остатка' => ['icon' => 'box', 'color' => 'danger', 'link' => route('admin.products', ['stock' => 'out'])],
        'Необработанные заказы' => ['icon' => 'cart', 'color' => 'warning', 'link' => route('admin.orders', ['status' => 'pending'])],
        'Заблокированные пользователи' => ['icon' => 'lock', 'color' => 'danger', 'link' => route('admin.users', ['status' => 'blocked'])],
        'Активные popup' => ['icon' => 'bell', 'color' => 'accent', 'link' => route('admin.popups.index')],
        'Товары на модерации' => ['icon' => 'package', 'color' => 'warning', 'link' => route('admin.products', ['status' => 'pending'])],
        'Продавцы на рассмотрении' => ['icon' => 'store', 'color' => 'warning', 'link' => route('admin.sellers', ['status' => 'pending'])],
    ];
@endphp
<x-dashboard-layout title="Центр управления" active="tools">
<style>
.tools-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px;margin-bottom:22px}
.tools-check{display:block;padding:20px;border-radius:16px;background:linear-gradient(145deg,var(--surface),color-mix(in oklch,var(--surface) 92%,var(--bg)));border:1px solid var(--border);box-shadow:0 1px 2px rgba(0,0,0,.04);transition:.2s;color:inherit}
.tools-check:hover{transform:translateY(-2px);border-color:var(--border-strong);box-shadow:0 12px 30px rgba(0,0,0,.1);color:inherit}
.tools-check .metric-value{font-size:26px;margin-top:12px}
.quick-actions{display:flex;gap:10px;flex-wrap:wrap}
</style>

<div class="page-head">
    <div><div class="page-title">Центр управления</div><div class="page-subtitle">Быстрые действия и контроль важных зон магазина</div></div>
    <span class="badge" style="background:var(--accent-soft);color:var(--accent)">Валюта: {{ $settings->currency }}</span>
</div>

<div class="tools-grid">
    @foreach($checks as $label => $value)
    @php($meta = $checkMeta[$label] ?? ['icon' => 'list', 'color' => 'accent', 'link' => '#'])
    <a href="{{ $meta['link'] }}" class="tools-check">
        <div style="display:flex;justify-content:space-between;align-items:start">
            <div class="kicker">{{ $label }}</div>
            <div class="stat-icon" style="background:var(--{{ $meta['color'] }}-soft);color:var(--{{ $meta['color'] }})"><x-icon :name="$meta['icon']" :size="17" /></div>
        </div>
        <div class="metric-value">{{ number_format($value, 0, '', ' ') }}</div>
    </a>
    @endforeach
</div>

<section class="card" style="padding:22px">
    <div style="font-size:16px;font-weight:700;margin-bottom:14px">Быстрые действия</div>
    <div class="quick-actions">
        <a class="btn-accent" href="{{ route('admin.products.create') }}"><x-icon name="plus" :size="15"/>Добавить товар</a>
        <a class="btn-ghost" href="{{ route('admin.categories.create') }}"><x-icon name="folder" :size="15"/>Новая категория</a>
        <a class="btn-ghost" href="{{ route('admin.popups.create') }}"><x-icon name="bell" :size="15"/>Создать popup</a>
        <a class="btn-ghost" href="{{ route('admin.marketplace') }}"><x-icon name="grid" :size="15"/>Маркетплейс</a>
        <a class="btn-ghost" href="{{ route('admin.audit.index') }}"><x-icon name="list" :size="15"/>Журнал действий</a>
        <a class="btn-ghost" href="{{ route('admin.settings.edit') }}"><x-icon name="settings" :size="15"/>Настройки бизнеса</a>
    </div>
</section>
</x-dashboard-layout>
