<?php
    $statuses = [
        'pending' => ['Заказ принят', 'Мы получили заказ и скоро начнём обработку.'],
        'processing' => ['Собираем заказ', 'Товары проверяются и готовятся к отправке.'],
        'shipped' => ['Передан в доставку', 'Заказ уже в пути к вам.'],
        'delivered' => ['Заказ доставлен', 'Доставка успешно завершена.'],
    ];
    $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
    $currentStep = isset($order) ? array_search($order->status, $statusOrder, true) : false;
?>

<x-layout title="Отследить заказ">
    <style>
        .tracking-page{padding:44px 24px 72px;max-width:1060px}.tracking-hero{text-align:center;max-width:650px;margin:0 auto 28px}.tracking-icon{width:58px;height:58px;border-radius:18px;margin:0 auto 16px;display:grid;place-items:center;color:#fff;background:linear-gradient(135deg,var(--accent),#6c5ce7);box-shadow:0 14px 30px rgba(37,99,235,.23)}.tracking-title{font-size:32px;letter-spacing:-.04em;margin:0 0 8px}.tracking-copy{color:var(--text-muted);font-size:14px;line-height:1.65}.tracking-card{background:var(--surface);border:1px solid var(--border);border-radius:22px;box-shadow:0 18px 50px rgba(22,34,60,.07)}.tracking-form{padding:24px;display:grid;grid-template-columns:1fr 1fr auto;gap:14px;align-items:end}.tracking-field label{display:block;font-size:12px;font-weight:700;margin:0 0 7px}.tracking-input{height:48px;width:100%;border:1px solid var(--border);border-radius:12px;padding:0 15px;background:var(--bg);outline:none;transition:.2s}.tracking-input:focus{border-color:var(--accent);box-shadow:0 0 0 4px var(--accent-soft)}.tracking-submit{height:48px;padding:0 24px;border:0;border-radius:12px;background:var(--accent);color:#fff;display:flex;gap:8px;align-items:center;justify-content:center;cursor:pointer;font-weight:700}.tracking-error{margin:0 24px 20px;padding:12px 14px;border-radius:11px;background:var(--danger-soft);color:var(--danger);font-size:13px}.tracking-result{margin-top:22px;overflow:hidden}.tracking-result-head{padding:22px 24px;display:flex;align-items:center;justify-content:space-between;gap:20px;border-bottom:1px solid var(--border)}.tracking-number{font-size:20px;font-weight:700}.tracking-meta{color:var(--text-faint);font-size:12px;margin-top:4px}.tracking-badge{padding:8px 12px;border-radius:999px;background:var(--accent-soft);color:var(--accent);font-size:12px;font-weight:700}.tracking-progress{padding:30px 28px;display:grid;grid-template-columns:repeat(4,1fr)}.tracking-step{position:relative;text-align:center;padding:0 8px}.tracking-step:before{content:'';position:absolute;height:3px;background:var(--border);left:-50%;right:50%;top:18px}.tracking-step:first-child:before{display:none}.tracking-step.done:before{background:var(--accent)}.tracking-dot{position:relative;z-index:1;margin:auto;width:38px;height:38px;border-radius:50%;display:grid;place-items:center;background:var(--bg);border:2px solid var(--border);color:var(--text-faint)}.tracking-step.done .tracking-dot{background:var(--accent);border-color:var(--accent);color:#fff}.tracking-step strong{display:block;font-size:12px;margin-top:10px}.tracking-step span{display:block;font-size:10px;line-height:1.45;color:var(--text-faint);margin-top:4px}.tracking-details{padding:22px 24px;border-top:1px solid var(--border);display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.tracking-detail{padding:15px;border-radius:14px;background:var(--bg)}.tracking-detail span{display:block;font-size:10px;color:var(--text-faint);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px}.tracking-detail strong{font-size:13px;font-weight:600}.tracking-cancelled{margin:24px;padding:16px;border-radius:13px;background:var(--danger-soft);color:var(--danger);display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600}.tracking-help{text-align:center;color:var(--text-faint);font-size:12px;margin-top:18px}@media(max-width:760px){.tracking-form{grid-template-columns:1fr}.tracking-progress{grid-template-columns:1fr;padding:22px}.tracking-step{text-align:left;display:grid;grid-template-columns:38px 1fr;column-gap:12px;padding:0 0 20px}.tracking-step:before{width:3px;height:auto;left:18px;top:-20px;bottom:38px}.tracking-step strong,.tracking-step span{grid-column:2;margin-top:0}.tracking-step span{margin-top:-14px}.tracking-details{grid-template-columns:1fr}.tracking-result-head{align-items:flex-start;flex-direction:column}.tracking-title{font-size:27px}}
    </style>
    <main class="wrap tracking-page">
        <header class="tracking-hero">
            <div class="tracking-icon"><x-icon name="truck" :size="28"/></div>
            <h1 class="tracking-title">Где мой заказ?</h1>
            <p class="tracking-copy">Введите номер заказа и телефон, использованный при оформлении. Мы покажем актуальный этап доставки без регистрации.</p>
        </header>

        <section class="tracking-card">
            <form method="POST" action="{{ route('orders.lookup') }}" class="tracking-form">
                @csrf
                <div class="tracking-field"><label for="number">Номер заказа</label><input class="tracking-input" id="number" name="number" value="{{ old('number', $order->number ?? '') }}" placeholder="Например, AK-X7QK2M" autocomplete="off" required></div>
                <div class="tracking-field"><label for="phone">Телефон получателя</label><input class="tracking-input" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+993 64 00 53 74" inputmode="tel" autocomplete="tel" required></div>
                <button class="tracking-submit"><x-icon name="search" :size="17"/>Проверить</button>
            </form>
            @error('number')<div class="tracking-error">{{ $message }}</div>@enderror
            @error('phone')<div class="tracking-error">{{ $message }}</div>@enderror
        </section>

        @isset($order)
            <section class="tracking-card tracking-result">
                <div class="tracking-result-head"><div><div class="tracking-number">Заказ {{ $order->number }}</div><div class="tracking-meta">Оформлен {{ $order->created_at->format('d.m.Y в H:i') }} · {{ $order->items->sum('quantity') }} товар(а)</div></div><div class="tracking-badge">{{ $order->status === 'cancelled' ? 'Заказ отменён' : ($statuses[$order->status][0] ?? $order->status) }}</div></div>
                @if($order->status === 'cancelled')
                    <div class="tracking-cancelled"><x-icon name="x" :size="20"/>Этот заказ отменён. Если это произошло по ошибке, свяжитесь с магазином.</div>
                @else
                    <div class="tracking-progress">
                        @foreach($statusOrder as $index => $key)
                            <div class="tracking-step {{ $currentStep !== false && $index <= $currentStep ? 'done' : '' }}"><div class="tracking-dot"><x-icon name="check" :size="17"/></div><strong>{{ $statuses[$key][0] }}</strong><span>{{ $statuses[$key][1] }}</span></div>
                        @endforeach
                    </div>
                @endif
                <div class="tracking-details">
                    <div class="tracking-detail"><span>Способ получения</span><strong>{{ $order->delivery_method === 'pickup' ? 'Самовывоз' : 'Курьерская доставка' }}</strong></div>
                    <div class="tracking-detail"><span>Направление</span><strong>{{ $order->city ?: 'Уточняется' }}{{ $order->address ? ', '.$order->address : '' }}</strong></div>
                    <div class="tracking-detail"><span>Сумма заказа</span><strong>{{ number_format($order->total, 0, '', ' ') }} TMT</strong></div>
                </div>
            </section>
        @endisset
        <p class="tracking-help">Данные защищены: для поиска нужны одновременно номер заказа и телефон получателя.</p>
    </main>
</x-layout>
