@php
    $routeName = $routeName ?? 'catalog';
    $rootCategories = $categories->whereNull('parent_id');
    $attributeSelections = collect(request('attr', []))
        ->map(fn ($values) => array_values(array_filter((array) $values, fn ($value) => filled($value))))
        ->filter();
    $selectedAttributeCount = $attributeSelections->sum(fn ($values) => count($values));
    $hasPriceFilter = request()->filled('min_price') || request()->filled('max_price');
    $purchaseFilterCount = collect([
        request()->boolean('in_stock'),
        request()->boolean('on_sale'),
        $routeName === 'marketplace.catalog' && request()->boolean('vip'),
        $routeName === 'marketplace.catalog' && in_array(request('condition'), ['new', 'used'], true),
    ])->filter()->count();
    $activeFilterCount = ($hasPriceFilter ? 1 : 0) + $purchaseFilterCount + $selectedAttributeCount;
    $resetQuery = collect([
        'q' => request('q'),
        'category' => request('category') ?: $activeCategory?->slug,
        'sort' => request('sort'),
        'per_page' => request('per_page'),
    ])->filter(fn ($value) => filled($value))->all();
    $resetUrl = route($routeName, $resetQuery);
    $priceCeiling = max(0, (int) ($priceCeiling ?? 0));
    $pricePresets = [];
    if ($priceCeiling >= 3) {
        $priceStep = match (true) {
            $priceCeiling <= 30 => 5,
            $priceCeiling <= 300 => 25,
            $priceCeiling <= 3000 => 250,
            $priceCeiling <= 30000 => 2500,
            default => 5000,
        };
        $firstPricePoint = min($priceCeiling, (int) ceil(($priceCeiling / 3) / $priceStep) * $priceStep);
        $secondPricePoint = min($priceCeiling, (int) ceil((($priceCeiling * 2) / 3) / $priceStep) * $priceStep);
        if ($firstPricePoint < $priceCeiling) {
            $pricePresets[] = ['До '.number_format($firstPricePoint, 0, '', ' '), null, $firstPricePoint];
            if ($secondPricePoint > $firstPricePoint && $secondPricePoint < $priceCeiling) {
                $pricePresets[] = [number_format($firstPricePoint, 0, '', ' ').'–'.number_format($secondPricePoint, 0, '', ' '), $firstPricePoint, $secondPricePoint];
                $pricePresets[] = ['От '.number_format($secondPricePoint, 0, '', ' '), $secondPricePoint, null];
            } else {
                $pricePresets[] = ['От '.number_format($firstPricePoint, 0, '', ' '), $firstPricePoint, null];
            }
        }
    }
    $facetLabels = collect($attributeFacets ?? [])->mapWithKeys(
        fn ($facet) => [$facet['attribute']->id => $facet['attribute']->name]
    );
@endphp
<style>.sort-dropdown{position:relative;min-width:220px}.sort-trigger{width:100%;height:44px;padding:0 13px 0 15px;display:flex;align-items:center;justify-content:space-between;gap:14px;border:1px solid var(--border);border-radius:12px;background:var(--surface);color:var(--text);font:600 13px var(--font);cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.03);transition:.2s}.sort-trigger:hover,.sort-dropdown.open .sort-trigger{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}.sort-trigger svg{transition:transform .22s}.sort-dropdown.open .sort-trigger svg{transform:rotate(180deg)}.sort-menu{position:absolute;right:0;top:51px;width:260px;padding:7px;background:var(--surface);border:1px solid var(--border);border-radius:14px;box-shadow:0 22px 50px rgba(7,15,35,.18);opacity:0;visibility:hidden;transform:translateY(-8px) scale(.98);transform-origin:top right;transition:opacity .18s,transform .18s,visibility .18s;z-index:35}.sort-dropdown.open .sort-menu{opacity:1;visibility:visible;transform:none}.sort-option{width:100%;padding:10px 11px;display:flex;align-items:center;justify-content:space-between;border:0;border-radius:9px;background:transparent;color:var(--text-muted);font:500 13px var(--font);cursor:pointer;text-align:left}.sort-option:hover{background:var(--surface-hover);color:var(--text)}.sort-option.active{background:var(--accent-soft);color:var(--accent);font-weight:700}.sort-check{opacity:0}.sort-option.active .sort-check{opacity:1}@media(max-width:560px){.sort-dropdown{min-width:180px}.sort-menu{width:240px}}</style>
<div class="wrap" style="padding:20px 24px 8px;display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-faint);font-weight:600;">
    <a href="{{ route($homeRouteName ?? 'home') }}" style="color:var(--text-faint);">Главная</a><span>/</span>
    @if($activeCategory?->parent)
        <a href="{{ route($routeName, ['category' => $activeCategory->parent->slug]) }}" style="color:var(--text-faint);">{{ $activeCategory->parent->name }}</a><span>/</span>
    @endif
    <span style="color:var(--text);">{{ $activeCategory->name ?? 'Каталог' }}</span>
</div>

<div id="catalog-filter-backdrop" class="catalog-filter-backdrop" aria-hidden="true"></div>

<div class="wrap catalog-layout {{ $routeName === 'marketplace.catalog' ? 'catalog-layout--marketplace' : 'catalog-layout--storefront' }}">
    <aside id="catalog-filter-drawer" class="catalog-sidebar" data-filter-drawer aria-labelledby="catalog-filter-title" tabindex="-1">
        <div class="catalog-sidebar-header">
            <div class="catalog-sidebar-title-row">
                <span class="catalog-sidebar-icon"><x-icon :name="$categoryRoot?->icon ?: 'filter'" :size="18"/></span>
                <div class="catalog-sidebar-title-copy">
                    <div id="catalog-filter-title" class="catalog-sidebar-title">Фильтры @if($activeFilterCount)<span>{{ $activeFilterCount }}</span>@endif</div>
                    <div class="catalog-sidebar-subtitle">{{ $activeCategory ? 'Для категории «'.$activeCategory->name.'»' : 'Уточните параметры товаров' }}</div>
                </div>
                <button type="button" class="catalog-sidebar-close" data-filter-close aria-label="Закрыть фильтры"><x-icon name="x" :size="18"/></button>
            </div>
            @if($activeFilterCount > 0)
                <a href="{{ $resetUrl }}" class="catalog-reset-link" data-filter-reset><x-icon name="refresh" :size="13"/>Очистить все</a>
            @endif
        </div>

        <div class="catalog-sidebar-body">
            <nav class="catalog-category-nav" aria-label="Категории каталога">
                <div class="catalog-side-section-title"><x-icon name="grid" :size="15"/><span>Категории</span></div>

                @if($categoryRoot)
                    <div class="catalog-category-current">
                        <span><x-icon :name="$categoryRoot->icon ?: 'package'" :size="16"/></span>
                        <div><small>Раздел</small><strong>{{ $categoryRoot->name }}</strong></div>
                    </div>

                    @if($activeCategory?->parent)
                        <a href="{{ route($routeName, ['category' => $categoryRoot->slug]) }}" class="catalog-category-parent"><x-icon name="chevron-left" :size="13"/>Все товары раздела</a>
                    @endif

                    @if($categoryRoot->children->isNotEmpty())
                        <div class="catalog-subcategory-list">
                            @if(!($activeCategory?->parent))
                                <a href="{{ route($routeName, ['category' => $categoryRoot->slug]) }}" class="catalog-subcategory-link active" aria-current="page">Все товары раздела</a>
                            @endif
                            @foreach($categoryRoot->children as $child)
                                <a
                                    href="{{ route($routeName, ['category' => $child->slug]) }}"
                                    class="catalog-subcategory-link {{ $activeCategory?->id === $child->id ? 'active' : '' }}"
                                    @if($activeCategory?->id === $child->id) aria-current="page" @endif
                                >
                                    <x-icon :name="$child->icon ?: 'chevron-right'" :size="13"/>
                                    <span>{{ $child->name }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p class="catalog-category-hint">Выберите раздел, чтобы увидеть его характеристики и подходящие фильтры.</p>
                @endif

                <details class="catalog-all-sections" @if(!$categoryRoot) open @endif>
                    <summary><span>Все разделы</span><x-icon name="chevron-down" :size="14"/></summary>
                    <div class="catalog-root-category-list">
                        @foreach($rootCategories as $cat)
                            <a
                                href="{{ route($routeName, ['category' => $cat->slug]) }}"
                                class="{{ $categoryRoot?->id === $cat->id ? 'active' : '' }}"
                                @if($activeCategory?->id === $cat->id) aria-current="page" @endif
                            >
                                <span><x-icon :name="$cat->icon ?: 'package'" :size="14"/></span>
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </details>
            </nav>

            <div class="catalog-filter-section">
                <div class="catalog-side-section-title"><x-icon name="filter" :size="15"/><span>Параметры</span></div>
            </div>

            <form id="catalog-filter-form" method="GET" action="{{ route($routeName) }}">
            @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif
            @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
            @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
            @if(request('per_page'))<input type="hidden" name="per_page" value="{{ request('per_page') }}">@endif

            <x-filter-group title="Покупка" icon="check" :active="true" group-id="catalog-filter-purchase" :selected-count="$purchaseFilterCount">
                <label class="filter-row"><input type="checkbox" name="in_stock" value="1" @checked(request()->boolean('in_stock'))><span class="filter-row-label">Только в наличии</span></label>
                <label class="filter-row"><input type="checkbox" name="on_sale" value="1" @checked(request()->boolean('on_sale'))><span class="filter-row-label">Со скидкой</span></label>
                @if($routeName==='marketplace.catalog')
                    <label class="filter-row"><input type="checkbox" name="vip" value="1" @checked(request()->boolean('vip'))><span class="filter-row-label">Рекомендуемые товары</span></label>
                    <label class="filter-row"><input type="radio" name="condition" value="new" @checked(request('condition')==='new')><span class="filter-row-label">Новое</span></label>
                    <label class="filter-row"><input type="radio" name="condition" value="used" @checked(request('condition')==='used')><span class="filter-row-label">Б/У</span></label>
                @endif
            </x-filter-group>

            <x-filter-group title="Цена, TMT" icon="cash" :active="true" group-id="catalog-filter-price" :selected-count="$hasPriceFilter ? 1 : 0">
                <div class="price-input-grid">
                    <label class="price-input-field"><span>От</span><input class="input" type="number" name="min_price" value="{{ request('min_price') }}" min="0" step="1" inputmode="numeric" aria-label="Минимальная цена"></label>
                    <span class="price-input-separator">&mdash;</span>
                    <label class="price-input-field"><span>До</span><input class="input" type="number" name="max_price" value="{{ request('max_price') }}" min="0" step="1" inputmode="numeric" aria-label="Максимальная цена"></label>
                </div>

                @if($priceCeiling > 0)
                    <div class="price-range" data-price-range data-ceiling="{{ $priceCeiling }}">
                        <div class="price-range-track"><span></span></div>
                        <input type="range" min="0" max="{{ $priceCeiling }}" value="{{ min($priceCeiling, max(0, (int) request('min_price', 0))) }}" step="1" data-price-range-min aria-label="Нижняя граница цены">
                        <input type="range" min="0" max="{{ $priceCeiling }}" value="{{ min($priceCeiling, max(0, (int) request('max_price', $priceCeiling))) }}" step="1" data-price-range-max aria-label="Верхняя граница цены">
                    </div>
                @endif

                @if(count($pricePresets))
                    <div class="quick-price-grid">
                        @foreach($pricePresets as $preset)
                            @php
                                $presetMin = $preset[1] ?? '';
                                $presetMax = $preset[2] ?? '';
                                $presetActive = (string) request('min_price', '') === (string) $presetMin
                                    && (string) request('max_price', '') === (string) $presetMax;
                            @endphp
                            <button type="button" data-min="{{ $presetMin }}" data-max="{{ $presetMax }}" aria-pressed="{{ $presetActive ? 'true' : 'false' }}" class="{{ $presetActive ? 'active' : '' }}">{{ $preset[0] }}</button>
                        @endforeach
                    </div>
                @endif
                @if($priceCeiling)<div class="price-caption">Максимальная цена в разделе: {{ number_format($priceCeiling,0,'',' ') }} TMT</div>@endif
            </x-filter-group>

            @foreach($attributeFacets ?? [] as $facet)
                @php
                    $selectedValues = array_values(array_filter((array) request("attr.{$facet['attribute']->id}", []), fn ($value) => filled($value)));
                    $facetValues = $facet['values']->sortByDesc(fn ($item) => in_array($item->value, $selectedValues, true));
                @endphp
                <x-filter-group :title="$facet['attribute']->name" :icon="$facet['attribute']->filter_icon" :active="!empty($selectedValues)" :group-id="'catalog-filter-attribute-'.$facet['attribute']->id" :selected-count="count($selectedValues)">
                    @if($facetValues->count() > 8)
                        <label class="filter-option-search"><x-icon name="search" :size="14"/><input type="search" data-facet-search placeholder="Найти значение" aria-label="Поиск: {{ $facet['attribute']->name }}"></label>
                    @endif
                    @foreach($facetValues as $item)
                        <label class="filter-row">
                            <input type="checkbox" name="attr[{{ $facet['attribute']->id }}][]" value="{{ $item->value }}" {{ in_array($item->value, $selectedValues) ? 'checked' : '' }}>
                            <span class="filter-row-label" title="{{ $item->value }}">{{ $item->value }}</span>
                            <span class="filter-count">{{ $item->aggregate }}</span>
                        </label>
                    @endforeach
                    <div class="filter-search-empty" hidden>Ничего не найдено</div>
                </x-filter-group>
            @endforeach
            </form>
        </div>

        <div class="catalog-sidebar-footer">
            @if($activeFilterCount > 0)
                <a href="{{ $resetUrl }}" class="catalog-footer-reset" data-filter-reset>Сбросить</a>
            @endif
            <button type="submit" form="catalog-filter-form" class="btn-accent catalog-filter-submit"><x-icon name="filter" :size="15"/><span data-filter-submit-label>Показать {{ number_format($products->total(), 0, '', ' ') }} товаров</span></button>
        </div>
    </aside>

    <div class="catalog-results">
        <div class="catalog-mobile-toolbar">
            <button type="button" class="catalog-filter-trigger" data-filter-open aria-haspopup="dialog" aria-controls="catalog-filter-drawer" aria-expanded="false">
                <x-icon name="filter" :size="16"/>
                <span>Фильтры</span>
                @if($activeFilterCount > 0)<strong>{{ $activeFilterCount }}</strong>@endif
            </button>
            @if($categoryRoot)
                <button type="button" class="catalog-category-trigger" data-filter-open aria-haspopup="dialog" aria-controls="catalog-filter-drawer" aria-expanded="false"><x-icon name="grid" :size="15"/><span>{{ $activeCategory?->name ?? $categoryRoot->name }}</span></button>
            @endif
        </div>

        <div class="catalog-results-header">
            <div>
                <h1 class="catalog-results-title">{{ $activeCategory->name ?? 'Все товары' }}</h1>
                <div class="catalog-results-count">{{ number_format($products->total(), 0, '', ' ') }} товаров</div>
            </div>
            <form method="GET" action="{{ route($routeName) }}" class="catalog-sort-form">
                @include('storefront.partials.hidden-filter-inputs', ['data' => request()->except(['sort','per_page','page','partial']), 'prefix' => null])
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
                <div class="sort-dropdown catalog-per-page" data-input="per_page"><button type="button" class="sort-trigger" aria-expanded="false"><span>Показывать: {{ $currentPerPage }}</span><x-icon name="chevron-down" :size="14"/></button><div class="sort-menu">@foreach($perPageOptions as $option)<button type="button" class="sort-option {{ $currentPerPage===$option?'active':'' }}" data-value="{{ $option }}"><span>{{ $option }}</span><span class="sort-check"><x-icon name="check" :size="14"/></span></button>@endforeach</div></div>
            </form>
        </div>

        @if($activeFilterCount > 0)
            <div class="active-filter-bar" data-active-filters aria-label="Выбранные фильтры">
                <span class="active-filter-label">Выбрано:</span>
                @if($hasPriceFilter)
                    @php
                        $removePriceQuery = request()->except(['page', 'min_price', 'max_price']);
                    @endphp
                    <a href="{{ route($routeName, $removePriceQuery) }}" class="active-filter-chip">
                        <span>Цена: {{ request('min_price', '0') }}–{{ request('max_price', number_format($priceCeiling, 0, '', ' ')) }} TMT</span><x-icon name="x" :size="12"/>
                    </a>
                @endif
                @if(request()->boolean('in_stock'))
                    <a href="{{ route($routeName, request()->except(['page', 'in_stock'])) }}" class="active-filter-chip"><span>В наличии</span><x-icon name="x" :size="12"/></a>
                @endif
                @if(request()->boolean('on_sale'))
                    <a href="{{ route($routeName, request()->except(['page', 'on_sale'])) }}" class="active-filter-chip"><span>Со скидкой</span><x-icon name="x" :size="12"/></a>
                @endif
                @if($routeName === 'marketplace.catalog' && request()->boolean('vip'))
                    <a href="{{ route($routeName, request()->except(['page', 'vip'])) }}" class="active-filter-chip"><span>Рекомендуемые</span><x-icon name="x" :size="12"/></a>
                @endif
                @if($routeName === 'marketplace.catalog' && in_array(request('condition'), ['new', 'used'], true))
                    <a href="{{ route($routeName, request()->except(['page', 'condition'])) }}" class="active-filter-chip"><span>{{ request('condition') === 'used' ? 'Б/У' : 'Новое' }}</span><x-icon name="x" :size="12"/></a>
                @endif
                @foreach($attributeSelections as $attributeId => $values)
                    @foreach($values as $selectedValue)
                        @php
                            $removeAttributeQuery = request()->except('page');
                            $remainingAttributeValues = array_values(array_filter(
                                (array) ($removeAttributeQuery['attr'][$attributeId] ?? []),
                                fn ($value) => (string) $value !== (string) $selectedValue
                            ));
                            if ($remainingAttributeValues) {
                                $removeAttributeQuery['attr'][$attributeId] = $remainingAttributeValues;
                            } else {
                                unset($removeAttributeQuery['attr'][$attributeId]);
                                if (empty($removeAttributeQuery['attr'])) unset($removeAttributeQuery['attr']);
                            }
                        @endphp
                        <a href="{{ route($routeName, $removeAttributeQuery) }}" class="active-filter-chip"><span>{{ $facetLabels->get($attributeId, 'Характеристика') }}: {{ $selectedValue }}</span><x-icon name="x" :size="12"/></a>
                    @endforeach
                @endforeach
                <a href="{{ $resetUrl }}" class="active-filter-clear" data-filter-reset>Очистить все</a>
            </div>
        @endif

        @if($products->isEmpty())
            <div style="padding:60px 0;text-align:center;color:var(--text-faint);font-size:15px;font-weight:600;">Товары не найдены. Попробуйте изменить фильтры.</div>
        @else
            <div class="catalog-product-grid" id="product-grid">
                @include('storefront.partials.product-cards', ['products' => $products])
            </div>

            <div id="infinite-scroll-area" data-next-url="{{ $products->nextPageUrl() }}" style="margin-top:28px;{{ $products->hasMorePages() ? '' : 'display:none' }}">
                <div class="catalog-product-grid" id="skeleton-grid" style="display:none;margin-bottom:20px;"></div>
                <button type="button" id="load-more-btn" class="btn-ghost" style="width:100%;height:48px;">Показать ещё товары</button>
            </div>
        @endif
    </div>
</div>
<style>
.catalog-layout{--catalog-sticky-top:120px;padding:12px 24px 40px;display:grid;grid-template-columns:296px minmax(0,1fr);gap:24px;align-items:start}
.catalog-layout--marketplace{--catalog-sticky-top:16px}
.catalog-sidebar{position:sticky;top:var(--catalog-sticky-top);height:min(820px,calc(100dvh - var(--catalog-sticky-top) - 16px));display:grid;grid-template-rows:auto minmax(0,1fr) auto;overflow:hidden;border:1px solid var(--border);border-radius:18px;background:var(--surface);box-shadow:0 14px 34px rgba(10,20,45,.07)}
:root[data-theme="light"] .catalog-sidebar{box-shadow:0 14px 34px rgba(22,31,55,.07)}
.catalog-sidebar-header{padding:16px;border-bottom:1px solid var(--border);background:var(--surface)}
.catalog-sidebar-title-row{display:flex;align-items:center;gap:11px;min-width:0}
.catalog-sidebar-icon{width:38px;height:38px;border-radius:11px;display:grid;place-items:center;background:var(--accent-soft);color:var(--accent);flex:0 0 auto}
.catalog-sidebar-title-copy{min-width:0;flex:1}.catalog-sidebar-title{display:flex;align-items:center;gap:7px;font-size:17px;line-height:22px;font-weight:900}.catalog-sidebar-title>span{min-width:22px;height:22px;padding:0 6px;border-radius:11px;display:inline-flex;align-items:center;justify-content:center;background:var(--accent);color:var(--accent-text);font-size:11px;font-weight:900}.catalog-sidebar-subtitle{margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-faint);font-size:11px;font-weight:600}
.catalog-sidebar-close{display:none;width:38px;height:38px;padding:0;border:1px solid var(--border);border-radius:10px;background:var(--bg);color:var(--text-muted);place-items:center;flex:0 0 auto}
.catalog-reset-link{width:max-content;margin-top:11px;display:flex;align-items:center;gap:6px;color:var(--text-faint);font-size:12px;font-weight:700}.catalog-reset-link:hover{color:var(--accent)}
.catalog-sidebar-body{min-height:0;padding:0 16px 18px;overflow-x:hidden;overflow-y:auto;overscroll-behavior:contain;scrollbar-gutter:stable;scrollbar-width:thin;scrollbar-color:var(--border-strong) transparent}
.catalog-sidebar-body::-webkit-scrollbar{width:5px}.catalog-sidebar-body::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:10px}.catalog-sidebar-body::-webkit-scrollbar-track{background:transparent}
.catalog-sidebar-footer{display:flex;align-items:center;gap:8px;padding:12px 16px 14px;border-top:1px solid var(--border);background:var(--surface)}
.catalog-filter-submit{height:46px;min-width:0;flex:1;font-size:13px;white-space:nowrap}.catalog-filter-submit span{overflow:hidden;text-overflow:ellipsis}
.catalog-footer-reset{height:46px;padding:0 12px;display:flex;align-items:center;justify-content:center;border:1px solid var(--border);border-radius:10px;background:var(--bg);color:var(--text-muted);font-size:12px;font-weight:800}.catalog-footer-reset:hover{background:var(--surface-hover);color:var(--text)}

.catalog-side-section-title{display:flex;align-items:center;gap:8px;margin:18px 2px 11px;color:var(--text);font-size:13px;font-weight:900;letter-spacing:.01em}.catalog-side-section-title svg{color:var(--accent)}
.catalog-category-current{display:flex;align-items:center;gap:10px;padding:11px;border:1px solid color-mix(in oklch,var(--accent) 20%,var(--border));border-radius:12px;background:var(--accent-soft)}.catalog-category-current>span{width:30px;height:30px;border-radius:9px;display:grid;place-items:center;background:var(--surface);color:var(--accent);flex:0 0 auto}.catalog-category-current div{min-width:0}.catalog-category-current small{display:block;color:var(--text-faint);font-size:10px;font-weight:700}.catalog-category-current strong{display:block;margin-top:1px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text);font-size:13px;font-weight:900}
.catalog-category-parent{min-height:38px;margin-top:4px;padding:8px 9px;display:flex;align-items:center;gap:6px;border-radius:9px;color:var(--text-muted);font-size:12px;font-weight:700}.catalog-category-parent:hover{background:var(--surface-hover);color:var(--text)}
.catalog-subcategory-list{display:grid;gap:2px;margin-top:5px}.catalog-subcategory-link{min-width:0;min-height:38px;padding:8px 9px;display:flex;align-items:center;gap:8px;border-radius:9px;color:var(--text-muted);font-size:13px;font-weight:650}.catalog-subcategory-link svg{color:var(--text-faint);flex:0 0 auto}.catalog-subcategory-link span{min-width:0;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.catalog-subcategory-link:hover{background:var(--surface-hover);color:var(--text)}.catalog-subcategory-link.active{background:var(--accent-soft);color:var(--accent);font-weight:800}.catalog-subcategory-link.active svg{color:var(--accent)}
.catalog-category-hint{margin:0;padding:11px;border-radius:10px;background:var(--bg);color:var(--text-faint);font-size:12px;line-height:1.45}
.catalog-all-sections{margin-top:8px;border-top:1px solid var(--border);padding-top:7px}.catalog-all-sections summary{min-height:40px;padding:7px 5px;display:flex;align-items:center;justify-content:space-between;gap:8px;border-radius:8px;color:var(--text-muted);font-size:12px;font-weight:800;cursor:pointer;list-style:none}.catalog-all-sections summary::-webkit-details-marker{display:none}.catalog-all-sections summary:hover{background:var(--surface-hover);color:var(--text)}.catalog-all-sections summary svg{transition:transform .2s}.catalog-all-sections[open] summary svg{transform:rotate(180deg)}
.catalog-root-category-list{display:grid;gap:2px;padding:2px 0 5px}.catalog-root-category-list a{min-height:38px;padding:7px 8px;display:flex;align-items:center;gap:8px;border-radius:9px;color:var(--text-muted);font-size:12.5px;font-weight:700}.catalog-root-category-list a>span{width:27px;height:27px;display:grid;place-items:center;border-radius:8px;background:var(--bg);color:var(--text-faint);flex:0 0 auto}.catalog-root-category-list a:hover{background:var(--surface-hover);color:var(--text)}.catalog-root-category-list a.active{background:var(--accent-soft);color:var(--accent)}.catalog-root-category-list a.active>span{background:var(--surface);color:var(--accent)}
.catalog-filter-section{margin-top:15px;border-top:1px solid var(--border)}

.filter-group{border-top:1px solid var(--border)}.filter-group:first-of-type{border-top:none}
.filter-group-head{width:100%;min-height:46px;padding:8px 5px;display:flex;align-items:center;justify-content:space-between;gap:10px;border:0;border-radius:9px;background:none;color:var(--text);font:inherit;text-align:left;cursor:pointer;transition:background .15s}
.filter-group-head:hover{background:var(--surface-hover)}
.filter-group-head-label{min-width:0;display:flex;align-items:center;gap:8px;color:var(--text);font-size:13.5px;font-weight:850;letter-spacing:.005em}.filter-group-head-label>svg{color:var(--text-faint);flex:0 0 auto}.filter-group.open .filter-group-head-label>svg{color:var(--accent)}
.filter-group-selected-count{min-width:20px;height:20px;padding:0 6px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;background:var(--accent-soft);color:var(--accent);font-size:10px;font-weight:900}
.filter-group-chevron{color:var(--text-faint);transition:transform .22s cubic-bezier(.4,0,.2,1);flex:0 0 auto}.filter-group.open .filter-group-chevron{transform:rotate(180deg);color:var(--accent)}
.filter-group-body{display:grid;grid-template-rows:0fr;transition:grid-template-rows .22s cubic-bezier(.4,0,.2,1)}.filter-group.open .filter-group-body{grid-template-rows:1fr}.filter-group-body-inner{min-height:0;overflow:hidden}.filter-group-body-content{display:flex;flex-direction:column;gap:2px;padding:2px 2px 15px}
.filter-row{position:relative;min-height:40px;padding:8px 7px;display:flex;align-items:center;gap:10px;border-radius:9px;color:var(--text-muted);font-size:13px;font-weight:650;cursor:pointer;transition:background .15s,color .15s;flex-shrink:0}.filter-row:hover{background:var(--surface-hover);color:var(--text)}
.filter-row input{appearance:none;-webkit-appearance:none;width:18px;height:18px;margin:0;display:grid;place-items:center;border:1.5px solid var(--border-strong);border-radius:5px;background:var(--bg);cursor:pointer;transition:background .15s,border-color .15s,box-shadow .15s;flex:0 0 auto}.filter-row input:checked{border-color:var(--accent);background:var(--accent)}.filter-row input[type=checkbox]:checked::after{content:'';width:4px;height:8px;border:solid var(--accent-text);border-width:0 2px 2px 0;transform:translateY(-1px) rotate(45deg)}.filter-row input:focus-visible{outline:none;box-shadow:0 0 0 3px var(--accent-soft)}
.filter-row-label{min-width:0;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.filter-row:has(input:checked) .filter-row-label{color:var(--text);font-weight:750}.filter-count{padding-left:6px;color:var(--text-faint);font-size:11px;font-weight:700;flex:0 0 auto}.filter-row:has(input:checked) .filter-count{color:var(--accent)}
.filter-row-extra{display:none}.filter-group.show-all .filter-row-extra,.filter-group.is-searching .filter-row-extra{display:flex}.filter-row.filter-search-hidden{display:none!important}
.filter-more-toggle{min-height:38px;margin-top:2px;padding:7px;border:0;border-radius:8px;background:none;color:var(--accent);font-size:12px;font-weight:850;text-align:left;cursor:pointer}.filter-more-toggle:hover{background:var(--accent-soft)}
.filter-option-search{height:39px;margin:2px 2px 6px;padding:0 10px;display:flex;align-items:center;gap:8px;border:1px solid var(--border);border-radius:9px;background:var(--bg);color:var(--text-faint)}.filter-option-search:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}.filter-option-search input{min-width:0;width:100%;height:100%;padding:0;border:0;outline:0;background:transparent;color:var(--text);font-size:12px}.filter-search-empty{padding:10px 7px;color:var(--text-faint);font-size:12px}

.price-input-grid{display:grid;grid-template-columns:minmax(0,1fr) auto minmax(0,1fr);align-items:end;gap:7px}.price-input-field{position:relative;display:block}.price-input-field>span{position:absolute;left:10px;top:50%;z-index:1;transform:translateY(-50%);color:var(--text-faint);font-size:11px;font-weight:700;pointer-events:none}.price-input-field .input{height:42px;padding:0 8px 0 31px;border-radius:9px;background:var(--bg);font-size:12px}.price-input-separator{padding-bottom:11px;color:var(--text-faint)}
.price-range{position:relative;height:28px;margin:10px 2px 1px}.price-range-track{position:absolute;left:7px;right:7px;top:12px;height:4px;border-radius:4px;background:var(--border)}.price-range-track span{position:absolute;top:0;bottom:0;left:var(--price-start,0%);right:calc(100% - var(--price-end,100%));border-radius:4px;background:var(--accent)}.price-range input[type=range]{position:absolute;inset:0;width:100%;height:28px;margin:0;background:transparent;pointer-events:none;-webkit-appearance:none;appearance:none}.price-range input[type=range]::-webkit-slider-runnable-track{height:4px;background:transparent}.price-range input[type=range]::-webkit-slider-thumb{width:18px;height:18px;margin-top:-7px;border:3px solid var(--surface);border-radius:50%;background:var(--accent);box-shadow:0 0 0 1px color-mix(in oklch,var(--accent) 35%,var(--border));pointer-events:auto;-webkit-appearance:none;appearance:none;cursor:grab}.price-range input[type=range]::-moz-range-track{height:4px;background:transparent}.price-range input[type=range]::-moz-range-thumb{width:13px;height:13px;border:3px solid var(--surface);border-radius:50%;background:var(--accent);box-shadow:0 0 0 1px color-mix(in oklch,var(--accent) 35%,var(--border));pointer-events:auto;cursor:grab}
.quick-price-grid{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:7px}.quick-price-grid button{min-height:36px;padding:6px;border:1px solid var(--border);border-radius:9px;background:var(--bg);color:var(--text-muted);font-size:10.5px;font-weight:700;transition:.15s}.quick-price-grid button:last-child:nth-child(odd){grid-column:1/-1}.quick-price-grid button:hover,.quick-price-grid button.active,.quick-price-grid button[aria-pressed="true"]{border-color:color-mix(in oklch,var(--accent) 50%,var(--border));background:var(--accent-soft);color:var(--accent)}.price-caption{margin-top:8px;color:var(--text-faint);font-size:11px;line-height:1.35}

.catalog-filter-backdrop{position:fixed;inset:0;z-index:160;background:rgba(8,15,30,.45);backdrop-filter:blur(3px);opacity:0;visibility:hidden;pointer-events:none;transition:opacity .24s,visibility .24s}.catalog-mobile-toolbar{display:none}.catalog-results{min-width:0}.catalog-results-header{margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;gap:18px}.catalog-results-title{margin:0;font-size:24px;line-height:1.2;font-weight:900}.catalog-results-count{margin-top:3px;color:var(--text-faint);font-size:13px;font-weight:600}.catalog-sort-form{display:flex;align-items:center;gap:10px}.catalog-per-page{min-width:150px}
.active-filter-bar{margin:0 0 18px;display:flex;align-items:center;gap:7px;flex-wrap:wrap}.active-filter-label{margin-right:2px;color:var(--text-faint);font-size:12px;font-weight:700}.active-filter-chip{min-height:34px;padding:6px 8px 6px 10px;display:inline-flex;align-items:center;gap:6px;border:1px solid color-mix(in oklch,var(--accent) 25%,var(--border));border-radius:9px;background:var(--accent-soft);color:var(--accent);font-size:12px;font-weight:750}.active-filter-chip:hover{border-color:var(--accent);background:color-mix(in oklch,var(--accent) 15%,transparent);color:var(--accent)}.active-filter-chip svg{flex:0 0 auto}.active-filter-clear{min-height:34px;padding:6px 7px;display:inline-flex;align-items:center;color:var(--text-faint);font-size:12px;font-weight:800}.active-filter-clear:hover{color:var(--danger)}
.catalog-product-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
.pcard-skeleton{background:var(--surface);border:1px solid var(--border);border-radius:16px;overflow:hidden;display:flex;flex-direction:column}
.pcard-skeleton .sk-img{aspect-ratio:1/1.12}
.pcard-skeleton .sk-body{padding:14px;display:flex;flex-direction:column;gap:10px;flex:1}
.pcard-skeleton .sk-line{height:11px;border-radius:6px}
.pcard-skeleton .sk-line.w40{width:42%}
.pcard-skeleton .sk-line.w60{width:68%;height:15px}
.pcard-skeleton .sk-btn{height:40px;border-radius:10px;margin-top:auto}
.pcard-skeleton .sk-img,.pcard-skeleton .sk-line,.pcard-skeleton .sk-btn{background:linear-gradient(90deg,var(--surface-hover) 25%,color-mix(in oklch,var(--surface-hover) 45%,var(--surface)) 50%,var(--surface-hover) 75%);background-size:400% 100%;animation:pcard-shimmer 1.6s ease-in-out infinite}
@keyframes pcard-shimmer{0%{background-position:100% 0}100%{background-position:0 0}}

@media(min-width:1024px) and (max-width:1080px){.catalog-layout--storefront{--catalog-sticky-top:16px}}
@media(max-width:1199px){.catalog-layout{grid-template-columns:280px minmax(0,1fr)}.catalog-product-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:1023px){
    .catalog-layout{padding-top:4px;grid-template-columns:minmax(0,1fr)}
    .catalog-sidebar{position:fixed;z-index:161;left:0;top:0;bottom:0;width:min(420px,calc(100vw - 32px));height:100dvh;max-height:none;border-width:0 1px 0 0;border-radius:0 20px 20px 0;box-shadow:22px 0 60px rgba(5,12,30,.2);transform:translateX(-103%);visibility:hidden;transition:transform .3s cubic-bezier(.22,.8,.25,1),visibility .3s}
    .catalog-sidebar-close{display:grid}
    .catalog-filter-backdrop{display:block}
    body.catalog-filters-open{overflow:hidden}
    body.catalog-filters-open .catalog-sidebar{transform:translateX(0);visibility:visible}
    body.catalog-filters-open .catalog-filter-backdrop{opacity:1;visibility:visible;pointer-events:auto}
    .catalog-mobile-toolbar{margin-bottom:14px;display:flex;gap:8px}
    .catalog-filter-trigger,.catalog-category-trigger{min-height:44px;padding:0 13px;display:flex;align-items:center;gap:8px;border:1px solid var(--border);border-radius:11px;background:var(--surface);color:var(--text);font-size:13px;font-weight:800}.catalog-filter-trigger{border-color:color-mix(in oklch,var(--accent) 25%,var(--border));color:var(--accent)}.catalog-filter-trigger strong{min-width:20px;height:20px;padding:0 5px;display:grid;place-items:center;border-radius:10px;background:var(--accent);color:var(--accent-text);font-size:10px}.catalog-category-trigger{min-width:0;max-width:55%;color:var(--text-muted)}.catalog-category-trigger span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
}
@media(max-width:720px){
    .catalog-results-header{align-items:flex-start;flex-direction:column}.catalog-sort-form{width:100%}.catalog-sort-form>.sort-dropdown:first-of-type{min-width:0;flex:1}.catalog-per-page{display:none}.sort-trigger{height:42px}.sort-menu{left:0;right:auto;max-width:calc(100vw - 28px)}
}
@media(max-width:560px){
    .catalog-layout{padding-left:14px;padding-right:14px}.catalog-sidebar{width:100%;border:0;border-radius:0}.catalog-sidebar-header{padding:14px 16px}.catalog-sidebar-body{padding-left:16px;padding-right:16px}.catalog-sidebar-footer{padding-bottom:max(14px,env(safe-area-inset-bottom))}.catalog-product-grid{grid-template-columns:1fr}.catalog-results-title{font-size:22px}.catalog-mobile-toolbar{overflow-x:auto;scrollbar-width:none}.catalog-mobile-toolbar::-webkit-scrollbar{display:none}.catalog-filter-trigger,.catalog-category-trigger{flex:0 0 auto}.active-filter-bar{margin-bottom:14px}.active-filter-label{flex-basis:100%}
}
@media(prefers-reduced-motion:reduce){.catalog-sidebar,.catalog-filter-backdrop,.filter-group-body,.filter-group-chevron,.catalog-all-sections summary svg{transition:none}.pcard-skeleton .sk-img,.pcard-skeleton .sk-line,.pcard-skeleton .sk-btn{animation:none}}
.catalog-sidebar button:focus-visible,.catalog-sidebar a:focus-visible,.catalog-filter-trigger:focus-visible,.catalog-category-trigger:focus-visible,.active-filter-bar a:focus-visible{outline:2px solid var(--accent);outline-offset:2px}
</style>
<script>
(function(){
    var LIMIT = 6;
    var filterForm = document.getElementById('catalog-filter-form');
    var submitLabel = document.querySelector('[data-filter-submit-label]');

    function markFiltersDirty(){
        if (submitLabel) submitLabel.textContent = 'Применить фильтры';
    }

    document.querySelectorAll('[data-filter-group]').forEach(function(group){
        var trigger = group.querySelector('[data-filter-group-trigger]');
        var panel = group.querySelector('[data-filter-group-panel]');
        var content = group.querySelector('.filter-group-body-content');
        var rows = Array.prototype.slice.call(group.querySelectorAll('.filter-row'));
        var selectedRows = rows.filter(function(row){
            var input = row.querySelector('input');
            return input && input.checked;
        });
        var unselectedRows = rows.filter(function(row){ return selectedRows.indexOf(row) === -1; });
        var visibleUnselectedCount = Math.max(0, LIMIT - selectedRows.length);
        var extraRows = unselectedRows.slice(visibleUnselectedCount);

        function setExpanded(expanded){
            group.classList.toggle('open', expanded);
            trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            panel.setAttribute('aria-hidden', expanded ? 'false' : 'true');
            if (expanded) panel.removeAttribute('inert');
            else panel.setAttribute('inert', '');
        }

        setExpanded(trigger.getAttribute('aria-expanded') === 'true');
        trigger.addEventListener('click', function(){
            setExpanded(trigger.getAttribute('aria-expanded') !== 'true');
        });

        if (extraRows.length) {
            extraRows.forEach(function(row){ row.classList.add('filter-row-extra'); });
            var toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'filter-more-toggle';
            toggle.setAttribute('aria-expanded', 'false');
            toggle.setAttribute('aria-controls', panel.id);
            var hiddenCount = extraRows.length;
            toggle.textContent = 'Показать ещё ' + hiddenCount + ' вариантов';
            toggle.addEventListener('click', function(){
                var expanded = group.classList.toggle('show-all');
                toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                toggle.textContent = expanded ? 'Скрыть дополнительные' : 'Показать ещё ' + hiddenCount + ' вариантов';
            });
            content.appendChild(toggle);
        }

        var search = group.querySelector('[data-facet-search]');
        var empty = group.querySelector('.filter-search-empty');
        if (search) {
            search.addEventListener('input', function(){
                var query = search.value.trim().toLocaleLowerCase();
                var matches = 0;
                group.classList.toggle('is-searching', query.length > 0);
                rows.forEach(function(row){
                    var label = row.querySelector('.filter-row-label');
                    var match = !query || (label && label.textContent.toLocaleLowerCase().indexOf(query) !== -1);
                    row.classList.toggle('filter-search-hidden', !match);
                    if (match) matches++;
                });
                if (toggle) toggle.hidden = query.length > 0;
                if (empty) empty.hidden = !query || matches > 0;
            });
        }
    });

    var priceRange = document.querySelector('[data-price-range]');
    var minPriceInput = filterForm && filterForm.querySelector('[name="min_price"]');
    var maxPriceInput = filterForm && filterForm.querySelector('[name="max_price"]');
    var presetButtons = filterForm ? Array.prototype.slice.call(filterForm.querySelectorAll('.quick-price-grid button')) : [];

    function clearPricePresets(){
        presetButtons.forEach(function(button){
            button.classList.remove('active');
            button.setAttribute('aria-pressed', 'false');
        });
    }

    function updatePriceRange(){
        if (!priceRange || !minPriceInput || !maxPriceInput) return;
        var ceiling = Number(priceRange.dataset.ceiling) || 1;
        var minSlider = priceRange.querySelector('[data-price-range-min]');
        var maxSlider = priceRange.querySelector('[data-price-range-max]');
        var rawMin = minPriceInput.value === '' ? 0 : Number(minPriceInput.value);
        var rawMax = maxPriceInput.value === '' ? ceiling : Number(maxPriceInput.value);
        var low = Math.max(0, Math.min(ceiling, Number.isFinite(rawMin) ? rawMin : 0));
        var high = Math.max(0, Math.min(ceiling, Number.isFinite(rawMax) ? rawMax : ceiling));
        minSlider.value = low;
        maxSlider.value = high;
        priceRange.style.setProperty('--price-start', Math.min(low, high) / ceiling * 100 + '%');
        priceRange.style.setProperty('--price-end', Math.max(low, high) / ceiling * 100 + '%');
    }

    if (priceRange && minPriceInput && maxPriceInput) {
        var minRangeInput = priceRange.querySelector('[data-price-range-min]');
        var maxRangeInput = priceRange.querySelector('[data-price-range-max]');
        minRangeInput.addEventListener('input', function(){
            var value = Math.min(Number(minRangeInput.value), Number(maxRangeInput.value));
            minRangeInput.value = value;
            minPriceInput.value = value;
            clearPricePresets();
            updatePriceRange();
            markFiltersDirty();
        });
        maxRangeInput.addEventListener('input', function(){
            var value = Math.max(Number(maxRangeInput.value), Number(minRangeInput.value));
            maxRangeInput.value = value;
            maxPriceInput.value = value;
            clearPricePresets();
            updatePriceRange();
            markFiltersDirty();
        });
        updatePriceRange();
    }

    presetButtons.forEach(function(button){
        button.addEventListener('click', function(){
            minPriceInput.value = button.dataset.min || '';
            maxPriceInput.value = button.dataset.max || '';
            clearPricePresets();
            button.classList.add('active');
            button.setAttribute('aria-pressed', 'true');
            updatePriceRange();
            markFiltersDirty();
        });
    });

    if (minPriceInput && maxPriceInput) {
        [minPriceInput, maxPriceInput].forEach(function(input){
            input.addEventListener('input', function(){
                maxPriceInput.setCustomValidity('');
                clearPricePresets();
                updatePriceRange();
                markFiltersDirty();
            });
        });
    }

    if (filterForm) {
        filterForm.addEventListener('change', function(event){
            if (event.target.name) markFiltersDirty();
        });
        filterForm.addEventListener('submit', function(event){
            var hasMin = minPriceInput && minPriceInput.value !== '';
            var hasMax = maxPriceInput && maxPriceInput.value !== '';
            if (hasMin && hasMax && Number(minPriceInput.value) > Number(maxPriceInput.value)) {
                event.preventDefault();
                maxPriceInput.setCustomValidity('Максимальная цена должна быть не меньше минимальной.');
                maxPriceInput.reportValidity();
            } else if (maxPriceInput) {
                maxPriceInput.setCustomValidity('');
            }
        });
    }

    document.querySelectorAll('.sort-dropdown').forEach(function(dropdown, index){
        var trigger = dropdown.querySelector('.sort-trigger');
        var menu = dropdown.querySelector('.sort-menu');
        var form = dropdown.closest('form');
        var input = form.querySelector('input[name="' + dropdown.dataset.input + '"]');
        menu.id = 'catalog-sort-menu-' + index;
        menu.setAttribute('role', 'menu');
        trigger.setAttribute('aria-haspopup', 'menu');
        trigger.setAttribute('aria-controls', menu.id);

        function closeSort(restoreFocus){
            dropdown.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
            if (restoreFocus) trigger.focus();
        }

        trigger.addEventListener('click', function(event){
            event.stopPropagation();
            document.querySelectorAll('.sort-dropdown.open').forEach(function(other){
                if (other !== dropdown) {
                    other.classList.remove('open');
                    other.querySelector('.sort-trigger').setAttribute('aria-expanded', 'false');
                }
            });
            var open = dropdown.classList.toggle('open');
            trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        dropdown.querySelectorAll('.sort-option').forEach(function(option){
            option.setAttribute('role', 'menuitemradio');
            option.setAttribute('aria-checked', option.classList.contains('active') ? 'true' : 'false');
            option.addEventListener('click', function(){
                input.value = option.dataset.value;
                closeSort(false);
                form.submit();
            });
        });
        dropdown.addEventListener('keydown', function(event){
            if (event.key === 'Escape' && dropdown.classList.contains('open')) {
                event.preventDefault();
                closeSort(true);
            }
        });
    });

    document.addEventListener('click', function(){
        document.querySelectorAll('.sort-dropdown.open').forEach(function(dropdown){
            dropdown.classList.remove('open');
            dropdown.querySelector('.sort-trigger').setAttribute('aria-expanded', 'false');
        });
    });

    var drawer = document.querySelector('[data-filter-drawer]');
    var backdrop = document.getElementById('catalog-filter-backdrop');
    var openers = Array.prototype.slice.call(document.querySelectorAll('[data-filter-open]'));
    var closeButton = drawer && drawer.querySelector('[data-filter-close]');
    var mobileMode = window.matchMedia('(max-width:1023px)');
    var drawerOpen = false;
    var returnFocus = null;
    var inertedElements = [];
    var focusSelector = 'a[href],button:not([disabled]),input:not([type="hidden"]):not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';

    function setOpenersExpanded(expanded){
        openers.forEach(function(opener){ opener.setAttribute('aria-expanded', expanded ? 'true' : 'false'); });
    }

    function makeOutsideInert(){
        inertedElements = [];
        var current = drawer;
        while (current && current.parentElement && current.parentElement !== document.documentElement) {
            Array.prototype.forEach.call(current.parentElement.children, function(sibling){
                if (sibling === current || sibling === backdrop || sibling.tagName === 'SCRIPT' || sibling.tagName === 'STYLE') return;
                inertedElements.push({ element: sibling, inert: sibling.inert });
                sibling.inert = true;
            });
            current = current.parentElement;
        }
    }

    function restoreOutsideInert(){
        inertedElements.forEach(function(item){ item.element.inert = item.inert; });
        inertedElements = [];
    }

    function drawerFocusableItems(){
        return Array.prototype.slice.call(drawer.querySelectorAll(focusSelector)).filter(function(element){
            return !element.closest('[inert]') && element.getClientRects().length > 0;
        });
    }

    function openDrawer(opener){
        if (!mobileMode.matches || drawerOpen) return;
        drawerOpen = true;
        returnFocus = opener || document.activeElement;
        drawer.setAttribute('role', 'dialog');
        drawer.setAttribute('aria-modal', 'true');
        drawer.setAttribute('aria-hidden', 'false');
        backdrop.setAttribute('aria-hidden', 'false');
        setOpenersExpanded(true);
        makeOutsideInert();
        document.body.classList.add('catalog-filters-open');
        requestAnimationFrame(function(){ if (drawerOpen) closeButton.focus(); });
    }

    function closeDrawer(restoreFocus){
        if (!drawer) return;
        var focusTarget = returnFocus;
        drawerOpen = false;
        document.body.classList.remove('catalog-filters-open');
        setOpenersExpanded(false);
        backdrop.setAttribute('aria-hidden', 'true');
        restoreOutsideInert();
        if (mobileMode.matches) drawer.setAttribute('aria-hidden', 'true');
        else drawer.removeAttribute('aria-hidden');
        drawer.removeAttribute('role');
        drawer.removeAttribute('aria-modal');
        returnFocus = null;
        if (restoreFocus && focusTarget && focusTarget.isConnected && mobileMode.matches) focusTarget.focus();
    }

    function syncDrawerMode(){
        closeDrawer(false);
        if (mobileMode.matches) drawer.setAttribute('aria-hidden', 'true');
        else drawer.removeAttribute('aria-hidden');
    }

    if (drawer && backdrop && closeButton && openers.length) {
        openers.forEach(function(opener){
            opener.addEventListener('click', function(){ openDrawer(opener); });
        });
        closeButton.addEventListener('click', function(){ closeDrawer(true); });
        backdrop.addEventListener('click', function(){ closeDrawer(true); });
        document.addEventListener('keydown', function(event){
            if (!drawerOpen || !mobileMode.matches) return;
            if (event.key === 'Escape') {
                event.preventDefault();
                event.stopPropagation();
                closeDrawer(true);
                return;
            }
            if (event.key !== 'Tab') return;
            var items = drawerFocusableItems();
            if (!items.length) {
                event.preventDefault();
                drawer.focus();
                return;
            }
            var first = items[0];
            var last = items[items.length - 1];
            if (event.shiftKey && (document.activeElement === first || !drawer.contains(document.activeElement))) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        });
        if (mobileMode.addEventListener) mobileMode.addEventListener('change', syncDrawerMode);
        else mobileMode.addListener(syncDrawerMode);
        window.addEventListener('pageshow', syncDrawerMode);
        syncDrawerMode();
    }
})();
</script>
<script>
(function(){
    var grid = document.getElementById('product-grid');
    var area = document.getElementById('infinite-scroll-area');
    if (!grid || !area) return;
    var skeletonGrid = document.getElementById('skeleton-grid');
    var loadMoreBtn = document.getElementById('load-more-btn');
    var nextUrl = area.dataset.nextUrl || null;
    var loading = false;

    function skeletonCard(){
        return '<div class="pcard-skeleton"><div class="sk-img"></div><div class="sk-body"><div class="sk-line w40"></div><div class="sk-line w60"></div><div class="sk-line w40"></div><div class="sk-btn"></div></div></div>';
    }
    function showSkeletons(){
        skeletonGrid.innerHTML = new Array(6).fill(0).map(skeletonCard).join('');
        skeletonGrid.style.display = 'grid';
        loadMoreBtn.style.display = 'none';
    }
    function hideSkeletons(){
        skeletonGrid.style.display = 'none';
        skeletonGrid.innerHTML = '';
    }

    function loadMore(){
        if (loading || !nextUrl) return;
        loading = true;
        showSkeletons();
        var url = nextUrl + (nextUrl.indexOf('?') === -1 ? '?' : '&') + 'partial=1';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r){ return r.json(); })
            .then(function(data){
                grid.insertAdjacentHTML('beforeend', data.html);
                nextUrl = data.nextPageUrl;
                hideSkeletons();
                loading = false;
                if (!nextUrl) {
                    area.style.display = 'none';
                    if (observer) observer.disconnect();
                } else {
                    loadMoreBtn.style.display = 'block';
                }
            })
            .catch(function(){
                hideSkeletons();
                loading = false;
                loadMoreBtn.style.display = 'block';
            });
    }

    loadMoreBtn.addEventListener('click', loadMore);

    var observer = null;
    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){ if (entry.isIntersecting) loadMore(); });
        }, { rootMargin: '400px' });
        observer.observe(area);
    }
})();
</script>
