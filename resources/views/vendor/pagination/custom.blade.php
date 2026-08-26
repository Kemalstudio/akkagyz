@if ($paginator->hasPages())
    <nav class="ak-pagination" aria-label="Навигация по страницам">
        @if ($paginator->onFirstPage())
            <span class="ak-page-btn ak-page-nav disabled"><x-icon name="chevron-left" :size="15" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="ak-page-btn ak-page-nav" rel="prev"><x-icon name="chevron-left" :size="15" /></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="ak-page-btn ak-page-dots">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="ak-page-btn active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="ak-page-btn">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="ak-page-btn ak-page-nav" rel="next"><x-icon name="chevron-right" :size="15" /></a>
        @else
            <span class="ak-page-btn ak-page-nav disabled"><x-icon name="chevron-right" :size="15" /></span>
        @endif
    </nav>
    <style>
        .ak-pagination{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
        .ak-page-btn{min-width:38px;height:38px;padding:0 6px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:var(--text-muted);background:var(--surface);border:1px solid var(--border);text-decoration:none;transition:border-color .15s ease,background .15s ease,color .15s ease;}
        a.ak-page-btn:hover{background:var(--surface-hover);border-color:var(--border-strong);color:var(--text);}
        .ak-page-btn.active{background:var(--accent);border-color:var(--accent);color:var(--accent-text);}
        .ak-page-btn.disabled{color:var(--text-faint);opacity:.5;}
        .ak-page-dots{background:none;border-color:transparent;color:var(--text-faint);min-width:auto;padding:0 2px;}
    </style>
@endif
