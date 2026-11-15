@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
    @endphp

    <nav class="flex flex-col items-center justify-between gap-5 border-t border-line pt-6 sm:flex-row" role="navigation" aria-label="Pagination">
        <p class="text-[0.6875rem] font-medium uppercase tracking-[0.14em] text-faint">
            Page {{ $current }} of {{ $last }}
        </p>

        <div class="flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span class="grid h-10 w-10 place-items-center border border-line text-faint opacity-40" aria-disabled="true">
                    <x-ui-icon name="chevron-left" class="h-4 w-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" wire:navigate rel="prev" class="grid h-10 w-10 place-items-center border border-line text-ink transition-colors hover:border-ink hover:bg-ink hover:text-bg" aria-label="Previous page">
                    <x-ui-icon name="chevron-left" class="h-4 w-4" />
                </a>
            @endif

            @if ($start > 1)
                <a href="{{ $paginator->url(1) }}" wire:navigate class="grid h-10 min-w-10 place-items-center border border-line px-2 text-xs text-secondary transition-colors hover:border-ink hover:text-ink" aria-label="Page 1">1</a>
                @if ($start > 2)
                    <span class="px-1 text-xs text-faint" aria-hidden="true">…</span>
                @endif
            @endif

            @foreach ($paginator->getUrlRange($start, $end) as $page => $url)
                @if ($page === $current)
                    <span class="grid h-10 min-w-10 place-items-center border border-ink bg-ink px-2 text-xs font-medium text-bg" aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" wire:navigate class="grid h-10 min-w-10 place-items-center border border-line px-2 text-xs text-secondary transition-colors hover:border-ink hover:text-ink" aria-label="Page {{ $page }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($end < $last)
                @if ($end < $last - 1)
                    <span class="px-1 text-xs text-faint" aria-hidden="true">…</span>
                @endif
                <a href="{{ $paginator->url($last) }}" wire:navigate class="grid h-10 min-w-10 place-items-center border border-line px-2 text-xs text-secondary transition-colors hover:border-ink hover:text-ink" aria-label="Page {{ $last }}">{{ $last }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" wire:navigate rel="next" class="grid h-10 w-10 place-items-center border border-line text-ink transition-colors hover:border-ink hover:bg-ink hover:text-bg" aria-label="Next page">
                    <x-ui-icon name="chevron-right" class="h-4 w-4" />
                </a>
            @else
                <span class="grid h-10 w-10 place-items-center border border-line text-faint opacity-40" aria-disabled="true">
                    <x-ui-icon name="chevron-right" class="h-4 w-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
