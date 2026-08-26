@php
    $languageSettings = \App\Models\BusinessSetting::current();
    $enabled = $languageSettings->enabled_locales ?: ['ru', 'tk', 'en'];
    $languages = ['ru' => ['🇷🇺', 'Русский'], 'tk' => ['🇹🇲', 'Türkmençe'], 'en' => ['🇬🇧', 'English']];
    $current = app()->getLocale();
@endphp
<div class="language-wrap"><button type="button" class="language-trigger" onclick="akToggleMenu(event,'language-menu')" aria-label="Выбрать язык"><x-icon name="globe" :size="17"/><span>{{ strtoupper($current) }}</span><x-icon name="chevron-down" :size="11"/></button><div id="language-menu" class="dropdown-panel language-menu"><div class="language-head">Язык интерфейса</div>@foreach($enabled as $locale)@if(isset($languages[$locale]))<form method="POST" action="{{ route('language.switch',$locale) }}">@csrf<button class="language-option {{ $current===$locale?'active':'' }}"><span class="language-flag">{{ $languages[$locale][0] }}</span><span>{{ $languages[$locale][1] }}</span>@if($current===$locale)<x-icon name="check" :size="14"/>@endif</button></form>@endif @endforeach</div></div>
