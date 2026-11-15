@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'actionRoute' => null,
    'actionLabel' => 'View all',
])

<div {{ $attributes->merge(['class' => 'flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between']) }} data-reveal>
    <div class="max-w-2xl">
        @if ($eyebrow)
            <p class="eyebrow mb-4 flex items-center gap-3">
                <span class="inline-block h-px w-6 bg-accent"></span>
                {{ $eyebrow }}
            </p>
        @endif

        <h2 class="display-3">{{ $title }}</h2>

        @if ($description)
            <p class="prose-editorial mt-4 max-w-xl">{{ $description }}</p>
        @endif
    </div>

    @if ($actionRoute)
        <a href="{{ route($actionRoute) }}" wire:navigate class="btn-ghost shrink-0 self-start sm:self-auto">
            {{ $actionLabel }}
            <x-ui-icon name="arrow-right" class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
        </a>
    @endif
</div>
