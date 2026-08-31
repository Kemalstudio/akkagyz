<x-marketplace-layout title="Маркетплейс продавцов">
    @php
        $sellerUrl = auth()->check() && auth()->user()->isSeller()
            ? route('seller.dashboard')
            : route('seller.become');
        $heroSlideCount = $banners->count();
        $mobileHeroRatio = $banners->first()?->image_aspect_ratio ?? 2.4;
    @endphp

    <style>
        .market-page{overflow:hidden}
        .market-cats{display:flex;gap:8px;overflow-x:auto;padding:16px 24px 10px;scrollbar-width:none}
        .market-cats::-webkit-scrollbar{display:none}
        .market-cat{display:flex;flex:0 0 auto;width:82px;flex-direction:column;align-items:center;gap:8px;padding:10px 4px;border-radius:14px;color:var(--text);text-align:center;font-size:11px;font-weight:700;line-height:1.25;transition:background .15s ease}
        .market-cat:hover{background:var(--surface-hover);color:var(--text)}
        .market-cat-icon{display:grid;width:48px;height:48px;place-items:center;border-radius:50%;background:var(--accent-soft);color:var(--accent)}
        .market-cat--deal .market-cat-icon{background:oklch(0.94 0.06 25);color:oklch(0.5 0.18 25)}
        .market-cat--used .market-cat-icon{background:oklch(0.94 0.02 264);color:oklch(0.42 0.02 264)}
        .market-hero-wrap{padding:0 24px}
        .market-hero{position:relative;isolation:isolate;min-height:var(--market-hero-h, 280px);overflow:hidden;border:1px solid var(--border);border-radius:20px;background:var(--surface);box-shadow:0 12px 32px rgba(15,30,65,.08)}
        .market-hero-slide{position:absolute;inset:0;background-position:center;background-size:cover;background-repeat:no-repeat;opacity:0;transition:opacity .7s ease;z-index:0}
        .market-hero-slide.is-active{opacity:1;z-index:1}
        .market-hero-slide-link{position:absolute;inset:0;z-index:1}
        .market-hero-content{position:relative;display:flex;min-height:var(--market-hero-h, 280px);max-width:560px;flex-direction:column;justify-content:center;padding:36px 36px 44px;color:#fff}
        .market-hero-eyebrow{display:inline-flex;width:max-content;align-items:center;gap:7px;padding:6px 10px;border:1px solid rgba(255,255,255,.2);border-radius:999px;background:rgba(255,255,255,.1);font-size:10.5px;font-weight:900;letter-spacing:.05em;text-transform:uppercase}
        .market-hero-title{max-width:480px;margin:14px 0 0;font-size:clamp(24px,3vw,34px);font-weight:900;line-height:1.12;letter-spacing:-.03em;text-wrap:balance}
        .market-hero-copy{max-width:460px;margin:8px 0 0;color:rgba(255,255,255,.78);font-size:13.5px;line-height:1.6}
        .market-hero-action{display:inline-flex;width:max-content;min-height:42px;align-items:center;justify-content:center;gap:8px;margin-top:18px;padding:0 18px;border-radius:11px;background:#fff;color:#102b67;font-size:13px;font-weight:900;box-shadow:0 10px 24px rgba(0,0,0,.18);transition:transform .18s ease}
        .market-hero-action:hover{transform:translateY(-2px);color:#102b67}
        .market-hero-dots{position:absolute;z-index:3;bottom:18px;left:36px;display:flex;gap:8px}
        .market-hero-dot{width:8px;height:8px;border-radius:4px;background:rgba(255,255,255,.4);border:none;padding:0;cursor:pointer;transition:all .25s ease}
        .market-hero-dot.active{width:24px;background:#fff}
        .market-hero-arrow{position:absolute;z-index:3;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:50%;background:rgba(0,0,0,.32);border:none;color:#fff;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);cursor:pointer}
        .market-hero-arrow--prev{left:14px}
        .market-hero-arrow--next{right:14px}
        .market-section{padding:40px 24px 4px}
        .market-section-head{display:flex;align-items:end;justify-content:space-between;gap:22px;margin-bottom:20px}
        .market-section-title{font-size:clamp(20px,2vw,25px);font-weight:900;line-height:1.15;letter-spacing:-.02em}
        .market-section-link{display:inline-flex;flex-shrink:0;align-items:center;gap:7px;padding:9px 12px;border-radius:10px;color:var(--accent);font-size:12px;font-weight:900;transition:background .18s ease,gap .18s ease}
        .market-section-link:hover{gap:10px;background:var(--accent-soft)}
        .market-product-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px}
        .market-selling{margin-top:44px;padding:40px;border:1px solid var(--border);border-radius:26px;background:linear-gradient(135deg,var(--surface),color-mix(in srgb,var(--accent-soft) 48%,var(--surface)))}
        .market-selling-head{display:flex;align-items:end;justify-content:space-between;gap:30px;margin-bottom:26px}
        .seller-flow{position:relative;display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
        .seller-flow:before{content:"";position:absolute;top:26px;right:17%;left:17%;height:1px;background:linear-gradient(90deg,var(--accent-soft),var(--accent),var(--accent-soft))}
        .flow-step{position:relative;padding:22px;border:1px solid var(--border);border-radius:18px;background:var(--surface);box-shadow:0 9px 24px rgba(13,29,67,.05)}
        .flow-num{position:relative;z-index:1;display:grid;width:38px;height:38px;margin-bottom:17px;place-items:center;border:5px solid var(--surface);border-radius:50%;background:var(--accent);box-sizing:content-box;color:#fff;font-size:12px;font-weight:900;box-shadow:0 0 0 1px var(--border)}
        .flow-step b{display:block;font-size:14px;font-weight:900;line-height:1.35}
        .flow-step p{margin:7px 0 0;color:var(--text-faint);font-size:11px;line-height:1.62}
        .seller-cta{position:relative;display:flex;overflow:hidden;align-items:center;gap:20px;padding:30px 32px;border:1px solid rgba(255,255,255,.12);border-radius:22px;background:linear-gradient(110deg,#08172f,#153675 68%,#284fc2);color:#fff;box-shadow:0 22px 50px rgba(8,25,62,.18)}
        .seller-cta:after{content:"";position:absolute;right:-80px;width:280px;height:280px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.16),transparent 68%);pointer-events:none}
        .seller-cta-icon{position:relative;z-index:1;display:grid;width:54px;height:54px;flex:0 0 54px;place-items:center;border:1px solid rgba(255,255,255,.16);border-radius:15px;background:rgba(255,255,255,.1)}
        .seller-cta-content{position:relative;z-index:1;flex:1}
        .seller-cta-title{font-size:19px;font-weight:900;letter-spacing:-.015em}
        .seller-cta-copy{max-width:700px;margin-top:6px;color:rgba(255,255,255,.66);font-size:12px;line-height:1.55}
        .seller-cta-action{position:relative;z-index:1;display:inline-flex;min-height:44px;flex-shrink:0;align-items:center;justify-content:center;gap:8px;padding:0 17px;border-radius:11px;background:#fff;color:#173573;font-size:12px;font-weight:900;box-shadow:0 10px 24px rgba(0,0,0,.16)}
        .seller-cta-action:hover{color:#173573;transform:translateY(-1px)}
        @media(max-width:900px){.market-product-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.market-selling{padding:30px 24px}}
        @media(max-width:760px){.market-hero{aspect-ratio:var(--mobile-hero-ratio, 2.4);height:auto;min-height:0;border-radius:16px}.market-hero-slide--flat{background-size:contain}.market-hero-content{padding:22px;min-height:0}.market-hero-title{font-size:22px}.market-hero-copy{font-size:12.5px}.market-hero-dots{left:22px}.market-section{padding:32px 16px 3px}.market-section-head,.market-selling-head{align-items:flex-start;flex-direction:column}.market-selling{margin:34px 16px 0;padding:24px 18px}.seller-flow{grid-template-columns:1fr}.seller-flow:before{display:none}.seller-cta{align-items:flex-start;flex-wrap:wrap;padding:22px 20px}.seller-cta-action{width:100%}}
        @media(max-width:520px){.market-product-grid{grid-template-columns:1fr}.seller-cta-icon{display:none}}
        @media(prefers-reduced-motion:reduce){.market-hero-action,.market-section-link,.seller-cta-action{transition:none}}
    </style>

    <div class="market-page">
        <nav class="wrap market-cats" aria-label="Категории маркетплейса">
            @foreach($quickCategories as $category)
                <a class="market-cat" href="{{ route('marketplace.catalog', ['category' => $category->slug]) }}">
                    <span class="market-cat-icon"><x-icon name="{{ $category->icon }}" :size="21" /></span>
                    {{ $category->name }}
                </a>
            @endforeach
            <a class="market-cat market-cat--deal" href="{{ route('marketplace.catalog', ['on_sale' => 1]) }}">
                <span class="market-cat-icon"><x-icon name="tag" :size="21" /></span>
                Уценка
            </a>
            <a class="market-cat market-cat--used" href="{{ route('marketplace.catalog', ['condition' => 'used']) }}">
                <span class="market-cat-icon"><x-icon name="box" :size="21" /></span>
                Б/У
            </a>
        </nav>

        @if($heroSlideCount > 0)
            <div class="market-hero-wrap">
                <section class="market-hero" aria-label="Промо" style="--mobile-hero-ratio:{{ $mobileHeroRatio }}">
                    @foreach($banners as $banner)
                        @php
                            $slideBg = $banner->show_overlay
                                ? "linear-gradient(100deg,rgba(4,12,29,.85) 0%,rgba(4,12,29,.35) 55%,rgba(4,12,29,.05) 100%),url('{$banner->image_url}')"
                                : "url('{$banner->image_url}')";
                        @endphp
                        <div class="market-hero-slide {{ $loop->first ? 'is-active' : '' }} {{ $banner->show_overlay ? '' : 'market-hero-slide--flat' }}" data-market-slide style="background-image:{{ $slideBg }};">
                            @if($banner->show_overlay)
                                <div class="market-hero-content">
                                    <span class="market-hero-eyebrow">{{ $businessSettings->site_name }}</span>
                                    <h2 class="market-hero-title">{{ $banner->title }}</h2>
                                    @if($banner->subtitle)
                                        <p class="market-hero-copy">{{ $banner->subtitle }}</p>
                                    @endif
                                    @if($banner->button_text)
                                        <a class="market-hero-action" href="{{ $banner->link_url ?: route('marketplace.catalog') }}">
                                            {{ $banner->button_text }} <x-icon name="arrow-right" :size="15" />
                                        </a>
                                    @endif
                                </div>
                            @elseif($banner->link_url)
                                <a href="{{ $banner->link_url }}" class="market-hero-slide-link" aria-label="{{ $banner->title ?: $businessSettings->site_name }}"></a>
                            @endif
                        </div>
                    @endforeach

                    @if($heroSlideCount > 1)
                        <div class="market-hero-dots">
                            @for($i = 0; $i < $heroSlideCount; $i++)
                                <button type="button" class="market-hero-dot {{ $i === 0 ? 'active' : '' }}" onclick="akMarketHeroGoTo({{ $i }})" aria-label="Слайд {{ $i + 1 }}"></button>
                            @endfor
                        </div>
                        <button type="button" class="market-hero-arrow market-hero-arrow--prev" onclick="akMarketHeroPrev()" aria-label="Предыдущий слайд"><x-icon name="chevron-left" :size="16" /></button>
                        <button type="button" class="market-hero-arrow market-hero-arrow--next" onclick="akMarketHeroNext()" aria-label="Следующий слайд"><x-icon name="chevron-right" :size="16" /></button>
                    @endif
                </section>
            </div>

            @if($heroSlideCount > 1)
                <script>
                (function(){
                    var slides = document.querySelectorAll('[data-market-slide]');
                    var dots = document.querySelectorAll('.market-hero-dot');
                    var current = 0, timer;
                    window.akMarketHeroGoTo = function(i){
                        if (!slides.length) return;
                        slides[current].classList.remove('is-active');
                        if (dots[current]) dots[current].classList.remove('active');
                        current = (i + slides.length) % slides.length;
                        slides[current].classList.add('is-active');
                        if (dots[current]) dots[current].classList.add('active');
                        resetTimer();
                    };
                    window.akMarketHeroNext = function(){ akMarketHeroGoTo(current + 1); };
                    window.akMarketHeroPrev = function(){ akMarketHeroGoTo(current - 1); };
                    function resetTimer(){ clearInterval(timer); if (slides.length > 1) timer = setInterval(window.akMarketHeroNext, 6000); }
                    resetTimer();
                })();
                </script>
            @endif
        @endif

        @if($popular->isNotEmpty())
            <section class="wrap market-section" aria-labelledby="popular-products-title">
                <div class="market-section-head">
                    <h2 id="popular-products-title" class="market-section-title">Популярные товары</h2>
                    <a class="market-section-link" href="{{ route('marketplace.catalog') }}">Весь каталог <x-icon name="arrow-right" :size="14" /></a>
                </div>
                <div class="market-product-grid">
                    @foreach($popular as $product)
                        <x-product-card :product="$product" :show-seller="true" />
                    @endforeach
                </div>
            </section>
        @endif

        @if($newest->isNotEmpty())
            <section class="wrap market-section" aria-labelledby="new-products-title">
                <div class="market-section-head">
                    <h2 id="new-products-title" class="market-section-title">Новинки</h2>
                    <a class="market-section-link" href="{{ route('marketplace.catalog', ['sort' => 'newest']) }}">Смотреть все <x-icon name="arrow-right" :size="14" /></a>
                </div>
                <div class="market-product-grid">
                    @foreach($newest as $product)
                        <x-product-card :product="$product" :show-seller="true" />
                    @endforeach
                </div>
            </section>
        @endif

        <section class="wrap market-selling" aria-labelledby="selling-title">
            <div class="market-selling-head">
                <div>
                    <h2 id="selling-title" class="market-section-title">Как начать продавать на {{ $businessSettings->site_name }}</h2>
                </div>
                <a class="market-section-link" href="{{ $sellerUrl }}">Подать заявку <x-icon name="arrow-right" :size="14" /></a>
            </div>
            <div class="seller-flow">
                <article class="flow-step">
                    <span class="flow-num">1</span>
                    <b>Расскажите о магазине</b>
                    <p>Укажите название, контакты, адрес и короткое описание будущей витрины.</p>
                </article>
                <article class="flow-step">
                    <span class="flow-num">2</span>
                    <b>Пройдите модерацию</b>
                    <p>Команда платформы проверит заявку и откроет защищённый кабинет продавца.</p>
                </article>
                <article class="flow-step">
                    <span class="flow-num">3</span>
                    <b>Опубликуйте товары</b>
                    <p>После проверки товары появятся в каталоге и на персональной витрине магазина.</p>
                </article>
            </div>
        </section>

        <section class="wrap market-section" style="padding-bottom:12px" aria-label="Стать продавцом">
            <div class="seller-cta">
                <span class="seller-cta-icon"><x-icon name="store" :size="24" /></span>
                <div class="seller-cta-content">
                    <div class="seller-cta-title">Готовы открыть собственную витрину?</div>
                    <div class="seller-cta-copy">Управляйте товарами, заказами и настройками магазина из единого кабинета продавца.</div>
                </div>
                <a class="seller-cta-action" href="{{ $sellerUrl }}">Стать продавцом <x-icon name="arrow-right" :size="15" /></a>
            </div>
        </section>
    </div>
</x-marketplace-layout>
