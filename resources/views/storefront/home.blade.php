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

    @if($testimonials->isNotEmpty())
    <div class="wrap" style="padding:44px 24px 8px;">
        <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:20px;">
            <div style="display:flex;align-items:baseline;gap:12px;">
                <span style="font-size:24px;font-weight:900;">Отзывы покупателей</span>
                <span style="font-size:13px;color:var(--text-faint);font-weight:600;">реальные оценки товаров</span>
            </div>
        </div>
        <div class="testi-viewport">
            <button type="button" class="cat-tiles-nav cat-tiles-nav-left" aria-label="Прокрутить влево" onclick="document.getElementById('testi-scroll').scrollBy({left:-360,behavior:'smooth'})">
                <x-icon name="chevron-left" :size="18" />
            </button>
            <div class="testi-scroll" id="testi-scroll">
                @foreach($testimonials as $review)
                    <div class="testi-card">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#7c6cf2);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0;overflow:hidden;">
                                @if($review->user->avatar_url)
                                    <img src="{{ $review->user->avatar_url }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ \Illuminate\Support\Str::of($review->user->name)->substr(0, 2)->upper() }}
                                @endif
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:13.5px;font-weight:800;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $review->user->name }}</div>
                                <x-star-rating :rating="$review->rating" :size="12" :gap="1" />
                            </div>
                            @if($review->is_verified_purchase)
                                <span class="badge" style="background:var(--success-soft);color:var(--success);font-size:9.5px;flex-shrink:0;white-space:nowrap;">Покупка подтверждена</span>
                            @endif
                        </div>
                        <p style="font-size:13.5px;color:var(--text-muted);line-height:1.55;margin:14px 0 0;display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;">{{ $review->comment }}</p>
                        @if($review->product)
                            <a href="{{ route($review->product->seller_id ? 'marketplace.products.show' : 'products.show', $review->product->slug) }}" style="display:block;margin-top:14px;padding-top:12px;border-top:1px solid var(--border);font-size:12px;font-weight:700;color:var(--accent);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                Отзыв о товаре «{{ $review->product->name }}»
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
            <button type="button" class="cat-tiles-nav cat-tiles-nav-right" aria-label="Прокрутить вправо" onclick="document.getElementById('testi-scroll').scrollBy({left:360,behavior:'smooth'})">
                <x-icon name="chevron-right" :size="18" />
            </button>
            <div class="cat-tiles-fade"></div>
        </div>
    </div>
    <style>
        .testi-viewport{position:relative;}
        .testi-scroll{display:flex;gap:16px;overflow-x:auto;scroll-behavior:smooth;scrollbar-width:none;padding-bottom:2px;}
        .testi-scroll::-webkit-scrollbar{display:none;}
        .testi-card{flex:0 0 300px;border-radius:16px;padding:20px;background:var(--surface);border:1px solid var(--border);display:flex;flex-direction:column;}
    </style>
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
    <div class="wrap" style="padding:44px 24px 8px;">
        <div style="font-size:24px;font-weight:900;margin-bottom:20px;">Новинки</div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:20px;">
            @foreach($newest as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
    @endif

    @php($business = \App\Models\BusinessSetting::current())
    @if($business->google_play_url || $business->app_store_url)
    <div class="wrap" style="padding:44px 24px 56px;">
        <div style="border-radius:20px;background:linear-gradient(100deg, oklch(0.24 0.05 264) 0%, oklch(0.16 0.025 264) 70%);border:1px solid var(--border);padding:44px;display:flex;align-items:center;justify-content:space-between;gap:32px;flex-wrap:wrap;">
            <div style="flex:1;min-width:260px;">
                <div class="badge" style="background:oklch(1 0 0 / .14);color:#fff;width:fit-content;margin-bottom:14px;">Мобильное приложение</div>
                <div style="font-size:26px;font-weight:900;color:#fff;max-width:420px;line-height:1.25;">Покупайте с телефона — приложение AK KAGYZ уже доступно</div>
                <div style="font-size:14px;color:oklch(1 0 0 / .7);margin-top:10px;max-width:420px;">Весь каталог, отслеживание заказов и уведомления о статусе — в одном приложении.</div>
                <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:24px;">
                    @if($business->google_play_url)
                        <a href="{{ $business->google_play_url }}" target="_blank" rel="noopener" class="app-store-badge">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M3.6 2.3c-.4.2-.6.6-.6 1.1v17.2c0 .5.2.9.6 1.1l9.8-9.7L3.6 2.3Z"/><path d="m14.4 12 3-3 3.9 2.2c.7.4.7 1.4 0 1.8L17.4 15l-3-3Z" opacity=".65"/><path d="m4.3 21.6 9-9 3.1 3.1-10.6 6.2c-.5.3-1.1.1-1.5-.3Z" opacity=".85"/><path d="m4.3 2.4 9 9-9 9c-.4-.2-.7-.6-.7-1.1V3.5c0-.5.3-.9.7-1.1Z" opacity=".85"/></svg>
                            <div><small>Доступно в</small><span>Google Play</span></div>
                        </a>
                    @endif
                    @if($business->app_store_url)
                        <a href="{{ $business->app_store_url }}" target="_blank" rel="noopener" class="app-store-badge">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 12.7c0-2.5 2-3.7 2.1-3.8-1.1-1.7-2.9-1.9-3.5-1.9-1.5-.2-2.9.9-3.6.9-.8 0-1.9-.9-3.2-.8-1.6 0-3.1.9-3.9 2.4-1.7 2.9-.4 7.3 1.2 9.6.8 1.2 1.7 2.5 3 2.4 1.2 0 1.6-.8 3.1-.8s1.9.8 3.2.7c1.3 0 2.1-1.2 2.9-2.4.6-.9 1.1-1.9 1.4-3-1.8-.7-2.7-2.4-2.7-3.3Z"/><path d="M14.9 5.2c.6-.8 1.1-1.9 1-3-.9.1-2 .6-2.7 1.4-.6.7-1.1 1.8-1 2.9 1 .1 2.1-.5 2.7-1.3Z"/></svg>
                            <div><small>Загрузите в</small><span>App Store</span></div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <style>
        .app-store-badge{display:flex;align-items:center;gap:10px;padding:10px 18px;border-radius:12px;background:oklch(1 0 0 / .1);border:1px solid oklch(1 0 0 / .18);color:#fff;transition:background .15s,transform .15s;}
        .app-store-badge:hover{background:oklch(1 0 0 / .18);transform:translateY(-2px);}
        .app-store-badge small{display:block;font-size:9.5px;color:oklch(1 0 0 / .65);line-height:1.2;}
        .app-store-badge span{display:block;font-size:14px;font-weight:800;line-height:1.25;}
    </style>
    @endif
</x-layout>
