<x-dashboard-layout title="Поиск" active="search"><div class="page-head"><div><div class="page-title">Поиск по системе</div><div class="page-subtitle">Товары, пользователи и заказы в одном месте</div></div><form method="GET" class="search-box" style="min-width:320px"><x-icon name="search" :size="16"/><input autofocus name="q" value="{{ $query }}" placeholder="Название, email, телефон или заказ">@if($type)<input type="hidden" name="type" value="{{ $type }}">@endif</form></div>
@if($query!=='')
<div class="toolbar" style="margin-bottom:18px">
    <a href="{{ route('admin.search',['q'=>$query]) }}" class="action-btn {{ !$type?'is-on':'' }}">Все</a>
    <a href="{{ route('admin.search',['q'=>$query,'type'=>'products']) }}" class="action-btn {{ $type==='products'?'is-on':'' }}"><x-icon name="package" :size="13"/>Товары</a>
    <a href="{{ route('admin.search',['q'=>$query,'type'=>'users']) }}" class="action-btn {{ $type==='users'?'is-on':'' }}"><x-icon name="users" :size="13"/>Пользователи</a>
    <a href="{{ route('admin.search',['q'=>$query,'type'=>'orders']) }}" class="action-btn {{ $type==='orders'?'is-on':'' }}"><x-icon name="cart" :size="13"/>Заказы</a>
</div>
@endif
@if($query==='')<div class="card empty-state">Введите запрос в строке поиска.</div>@else<div style="display:grid;gap:18px">@foreach([['products',$products],['users',$users],['orders',$orders]] as [$itemType,$items])
@continue($type && $type!==$itemType)
@php($label=['products'=>'Товары','users'=>'Пользователи','orders'=>'Заказы'][$itemType])
<section class="card" style="padding:20px"><div style="display:flex;justify-content:space-between;margin-bottom:12px"><strong>{{ $label }}</strong><span class="badge">{{ $items->count() }}</span></div>@forelse($items as $item)
@php($href=match($itemType){'products'=>route('admin.products.edit',$item),'users'=>route('admin.users',['search'=>$item->email]),'orders'=>route('admin.orders.show',$item)})
<a href="{{ $href }}" style="padding:11px 0;border-top:1px solid var(--border);display:flex;justify-content:space-between;gap:15px;color:inherit"><span>{{ $item->name ?? $item->number }}</span><span class="entity-meta">{{ $item->email ?? ($item->sku ?? (($item->total ?? 0).' TMT')) }}</span></a>
@empty<div class="empty-state">Совпадений нет</div>@endforelse</section>@endforeach</div>@endif
<style>.action-btn.is-on{background:var(--accent);border-color:var(--accent);color:#fff}</style>
</x-dashboard-layout>
