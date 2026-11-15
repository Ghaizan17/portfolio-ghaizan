@props([
    'label' => 'Visual pending',
    'index' => null,
    'aspect' => 'aspect-[4/3]',
])

<div {{ $attributes->merge(['class' => 'media-placeholder '.$aspect]) }} role="img" aria-label="{{ $label }}">
    @if ($index !== null)
        <span class="media-placeholder-index">{{ $index }}</span>
    @endif

    <span class="media-placeholder-label">{{ $label }}</span>
</div>
