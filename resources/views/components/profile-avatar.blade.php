@props([
    'profile',
    'class' => 'h-20 w-20',
])

@if ($profile?->has_avatar)
    <img
        src="{{ $profile->avatar_url }}"
        alt="Portrait of {{ $profile->full_name }}"
        loading="lazy"
        decoding="async"
        {{ $attributes->merge(['class' => $class.' object-cover']) }}
    >
@else
    <div
        {{ $attributes->merge(['class' => $class.' relative grid place-items-center overflow-hidden border border-line bg-elevated']) }}
        role="img"
        aria-label="Placeholder portrait for {{ $profile?->full_name ?? 'profile' }}"
    >
        <span class="absolute inset-0 bg-[radial-gradient(circle_at_30%_25%,var(--accent-soft),transparent_60%)]"></span>
        <span class="relative font-display text-2xl font-medium tracking-[-0.04em] text-faint">{{ $profile?->initials ?: 'YN' }}</span>
    </div>
@endif
