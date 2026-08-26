@php
    $user = auth()->user();
@endphp
<x-layout title="Оформление заказа">
    <div class="wrap" style="padding:32px 24px 64px;max-width:1100px;">
        <div style="display:flex;align-items:center;gap:8px;color:var(--text-faint);font-size:13px;font-weight:700;margin-bottom:24px;">
            <x-icon name="shield" :size="15" /> Безопасное оформление заказа
        </div>

        <form method="POST" action="{{ route('checkout.store') }}">
            @csrf
            <input type="hidden" name="idempotency_key" value="{{ \Illuminate\Support\Str::uuid() }}">
            <div style="margin-bottom:16px"><label class="label">Промокод</label><input class="input" name="promo_code" value="{{ old('promo_code') }}" placeholder="Введите код скидки">@error('promo_code')<div style="color:var(--danger);font-size:12px;margin-top:5px">{{ $message }}</div>@enderror</div>
            <div style="display:grid;grid-template-columns:1fr 380px;gap:28px;align-items:start;">
                <div style="display:flex;flex-direction:column;gap:20px;">

                    <div style="border:1px solid var(--border);background:var(--surface);border-radius:16px;padding:24px;">
                        <div style="font-size:16px;font-weight:900;margin-bottom:16px;">Адрес доставки</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                            <div><label class="label">Город</label><input required class="input" name="city" value="{{ old('city') }}" placeholder="Ашхабад"></div>
                            <div><label class="label">Телефон</label><input required class="input" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+993 65 123456"></div>
                            <div style="grid-column:1/-1;"><label class="label">Улица, дом, квартира</label><input required class="input" name="address" value="{{ old('address') }}" placeholder="ул. Магтымгулы 12, кв. 5"></div>
                        </div>
                    </div>

                    <div style="border:1px solid var(--border);background:var(--surface);border-radius:16px;padding:24px;">
                        <div style="font-size:16px;font-weight:900;margin-bottom:16px;">Способ доставки</div>
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            <label style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:12px;border:1.5px solid var(--border);cursor:pointer;">
                                <input type="radio" name="delivery_method" value="courier" checked style="width:20px;height:20px;accent-color:var(--accent);">
                                <x-icon name="truck" :size="22" style="color:var(--accent);" />
                                <div style="flex:1;"><div style="font-weight:800;font-size:14px;">Курьером</div><div style="font-size:12px;color:var(--text-faint);margin-top:2px;">1&ndash;3 дня</div></div>
                                <div style="font-weight:800;font-size:14px;color:var(--success);">Бесплатно</div>
                            </label>
                            <label style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:12px;border:1.5px solid var(--border);cursor:pointer;">
                                <input type="radio" name="delivery_method" value="pickup" style="width:20px;height:20px;accent-color:var(--accent);">
                                <x-icon name="store" :size="22" style="color:var(--accent);" />
                                <div style="flex:1;"><div style="font-weight:800;font-size:14px;">Самовывоз</div><div style="font-size:12px;color:var(--text-faint);margin-top:2px;">Из пункта выдачи</div></div>
                                <div style="font-weight:800;font-size:14px;">0 TMT</div>
                            </label>
                        </div>
                    </div>

                    <div style="border:1px solid var(--border);background:var(--surface);border-radius:16px;padding:24px;">
                        <div style="font-size:16px;font-weight:900;margin-bottom:16px;">Способ оплаты</div>
                        <input type="hidden" name="payment_method" value="cash">
                        <div style="display:flex;align-items:center;gap:14px;padding:16px;border-radius:12px;border:1.5px solid var(--accent);background:var(--accent-soft);">
                            <x-icon name="money" :size="22" style="color:var(--accent);" />
                            <div style="flex:1;">
                                <div style="font-weight:800;font-size:14px;">Наличными при получении</div>
                                <div style="font-size:12px;color:var(--text-faint);margin-top:2px;">Оплатите курьеру или в пункте самовывоза — предоплата не требуется</div>
                            </div>
                            <x-icon name="check" :size="18" style="color:var(--accent);" />
                        </div>
                    </div>
                </div>

                <div style="position:sticky;top:24px;border-radius:16px;background:var(--surface);border:1px solid var(--border);padding:24px;">
                    <div style="font-size:16px;font-weight:900;margin-bottom:14px;">Ваш заказ</div>
                    @foreach($items as $item)
                        <div style="display:flex;gap:10px;margin-bottom:10px;">
                            <div style="width:44px;height:44px;border-radius:8px;background:var(--bg-elevated);flex-shrink:0;display:flex;align-items:center;justify-content:center;color:var(--text-faint);"><x-icon name="image" :size="18" /></div>
                            <div style="flex:1;font-size:13px;font-weight:700;">{{ $item->product->name }} <span style="color:var(--text-faint);font-weight:600;">&times; {{ $item->quantity }}</span></div>
                            <div style="font-size:13px;font-weight:800;">{{ number_format($item->product->price * $item->quantity, 0, '', ' ') }} TMT</div>
                        </div>
                    @endforeach

                    <div style="border-top:1px solid var(--border);margin-top:10px;padding-top:10px;">
                        <div style="display:flex;justify-content:space-between;font-size:14px;padding:9px 0;color:var(--text-muted);font-weight:600;"><span>Товары</span><span style="color:var(--text);">{{ number_format($subtotal + $discount, 0, '', ' ') }} TMT</span></div>
                        @if($discount > 0)
                            <div style="display:flex;justify-content:space-between;font-size:14px;padding:9px 0;color:var(--text-muted);font-weight:600;"><span>Скидка</span><span style="color:var(--danger);">&minus;{{ number_format($discount, 0, '', ' ') }} TMT</span></div>
                        @endif
                        <div style="display:flex;justify-content:space-between;font-size:14px;padding:9px 0;color:var(--text-muted);font-weight:600;"><span>Доставка</span><span style="color:var(--success);">Бесплатно</span></div>
                    </div>
                    <div style="border-top:1px solid var(--border);margin-top:6px;padding-top:14px;display:flex;justify-content:space-between;align-items:baseline;">
                        <span style="font-size:16px;font-weight:800;">К оплате</span>
                        <span style="font-size:24px;font-weight:900;">{{ number_format($total, 0, '', ' ') }} TMT</span>
                    </div>
                    <button type="submit" class="btn-accent" style="width:100%;margin-top:20px;height:52px;font-size:15px;">Подтвердить заказ</button>
                </div>
            </div>
        </form>
    </div>
</x-layout>
