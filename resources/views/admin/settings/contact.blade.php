<style>
#company .form-grid>.field:nth-child(3){display:none}.phones-list{display:grid;gap:10px}.phone-editor-row{display:grid;grid-template-columns:42px minmax(0,1fr) 38px;gap:9px;align-items:center}.phone-editor-icon{width:42px;height:42px;border-radius:11px;display:grid;place-items:center;background:var(--accent-soft);color:var(--accent)}.phone-remove{width:38px;height:38px;border:1px solid var(--border);border-radius:10px;background:var(--surface);color:var(--text-faint);display:grid;place-items:center;cursor:pointer}.phone-remove:hover{color:var(--danger);border-color:color-mix(in oklch,var(--danger) 35%,var(--border));background:var(--danger-soft)}.contact-actions{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:18px;padding-top:18px;border-top:1px solid var(--border)}
</style>
@php($contactPhones = old('contact_phones', $settings->contact_phones ?: array_filter([$settings->phone ?: '+99364005374'])))
<section id="contacts" class="card settings-section">
    <div class="settings-section-head"><span class="entity-icon"><x-icon name="phone" :size="18"/></span><div><div class="settings-section-title">Связь с магазином</div><div class="settings-section-note">Добавьте до 10 номеров, которые будут доступны в верхней панели сайта</div></div></div>
    <div class="field full"><label>Телефоны магазина</label><div id="contact-phones-list" class="phones-list">
        @forelse($contactPhones as $phone)
            <div class="phone-editor-row"><span class="phone-editor-icon"><x-icon name="phone" :size="16"/></span><input class="input" name="contact_phones[]" value="{{ $phone }}" placeholder="+99364005374" autocomplete="tel"><button class="phone-remove" type="button" onclick="removeContactPhone(this)" title="Удалить номер"><x-icon name="trash" :size="15"/></button></div>
        @empty
            <div class="phone-editor-row"><span class="phone-editor-icon"><x-icon name="phone" :size="16"/></span><input class="input" name="contact_phones[]" placeholder="+99364005374" autocomplete="tel"><button class="phone-remove" type="button" onclick="removeContactPhone(this)" title="Удалить номер"><x-icon name="trash" :size="15"/></button></div>
        @endforelse
    </div><small>Можно добавить до 10 номеров. Пустые строки не сохраняются.</small></div>
    <button class="btn-ghost" type="button" style="margin-top:12px" onclick="addContactPhone()"><x-icon name="plus" :size="15"/>Добавить номер</button>
    <div class="switch-row"><div class="switch-copy"><b>Показывать кнопку «Связаться с нами»</b><span>Полностью скрывает или возвращает контактный модуль.</span></div><label class="switch"><input type="checkbox" name="contact_button_enabled" value="1" @checked(old('contact_button_enabled',$settings->contact_button_enabled ?? true))><span></span></label></div>
    <div class="switch-row"><div class="switch-copy"><b>Показывать номера телефонов</b><span>Номера останутся сохранёнными, но не будут видны посетителям.</span></div><label class="switch"><input type="checkbox" name="contact_phone_visible" value="1" @checked(old('contact_phone_visible',$settings->contact_phone_visible ?? true))><span></span></label></div>
    <div class="contact-actions"><span style="font-size:11px;color:var(--text-faint)">Сохраняются только номера и настройки контактной кнопки.</span><button class="btn-accent" type="submit" name="save_section" value="contacts" formnovalidate><x-icon name="check" :size="15"/>Сохранить контакты</button></div>
</section>
<template id="contact-phone-template"><div class="phone-editor-row"><span class="phone-editor-icon"><x-icon name="phone" :size="16"/></span><input class="input" name="contact_phones[]" placeholder="+993 64 00 53 74" autocomplete="tel"><button class="phone-remove" type="button" onclick="removeContactPhone(this)" title="Удалить номер"><x-icon name="trash" :size="15"/></button></div></template>
<script>
function addContactPhone(){const list=document.getElementById('contact-phones-list');if(list.children.length>=10)return;list.appendChild(document.getElementById('contact-phone-template').content.cloneNode(true));list.lastElementChild.querySelector('input').focus()}
function removeContactPhone(button){button.closest('.phone-editor-row').remove()}
</script>
