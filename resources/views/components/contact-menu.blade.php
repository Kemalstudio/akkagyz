@props(['business'])
@php($phones = $business->contact_phones ?: array_filter([$business->phone]))
@if($business->contact_button_enabled ?? true)
<div class="contact-wrap">
    <button type="button" class="contact-trigger" onclick="akToggleMenu(event,'contact-menu')" aria-label="Открыть контакты"><x-icon name="phone" :size="13"/>Связаться с нами<x-icon class="contact-chevron" name="chevron-down" :size="11"/></button>
    <div id="contact-menu" class="dropdown-panel contact-menu">
        <div class="contact-head"><div class="contact-title">Связаться с нами</div><div class="contact-copy">Выберите удобный номер — мы готовы помочь.</div></div>
        <div class="contact-list">
            @if($business->contact_phone_visible ?? true)
                @foreach($phones as $phone)
                    <a class="contact-row" href="tel:{{ preg_replace('/[^+0-9]/', '', $phone) }}"><span class="contact-icon"><x-icon name="phone" :size="16"/></span><span><span class="contact-label">Магазин {{ count($phones)>1 ? '· '.($loop->iteration) : '' }}</span><span class="contact-value">{{ $phone }}</span></span></a>
                @endforeach
            @endif
            @if($business->email)<a class="contact-row" href="mailto:{{ $business->email }}"><span class="contact-icon"><x-icon name="mail" :size="16"/></span><span><span class="contact-label">Электронная почта</span><span class="contact-value">{{ $business->email }}</span></span></a>@endif
            @if($business->address)<div class="contact-row"><span class="contact-icon"><x-icon name="store" :size="16"/></span><span><span class="contact-label">Адрес</span><span class="contact-value">{{ $business->address }}</span></span></div>@endif
            @if((!($business->contact_phone_visible ?? true) || count($phones)===0) && !$business->email && !$business->address)<div class="contact-empty">Контактные данные временно скрыты.</div>@endif
        </div>
    </div>
</div>
@endif
