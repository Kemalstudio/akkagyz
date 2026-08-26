@props(['categories', 'routeName' => 'catalog'])
<div class="catalog-trigger-wrap" style="position:relative;flex-shrink:0;">
    <button type="button" onclick="akToggleMenu(event,'catalog-mega')" class="catalog-trigger-btn">
        <x-icon name="grid" :size="15" /> Каталог <x-icon name="chevron-down" :size="12" style="opacity:.7;" />
    </button>

    <div id="catalog-mega" class="dropdown-panel catalog-mega" style="position:absolute;left:0;top:52px;z-index:60;">
        <div class="catalog-mega-grid">
            @foreach($categories as $cat)
                <div class="catalog-mega-col">
                    <a href="{{ route($routeName, ['category' => $cat->slug]) }}" class="catalog-mega-head">
                        <span class="catalog-mega-icon"><x-icon :name="$cat->icon ?: 'package'" :size="16" /></span>
                        {{ $cat->name }}
                    </a>
                    @if($cat->children->isNotEmpty())
                        <div class="catalog-mega-children">
                            @foreach($cat->children->take(6) as $child)
                                <a href="{{ route($routeName, ['category' => $child->slug]) }}">{{ $child->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <a href="{{ route($routeName) }}" class="catalog-mega-all">
            Смотреть все категории <x-icon name="arrow-right" :size="14" />
        </a>
    </div>
</div>

<style>
.catalog-trigger-btn{
    display:flex;align-items:center;gap:7px;padding:0 16px;height:34px;border-radius:9px;
    background:var(--accent);color:var(--accent-text);border:none;font-weight:800;font-size:14px;
    font-family:var(--font);white-space:nowrap;transition:background .15s ease;
}
.catalog-trigger-btn:hover{background:var(--accent-strong);}
.catalog-trigger-wrap::after{content:'';position:absolute;left:-8px;right:-8px;top:100%;height:24px;}
.catalog-trigger-wrap:hover .catalog-mega{opacity:1;visibility:visible;pointer-events:auto;transform:translateY(0) scale(1);}
.catalog-mega{
    width:min(920px, 92vw);padding:26px 28px 22px;border-radius:20px;
    background:var(--surface);border:1px solid var(--border);
    box-shadow:0 28px 56px rgba(0,0,0,.28);
}
:root[data-theme="light"] .catalog-mega{box-shadow:0 28px 56px rgba(20,20,30,.14);}
.catalog-mega-grid{display:grid;grid-template-columns:repeat(3, 1fr);gap:24px 28px;}
.catalog-mega-col{min-width:0;}
.catalog-mega-head{display:flex;align-items:center;gap:11px;font-size:14px;font-weight:700;color:var(--text);padding-bottom:11px;margin-bottom:8px;border-bottom:1px solid var(--border);transition:color .15s;}.catalog-mega-head:hover{color:var(--accent)}
.catalog-mega-icon{width:34px;height:34px;border-radius:10px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:.18s}.catalog-mega-head:hover .catalog-mega-icon{background:var(--accent);color:white;transform:translateY(-1px)}
.catalog-mega-children{display:flex;flex-direction:column;gap:2px;}
.catalog-mega-children a{position:relative;display:block;padding:6px 8px;font-size:12.5px;font-weight:500;color:var(--text-muted);border-radius:7px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;transition:.15s}.catalog-mega-children a:hover{color:var(--accent);background:var(--accent-soft);padding-left:12px}
.catalog-mega-all{display:flex;align-items:center;gap:7px;justify-content:center;margin-top:22px;padding-top:19px;border-top:1px solid var(--border);font-size:13px;font-weight:700;color:var(--accent)}
@media(max-width:760px){.catalog-mega{width:calc(100vw - 28px);left:-130px!important;padding:18px}.catalog-mega-grid{grid-template-columns:1fr 1fr;gap:18px}.catalog-mega-col:nth-child(n+5){display:none}}
</style>
