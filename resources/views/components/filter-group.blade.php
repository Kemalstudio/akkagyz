@props(['title', 'icon' => 'filter', 'active' => false])
<div class="filter-group {{ $active ? 'open' : '' }}">
    <button type="button" class="filter-group-head">
        <span class="filter-group-head-label"><x-icon :name="$icon" :size="15"/>{{ $title }}</span>
        <x-icon name="chevron-down" :size="14" class="filter-group-chevron"/>
    </button>
    <div class="filter-group-body">
        <div class="filter-group-body-inner">
            <div class="filter-group-body-content">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
