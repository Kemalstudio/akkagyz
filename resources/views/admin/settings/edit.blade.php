@php
    $settingsCards = [
        'contacts' => ['icon' => 'phone', 'color' => 'accent', 'title' => 'Контакты', 'desc' => 'Телефоны магазина и контактная кнопка', 'status' => count($settings->contact_phones ?: []).' номер(а)', 'statusColor' => 'accent'],
        'languages' => ['icon' => 'globe', 'color' => 'accent', 'title' => 'Языки', 'desc' => 'Основной язык и переключатель на сайте', 'status' => count($settings->enabled_locales ?: []).' языка', 'statusColor' => 'accent'],
        'storefront' => ['icon' => 'image', 'color' => 'accent', 'title' => 'Витрина и приложение', 'desc' => 'Слоган, соцсети, ссылки на приложения', 'status' => null, 'statusColor' => null],
        'company' => ['icon' => 'store', 'color' => 'accent', 'title' => 'Компания и бренд', 'desc' => 'Название, логотип, юридические данные', 'status' => null, 'statusColor' => null],
        'commission' => ['icon' => 'chart', 'color' => 'success', 'title' => 'Комиссия площадки', 'desc' => 'Процент удержания с продаж продавцов', 'status' => rtrim(rtrim(number_format($settings->platform_commission_percent, 2), '0'), '.').'%', 'statusColor' => 'success'],
        'access' => ['icon' => 'shield', 'color' => 'warning', 'title' => 'Доступ к сайту', 'desc' => 'Публичная доступность маркетплейса', 'status' => $settings->development_mode ? 'Разработка' : 'Онлайн', 'statusColor' => $settings->development_mode ? 'warning' : 'success'],
        'notifications' => ['icon' => 'bell', 'color' => 'accent', 'title' => 'Уведомления', 'desc' => 'События, о которых сообщает система', 'status' => collect([$settings->email_notifications, $settings->order_notifications, $settings->seller_notifications])->filter()->count().' из 3 вкл.', 'statusColor' => 'accent'],
        'otp' => ['icon' => 'lock', 'color' => 'accent', 'title' => 'OTP-защита', 'desc' => 'Одноразовый код подтверждения при входе', 'status' => $settings->otp_enabled ? 'Включено' : 'Выключено', 'statusColor' => $settings->otp_enabled ? 'success' : 'accent'],
        'smtp' => ['icon' => 'mail', 'color' => 'accent', 'title' => 'SMTP-почта', 'desc' => 'Сервер для OTP, заказов и уведомлений', 'status' => $settings->smtp_enabled ? 'Включено' : 'Выключено', 'statusColor' => $settings->smtp_enabled ? 'success' : 'accent'],
    ];
    $sectionFields = [
        'contacts' => ['contact_phones', 'contact_button_enabled', 'contact_phone_visible'],
        'languages' => ['default_locale', 'enabled_locales'],
        'storefront' => ['tagline', 'store_description', 'currency', 'primary_color', 'support_hours', 'instagram_url', 'telegram_url', 'whatsapp_url', 'google_play_url', 'app_store_url'],
        'company' => ['site_name', 'company_name', 'phone', 'email', 'address', 'logo'],
        'commission' => ['platform_commission_percent'],
        'access' => ['development_mode', 'development_title', 'development_message'],
        'notifications' => ['email_notifications', 'order_notifications', 'seller_notifications'],
        'otp' => ['otp_enabled', 'otp_channel', 'otp_ttl', 'otp_length'],
        'smtp' => ['smtp_enabled', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'smtp_from_address', 'smtp_from_name', 'smtp'],
    ];
    $errorPanel = null;
    if ($errors->any()) {
        foreach ($sectionFields as $sectionId => $fields) {
            if (collect($fields)->contains(fn ($f) => $errors->has($f))) { $errorPanel = $sectionId; break; }
        }
    }
@endphp
<x-dashboard-layout title="Бизнес-настройки" active="settings">
<style>
.settings-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:14px;margin-bottom:22px}
.settings-card{position:relative;text-align:left;padding:20px;border-radius:16px;background:linear-gradient(145deg,var(--surface),color-mix(in oklch,var(--surface) 92%,var(--bg)));border:1px solid var(--border);box-shadow:0 1px 2px rgba(0,0,0,.04);transition:.2s;cursor:pointer;font-family:var(--font);color:inherit;display:flex;flex-direction:column;gap:10px;width:100%}
.settings-card:hover{transform:translateY(-2px);border-color:var(--border-strong);box-shadow:0 12px 30px rgba(0,0,0,.1)}
.settings-card-icon{width:40px;height:40px;border-radius:12px;display:grid;place-items:center}
.settings-card-title{font-size:14px;font-weight:700;color:var(--text)}
.settings-card-desc{font-size:11px;color:var(--text-faint);line-height:1.5}
.settings-card-badge{align-self:flex-start}
.settings-section{padding:26px;margin-bottom:18px}
.settings-section-head{display:flex;gap:13px;align-items:flex-start;padding-bottom:20px;margin-bottom:20px;border-bottom:1px solid var(--border)}.settings-section-head .entity-icon{width:40px;height:40px}.settings-section-title{font-size:16px;font-weight:700}.settings-section-note{font-size:11px;color:var(--text-faint);margin-top:3px}
.switch-row{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:14px 0;border-bottom:1px solid var(--border)}.switch-row:last-child{border:0}.switch-copy b{display:block;font-size:13px;font-weight:600}.switch-copy span{display:block;font-size:11px;color:var(--text-faint);margin-top:3px}.switch{position:relative;width:42px;height:24px;flex-shrink:0}.switch input{opacity:0;width:0;height:0}.switch span{position:absolute;inset:0;border-radius:20px;background:var(--surface-hover);border:1px solid var(--border);transition:.2s}.switch span:after{content:'';position:absolute;width:18px;height:18px;left:2px;top:2px;border-radius:50%;background:var(--text-faint);transition:.2s}.switch input:checked+span{background:var(--accent);border-color:var(--accent)}.switch input:checked+span:after{transform:translateX(18px);background:white}
.dev-panel{padding:16px;border-radius:13px;background:var(--warning-soft);border:1px solid color-mix(in oklch,var(--warning) 25%,transparent);margin-bottom:18px}
.logo-preview{height:74px;max-width:220px;display:flex;align-items:center;padding:12px;border-radius:12px;background:var(--bg);border:1px solid var(--border);margin-bottom:10px}.logo-preview img{max-height:48px;max-width:190px}
.save-bar{position:sticky;bottom:16px;display:flex;justify-content:flex-end;gap:10px;padding:14px 16px;background:color-mix(in oklch,var(--surface) 90%,transparent);border:1px solid var(--border);border-radius:14px;backdrop-filter:blur(15px);box-shadow:0 12px 30px rgba(0,0,0,.16);z-index:5}
.notify-row{display:flex;align-items:center;gap:12px;min-width:0}
.settings-back{display:inline-flex;margin-bottom:16px}
@media(max-width:850px){.settings-section{padding:20px}}
</style>
<div class="page-head"><div><div class="page-title">Бизнес-настройки</div><div class="page-subtitle">Управление брендом, доступностью сайта, безопасностью и системными уведомлениями</div></div><span class="badge" style="background:{{ $settings->development_mode?'var(--warning-soft)':'var(--success-soft)' }};color:{{ $settings->development_mode?'var(--warning)':'var(--success)' }}">{{ $settings->development_mode?'Режим разработки':'Сайт работает' }}</span></div>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<div id="settings-grid" class="settings-grid">
    @foreach($settingsCards as $id => $card)
    <button type="button" class="settings-card" data-open-panel="{{ $id }}">
        <div style="display:flex;justify-content:space-between;align-items:start">
            <div class="settings-card-icon" style="background:var(--{{ $card['color'] }}-soft);color:var(--{{ $card['color'] }})"><x-icon :name="$card['icon']" :size="19"/></div>
            <x-icon name="chevron-right" :size="15" style="color:var(--text-faint)"/>
        </div>
        <div class="settings-card-title">{{ $card['title'] }}</div>
        <div class="settings-card-desc">{{ $card['desc'] }}</div>
        @if($card['status'])<span class="badge settings-card-badge" style="background:var(--{{ $card['statusColor'] }}-soft);color:var(--{{ $card['statusColor'] }})">{{ $card['status'] }}</span>@endif
    </button>
    @endforeach
</div>

<div id="settings-panels">
<button type="button" class="btn-ghost settings-back" id="settings-back"><x-icon name="chevron-left" :size="15"/>Назад к разделам</button>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">@csrf @method('PUT')
<div data-panel="contacts">@include('admin.settings.contact')</div>
<div data-panel="languages">@include('admin.settings.languages')</div>
<div data-panel="storefront">@include('admin.settings.storefront')</div>
<div data-panel="company"><section id="company" class="card settings-section"><div class="settings-section-head"><span class="entity-icon"><x-icon name="store" :size="18"/></span><div><div class="settings-section-title">Компания и бренд</div><div class="settings-section-note">Данные отображаются клиентам и используются в системных письмах</div></div></div><div class="form-grid"><div class="field"><label>Название сайта</label><input class="input" required name="site_name" value="{{ old('site_name',$settings->site_name) }}"></div><div class="field"><label>Юридическое название</label><input class="input" name="company_name" value="{{ old('company_name',$settings->company_name) }}"></div><div class="field"><label>Номер телефона</label><input class="input" name="phone" value="{{ old('phone',$settings->phone) }}" placeholder="+993 61 00 00 00"></div><div class="field"><label>Контактный email</label><input class="input" type="email" name="email" value="{{ old('email',$settings->email) }}"></div><div class="field full"><label>Адрес</label><input class="input" name="address" value="{{ old('address',$settings->address) }}"></div><div class="field full"><label>Логотип</label>@if($settings->logo_url)<div class="logo-preview"><img src="{{ $settings->logo_url }}" alt="{{ $settings->site_name }}"></div>@endif<input class="input" style="padding:10px;height:auto" type="file" name="logo" accept="image/*"><small>PNG, JPG, WEBP или SVG до 4 МБ. Рекомендуется прозрачный фон.</small></div></div></section></div>
<div data-panel="commission"><section id="commission" class="card settings-section"><div class="settings-section-head"><span class="entity-icon" style="background:var(--success-soft);color:var(--success)"><x-icon name="chart" :size="18"/></span><div><div class="settings-section-title">Комиссия площадки</div><div class="settings-section-note">Процент, который платформа удерживает с каждой продажи продавца — используется при расчёте выплат в разделе «Финансы»</div></div></div><div class="form-grid"><div class="field"><label>Комиссия, %</label><input class="input" type="number" step="0.01" min="0" max="100" required name="platform_commission_percent" value="{{ old('platform_commission_percent',$settings->platform_commission_percent) }}"><small style="color:var(--text-faint)">Например, 10 значит продавец получает 90% от цены товара</small></div></div></section></div>
<div data-panel="access"><section id="access" class="card settings-section"><div class="settings-section-head"><span class="entity-icon" style="background:var(--warning-soft);color:var(--warning)"><x-icon name="shield" :size="18"/></span><div><div class="settings-section-title">Доступ к сайту</div><div class="settings-section-note">Управление публичной доступностью маркетплейса</div></div></div><div class="dev-panel"><div class="switch-row" style="padding:0"><div class="switch-copy"><b>Режим разработки</b><span>Покупатели и продавцы увидят техническую страницу. Админ-панель останется доступна.</span></div><label class="switch"><input type="checkbox" id="dev-mode-toggle" name="development_mode" value="1" @checked(old('development_mode',$settings->development_mode))><span></span></label></div></div><div class="form-grid"><div class="field full"><label>Заголовок технической страницы</label><input class="input" name="development_title" required value="{{ old('development_title',$settings->development_title) }}"></div><div class="field full"><label>Сообщение посетителям</label><textarea class="input" name="development_message" style="height:90px;padding:12px">{{ old('development_message',$settings->development_message) }}</textarea></div></div></section></div>
<div data-panel="notifications"><section id="notifications" class="card settings-section"><div class="settings-section-head"><span class="entity-icon"><x-icon name="bell" :size="18"/></span><div><div class="settings-section-title">Уведомления</div><div class="settings-section-note">Выберите события, о которых должна сообщать система</div></div></div>@foreach([['email_notifications','mail','accent','Email-уведомления','Разрешить отправку системных писем'],['order_notifications','cart','success','Новые заказы','Сообщать о новых заказах'],['seller_notifications','store','warning','Заявки продавцов','Сообщать о регистрации и модерации продавцов']] as $option)<div class="switch-row"><div class="notify-row"><span class="entity-icon" style="background:var(--{{ $option[2] }}-soft);color:var(--{{ $option[2] }});flex-shrink:0"><x-icon :name="$option[1]" :size="17"/></span><div class="switch-copy"><b>{{ $option[3] }}</b><span>{{ $option[4] }}</span></div></div><label class="switch"><input type="checkbox" name="{{ $option[0] }}" value="1" @checked(old($option[0],$settings->{$option[0]}))><span></span></label></div>@endforeach</section></div>
<div data-panel="otp"><section id="otp" class="card settings-section"><div class="settings-section-head"><span class="entity-icon"><x-icon name="lock" :size="18"/></span><div><div class="settings-section-title">OTP и безопасность</div><div class="settings-section-note">Настройки одноразового кода подтверждения</div></div></div><div class="switch-row" style="padding-top:0"><div class="switch-copy"><b>Включить OTP</b><span>Требовать одноразовый email-код при входе покупателей и продавцов</span></div><label class="switch"><input type="checkbox" name="otp_enabled" value="1" @checked(old('otp_enabled',$settings->otp_enabled))><span></span></label></div><div class="form-grid" style="margin-top:18px"><div class="field"><label>Канал доставки</label><select class="input" name="otp_channel"><option value="email">Email через SMTP</option></select></div><div class="field"><label>Срок действия, минут</label><input class="input" type="number" min="1" max="30" name="otp_ttl" value="{{ old('otp_ttl',$settings->otp_ttl) }}"></div><div class="field"><label>Длина кода</label><select class="input" name="otp_length">@foreach([4,5,6,7,8] as $length)<option value="{{ $length }}" @selected($settings->otp_length==$length)>{{ $length }} цифр</option>@endforeach</select></div></div></section></div>
<div data-panel="smtp"><section id="smtp" class="card settings-section"><div class="settings-section-head"><span class="entity-icon"><x-icon name="mail" :size="18"/></span><div><div class="settings-section-title">SMTP-почта</div><div class="settings-section-note">Сервер для OTP, заказов и служебных уведомлений</div></div></div><div class="switch-row" style="padding-top:0"><div class="switch-copy"><b>Использовать SMTP</b><span>Отправлять реальные письма через указанный сервер</span></div><label class="switch"><input type="checkbox" name="smtp_enabled" value="1" @checked(old('smtp_enabled',$settings->smtp_enabled))><span></span></label></div><div class="form-grid" style="margin-top:18px"><div class="field"><label>SMTP-сервер</label><input class="input" name="smtp_host" value="{{ old('smtp_host',$settings->smtp_host) }}" placeholder="smtp.example.com"></div><div class="field"><label>Порт</label><input class="input" type="number" name="smtp_port" value="{{ old('smtp_port',$settings->smtp_port) }}"></div><div class="field"><label>Логин</label><input class="input" name="smtp_username" value="{{ old('smtp_username',$settings->smtp_username) }}"></div><div class="field"><label>Пароль</label><input class="input" type="password" name="smtp_password" placeholder="{{ $settings->smtp_password?'••••••••':'Пароль SMTP' }}"><small>Оставьте пустым, чтобы сохранить текущий пароль.</small></div><div class="field"><label>Шифрование</label><select class="input" name="smtp_encryption"><option value="tls" @selected($settings->smtp_encryption==='tls')>TLS</option><option value="ssl" @selected($settings->smtp_encryption==='ssl')>SSL</option><option value="none" @selected($settings->smtp_encryption==='none')>Без шифрования</option></select></div><div class="field"><label>Email отправителя</label><input class="input" type="email" name="smtp_from_address" value="{{ old('smtp_from_address',$settings->smtp_from_address) }}"></div><div class="field"><label>Имя отправителя</label><input class="input" name="smtp_from_name" value="{{ old('smtp_from_name',$settings->smtp_from_name) }}"></div></div></section></div>
<div class="save-bar"><span style="margin-right:auto;font-size:11px;color:var(--text-faint);align-self:center">Изменения применятся сразу после сохранения</span><button class="btn-accent"><x-icon name="check" :size="15"/>Сохранить настройки</button></div>
</form>
<div data-panel="smtp"><form method="POST" action="{{ route('admin.settings.test-mail') }}" class="card" style="padding:22px;margin-top:18px">@csrf<div style="font-size:14px;font-weight:700">Проверка почты</div><div class="page-subtitle" style="margin-bottom:14px">Сначала сохраните SMTP-настройки, затем отправьте тестовое письмо</div><div class="toolbar"><input class="admin-input" style="flex:1" type="email" required name="test_email" value="{{ $settings->email }}" placeholder="email@example.com"><button class="btn-ghost"><x-icon name="mail" :size="14"/>Отправить тест</button></div></form></div>
</div>

<script>
(function () {
    var grid = document.getElementById('settings-grid');
    var panelsWrap = document.getElementById('settings-panels');
    var panels = Array.from(panelsWrap.querySelectorAll('[data-panel]'));
    var panelIds = Array.from(new Set(panels.map(function (p) { return p.dataset.panel; })));

    function openPanel(id) {
        grid.style.display = 'none';
        panelsWrap.style.display = 'block';
        panels.forEach(function (p) { p.style.display = p.dataset.panel === id ? 'block' : 'none'; });
        window.scrollTo({ top: 0, behavior: 'instant' });
        history.replaceState(null, '', '#' + id);
    }
    function showGrid() {
        panelsWrap.style.display = 'none';
        grid.style.display = 'grid';
        history.replaceState(null, '', location.pathname + location.search);
    }

    document.querySelectorAll('[data-open-panel]').forEach(function (card) {
        card.addEventListener('click', function () { openPanel(card.dataset.openPanel); });
    });
    document.getElementById('settings-back').addEventListener('click', showGrid);

    var hash = location.hash.replace('#', '');
    var errorPanel = @json($errorPanel);
    var initial = panelIds.includes(hash) ? hash : errorPanel;
    if (initial) { openPanel(initial); } else { showGrid(); }

    document.getElementById('dev-mode-toggle')?.addEventListener('change', function () {
        if (this.checked && !confirm('Включить режим разработки? Сайт станет недоступен для покупателей и продавцов, останется доступна только админ-панель.')) {
            this.checked = false;
        }
    });
})();
</script>
</x-dashboard-layout>
