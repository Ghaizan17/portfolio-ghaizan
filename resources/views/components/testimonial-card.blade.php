@props([
    'testimonial',
])

<figure class="flex h-full flex-col border border-line bg-surface p-6 sm:p-8">
    <x-ui-icon name="quote" class="h-7 w-7 text-accent" />

    <blockquote class="mt-6 flex-1 font-display text-[1.125rem] leading-relaxed tracking-[-0.01em] text-ink">
        {{ $testimonial->content }}
    </blockquote>

    <figcaption class="mt-8 flex items-start gap-4 border-t border-line pt-5">
        @if ($testimonial->photo_url)
            <img src="{{ $testimonial->photo_url }}" alt="Portrait of {{ $testimonial->name }}" loading="lazy" class="h-11 w-11 shrink-0 object-cover">
        @else
            <span class="grid h-11 w-11 shrink-0 place-items-center border border-line bg-elevated font-display text-sm font-medium text-faint">
                {{ \Illuminate\Support\Str::of($testimonial->name)->explode(' ')->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') }}
            </span>
        @endif

        <div class="min-w-0">
            <p class="text-sm font-medium text-ink">{{ $testimonial->name }}</p>
            <p class="mt-0.5 text-xs leading-relaxed text-secondary">
                {{ collect([$testimonial->position, $testimonial->organization])->filter()->implode(', ') }}
            </p>
            <p class="mt-1 text-[0.625rem] uppercase tracking-[0.14em] text-faint">{{ $testimonial->relationship_label }}</p>

            @if ($testimonial->project)
                <a href="{{ route('projects.show', $testimonial->project) }}" wire:navigate class="mt-2 inline-flex items-center gap-1.5 text-[0.625rem] font-medium uppercase tracking-[0.12em] text-accent">
                    {{ $testimonial->project->title }}
                    <x-ui-icon name="arrow-up-right" class="h-3 w-3" />
                </a>
            @endif
        </div>
    </figcaption>
</figure>
