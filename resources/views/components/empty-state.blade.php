@props([
    'icon' => 'folder',
    'title' => 'Nothing here yet',
    'description' => null,
    'actionRoute' => null,
    'actionLabel' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center border border-dashed border-line px-6 py-16 text-center']) }}>
    <span class="grid h-14 w-14 place-items-center border border-line text-faint">
        <x-ui-icon :name="$icon" class="h-5 w-5" />
    </span>

    <h3 class="mt-6 font-display text-xl font-medium tracking-[-0.02em]">{{ $title }}</h3>

    @if ($description)
        <p class="mt-3 max-w-sm text-sm leading-relaxed text-secondary">{{ $description }}</p>
    @endif

    @if ($actionRoute && $actionLabel)
        <a href="{{ route($actionRoute) }}" wire:navigate class="btn-secondary mt-7">{{ $actionLabel }}</a>
    @endif
</div>
