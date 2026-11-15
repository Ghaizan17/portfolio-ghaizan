@props([
    'links' => collect(),
    'showLabels' => false,
    'class' => '',
])

@if ($links->isNotEmpty())
    <ul {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-x-4 gap-y-2 '.$class]) }}>
        @foreach ($links as $link)
            <li>
                <a
                    href="{{ $link->url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="group inline-flex items-center gap-2 text-sm text-secondary transition-colors hover:text-ink"
                    aria-label="{{ $link->label ?: $link->platform }}"
                >
                    <x-ui-icon :name="$link->icon_name" class="h-4 w-4" />
                    @if ($showLabels)
                        <span>{{ $link->label ?: $link->platform }}</span>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
@endif
