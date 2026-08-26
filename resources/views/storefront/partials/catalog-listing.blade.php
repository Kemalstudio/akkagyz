@php($routeName = $routeName ?? 'catalog')
<style>.filter-context .entity-icon{width:38px;height:38px;border-radius:11px;display:grid;place-items:center;background:var(--accent-soft);color:var(--accent);flex-shrink:0}.sort-dropdown{position:relative;min-width:220px}.sort-trigger{width:100%;height:44px;padding:0 13px 0 15px;display:flex;align-items:center;justify-content:space-between;gap:14px;border:1px solid var(--border);border-radius:12px;background:var(--surface);color:var(--text);font:600 13px var(--font);cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.03);transition:.2s}.sort-trigger:hover,.sort-dropdown.open .sort-trigger{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}.sort-trigger svg{transition:transform .22s}.sort-dropdown.open .sort-trigger svg{transform:rotate(180deg)}.sort-menu{position:absolute;right:0;top:51px;width:260px;padding:7px;background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:0 22px 50px rgba(7,15,35,.18);opacity:0;visibility:hidden;transform:translateY(-8px) scale(.98);transform-origin:top right;transition:opacity .18s,transform .18s,visibility .18s;z-index:35}.sort-dropdown.open .sort-menu{opacity:1;visibility:visible;transform:none}.sort-option{width:100%;padding:10px 11px;display:flex;align-items:center;justify-content:space-between;border:0;border-radius:9px;background:transparent;color:var(--text-muted);font:500 13px var(--font);cursor:pointer;text-align:left}.sort-option:hover{background:var(--surface-hover);color:var(--text)}.sort-option.active{background:var(--accent-soft);color:var(--accent);font-weight:700}.sort-check{opacity:0}.sort-option.active .sort-check{opacity:1}@media(max-width:560px){.sort-dropdown{min-width:180px}.sort-menu{width:240px}}</style>
<div class="wrap" style="padding:20px 24px 8px;display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-faint);font-weight:600;">
    <a href="{{ route($homeRouteName ?? 'home') }}" style="color:var(--text-faint);">Главная</a><span>/</span>
    <span style="color:var(--text);">{{ $activeCategory->name ?? 'Каталог' }}</span>
</div>

<div class="wrap catalog-layout" style="padding:12px 24px 40px;display:grid;grid-template-columns:280px 1fr;gap:30px;">
    <aside class="catalog-sidebar">
        <div class="filter-context"><span class="entity-icon"><x-icon :name="$categoryRoot?->icon ?: 'filter'" :size="18"/></span><div><strong>{{ $filterContext['title'] }}</strong><span>{{ $filterContext['hint'] }}</span></div></div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:16px;font-weight:900;display:flex;align-items:center;gap:8px;"><x-icon name="filter" :size="17" />Фильтры</span>
            @if(request()->anyFilled(['q','category','min_price','max_price','in_stock','on_sale','vip','attr']))
                <a href="{{ route($routeName, $activeCategory ? ['category'=>$activeCategory->slug] : []) }}" style="font-size:12px;font-weight:600;color:var(--text-faint);">Сбросить</a>
            @endif
        </div>

        <form method="GET" action="{{ route($routeName) }}">
            @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
            @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
            @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif

            <x-filter-group title="Цена, TMT" icon="money" :active="true">
                <div style="display:flex;align-items:center;gap:10px;">
                    <input class="input" style="height:38px;font-size:13px;" type="number" name="min_price" value="{{ request('min_price') }}" placeholder="От">
                    <span style="color:var(--text-faint);">&mdash;</span>
                    <input class="input" style="height:38px;font-size:13px;" type="number" name="max_price" value="{{ request('max_price') }}" placeholder="До">
                </div>
                <div class="quick-price-grid">@foreach($filterContext['quick'] as $preset)<button type="button" data-min="{{ $preset[1] }}" data-max="{{ $preset[2] }}" onclick="akPricePreset(this)">{{ $preset[0] }}</button>@endforeach</div>
                @if($priceCeiling)<div class="price-caption">В категории до {{ number_format($priceCeiling,0,'',' ') }} TMT</div>@endif
            </x-filter-group>

            <x-filter-group title="Наличие" icon="check" :active="request()->boolean('in_stock') || request()->boolean('on_sale') || request()->boolean('vip')">
                <label class="filter-row"><input type="checkbox" name="in_stock" value="1" {{ request()->boolean('in_stock') ? 'checked' : '' }}><span class="filter-row-label">Только в наличии</span></label>
                <label class="filter-row"><input type="checkbox" name="on_sale" value="1" @checked(request()->boolean('on_sale'))><span class="filter-row-label">Товары со скидкой</span></label>
                @if($routeName==='marketplace.catalog')
                    <label class="filter-row"><input type="checkbox" name="vip" value="1" @checked(request()->boolean('vip'))><span class="filter-row-label">Только VIP-товары</span></label>
                @endif
            </x-filter-group>

            @foreach($attributeFacets ?? [] as $facet)
                @php($selectedValues = request("attr.{$facet['attribute']->id}", []))
                <x-filter-group :title="$facet['attribute']->name" :icon="$facet['attribute']->filter_icon" :active="!empty($selectedValues)">
                    @foreach($facet['values'] as $item)
                        <label class="filter-row">
                            <input type="checkbox" name="attr[{{ $facet['attribute']->id }}][]" value="{{ $item->value }}" {{ in_array($item->value, $selectedValues) ? 'checked' : '' }}>
                            <span class="filter-row-label" title="{{ $item->value }}">{{ $item->value }}</span>
                            <span class="filter-count">{{ $item->aggregate }}</span>
                        </label>
                    @endforeach
                </x-filter-group>
            @endforeach

            <button type="submit" class="btn-accent" style="width:100%;margin-top:20px;">Применить</button>
        </form>

        @if($categoryRoot && $categoryRoot->children->isNotEmpty())<div class="subcategory-box"><div class="filter-label">{{ $categoryRoot->name }}</div><a href="{{ route($routeName,['category'=>$categoryRoot->slug]) }}" class="subcat-link {{ $activeCategory?->id===$categoryRoot->id?'active':'' }}">Все товары раздела</a>@foreach($categoryRoot->children as $child)<a href="{{ route($routeName,['category'=>$child->slug]) }}" class="subcat-link {{ $activeCategory?->id===$child->id?'active':'' }}"><x-icon :name="$child->icon?:'chevron-right'" :size="13"/>{{ $child->name }}</a>@endforeach</div>@endif
        <div style="border-top:1px solid var(--border);padding-top:18px;">
            <div style="font-size:14px;font-weight:700;margin-bottom:10px;">Все разделы</div>
            <div style="display:flex;flex-direction:column;gap:2px;">
                @foreach($categories->whereNull('parent_id') as $cat)
                    <a href="{{ route($routeName, ['category' => $cat->slug]) }}" style="padding:9px 10px;border-radius:8px;font-size:14px;font-weight:700;color:{{ ($activeCategory?->id === $cat->id) ? 'var(--accent)' : 'var(--text-muted)' }};background:{{ ($activeCategory?->id === $cat->id) ? 'var(--accent-soft)' : 'transparent' }};">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </aside>

    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div>
                <div style="font-size:24px;font-weight:900;">{{ $activeCategory->name ?? 'Все товары' }}</div>
                <div style="font-size:13px;color:var(--text-faint);margin-top:2px;">{{ $products->total() }} товаров</div>
            </div>
            <form method="GET" action="{{ route($routeName) }}" style="display:flex;align-items:center;gap:10px;">
                @include('storefront.partials.hidden-filter-inputs', ['data' => request()->except(['sort','per_page']), 'prefix' => null])
                <?php
                    $sortOptions = ['popular'=>'По популярности','newest'=>'Сначала новые','price_asc'=>'Цена: по возрастанию','price_desc'=>'Цена: по убыванию','rating'=>'По рейтингу','name_asc'=>'Название: А–Я','name_desc'=>'Название: Я–А'];
                    $currentSort = request('sort', 'popular');
                    $perPageOptions = [30,50,100,200,300];
                    $currentPerPage = (int) request('per_page', 30);
                    if (!in_array($currentPerPage, $perPageOptions, true)) { $currentPerPage = 30; }
                ?>
                <input type="hidden" name="sort" value="{{ $currentSort }}">
                <div class="sort-dropdown" data-input="sort"><button type="button" class="sort-trigger" aria-expanded="false"><span>{{ $sortOptions[$currentSort] ?? $sortOptions['popular'] }}</span><x-icon name="chevron-down" :size="14"/></button><div class="sort-menu">@foreach($sortOptions as $value=>$label)<button type="button" class="sort-option {{ $currentSort===$value?'active':'' }}" data-value="{{ $value }}"><span>{{ $label }}</span><span class="sort-check"><x-icon name="check" :size="14"/></span></button>@endforeach</div></div>

                <input type="hidden" name="per_page" value="{{ $currentPerPage }}">
                <div class="sort-dropdown" data-input="per_page" style="min-width:150px;"><button type="button" class="sort-trigger" aria-expanded="false"><span>Показывать: {{ $currentPerPage }}</span><x-icon name="chevron-down" :size="14"/></button><div class="sort-menu">@foreach($perPageOptions as $option)<button type="button" class="sort-option {{ $currentPerPage===$option?'active':'' }}" data-value="{{ $option }}"><span>{{ $option }}</span><span class="sort-check"><x-icon name="check" :size="14"/></span></button>@endforeach</div></div>
            </form>
        </div>

        @if($products->isEmpty())
            <div style="padding:60px 0;text-align:center;color:var(--text-faint);font-size:15px;font-weight:600;">Товары не найдены. Попробуйте изменить фильтры.</div>
        @else
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">
                @foreach($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            <div style="margin-top:36px;">
                {{ $products->onEachSide(1)->links() }}
            </div>
        @endif
    </div>
</div>
<style>
.catalog-sidebar{display:flex;flex-direction:column;gap:16px;padding:18px;border-radius:18px;background:var(--surface);border:1px solid var(--border);height:max-content;position:sticky;top:96px;max-height:calc(100vh - 116px);overflow-y:auto}
.catalog-sidebar::-webkit-scrollbar{width:5px}.catalog-sidebar::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:10px}.catalog-sidebar::-webkit-scrollbar-track{background:transparent}
.catalog-sidebar{scrollbar-width:thin;scrollbar-color:var(--border-strong) transparent}
.filter-context{display:flex;align-items:center;gap:11px;padding:4px 0 16px;border-bottom:1px solid var(--border)}.filter-context strong{display:block;font-size:13px;font-weight:700}.filter-context span:not(.entity-icon){display:block;font-size:10px;color:var(--text-faint);margin-top:3px}
.quick-price-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:10px}.quick-price-grid button{min-height:31px;padding:5px;border:1px solid var(--border);border-radius:8px;background:var(--bg);color:var(--text-muted);font-size:10px}.quick-price-grid button:hover,.quick-price-grid button.active{border-color:var(--accent);color:var(--accent);background:var(--accent-soft)}.price-caption{font-size:9px;color:var(--text-faint);margin-top:8px}
.filter-label{font-size:12px;font-weight:700;margin-bottom:3px}
.subcategory-box{display:grid;gap:4px;padding:14px;border-radius:13px;background:var(--bg);border:1px solid var(--border)}.subcat-link{display:flex;align-items:center;gap:7px;padding:7px 8px;border-radius:7px;color:var(--text-muted);font-size:11.5px}.subcat-link:hover,.subcat-link.active{background:var(--accent-soft);color:var(--accent)}

.filter-group{border-top:1px solid var(--border)}
.filter-group:first-of-type{border-top:none}
.filter-group-head{width:100%;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:15px 2px;background:none;border:none;cursor:pointer;color:var(--text);font:inherit;text-align:left;border-radius:8px;transition:background .15s}
.filter-group-head:hover{background:var(--surface-hover)}
.filter-group-head-label{display:flex;align-items:center;gap:9px;font-size:13px;font-weight:800;letter-spacing:.01em;color:var(--text)}
.filter-group-head-label svg{color:var(--text-faint);flex-shrink:0}
.filter-group.open .filter-group-head-label svg{color:var(--accent)}
.filter-group-chevron{color:var(--text-faint);transition:transform .25s cubic-bezier(.4,0,.2,1);flex-shrink:0}
.filter-group.open .filter-group-chevron{transform:rotate(180deg);color:var(--accent)}
.filter-group-body{display:grid;grid-template-rows:0fr;transition:grid-template-rows .25s cubic-bezier(.4,0,.2,1)}
.filter-group.open .filter-group-body{grid-template-rows:1fr}
.filter-group-body-inner{overflow:hidden;min-height:0}
.filter-group-body-content{display:flex;flex-direction:column;gap:1px;padding:2px 2px 16px}
.filter-row{position:relative;display:flex;align-items:center;gap:11px;padding:7px 8px;border-radius:9px;font-size:13px;color:var(--text-muted);font-weight:600;cursor:pointer;transition:background .15s;flex-shrink:0}
.filter-row:hover{background:var(--surface-hover)}
.filter-row input{appearance:none;-webkit-appearance:none;margin:0;width:17px;height:17px;border:1.5px solid var(--border-strong);border-radius:5px;background:var(--bg);flex-shrink:0;display:grid;place-items:center;cursor:pointer;transition:background .15s,border-color .15s}
.filter-row input[type=radio]{border-radius:50%}
.filter-row input:checked{background:var(--accent);border-color:var(--accent)}
.filter-row input[type=checkbox]:checked::after{content:'';width:4px;height:8px;border:solid var(--accent-text);border-width:0 2px 2px 0;transform:translateY(-1px) rotate(45deg)}
.filter-row input[type=radio]:checked::after{content:'';width:7px;height:7px;border-radius:50%;background:var(--accent-text)}
.filter-row-label{flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.filter-row:has(input:checked) .filter-row-label{color:var(--text)}
.filter-count{font-size:11px;font-weight:700;color:var(--text-faint);background:var(--bg);padding:2px 8px;border-radius:20px;flex-shrink:0}
.filter-row:has(input:checked) .filter-count{background:var(--accent-soft);color:var(--accent)}
.filter-row-extra{display:none}
.filter-group.show-all .filter-row-extra{display:flex}
.filter-more-toggle{margin:2px 2px 0;padding:8px;border:none;background:none;color:var(--accent);font-size:12px;font-weight:800;cursor:pointer;text-align:left;border-radius:8px;transition:background .15s}
.filter-more-toggle:hover{background:var(--accent-soft)}

@media(max-width:900px){.catalog-layout{grid-template-columns:1fr!important}.catalog-sidebar{position:static;max-height:none;overflow:visible}.catalog-layout>div>div[style*="grid-template-columns:repeat(3"]{grid-template-columns:repeat(2,1fr)!important}}
@media(max-width:520px){.catalog-layout{padding-left:14px!important;padding-right:14px!important}.catalog-layout>div>div[style*="grid-template-columns:repeat(3"]{grid-template-columns:1fr!important}}
</style>
<script>
(function(){
    var LIMIT = 6;
    document.querySelectorAll('.filter-group').forEach(function(group){
        var content = group.querySelector('.filter-group-body-content');
        var rows = Array.prototype.slice.call(group.querySelectorAll('.filter-row'));
        if (rows.length > LIMIT) {
            rows.slice(LIMIT).forEach(function(row){ row.classList.add('filter-row-extra'); });
            var toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'filter-more-toggle';
            var hiddenCount = rows.length - LIMIT;
            toggle.textContent = 'Показать ещё ' + hiddenCount;
            toggle.addEventListener('click', function(){
                var expanded = group.classList.toggle('show-all');
                toggle.textContent = expanded ? 'Скрыть' : 'Показать ещё ' + hiddenCount;
            });
            content.appendChild(toggle);
        }
    });
    document.querySelectorAll('.filter-group-head').forEach(function(btn){
        btn.addEventListener('click', function(){ btn.closest('.filter-group').classList.toggle('open'); });
    });
})();
</script>
<script>function akPricePreset(btn){const form=btn.closest('form'),min=form.querySelector('[name=min_price]'),max=form.querySelector('[name=max_price]');min.value=btn.dataset.min||'';max.value=btn.dataset.max||'';form.querySelectorAll('.quick-price-grid button').forEach(x=>x.classList.remove('active'));btn.classList.add('active')}document.querySelectorAll('.sort-dropdown').forEach(function(dropdown){const trigger=dropdown.querySelector('.sort-trigger'),form=dropdown.closest('form'),input=form.querySelector('input[name="'+dropdown.dataset.input+'"]');trigger.addEventListener('click',function(e){e.stopPropagation();const open=dropdown.classList.toggle('open');trigger.setAttribute('aria-expanded',open)});dropdown.querySelectorAll('.sort-option').forEach(function(option){option.addEventListener('click',function(){input.value=option.dataset.value;dropdown.classList.remove('open');form.submit()})})});document.addEventListener('click',function(){document.querySelectorAll('.sort-dropdown.open').forEach(function(el){el.classList.remove('open');el.querySelector('.sort-trigger').setAttribute('aria-expanded','false')})});</script>
