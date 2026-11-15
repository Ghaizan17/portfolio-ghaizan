@props([
    'experience',
])

<article class="timeline-item print-block">
    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[0.625rem] font-medium uppercase tracking-[0.16em] text-faint">
        <span>{{ $experience->period }}</span>
        <span class="h-px w-5 bg-line"></span>
        <span class="text-accent">{{ $experience->category_label }}</span>
        @if ($experience->is_current)
            <span class="inline-flex items-center gap-1.5 text-ink">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-accent"></span>
                Current
            </span>
        @endif
    </div>

    <h3 class="mt-3 font-display text-2xl font-medium leading-tight tracking-[-0.025em]">{{ $experience->title }}</h3>

    <p class="mt-1.5 text-sm text-secondary">
        {{ collect([$experience->position, $experience->organization])->filter()->implode(' · ') }}
    </p>

    @if ($experience->description)
        <p class="mt-4 max-w-2xl text-sm leading-relaxed text-secondary">{{ $experience->description }}</p>
    @endif

    @if ($experience->responsibilities)
        <div class="mt-5 max-w-2xl">
            <p class="eyebrow mb-2">Responsibilities</p>
            <div class="text-sm leading-relaxed text-secondary">{!! nl2br(e($experience->responsibilities)) !!}</div>
        </div>
    @endif

    @if ($experience->achievements)
        <div class="mt-5 max-w-2xl">
            <p class="eyebrow mb-2">Outcomes</p>
            <div class="text-sm leading-relaxed text-secondary">{!! nl2br(e($experience->achievements)) !!}</div>
        </div>
    @endif

    @if ($experience->relationLoaded('media') && $experience->media->isNotEmpty())
        <div class="mt-5 flex flex-wrap gap-3">
            @foreach ($experience->media as $media)
                @if ($media->url)
                    <button
                        type="button"
                        class="media-frame h-20 w-28 shrink-0 cursor-zoom-in"
                        data-lightbox-item
                        data-lightbox-group="experience-{{ $experience->id }}"
                        data-src="{{ $media->url }}"
                        data-alt="{{ $media->caption ?: $experience->title }}"
                        data-caption="{{ $media->caption }}"
                        aria-label="View image for {{ $experience->title }}"
                    >
                        <img src="{{ $media->url }}" alt="{{ $media->caption ?: $experience->title }}" loading="lazy" class="h-full w-full object-cover">
                    </button>
                @endif
            @endforeach
        </div>
    @endif
</article>
