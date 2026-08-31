@php
    $user = auth()->user();
    $currency = \App\Models\BusinessSetting::current()->currency ?: 'TMT';
    $units = $items->sum('quantity');
@endphp
<x-layout title="Оформление заказа">
    <style>
        .checkout-page{max-width:1220px;padding:26px 24px 72px}.checkout-crumbs{display:flex;align-items:center;gap:8px;margin-bottom:20px;color:var(--text-faint);font-size:12px;font-weight:700}.checkout-crumbs a{color:var(--text-faint)}.checkout-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:25px}.checkout-heading h1{margin:0;font-size:32px;letter-spacing:-.04em}.checkout-heading p{margin:7px 0 0;color:var(--text-faint);font-size:13px}.checkout-secure{display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:999px;background:var(--success-soft);color:var(--success);font-size:11px;font-weight:800}.checkout-grid{display:grid;grid-template-columns:minmax(0,1fr) 390px;gap:28px;align-items:start}.checkout-flow{display:grid;gap:17px}.checkout-card{padding:24px;border:1px solid var(--border);border-radius:21px;background:var(--surface)}.checkout-card-head{display:flex;align-items:center;gap:13px;margin-bottom:20px}.checkout-step{width:34px;height:34px;border-radius:11px;background:var(--accent);color:#fff;display:grid;place-items:center;font-size:13px;font-weight:900;box-shadow:0 7px 16px color-mix(in srgb,var(--accent) 25%,transparent)}.checkout-card-head h2{margin:0;font-size:17px}.checkout-card-head p{margin:3px 0 0;color:var(--text-faint);font-size:10px}.checkout-fields{display:grid;grid-template-columns:1fr 1fr;gap:15px}.checkout-field label{display:block;margin-bottom:7px;font-size:11px;font-weight:800}.checkout-field .input{background:var(--bg)}.checkout-field-error{margin-top:6px;color:var(--danger);font-size:11px;font-weight:700}.checkout-note{display:flex;align-items:flex-start;gap:9px;margin-top:15px;padding:12px 14px;border-radius:12px;background:var(--bg);color:var(--text-faint);font-size:11px;line-height:1.5}.checkout-options{display:grid;grid-template-columns:1fr 1fr;gap:12px}.checkout-option{position:relative;display:flex;align-items:flex-start;gap:12px;min-height:104px;padding:16px;border:1.5px solid var(--border);border-radius:15px;background:var(--bg);cursor:pointer;transition:.18s}.checkout-option:hover{border-color:var(--border-strong);transform:translateY(-1px)}.checkout-option:has(input:checked),.checkout-option.selected{border-color:var(--accent);background:var(--accent-soft);box-shadow:0 0 0 3px color-mix(in srgb,var(--accent) 9%,transparent)}.checkout-option input{margin:3px 0 0;width:18px;height:18px;accent-color:var(--accent)}.checkout-option-icon{width:38px;height:38px;border-radius:11px;background:var(--surface);color:var(--accent);display:grid;place-items:center;flex:0 0 auto}.checkout-option strong{display:block;font-size:13px}.checkout-option small{display:block;margin-top:4px;color:var(--text-faint);font-size:10px;line-height:1.45}.checkout-option-price{position:absolute;right:13px;bottom:12px;color:var(--success);font-size:10px;font-weight:900}.checkout-payment{display:flex;align-items:center;gap:14px;padding:16px;border:1.5px solid var(--accent);border-radius:15px;background:var(--accent-soft)}.checkout-payment strong{display:block;font-size:13px}.checkout-payment span{display:block;margin-top:4px;color:var(--text-faint);font-size:10.5px;line-height:1.5}.promo-wrap{display:grid;grid-template-columns:1fr auto;gap:9px}.promo-button{height:46px;padding:0 18px;border:1px solid var(--accent);border-radius:12px;background:transparent;color:var(--accent);font-weight:800;cursor:pointer}.promo-button:disabled{opacity:.55;cursor:wait}.promo-result{display:none;align-items:center;gap:8px;margin-top:10px;padding:10px 12px;border-radius:11px;font-size:11px;font-weight:700}.promo-result.success{display:flex;background:var(--success-soft);color:var(--success)}.promo-result.error{display:flex;background:var(--danger-soft);color:var(--danger)}.checkout-summary{position:sticky;top:24px;padding:23px;border:1px solid var(--border);border-radius:22px;background:var(--surface);box-shadow:0 18px 45px rgba(14,30,60,.08)}.checkout-summary-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px}.checkout-summary-head h2{margin:0;font-size:18px}.checkout-summary-head a{font-size:11px;font-weight:800}.checkout-products{display:grid;gap:12px;max-height:300px;overflow:auto;padding-right:4px}.checkout-product{display:grid;grid-template-columns:58px 1fr auto;gap:11px;align-items:center}.checkout-product-img{width:58px;height:58px;border:1px solid var(--border);border-radius:12px;background:var(--bg);display:grid;place-items:center;overflow:hidden;color:var(--text-faint)}.checkout-product-img img{width:100%;height:100%;object-fit:contain}.checkout-product-name{font-size:11.5px;font-weight:800;line-height:1.35}.checkout-product-meta{margin-top:4px;color:var(--text-faint);font-size:10px}.checkout-product-price{font-size:11.5px;font-weight:900;white-space:nowrap}.checkout-totals{margin-top:17px;padding-top:8px;border-top:1px solid var(--border)}.checkout-line{display:flex;justify-content:space-between;gap:16px;padding:7px 0;color:var(--text-muted);font-size:12.5px}.checkout-line[hidden]{display:none}.checkout-line strong{color:var(--text)}.checkout-total{display:flex;justify-content:space-between;align-items:flex-end;gap:15px;margin-top:8px;padding-top:16px;border-top:1px solid var(--border)}.checkout-total span{font-size:14px;font-weight:900}.checkout-total strong{font-size:25px;letter-spacing:-.04em}.checkout-submit{width:100%;height:55px;margin-top:20px;border-radius:14px}.checkout-submit[disabled]{opacity:.65;cursor:wait}.checkout-legal{margin-top:10px;color:var(--text-faint);font-size:9.5px;text-align:center;line-height:1.5}.checkout-trust{display:grid;gap:9px;margin-top:17px;padding-top:17px;border-top:1px solid var(--border)}.checkout-trust div{display:flex;align-items:center;gap:8px;color:var(--text-faint);font-size:10.5px}.checkout-trust svg{color:var(--success)}
        @media(max-width:920px){.checkout-grid{grid-template-columns:1fr}.checkout-summary{position:static}.checkout-heading{align-items:flex-start}}
        @media(max-width:620px){.checkout-page{padding:22px 14px 54px}.checkout-heading{display:block}.checkout-heading h1{font-size:27px}.checkout-secure{width:max-content;margin-top:13px}.checkout-card{padding:18px;border-radius:17px}.checkout-fields,.checkout-options{grid-template-columns:1fr}.checkout-option{min-height:92px}.promo-wrap{grid-template-columns:1fr}.checkout-product{grid-template-columns:52px 1fr}.checkout-product-img{width:52px;height:52px}.checkout-product-price{grid-column:2}.checkout-summary{padding:19px}}
    </style>

    <div class="wrap checkout-page">
        <nav class="checkout-crumbs" aria-label="Навигация"><a href="{{ route('home') }}">Главная</a><span>/</span><a href="{{ route('cart.index') }}">Корзина</a><span>/</span><span style="color:var(--text)">Оформление</span></nav>
        <header class="checkout-heading">
            <div><h1>Оформление заказа</h1><p>Проверьте контакты и выберите удобный способ получения.</p></div>
            <div class="checkout-secure"><x-icon name="shield" :size="15" /> Безопасное оформление</div>
        </header>

        <form id="checkout-form" method="POST" action="{{ route('checkout.store') }}">
            @csrf
            <input type="hidden" name="idempotency_key" value="{{ old('idempotency_key', \Illuminate\Support\Str::uuid()) }}">
            <div class="checkout-grid">
                <div class="checkout-flow">
                    <section class="checkout-card">
                        <div class="checkout-card-head"><span class="checkout-step">1</span><div><h2>Получатель</h2><p>Данные для подтверждения и доставки заказа</p></div></div>
                        <div class="checkout-fields">
                            <div class="checkout-field"><label for="checkout-name">Имя и фамилия</label><input id="checkout-name" required autocomplete="name" class="input" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Как к вам обращаться">@error('name')<div class="checkout-field-error">{{ $message }}</div>@enderror</div>
                            <div class="checkout-field"><label for="checkout-phone">Телефон</label><input id="checkout-phone" required autocomplete="tel" inputmode="tel" class="input" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+993 65 123456">@error('phone')<div class="checkout-field-error">{{ $message }}</div>@enderror</div>
                            <div class="checkout-field"><label for="checkout-city">Город</label><input id="checkout-city" required autocomplete="address-level2" class="input" name="city" value="{{ old('city') }}" placeholder="Ашхабад">@error('city')<div class="checkout-field-error">{{ $message }}</div>@enderror</div>
                            <div id="address-field" class="checkout-field"><label for="checkout-address">Улица, дом, квартира</label><input id="checkout-address" required autocomplete="street-address" class="input" name="address" value="{{ old('address') }}" placeholder="ул. Махтумкули, дом 12">@error('address')<div class="checkout-field-error">{{ $message }}</div>@enderror</div>
                        </div>
                        @unless($user)<div class="checkout-note"><x-icon name="user" :size="15" />Вы оформляете заказ без регистрации. Сохраните ссылку на заказ после оформления; мы также свяжемся с вами по указанному телефону.</div>@endunless
                    </section>

                    <section class="checkout-card">
                        <div class="checkout-card-head"><span class="checkout-step">2</span><div><h2>Получение</h2><p>Выберите доставку или самовывоз</p></div></div>
                        <div class="checkout-options">
                            <label class="checkout-option"><input type="radio" name="delivery_method" value="courier" {{ old('delivery_method', 'courier') === 'courier' ? 'checked' : '' }}><span class="checkout-option-icon"><x-icon name="truck" :size="20" /></span><span><strong>Курьерская доставка</strong><small>Доставим по указанному адресу после подтверждения заказа</small></span><span class="checkout-option-price">Бесплатно</span></label>
                            <label class="checkout-option"><input type="radio" name="delivery_method" value="pickup" {{ old('delivery_method') === 'pickup' ? 'checked' : '' }}><span class="checkout-option-icon"><x-icon name="store" :size="20" /></span><span><strong>Самовывоз</strong><small>Мы сообщим адрес и время выдачи после подтверждения</small></span><span class="checkout-option-price">0 {{ $currency }}</span></label>
                        </div>
                        @error('delivery_method')<div class="checkout-field-error">{{ $message }}</div>@enderror
                    </section>

                    <section class="checkout-card">
                        <div class="checkout-card-head"><span class="checkout-step">3</span><div><h2>Оплата</h2><p>Доступный и подтверждённый способ</p></div></div>
                        <input type="hidden" name="payment_method" value="cash">
                        <div class="checkout-payment"><span class="checkout-option-icon"><x-icon name="cash" :size="20" /></span><div style="flex:1"><strong>Наличными при получении</strong><span>Оплатите заказ курьеру или в пункте самовывоза. Предоплата не требуется.</span></div><x-icon name="check" :size="18" style="color:var(--success)" /></div>
                    </section>

                    @auth
                        <section class="checkout-card">
                            <div class="checkout-card-head"><span class="checkout-step">4</span><div><h2>Промокод</h2><p>Скидка будет перепроверена перед созданием заказа</p></div></div>
                            <div class="promo-wrap"><input id="promo-code" class="input" name="promo_code" value="{{ old('promo_code') }}" maxlength="50" autocomplete="off" placeholder="Введите промокод"><button id="promo-apply" class="promo-button" type="button">Применить</button></div>
                            @error('promo_code')<div class="checkout-field-error">{{ $message }}</div>@enderror
                            <div id="promo-result" class="promo-result" role="status" aria-live="polite"></div>
                        </section>
                    @endauth
                </div>

                <aside class="checkout-summary" aria-label="Состав и итог заказа">
                    <div class="checkout-summary-head"><h2>Ваш заказ</h2><a href="{{ route('cart.index') }}">Изменить</a></div>
                    <div class="checkout-products">
                        @foreach($items as $item)
                            @php($image = $item->product->images->first())
                            <div class="checkout-product">
                                <div class="checkout-product-img">@if($image)<img src="{{ $image->url }}" alt="{{ $item->product->name }}">@else<x-icon name="image" :size="19" />@endif</div>
                                <div><div class="checkout-product-name">{{ $item->product->name }}</div><div class="checkout-product-meta">{{ $item->quantity }} × {{ number_format($item->product->price, 0, '', ' ') }} {{ $currency }}</div></div>
                                <div class="checkout-product-price">{{ number_format($item->product->price * $item->quantity, 0, '', ' ') }} {{ $currency }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="checkout-totals">
                        <div class="checkout-line"><span>Товары ({{ $units }} шт.)</span><strong>{{ number_format($subtotal + $discount, 0, '', ' ') }} {{ $currency }}</strong></div>
                        @if($discount > 0)<div class="checkout-line"><span>Скидка на товары</span><strong style="color:var(--success)">&minus;{{ number_format($discount, 0, '', ' ') }} {{ $currency }}</strong></div>@endif
                        <div id="promo-line" class="checkout-line" hidden><span>Промокод</span><strong id="promo-discount" style="color:var(--success)"></strong></div>
                        <div class="checkout-line"><span>Доставка</span><strong style="color:var(--success)">Бесплатно</strong></div>
                    </div>
                    <div class="checkout-total"><span>К оплате</span><strong id="checkout-total">{{ number_format($total, 0, '', ' ') }} {{ $currency }}</strong></div>
                    <button id="checkout-submit" type="submit" class="btn-accent checkout-submit"><x-icon name="shield" :size="16" /><span>Подтвердить заказ</span></button>
                    <div class="checkout-legal">Нажимая кнопку, вы подтверждаете корректность контактных данных и состава заказа.</div>
                    <div class="checkout-trust"><div><x-icon name="check" :size="14" />Остатки и цены проверяются повторно</div><div><x-icon name="phone" :size="14" />Менеджер подтвердит заказ по телефону</div><div><x-icon name="cash" :size="14" />Никаких скрытых онлайн-списаний</div></div>
                </aside>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const deliveryInputs = document.querySelectorAll('input[name="delivery_method"]');
            const addressField = document.getElementById('address-field');
            const addressInput = document.getElementById('checkout-address');
            const syncDelivery = () => {
                const pickup = document.querySelector('input[name="delivery_method"]:checked')?.value === 'pickup';
                addressField.hidden = pickup;
                addressInput.disabled = pickup;
                addressInput.required = !pickup;
            };
            deliveryInputs.forEach(input => input.addEventListener('change', syncDelivery));
            syncDelivery();

            const form = document.getElementById('checkout-form');
            const submit = document.getElementById('checkout-submit');
            form.addEventListener('submit', () => {
                submit.disabled = true;
                submit.querySelector('span').textContent = 'Оформляем заказ…';
            });

            const promoButton = document.getElementById('promo-apply');
            if (!promoButton) return;
            const promoInput = document.getElementById('promo-code');
            const promoResult = document.getElementById('promo-result');
            const promoLine = document.getElementById('promo-line');
            const promoDiscount = document.getElementById('promo-discount');
            const total = document.getElementById('checkout-total');
            const money = value => `${new Intl.NumberFormat('ru-RU').format(value)} @json($currency)`;

            promoInput.addEventListener('input', () => {
                promoLine.hidden = true;
                promoResult.className = 'promo-result';
                total.textContent = money(@json($total));
            });
            promoButton.addEventListener('click', async () => {
                const code = promoInput.value.trim();
                if (!code) {
                    promoResult.className = 'promo-result error';
                    promoResult.textContent = 'Введите промокод.';
                    return;
                }
                promoButton.disabled = true;
                promoButton.textContent = 'Проверяем…';
                try {
                    const response = await fetch(@json(route('checkout.promo')), {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token())},
                        body: JSON.stringify({promo_code: code})
                    });
                    const data = await response.json();
                    if (!response.ok) throw new Error(data.errors?.promo_code?.[0] || data.message || 'Промокод не применён.');
                    promoInput.value = data.code;
                    promoDiscount.textContent = `−${money(data.discount)}`;
                    promoLine.hidden = false;
                    total.textContent = money(data.total);
                    promoResult.className = 'promo-result success';
                    promoResult.textContent = data.message;
                } catch (error) {
                    promoLine.hidden = true;
                    total.textContent = money(@json($total));
                    promoResult.className = 'promo-result error';
                    promoResult.textContent = error.message;
                } finally {
                    promoButton.disabled = false;
                    promoButton.textContent = 'Применить';
                }
            });
        })();
    </script>
</x-layout>
