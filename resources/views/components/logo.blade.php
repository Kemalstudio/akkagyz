@props(['height' => 36, 'forceDark' => false])
@php($businessSettings = \App\Models\BusinessSetting::current())
@if($businessSettings->logo_url)
    <img src="{{ $businessSettings->logo_url }}" alt="{{ $businessSettings->site_name }}" style="height:{{ $height }}px;width:auto;max-width:190px;object-fit:contain;display:block" {{ $attributes }}>
@elseif($forceDark)
    <img src="{{ asset('logo-full-dark.png') }}" alt="AK KAGYZ" style="height:{{ $height }}px;width:auto;display:block;" {{ $attributes }}>
@else
    <img src="{{ asset('logo-full.png') }}" alt="AK KAGYZ" class="ak-logo-light" style="height:{{ $height }}px;width:auto;display:block;" {{ $attributes }}>
    <img src="{{ asset('logo-full-dark.png') }}" alt="AK KAGYZ" class="ak-logo-dark" style="height:{{ $height }}px;width:auto;display:none;" {{ $attributes }}>
@endif
