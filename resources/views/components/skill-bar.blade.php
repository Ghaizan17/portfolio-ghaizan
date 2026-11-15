@props([
    'skill',
])

@php
    $value = max(0, min(100, (int) $skill->percentage));
@endphp

<div data-progress="{{ $value }}" class="print-block">
    <div class="flex items-baseline justify-between gap-4">
        <p class="text-sm font-medium text-ink">{{ $skill->name }}</p>
        <span class="font-mono text-[0.6875rem] text-faint">{{ $value }}%</span>
    </div>

    <div class="mt-2.5 h-px w-full overflow-hidden bg-line" role="meter" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $value }}" aria-label="{{ $skill->name }} proficiency, self-assessed">
        <div data-progress-bar class="h-full w-full origin-left bg-accent" style="transform: scaleX({{ $value / 100 }});"></div>
    </div>

    @if ($skill->description)
        <p class="mt-2.5 text-xs leading-relaxed text-faint">{{ $skill->description }}</p>
    @endif

    @if ($skill->evidence)
        <p class="mt-2 text-xs leading-relaxed text-secondary">{{ $skill->evidence }}</p>
    @endif

    @if ($skill->project)
        <a href="{{ route('projects.show', $skill->project) }}" wire:navigate class="mt-2 inline-flex items-center gap-1.5 text-[0.625rem] font-medium uppercase tracking-[0.12em] text-accent">
            Evidence: {{ $skill->project->title }}
            <x-ui-icon name="arrow-up-right" class="h-3 w-3" />
        </a>
    @endif
</div>
