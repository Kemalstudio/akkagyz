@props([
    'title',
    'icon' => 'filter',
    'active' => false,
    'groupId' => null,
    'selectedCount' => 0,
])
@php
    $resolvedGroupId = $groupId ?: 'catalog-filter-'.\Illuminate\Support\Str::slug($title);
    $triggerId = $resolvedGroupId.'-trigger';
    $panelId = $resolvedGroupId.'-panel';
@endphp
<div class="filter-group {{ $active ? 'open' : '' }}" data-filter-group>
    <button
        type="button"
        id="{{ $triggerId }}"
        class="filter-group-head"
        data-filter-group-trigger
        aria-expanded="{{ $active ? 'true' : 'false' }}"
        aria-controls="{{ $panelId }}"
    >
        <span class="filter-group-head-label">
            <x-icon :name="$icon" :size="15" aria-hidden="true" focusable="false"/>
            <span>{{ $title }}</span>
            @if($selectedCount > 0)
                <span class="filter-group-selected-count" aria-label="Выбрано: {{ $selectedCount }}">{{ $selectedCount }}</span>
            @endif
        </span>
        <x-icon name="chevron-down" :size="14" class="filter-group-chevron" aria-hidden="true" focusable="false"/>
    </button>
    <div
        id="{{ $panelId }}"
        class="filter-group-body"
        data-filter-group-panel
        aria-labelledby="{{ $triggerId }}"
        aria-hidden="{{ $active ? 'false' : 'true' }}"
        @if(!$active) inert @endif
    >
        <div class="filter-group-body-inner">
            <div class="filter-group-body-content">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
