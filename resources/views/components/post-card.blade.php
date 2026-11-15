@props([
    'post',
])

<article class="group border-b border-line pb-8">
    <a href="{{ route('journal.show', $post) }}" wire:navigate class="grid gap-6 sm:grid-cols-[7.5rem_1fr] sm:gap-8">
        <div class="flex flex-col gap-1.5 text-[0.625rem] font-medium uppercase tracking-[0.16em] text-faint">
            <time datetime="{{ $post->published_at?->toDateString() }}">
                {{ $post->published_at?->format('M j, Y') ?? 'Unscheduled' }}
            </time>
            @if ($post->category)
                <span class="text-accent">{{ $post->category->name }}</span>
            @endif
        </div>

        <div>
            <h3 class="font-display text-2xl font-medium leading-tight tracking-[-0.025em] transition-colors group-hover:text-accent">
                {{ $post->title }}
            </h3>
            <p class="mt-3 max-w-2xl text-sm leading-relaxed text-secondary">{{ $post->excerpt }}</p>
            <div class="mt-4 flex items-center gap-4 text-[0.625rem] font-medium uppercase tracking-[0.14em] text-faint">
                <span>{{ $post->reading_time }} min read</span>
                <span class="inline-flex items-center gap-1.5">
                    Read article
                    <x-ui-icon name="arrow-right" class="h-3 w-3 transition-transform duration-300 group-hover:translate-x-1" />
                </span>
            </div>
        </div>
    </a>
</article>
