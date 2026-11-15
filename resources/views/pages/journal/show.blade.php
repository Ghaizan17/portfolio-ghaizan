@extends('layouts.app')

@section('title', $post->meta_title ?: $post->title)
@section('description', $post->meta_description ?: $post->excerpt)
@section('og_type', 'article')
@if ($post->thumbnail_url)
    @section('og_image', $post->thumbnail_url)
@endif

@push('head')
    @php
        $articleSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => strip_tags((string) ($post->meta_description ?: $post->excerpt)),
            'url' => route('journal.show', $post),
            'image' => $post->thumbnail_url,
            'datePublished' => $post->published_at?->toAtomString(),
            'dateModified' => $post->updated_at?->toAtomString(),
            'author' => [
                '@type' => 'Person',
                'name' => $post->author?->name ?: $profile?->full_name,
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteSettings['site_title'] ?? 'Digital Dimension',
            ],
        ]);
    @endphp
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <article>
        <header class="page-hero">
            <div class="container-editorial">
                <nav class="mb-8 flex flex-wrap items-center gap-2 text-[0.625rem] uppercase tracking-[0.16em] text-faint" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" wire:navigate class="transition-colors hover:text-ink">Home</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('journal.index') }}" wire:navigate class="transition-colors hover:text-ink">Journal</a>
                    <span aria-hidden="true">/</span>
                    <span class="truncate text-ink" aria-current="page">{{ $post->title }}</span>
                </nav>

                <div class="max-w-4xl" data-reveal>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[0.625rem] font-medium uppercase tracking-[0.14em] text-faint">
                        @if ($post->category)
                            <a href="{{ route('journal.index', ['category' => $post->category->slug]) }}" wire:navigate class="text-accent transition-opacity hover:opacity-70">{{ $post->category->name }}</a>
                        @endif
                        <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->format('F j, Y') }}</time>
                        <span>{{ $post->reading_time }} min read</span>
                    </div>

                    <h1 class="display-1 mt-5">{{ $post->title }}</h1>
                    <p class="body-lg mt-6 max-w-2xl text-secondary">{{ $post->excerpt }}</p>

                    <div class="mt-8 flex items-center gap-3 border-t border-line pt-5 text-xs text-secondary">
                        <span class="grid h-9 w-9 place-items-center border border-line bg-elevated font-display text-[0.6875rem] font-medium text-faint">
                            {{ \Illuminate\Support\Str::of($post->author?->name ?: $profile?->full_name ?: 'DD')->explode(' ')->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->implode('') }}
                        </span>
                        <span>{{ $post->author?->name ?: $profile?->full_name ?: 'Digital Dimension' }}</span>
                    </div>
                </div>
            </div>
        </header>

        @if ($post->thumbnail_url)
            <div class="border-b border-line">
                <div class="container-editorial py-8 sm:py-10">
                    <div class="media-frame aspect-[16/9]">
                        <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}" class="h-full w-full object-cover" fetchpriority="high">
                    </div>
                </div>
            </div>
        @endif

        <div class="container-editorial py-14 sm:py-20">
            <div class="mx-auto max-w-3xl">
                <div class="prose-editorial article-prose">
                    {!! $post->content_html !!}
                </div>

                @if ($post->tags->isNotEmpty())
                    <div class="mt-12 flex flex-wrap items-center gap-2 border-t border-line pt-6">
                        <span class="eyebrow me-2">Topics</span>
                        @foreach ($post->tags as $tag)
                            <span class="tag">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-12 flex flex-col justify-between gap-4 border-t border-line pt-6 sm:flex-row sm:items-center">
                    <a href="{{ route('journal.index') }}" wire:navigate class="link-underline text-[0.6875rem] font-medium uppercase tracking-[0.14em]">
                        <x-ui-icon name="arrow-left" class="h-3.5 w-3.5" />
                        Back to the journal
                    </a>

                    @if ($post->category)
                        <a href="{{ route('journal.index', ['category' => $post->category->slug]) }}" wire:navigate class="text-[0.6875rem] font-medium uppercase tracking-[0.14em] text-secondary transition-colors hover:text-ink">
                            More in {{ $post->category->name }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </article>

    @if ($relatedPosts->isNotEmpty())
        <section class="border-t border-line">
            <div class="container-editorial section-tight">
                <x-section-heading
                    eyebrow="Keep reading"
                    title="Related entries."
                />

                <div class="mt-10 space-y-8">
                    @foreach ($relatedPosts as $relatedPost)
                        <x-post-card :post="$relatedPost" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
