@extends('layouts.app')

@section('title', $project->meta_title ?: $project->title)
@section('description', $project->meta_description ?: $project->excerpt)
@section('og_type', 'article')
@if ($project->cover_url)
    @section('og_image', $project->cover_url)
@endif

@push('head')
    @php
        $projectSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $project->title,
            'description' => strip_tags((string) ($project->meta_description ?: $project->excerpt)),
            'url' => route('projects.show', $project),
            'image' => $project->cover_url,
            'dateCreated' => $project->year,
            'author' => [
                '@type' => 'Person',
                'name' => $profile?->full_name,
                'url' => route('home'),
            ],
            'keywords' => $project->technologies->pluck('name')->implode(', '),
        ]);
    @endphp
    <script type="application/ld+json">{!! json_encode($projectSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <article>
        <header class="page-hero grain">
            <div class="container-editorial">
                <nav class="mb-8 flex items-center gap-2 text-[0.625rem] uppercase tracking-[0.16em] text-faint" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" wire:navigate class="transition-colors hover:text-ink">Home</a>
                    <span aria-hidden="true">/</span>
                    <a href="{{ route('projects.index') }}" wire:navigate class="transition-colors hover:text-ink">Projects</a>
                    <span aria-hidden="true">/</span>
                    <span class="text-ink" aria-current="page">{{ $project->title }}</span>
                </nav>

                <div class="grid gap-10 lg:grid-cols-[1.4fr_0.6fr] lg:items-end" data-reveal>
                    <div>
                        <p class="eyebrow mb-4 flex items-center gap-3">
                            <span class="inline-block h-px w-8 bg-accent"></span>
                            {{ $project->category?->name ?? 'Case study' }}
                        </p>
                        <h1 class="display-1">{{ $project->title }}</h1>
                        <p class="body-lg mt-6 max-w-2xl text-secondary">{{ $project->excerpt }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-x-6 gap-y-4 border-t border-line pt-5 lg:border-t-0 lg:pt-0">
                        @if ($project->year)
                            <div>
                                <p class="eyebrow">Year</p>
                                <p class="mt-1.5 text-sm">{{ $project->year }}</p>
                            </div>
                        @endif
                        @if ($project->role)
                            <div>
                                <p class="eyebrow">Role</p>
                                <p class="mt-1.5 text-sm">{{ $project->role }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="eyebrow">Status</p>
                            <p class="mt-1.5 flex items-center gap-2 text-sm">
                                <span class="inline-block h-1.5 w-1.5 rounded-full bg-accent"></span>
                                Published
                            </p>
                        </div>
                        @if ($project->technologies->isNotEmpty())
                            <div>
                                <p class="eyebrow">Stack</p>
                                <p class="mt-1.5 text-sm">{{ $project->technologies->take(3)->pluck('name')->implode(', ') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        @if ($project->cover_url)
            <div class="border-b border-line">
                <div class="container-editorial py-8 sm:py-10">
                    <div class="media-frame aspect-[16/9]">
                        <img
                            src="{{ $project->cover_url }}"
                            alt="{{ $project->title }} cover"
                            class="h-full w-full object-cover"
                            fetchpriority="high"
                        >
                    </div>
                </div>
            </div>
        @endif

        <div class="container-editorial py-14 sm:py-20">
            <div class="grid gap-14 lg:grid-cols-[0.3fr_0.7fr] lg:gap-20">
                <aside class="space-y-8 lg:sticky lg:top-28 lg:self-start">
                    <div class="border-t border-line pt-5">
                        <p class="eyebrow mb-4">Project detail</p>
                        <dl class="space-y-4 text-sm">
                            @if ($project->category)
                                <div>
                                    <dt class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">Category</dt>
                                    <dd class="mt-1">{{ $project->category->name }}</dd>
                                </div>
                            @endif
                            @if ($project->role)
                                <div>
                                    <dt class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">Role</dt>
                                    <dd class="mt-1">{{ $project->role }}</dd>
                                </div>
                            @endif
                            @if ($project->year)
                                <div>
                                    <dt class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">Year</dt>
                                    <dd class="mt-1">{{ $project->year }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    @if ($project->technologies->isNotEmpty())
                        <div class="border-t border-line pt-5">
                            <p class="eyebrow mb-3">Technology</p>
                            <ul class="flex flex-wrap gap-2">
                                @foreach ($project->technologies as $technology)
                                    <li class="tag">{{ $technology->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (($project->demo_is_valid && $project->demo_url) || ($project->github_is_valid && $project->github_url))
                        <div class="border-t border-line pt-5">
                            <p class="eyebrow mb-3">Links</p>
                            <ul class="space-y-3">
                                @if ($project->demo_is_valid && $project->demo_url)
                                    <li>
                                        <a href="{{ $project->demo_url }}" target="_blank" rel="noopener noreferrer" class="link-underline text-sm">
                                            <x-ui-icon name="external-link" class="h-4 w-4" />
                                            Live demo
                                        </a>
                                    </li>
                                @endif
                                @if ($project->github_is_valid && $project->github_url)
                                    <li>
                                        <a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer" class="link-underline text-sm">
                                            <x-ui-icon name="github" class="h-4 w-4" />
                                            Source repository
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    @if ($project->stories->isNotEmpty())
                        <div class="border-t border-line pt-5">
                            <p class="eyebrow mb-3">Development story</p>
                            <ol class="space-y-2">
                                @foreach ($project->stories as $story)
                                    <li>
                                        <a href="#story-{{ $story->id }}" class="flex items-baseline gap-2 text-sm text-secondary transition-colors hover:text-ink">
                                            <span class="font-mono text-[0.625rem] text-faint">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                            {{ $story->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    <a href="{{ route('projects.index') }}" wire:navigate class="link-underline inline-flex text-[0.6875rem] font-medium uppercase tracking-[0.14em]">
                        <x-ui-icon name="arrow-left" class="h-3.5 w-3.5" />
                        Back to archive
                    </a>
                </aside>

                <div class="min-w-0 space-y-16">
                    @if ($project->description)
                        <section data-reveal>
                            <p class="eyebrow mb-4">Overview</p>
                            <div class="prose-editorial article-prose">{!! $project->description !!}</div>
                        </section>
                    @endif

                    @if ($project->problem_statement || $project->objectives)
                        <section class="grid gap-10 border-t border-line pt-10 sm:grid-cols-2" data-reveal>
                            @if ($project->problem_statement)
                                <div>
                                    <p class="eyebrow mb-3">The problem</p>
                                    <div class="prose-editorial">{!! $project->problem_statement !!}</div>
                                </div>
                            @endif
                            @if ($project->objectives)
                                <div>
                                    <p class="eyebrow mb-3">Objectives</p>
                                    <div class="prose-editorial">{!! $project->objectives !!}</div>
                                </div>
                            @endif
                        </section>
                    @endif

                    @if ($project->team_collaboration)
                        <section class="border-t border-line pt-10" data-reveal>
                            <p class="eyebrow mb-3">Collaboration</p>
                            <div class="prose-editorial">{!! $project->team_collaboration !!}</div>
                        </section>
                    @endif

                    @if ($project->features->isNotEmpty())
                        <section class="border-t border-line pt-10" data-reveal>
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <p class="eyebrow mb-3">Core features</p>
                                    <h2 class="display-3">What it does.</h2>
                                </div>
                                <span class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">{{ str_pad((string) $project->features->count(), 2, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <div class="mt-8 grid gap-px border border-line bg-line sm:grid-cols-2">
                                @foreach ($project->features as $feature)
                                    <div class="bg-surface p-6">
                                        <span class="font-mono text-[0.625rem] text-accent">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <h3 class="mt-3 font-display text-lg font-medium tracking-[-0.02em]">{{ $feature->title }}</h3>
                                        <p class="mt-2 text-sm leading-relaxed text-secondary">{{ $feature->description }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($project->processes->isNotEmpty())
                        <section class="border-t border-line pt-10" data-reveal>
                            <p class="eyebrow mb-3">Process</p>
                            <h2 class="display-3">How it was made.</h2>

                            <div class="mt-8 space-y-10">
                                @foreach ($project->processes as $process)
                                    <article class="grid gap-6 sm:grid-cols-[6rem_1fr]">
                                        <p class="font-mono text-xs text-faint">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                        <div>
                                            <h3 class="font-display text-xl font-medium tracking-[-0.02em]">{{ $process->title }}</h3>
                                            <p class="mt-2 text-sm leading-relaxed text-secondary">{{ $process->description }}</p>

                                            @if ($process->image_url)
                                                <button
                                                    type="button"
                                                    class="media-frame mt-5 aspect-[16/9] w-full cursor-zoom-in"
                                                    data-lightbox-item
                                                    data-lightbox-group="project-{{ $project->id }}-process"
                                                    data-src="{{ $process->image_url }}"
                                                    data-alt="{{ $process->title }}"
                                                    data-caption="{{ $process->title }}"
                                                    aria-label="View process image for {{ $process->title }}"
                                                >
                                                    <img src="{{ $process->image_url }}" alt="{{ $process->title }}" loading="lazy" class="h-full w-full object-cover">
                                                </button>
                                            @endif

                                            @if ($process->code_snippet)
                                                <pre class="mt-5 overflow-x-auto border border-line bg-[var(--code-bg)] p-5 text-sm text-[var(--code-ink)]"><code>{{ $process->code_snippet }}</code></pre>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($project->stories->isNotEmpty())
                        <section class="border-t border-line pt-10" data-reveal>
                            <p class="eyebrow mb-3">Project development story</p>
                            <h2 class="display-3">The decisions behind it.</h2>
                            <p class="prose-editorial mt-4 max-w-2xl">
                                A structured account of how the project evolved — the initial idea, the discoveries, the trade-offs, and the code that carried it.
                            </p>

                            <div class="mt-10 space-y-14">
                                @foreach ($project->stories as $story)
                                    <article id="story-{{ $story->id }}" class="scroll-mt-28 border-t border-line pt-8">
                                        <div class="flex items-baseline gap-4">
                                            <span class="font-mono text-xs text-accent">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                            <h3 class="font-display text-2xl font-medium tracking-[-0.03em]">{{ $story->title }}</h3>
                                        </div>

                                        <div class="prose-editorial mt-5">{!! $story->content_html !!}</div>

                                        @if ($story->image_url)
                                            <button
                                                type="button"
                                                class="media-frame mt-6 aspect-[16/9] w-full cursor-zoom-in"
                                                data-lightbox-item
                                                data-lightbox-group="project-{{ $project->id }}-story"
                                                data-src="{{ $story->image_url }}"
                                                data-alt="{{ $story->title }}"
                                                data-caption="{{ $story->title }}"
                                                aria-label="View story image for {{ $story->title }}"
                                            >
                                                <img src="{{ $story->image_url }}" alt="{{ $story->title }}" loading="lazy" class="h-full w-full object-cover">
                                            </button>
                                        @endif

                                        @if ($story->code_snippet)
                                            <pre class="mt-6 overflow-x-auto border border-line bg-[var(--code-bg)] p-5 text-sm text-[var(--code-ink)]"><code>{{ $story->code_snippet }}</code></pre>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($project->media->where('type', 'screenshot')->isNotEmpty() || $project->media->where('type', 'documentation')->isNotEmpty())
                        <section class="border-t border-line pt-10" data-reveal>
                            <p class="eyebrow mb-3">Gallery</p>
                            <h2 class="display-3">The interface.</h2>

                            <div class="mt-8 grid gap-4 sm:grid-cols-2">
                                @foreach ($project->media as $media)
                                    @if ($media->url)
                                        <button
                                            type="button"
                                            class="media-frame aspect-[4/3] cursor-zoom-in {{ $loop->first ? 'sm:col-span-2 sm:aspect-[16/9]' : '' }}"
                                            data-lightbox-item
                                            data-lightbox-group="project-{{ $project->id }}-gallery"
                                            data-src="{{ $media->url }}"
                                            data-alt="{{ $media->alt ?: $media->caption ?: $project->title }}"
                                            data-caption="{{ $media->caption }}"
                                            aria-label="View {{ $media->caption ?: $project->title }} larger"
                                        >
                                            <img
                                                src="{{ $media->url }}"
                                                alt="{{ $media->alt ?: $media->caption ?: $project->title }}"
                                                loading="lazy"
                                                class="h-full w-full object-cover transition-transform duration-700 hover:scale-[1.02]"
                                            >
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </section>
                    @endif

                    @if ($project->feedback->where('is_published', true)->isNotEmpty() || $project->testimonials->isNotEmpty())
                        <section class="border-t border-line pt-10" data-reveal>
                            <p class="eyebrow mb-3">Feedback</p>
                            <h2 class="display-3">What people said.</h2>

                            <div class="mt-8 grid gap-6 sm:grid-cols-2">
                                @foreach ($project->feedback->where('is_published', true) as $feedback)
                                    <figure class="border border-line bg-surface p-6">
                                        <x-ui-icon name="quote" class="h-6 w-6 text-accent" />
                                        <blockquote class="mt-4 text-sm leading-relaxed text-secondary">{{ $feedback->content }}</blockquote>
                                        <figcaption class="mt-4 border-t border-line pt-3 text-xs">
                                            <span class="font-medium text-ink">{{ $feedback->name }}</span>
                                            @if ($feedback->role)
                                                <span class="text-faint"> — {{ $feedback->role }}</span>
                                            @endif
                                        </figcaption>
                                    </figure>
                                @endforeach

                                @foreach ($project->testimonials as $testimonial)
                                    <x-testimonial-card :testimonial="$testimonial" />
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>
            </div>
        </div>
    </article>

    @if ($nextProject)
        <section class="border-t border-line">
            <a href="{{ route('projects.show', $nextProject) }}" wire:navigate class="group block">
                <div class="container-editorial flex flex-col justify-between gap-6 py-14 sm:flex-row sm:items-end">
                    <div>
                        <p class="eyebrow mb-3">Next project</p>
                        <h2 class="display-2 transition-colors group-hover:text-accent">{{ $nextProject->title }}</h2>
                        <p class="mt-3 text-sm text-secondary">{{ $nextProject->category?->name }}</p>
                    </div>
                    <span class="grid h-16 w-16 shrink-0 place-items-center rounded-full border border-line transition-colors duration-300 group-hover:border-accent group-hover:bg-accent group-hover:text-accent-ink">
                        <x-ui-icon name="arrow-right" class="h-6 w-6" />
                    </span>
                </div>
            </a>
        </section>
    @endif
@endsection
