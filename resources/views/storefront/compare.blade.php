@php
$products=$items->pluck('product');$bestRating=$products->where('rating_count','>',0)->sortByDesc('rating_avg')->first();$bestPrice=$products->where('stock','>',0)->sortBy('price')->first();
$rows=[
 ['Цена',fn($p)=>number_format($p->price,0,'',' ').' TMT','price'],
 ['Скидка',fn($p)=>$p->discount_percent?'-'.$p->discount_percent.'%':'—','discount'],
 ['Рейтинг',fn($p)=>$p->rating_count?number_format($p->rating_avg,1).' / 5':'Нет оценок','rating'],
 ['Количество отзывов',fn($p)=>(string)$p->rating_count,'reviews'],
 ['Категория',fn($p)=>$p->category?->name?:'—','category'],
 ['Наличие',fn($p)=>$p->in_stock?'В наличии':'Нет в наличии','available'],
 ['Остаток',fn($p)=>$p->stock.' шт.','stock'],
 ['Продано',fn($p)=>$p->sales_count.' шт.','sales'],
 ['Продавец',fn($p)=>$p->seller?->store_name?:$businessSettings->site_name,'seller'],
];
@endphp
<x-layout title="Сравнение товаров">
<style>
.compare-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.compare-shell{border:1px solid var(--border);border-radius:20px;overflow:auto;background:var(--surface);box-shadow:0 18px 44px rgba(15,20,35,.06)}
.compare-table{border-collapse:collapse;width:100%;min-width:820px;table-layout:fixed}
.compare-table th,.compare-table td{border-right:1px solid var(--border);border-bottom:1px solid var(--border);padding:16px 18px;text-align:left;vertical-align:middle}
.compare-table th:last-child,.compare-table td:last-child{border-right:0}
.compare-table tbody tr:hover td{background:color-mix(in oklch,var(--surface-hover) 55%,transparent)}
.compare-table tbody tr:last-child td{border-bottom:0}
.compare-label{width:170px;background:var(--bg-elevated);color:var(--text-faint);font-size:11.5px;font-weight:700;position:sticky;left:0;z-index:3}
.compare-progress{display:flex;gap:5px;margin-top:11px}
.compare-progress span{width:7px;height:7px;border-radius:50%;background:var(--border-strong)}
.compare-progress span.filled{background:var(--accent)}
.compare-product{vertical-align:top!important;min-width:230px;padding:20px!important;background:var(--surface)}
.compare-image{position:relative;width:100%;aspect-ratio:1;background:var(--bg);border:1px solid var(--border);border-radius:16px;display:grid;place-items:center;overflow:hidden;margin-bottom:14px;color:var(--text-faint)}
.compare-image img{width:100%;height:100%;object-fit:contain;padding:10px}
.compare-remove{position:absolute;right:8px;top:8px;width:28px;height:28px;border-radius:9px;border:1px solid var(--border);background:color-mix(in oklch,var(--surface) 88%,transparent);backdrop-filter:blur(6px);color:var(--text-faint);display:grid;place-items:center;transition:.15s}
.compare-remove:hover{color:var(--danger);border-color:var(--danger)}
.compare-name{font-size:13.5px;font-weight:700;color:var(--text);line-height:1.45;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:38px}
.compare-price{font-size:20px;font-weight:900;margin-top:9px;letter-spacing:-.01em}
.best-value{color:var(--success);background:var(--success-soft);font-weight:700}
.diff-toggle{display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-muted);cursor:pointer}
.diff-toggle input{accent-color:var(--accent)}
.compare-empty{max-width:500px;margin:30px auto;padding:60px 30px;text-align:center;border:1px dashed var(--border);border-radius:20px;background:var(--surface)}
.compare-add-slot{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;height:100%;min-height:214px;border:1.5px dashed var(--border-strong);border-radius:16px;color:var(--text-faint);font-size:12px;font-weight:700;text-align:center;padding:18px;transition:.18s}
.compare-add-slot:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-soft)}
.compare-add-slot .icon-circle{width:40px;height:40px;border-radius:12px;background:var(--bg-elevated);display:grid;place-items:center;color:inherit}
.compare-blank-cell{text-align:center;color:var(--text-faint);background:var(--bg-elevated)}
@media(max-width:700px){.compare-label{width:130px}.compare-table th,.compare-table td{padding:12px}.compare-product{min-width:190px}}
</style>
<div class="wrap" style="padding:32px 24px 64px"><div class="compare-head"><div><div style="font-size:28px;font-weight:700;letter-spacing:-.02em;display:flex;align-items:center;gap:10px"><span style="color:var(--accent)"><x-icon name="compare" :size="24"/></span>Сравнение товаров</div><div style="font-size:13px;color:var(--text-faint);margin-top:4px">Сопоставьте характеристики и выберите подходящий вариант</div></div>@if($items->isNotEmpty())<div class="toolbar"><label class="diff-toggle"><input id="diff-only" type="checkbox">Показывать только различия</label><form method="POST" action="{{ route('compare.clear') }}" onsubmit="return confirm('Очистить список сравнения?')">@csrf @method('DELETE')<button class="btn-ghost" style="height:36px;color:var(--danger)"><x-icon name="trash" :size="14"/>Очистить</button></form></div>@endif</div>
@if($items->isEmpty())<div class="compare-empty"><div style="width:62px;height:62px;border-radius:18px;background:var(--accent-soft);color:var(--accent);display:grid;place-items:center;margin:0 auto 17px"><x-icon name="compare" :size="27"/></div><div style="font-size:19px;font-weight:700">Список сравнения пуст</div><p style="font-size:13px;color:var(--text-faint);line-height:1.6">Добавьте от двух до четырёх товаров, чтобы наглядно сравнить их характеристики.</p><a href="{{ route('catalog') }}" class="btn-accent" style="display:inline-flex">Выбрать товары</a></div>@else
@php($emptySlots=max(0,4-$items->count()))
<div class="compare-shell"><table class="compare-table"><thead><tr>
<th class="compare-label"><div style="font-size:14px;color:var(--text);font-weight:800">{{ $items->count() }} из 4</div><div style="font-size:10px;margin-top:2px">товаров добавлено</div><div class="compare-progress">@foreach(range(0,3) as $i)<span class="{{ $i<$items->count()?'filled':'' }}"></span>@endforeach</div></th>
@foreach($items as $item)@php($p=$item->product)@php($image=$p->images->first())<th class="compare-product"><div class="compare-image">@if($image)<img src="{{ $image->url }}" alt="{{ $p->name }}" loading="lazy">@else<x-icon name="image" :size="30"/>@endif<form method="POST" action="{{ route('compare.toggle',$p) }}">@csrf<button class="compare-remove" title="Убрать"><x-icon name="x" :size="13"/></button></form>@if($bestRating?->id===$p->id)<span class="badge" style="position:absolute;left:8px;top:8px;background:var(--accent);color:white">Лучший рейтинг</span>@endif</div><a class="compare-name" href="{{ route($p->seller_id?'marketplace.products.show':'products.show',$p->slug) }}">{{ $p->name }}</a><div class="compare-price">{{ number_format($p->price,0,'',' ') }} TMT</div>@if($p->in_stock)<form method="POST" action="{{ route('cart.add',$p) }}">@csrf<button class="btn-accent" style="width:100%;height:36px;font-size:11px;margin-top:12px"><x-icon name="cart" :size="14"/>В корзину</button></form>@else<button disabled class="btn-ghost" style="width:100%;height:36px;margin-top:12px;opacity:.6">Нет в наличии</button>@endif</th>@endforeach
@if($emptySlots>0)@foreach(range(1,$emptySlots) as $i)<th class="compare-product"><a href="{{ route('catalog') }}" class="compare-add-slot"><span class="icon-circle"><x-icon name="plus" :size="18"/></span>Добавить товар<br>для сравнения</a></th>@endforeach @endif
</tr></thead>
<tbody>
@foreach($rows as [$label,$value,$key])
@php($values=$products->map(fn($p)=>$value($p))->unique())
<tr class="compare-row" data-different="{{ $values->count()>1?'1':'0' }}">
<td class="compare-label">{{ $label }}</td>
@foreach($items as $item)
@php($p=$item->product)
<td class="{{ ($key==='price'&&$bestPrice?->id===$p->id)||($key==='rating'&&$bestRating?->id===$p->id)?'best-value':'' }}">{{ $value($p) }}</td>
@endforeach
@if($emptySlots>0)@foreach(range(1,$emptySlots) as $i)<td class="compare-blank-cell">—</td>@endforeach @endif
</tr>
@endforeach
<tr data-different="1">
<td class="compare-label">Описание</td>
@foreach($items as $item)
<td style="font-size:11px;color:var(--text-muted);line-height:1.55">{{ Str::limit($item->product->description?:'Описание не добавлено',180) }}</td>
@endforeach
@if($emptySlots>0)@foreach(range(1,$emptySlots) as $i)<td class="compare-blank-cell">—</td>@endforeach @endif
</tr>
</tbody>
</table></div>
@endif
</div>
<script>document.getElementById('diff-only')?.addEventListener('change',function(){document.querySelectorAll('.compare-row').forEach(row=>row.style.display=this.checked&&row.dataset.different==='0'?'none':'table-row')})</script>
</x-layout>
