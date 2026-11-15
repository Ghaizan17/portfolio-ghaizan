@props([
    'project',
    'index' => null,
])

<article {{ $attributes->merge(['class' => 'project-card group']) }}>
    <a href="{{ route('projects.show', $project) }}" wire:navigate class="block focus-visible:outline-none">
        <div class="project-media media-frame relative aspect-[4/3] overflow-hidden">
            @if ($project->cover_url)
                <img
                    src="{{ $project->cover_url }}"
                    alt="{{ $project->title }} cover image"
                    loading="lazy"
                    decoding="async"
                    class="h-full w-full object-cover"
                >
            @else
                <x-media-placeholder
                    :label="$project->category?->name ?? 'Project visual'"
                    :index="$index !== null ? str_pad((string) $index, 2, '0', STR_PAD_LEFT) : null"
                    aspect="aspect-[4/3]"
                />
            @endif

            <div class="pointer-events-none absolute inset-x-0 top-0 flex items-start justify-between p-4">
                <span class="bg-bg/90 px-2.5 py-1 text-[0.625rem] font-medium uppercase tracking-[0.14em] text-ink backdrop-blur">
                    {{ $project->category?->name ?? 'Selected work' }}
                </span>
                @if ($project->year)
                    <span class="text-[0.625rem] font-medium uppercase tracking-[0.14em] text-white mix-blend-difference">{{ $project->year }}</span>
                @endif
            </div>

            @if ($project->featured)
                <span class="absolute bottom-4 left-4 inline-flex items-center gap-1.5 bg-accent px-2.5 py-1 text-[0.625rem] font-medium uppercase tracking-[0.14em] text-accent-ink">
                    <x-ui-icon name="sparkles" class="h-3 w-3" />
                    Featured
                </span>
            @endif
        </div>

        <div class="flex items-start justify-between gap-6 border-b border-line py-5">
            <div class="min-w-0">
                <h3 class="font-display text-[1.375rem] font-medium leading-tight tracking-[-0.025em] transition-colors group-hover:text-accent">
                    {{ $project->title }}
                </h3>
                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-secondary">{{ $project->excerpt }}</p>

                @if ($project->relationLoaded('technologies') && $project->technologies->isNotEmpty())
                    <ul class="mt-4 flex flex-wrap gap-x-3 gap-y-1.5" aria-label="Technologies used">
                        @foreach ($project->technologies->take(4) as $technology)
                            <li class="text-[0.625rem] font-medium uppercase tracking-[0.12em] text-faint">{{ $technology->name }}</li>
                        @endforeach
                        @if ($project->technologies->count() > 4)
                            <li class="text-[0.625rem] font-medium uppercase tracking-[0.12em] text-faint">+{{ $project->technologies->count() - 4 }}</li>
                        @endif
                    </ul>
                @endif
            </div>

            <span class="project-arrow shrink-0">
                <x-ui-icon name="arrow-up-right" class="h-4 w-4" />
            </span>
        </div>
    </a>
</article>
