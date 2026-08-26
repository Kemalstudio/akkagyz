<x-layout title="Главная">
    <div class="wrap" style="padding:24px;">
        @if($banners->isNotEmpty())
        <div class="hero-slider" style="position:relative;border-radius:20px;overflow:hidden;min-height:340px;background:var(--surface);">
            @foreach($banners as $banner)
                <div class="hero-slide" style="position:absolute;inset:0;background-image:url('{{ $banner->image_url }}');background-size:cover;background-position:center;opacity:{{ $loop->first ? 1 : 0 }};transition:opacity .9s ease;">
                    <div style="position:absolute;inset:0;background:linear-gradient(100deg, oklch(0.10 0.02 264 / .88) 0%, oklch(0.10 0.02 264 / .45) 55%, oklch(0.10 0.02 264 / .1) 100%);"></div>
                    <div style="position:relative;padding:48px;display:flex;flex-direction:column;justify-content:center;height:100%;min-height:340px;">
                        <div class="badge" style="background:var(--accent-soft);color:var(--accent);width:fit-content;margin-bottom:16px;">AK KAGYZ</div>
                        <div style="font-size:36px;font-weight:900;line-height:1.15;max-width:480px;color:#fff;">{{ $banner->title }}</div>
                        @if($banner->subtitle)
                            <div style="font-size:15px;color:rgba(255,255,255,.8);margin-top:12px;max-width:440px;">{{ $banner->subtitle }}</div>
                        @endif
                        @if($banner->button_text)
                            <a href="{{ $banner->link_url ?: route('catalog') }}" class="btn-accent" style="width:fit-content;padding:0 24px;margin-top:24px;font-size:15px;height:48px;">
                                {{ $banner->button_text }} <x-icon name="arrow-right" :size="16" />
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($banners->count() > 1)
            <div style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);display:flex;gap:8px;z-index:10;">
                @foreach($banners as $i => $banner)
                    <button type="button" class="hero-dot {{ $i === 0 ? 'active' : '' }}" onclick="akHeroGoTo({{ $i }})" aria-label="Слайд {{ $i + 1 }}"></button>
                @endforeach
            </div>
            <button type="button" onclick="akHeroPrev()" aria-label="Предыдущий слайд" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:50%;background:rgba(0,0,0,.35);border:none;color:#fff;display:flex;align-items:center;justify-content:center;z-index:10;backdrop-filter:blur(4px);">
                <x-icon name="chevron-left" :size="18" />
            </button>
            <button type="button" onclick="akHeroNext()" aria-label="Следующий слайд" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:50%;background:rgba(0,0,0,.35);border:none;color:#fff;display:flex;align-items:center;justify-content:center;z-index:10;backdrop-filter:blur(4px);">
                <x-icon name="chevron-right" :size="18" />
            </button>
            @endif
        </div>
        <style>
            .hero-dot{width:8px;height:8px;border-radius:4px;background:rgba(255,255,255,.4);border:none;padding:0;cursor:pointer;transition:all .25s ease;}
            .hero-dot.active{width:24px;background:#fff;}
        </style>
        <script>
        (function(){
            var slides = document.querySelectorAll('.hero-slide');
            var dots = document.querySelectorAll('.hero-dot');
            var current = 0, timer;
            window.akHeroGoTo = function(i){
                if (!slides.length) return;
                slides[current].style.opacity = 0;
                if (dots[current]) dots[current].classList.remove('active');
                current = (i + slides.length) % slides.length;
                slides[current].style.opacity = 1;
                if (dots[current]) dots[current].classList.add('active');
                resetTimer();
            };
            window.akHeroNext = function(){ akHeroGoTo(current + 1); };
            window.akHeroPrev = function(){ akHeroGoTo(current - 1); };
            function resetTimer(){ clearInterval(timer); if (slides.length > 1) timer = setInterval(window.akHeroNext, 5500); }
            resetTimer();
        })();
        </script>
        @else
        <div style="position:relative;border-radius:20px;overflow:hidden;background:linear-gradient(120deg, oklch(0.30 0.06 264) 0%, oklch(0.19 0.02 264) 65%);padding:48px;display:flex;flex-direction:column;justify-content:center;min-height:340px;">
            <div class="badge" style="background:var(--accent-soft);color:var(--accent);width:fit-content;margin-bottom:16px;">Сезонная распродажа</div>
            <div style="font-size:38px;font-weight:900;line-height:1.15;max-width:480px;">Канцтовары и упаковка для вашего бизнеса — до &minus;30%</div>
            <div style="font-size:15px;color:var(--text-muted);margin-top:12px;max-width:440px;">Более {{ \App\Models\Product::count() }} товаров от проверенных продавцов Туркменистана. Быстрая доставка по всей стране.</div>
            <a href="{{ route('catalog') }}" class="btn-accent" style="width:fit-content;padding:0 24px;margin-top:24px;font-size:15px;height:48px;">
                Смотреть акции <x-icon name="arrow-right" :size="16" />
            </a>
        </div>
        @endif
    </div>

    <div class="wrap home-benefits" style="padding:20px 24px 0;display:grid;grid-template-columns:1fr 1fr;gap:14px;">
        <a href="{{ route('catalog') }}" class="home-benefit-card">
            <div class="benefit-icon" style="background:var(--success-soft);color:var(--success);"><x-icon name="truck" :size="23" /></div>
            <div style="flex:1;">
                <div class="benefit-kicker">Логистика</div><div class="benefit-title">Быстрая доставка по Туркменистану</div>
                <div class="benefit-copy">Собственный склад и отправка заказа в течение 1–3 дней</div>
            </div>
            <span class="benefit-arrow"><x-icon name="arrow-right" :size="16"/></span>
        </a>
        <a href="{{ route('catalog') }}" class="home-benefit-card">
            <div class="benefit-icon" style="background:var(--accent-soft);color:var(--accent);"><x-icon name="grid" :size="23" /></div>
            <div style="flex:1;">
                <div class="benefit-kicker">Большой выбор</div><div class="benefit-title">{{ $categories->count() }} категорий товаров</div>
                <div class="benefit-copy">Канцелярия, упаковка, творчество и товары для бизнеса</div>
            </div>
            <span class="benefit-arrow"><x-icon name="arrow-right" :size="16"/></span>
        </a>
    </div>
    <style>.home-benefit-card{position:relative;display:flex;align-items:center;gap:18px;padding:22px 24px;border-radius:18px;background:linear-gradient(135deg,var(--surface),color-mix(in oklch,var(--surface) 88%,var(--accent-soft)));border:1px solid var(--border);color:var(--text);overflow:hidden;transition:transform .2s,border-color .2s,box-shadow .2s}.home-benefit-card:before{content:'';position:absolute;inset:auto -35px -45px auto;width:110px;height:110px;border-radius:50%;background:var(--accent-soft);filter:blur(4px)}.home-benefit-card:hover{color:var(--text);transform:translateY(-3px);border-color:var(--border-strong);box-shadow:0 16px 34px rgba(0,0,0,.12)}.benefit-icon{width:52px;height:52px;border-radius:15px;display:grid;place-items:center;flex-shrink:0}.benefit-kicker{font-size:9px;text-transform:uppercase;letter-spacing:.1em;color:var(--text-faint);font-weight:700}.benefit-title{font-size:16px;font-weight:700;margin-top:3px}.benefit-copy{font-size:12px;color:var(--text-muted);margin-top:4px}.benefit-arrow{width:34px;height:34px;border-radius:10px;border:1px solid var(--border);display:grid;place-items:center;color:var(--accent);z-index:1;transition:.2s}.home-benefit-card:hover .benefit-arrow{background:var(--accent);border-color:var(--accent);color:white;transform:translateX(2px)}@media(max-width:760px){.home-benefits{grid-template-columns:1fr!important}.home-benefit-card{padding:18px}.benefit-arrow{display:none}}</style>

    @php
        $catImages = [
            'kancelyariya' => 'kancelyariya.jpg',
            'ofis-i-biznes' => 'ofis.jpg',
            'tvorchestvo' => 'tvorchestvo.jpg',
            'upakovka' => 'upakovka.jpg',
            'knigi-i-pechat' => 'knigi.jpg',
            'elektronika' => 'elektronika.jpg',
        ];
    @endphp
    <div class="wrap" style="padding:36px 24px 8px;position:relative;">
        <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:18px;">
            <span style="font-size:20px;font-weight:900;">Категории товаров</span>
            <a href="{{ route('catalog') }}" style="font-size:13px;font-weight:800;display:flex;align-items:center;gap:6px;">Все категории <x-icon name="chevron-right" :size="13" /></a>
        </div>
        <div class="cat-tiles-viewport">
            <button type="button" class="cat-tiles-nav cat-tiles-nav-left" aria-label="Прокрутить влево" onclick="document.getElementById('cat-tiles-scroll').scrollBy({left:-600,behavior:'smooth'})">
                <x-icon name="chevron-left" :size="18" />
            </button>
            <div class="cat-tiles-scroll" id="cat-tiles-scroll">
                @foreach($categories as $cat)
                    <a href="{{ route('catalog', ['category' => $cat->slug]) }}" class="cat-tile" style="background:var(--surface) url('{{ asset('storage/categories/'.($catImages[$cat->slug] ?? 'kancelyariya.jpg')) }}') center/cover;">
                        <div style="position:absolute;inset:0;background:linear-gradient(0deg, oklch(0.10 0.02 264 / .88) 0%, oklch(0.10 0.02 264 / .15) 65%, oklch(0.10 0.02 264 / .05) 100%);"></div>
                        <div style="position:relative;padding:14px;display:flex;flex-direction:column;gap:8px;">
                            <div style="width:34px;height:34px;border-radius:10px;background:oklch(1 0 0 / .16);backdrop-filter:blur(4px);color:#fff;display:flex;align-items:center;justify-content:center;">
                                <x-icon :name="$cat->icon ?: 'package'" :size="17" />
                            </div>
                            <span style="font-size:13px;font-weight:800;color:#fff;line-height:1.25;">{{ $cat->name }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
            <button type="button" class="cat-tiles-nav cat-tiles-nav-right" aria-label="Прокрутить вправо" onclick="document.getElementById('cat-tiles-scroll').scrollBy({left:600,behavior:'smooth'})">
                <x-icon name="chevron-right" :size="18" />
            </button>
            <div class="cat-tiles-fade"></div>
        </div>
    </div>
    <style>
        .cat-tiles-viewport{position:relative;}
        .cat-tiles-scroll{display:flex;gap:16px;overflow-x:auto;scroll-behavior:smooth;scrollbar-width:none;padding-bottom:2px;}
        .cat-tiles-scroll::-webkit-scrollbar{display:none;}
        .cat-tile{position:relative;flex:0 0 180px;aspect-ratio:4/3.2;border-radius:16px;overflow:hidden;display:flex;flex-direction:column;justify-content:flex-end;transition:transform .25s ease, box-shadow .25s ease;}
        .cat-tile:hover{transform:translateY(-3px);box-shadow:0 16px 32px rgba(0,0,0,.28);}
        .cat-tiles-fade{position:absolute;top:0;right:0;bottom:2px;width:64px;background:linear-gradient(90deg, transparent, var(--bg));pointer-events:none;}
        .cat-tiles-nav{position:absolute;top:50%;transform:translateY(-50%);width:38px;height:38px;border-radius:50%;background:var(--surface);border:1px solid var(--border);box-shadow:0 8px 20px rgba(0,0,0,.2);display:flex;align-items:center;justify-content:center;color:var(--text);z-index:5;}
        .cat-tiles-nav:hover{background:var(--accent);color:var(--accent-text);border-color:var(--accent);}
        .cat-tiles-nav-left{left:-8px;}
        .cat-tiles-nav-right{right:-8px;}
    </style>

    @foreach($categorySections as $section)
    <div class="wrap" style="padding:44px 24px 8px;">
        <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:20px;">
            <span style="font-size:22px;font-weight:900;display:flex;align-items:center;gap:9px;">
                <x-icon :name="$section['category']->icon ?: 'package'" :size="19" style="color:var(--accent);" />
                {{ $section['category']->name }}
            </span>
            <a href="{{ route('catalog', ['category' => $section['category']->slug]) }}" style="font-size:14px;font-weight:800;display:flex;align-items:center;gap:6px;">Смотреть все товары <x-icon name="chevron-right" :size="14" /></a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
            @foreach($section['products'] as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
    @endforeach

    @if($popular->isNotEmpty())
    <div class="wrap" style="padding:44px 24px 8px;">
        <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:20px;">
            <div style="display:flex;align-items:baseline;gap:12px;">
                <span style="font-size:24px;font-weight:900;">Популярные товары</span>
                <span style="font-size:13px;color:var(--text-faint);font-weight:600;">по продажам</span>
            </div>
            <a href="{{ route('catalog') }}" style="font-size:14px;font-weight:800;display:flex;align-items:center;gap:6px;">Все товары <x-icon name="chevron-right" :size="14" /></a>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
            @foreach($popular as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
    @endif

    <div class="wrap" style="padding:44px 24px 8px;">
        <div style="border-radius:20px;background:linear-gradient(100deg, oklch(0.28 0.055 264) 0%, oklch(0.24 0.045 264) 55%);border:1px solid var(--border);display:flex;align-items:stretch;justify-content:space-between;overflow:hidden;">
            <div style="padding:40px 44px;flex:1;">
                <div class="badge" style="background:oklch(1 0 0 / .14);color:#fff;width:fit-content;margin-bottom:14px;">Отдельный раздел</div>
                <div style="font-size:24px;font-weight:900;color:#fff;max-width:440px;line-height:1.25;">Загляните в «Магазины» — витрину независимых продавцов</div>
                <div style="font-size:14px;color:oklch(1 0 0 / .7);margin-top:10px;max-width:460px;">Сотни магазинов с собственным ассортиментом, рейтингами и отзывами. А если у вас есть свой товар — там же можно открыть магазин и начать продавать.</div>
                <div style="display:flex;flex-wrap:wrap;gap:18px;margin-top:20px;">
                    <div style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:oklch(1 0 0 / .85);"><x-icon name="check" :size="15" style="color:var(--accent);flex-shrink:0;" /> Свой каталог и поиск</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:oklch(1 0 0 / .85);"><x-icon name="check" :size="15" style="color:var(--accent);flex-shrink:0;" /> Проверенные продавцы</div>
                    <div style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:oklch(1 0 0 / .85);"><x-icon name="check" :size="15" style="color:var(--accent);flex-shrink:0;" /> Откройте свой магазин</div>
                </div>
                <a href="{{ route('marketplace.home') }}" class="btn-accent" style="padding:0 28px;height:48px;font-size:15px;width:fit-content;margin-top:26px;">Перейти в Магазины <x-icon name="arrow-right" :size="16" /></a>
            </div>
        </div>
    </div>

    @if($newest->isNotEmpty())
    <div class="wrap" style="padding:44px 24px 56px;">
        <div style="font-size:24px;font-weight:900;margin-bottom:20px;">Новинки</div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
            @foreach($newest as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
    @endif
</x-layout>
