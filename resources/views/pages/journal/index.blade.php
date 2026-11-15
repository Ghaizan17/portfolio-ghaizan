@extends('layouts.app')

@section('title', 'Journal')

@section('description', 'Working notes on Laravel, interface craft, and the decisions behind the work.')

@section('content')
    <x-page-hero
        eyebrow="Journal"
        title="Notes from the process."
        description="Working notes on technical decisions, interface details, and the parts of the work that do not fit neatly into a case study."
    >
        <x-slot:aside>
            <dl class="grid grid-cols-2 gap-4 border-t border-line pt-4">
                <div>
                    <dt class="eyebrow">Entries</dt>
                    <dd class="stat-value mt-2 text-[2.6rem]">{{ str_pad((string) $posts->total(), 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
                <div>
                    <dt class="eyebrow">Topics</dt>
                    <dd class="stat-value mt-2 text-[2.6rem]">{{ str_pad((string) $categories->count(), 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
            </dl>
        </x-slot:aside>
    </x-page-hero>

    @if ($featuredPost)
        <section class="border-b border-line">
            <div class="container-editorial py-10 sm:py-14">
                <a href="{{ route('journal.show', $featuredPost) }}" wire:navigate class="group grid gap-8 lg:grid-cols-[1fr_1.1fr] lg:items-center" data-reveal>
                    <div class="media-frame aspect-[16/10] overflow-hidden">
                        @if ($featuredPost->thumbnail_url)
                            <img src="{{ $featuredPost->thumbnail_url }}" alt="{{ $featuredPost->title }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-[1.03]">
                        @else
                            <x-media-placeholder label="Featured journal entry" aspect="aspect-[16/10]" />
                        @endif
                    </div>

                    <div class="lg:ps-6">
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[0.625rem] font-medium uppercase tracking-[0.14em] text-faint">
                            <span class="text-accent">Latest entry</span>
                            <time datetime="{{ $featuredPost->published_at?->toDateString() }}">{{ $featuredPost->published_at?->format('M j, Y') }}</time>
                            <span>{{ $featuredPost->reading_time }} min read</span>
                        </div>

                        <h2 class="mt-5 display-2 transition-colors group-hover:text-accent">{{ $featuredPost->title }}</h2>
                        <p class="prose-editorial mt-4 max-w-xl">{{ $featuredPost->excerpt }}</p>

                        <span class="mt-6 inline-flex items-center gap-2 text-[0.6875rem] font-medium uppercase tracking-[0.14em] text-ink">
                            Read the entry
                            <x-ui-icon name="arrow-right" class="h-3.5 w-3.5 transition-transform duration-300 group-hover:translate-x-1" />
                        </span>
                    </div>
                </a>
            </div>
        </section>
    @endif

    <section class="section">
        <div class="container-editorial">
            <div class="flex flex-wrap items-center gap-2 border-b border-line pb-6">
                <a
                    href="{{ route('journal.index') }}"
                    wire:navigate
                    @class(['filter-pill', 'is-active' => ! $activeCategory])
                    @if (! $activeCategory) aria-current="page" @endif
                >
                    All topics
                    <span class="filter-count">{{ $categories->sum('posts_count') }}</span>
                </a>

                @foreach ($categories as $category)
                    <a
                        href="{{ route('journal.index', ['category' => $category->slug]) }}"
                        wire:navigate
                        @class(['filter-pill', 'is-active' => $activeCategory?->is($category)])
                        @if ($activeCategory?->is($category)) aria-current="page" @endif
                    >
                        {{ $category->name }}
                        <span class="filter-count">{{ $category->posts_count }}</span>
                    </a>
                @endforeach
            </div>

            @if ($activeCategory)
                <div class="mt-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="eyebrow mb-2">Filtered topic</p>
                        <h2 class="display-3">{{ $activeCategory->name }}</h2>
                        @if ($activeCategory->description)
                            <p class="prose-editorial mt-2 max-w-xl">{{ $activeCategory->description }}</p>
                        @endif
                    </div>
                    <p class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">{{ $posts->total() }} {{ \Illuminate\Support\Str::plural('entry', $posts->total()) }}</p>
                </div>
            @endif

            <div class="mt-10">
                @if ($posts->isEmpty())
                    <x-empty-state
                        icon="book-open"
                        title="No journal entries here yet"
                        description="Publish an article from the admin panel, or choose another topic."
                        action-route="journal.index"
                        action-label="All entries"
                    />
                @else
                    <div class="space-y-8">
                        @foreach ($posts as $post)
                            <x-post-card :post="$post" />
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
