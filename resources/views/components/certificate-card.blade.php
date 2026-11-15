@props([
    'certificate',
])

@php
    $previewUrl = $certificate->preview_url;
    $caption = $certificate->title.' — '.$certificate->issuer;
@endphp

<article class="group flex flex-col border border-line bg-surface">
    <div class="relative aspect-[4/3] overflow-hidden border-b border-line bg-elevated">
        @if ($previewUrl)
            <button
                type="button"
                data-lightbox-item
                data-lightbox-group="certificates"
                data-src="{{ $previewUrl }}"
                data-alt="{{ $caption }}"
                data-caption="{{ $caption }}"
                class="block h-full w-full cursor-zoom-in"
                aria-label="View {{ $certificate->title }} larger"
            >
                <img
                    src="{{ $previewUrl }}"
                    alt="{{ $caption }}"
                    loading="lazy"
                    decoding="async"
                    class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]"
                >
            </button>
        @else
            <x-media-placeholder
                :label="$certificate->category ?: 'Certificate preview'"
                aspect="aspect-[4/3]"
            />
        @endif

        <span class="absolute left-4 top-4 bg-bg/90 px-2.5 py-1 text-[0.625rem] font-medium uppercase tracking-[0.14em] text-ink backdrop-blur">
            {{ $certificate->category ?: 'Certification' }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-center justify-between gap-4 text-[0.625rem] font-medium uppercase tracking-[0.14em] text-faint">
            <span>{{ $certificate->issuer }}</span>
            @if ($certificate->issued_at)
                <time datetime="{{ $certificate->issued_at->toDateString() }}">{{ $certificate->issued_at->format('M Y') }}</time>
            @endif
        </div>

        <h3 class="mt-3 font-display text-lg font-medium leading-snug tracking-[-0.02em]">{{ $certificate->title }}</h3>

        @if ($certificate->description)
            <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-secondary">{{ $certificate->description }}</p>
        @endif

        <div class="mt-auto flex items-center gap-4 pt-5">
            @if ($certificate->is_pdf && $certificate->file_url)
                <a href="{{ $certificate->file_url }}" target="_blank" rel="noopener noreferrer" class="link-underline text-[0.6875rem] font-medium uppercase tracking-[0.12em]">
                    <x-ui-icon name="download" class="h-3.5 w-3.5" />
                    Open PDF
                </a>
            @elseif ($previewUrl)
                <button
                    type="button"
                    data-lightbox-item
                    data-lightbox-group="certificates"
                    data-src="{{ $previewUrl }}"
                    data-alt="{{ $caption }}"
                    data-caption="{{ $caption }}"
                    class="link-underline text-[0.6875rem] font-medium uppercase tracking-[0.12em]"
                >
                    <x-ui-icon name="eye" class="h-3.5 w-3.5" />
                    View
                </button>
            @endif
        </div>
    </div>
</article>
