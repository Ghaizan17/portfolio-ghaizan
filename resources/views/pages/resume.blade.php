@extends('layouts.app')

@section('title', 'Resume')

@section('description', 'A printable summary of experience, education, and capabilities, with the active CV available to download.')

@section('content')
    <x-page-hero
        eyebrow="Resume"
        title="The one-page version."
        description="A concise, printable summary of the same record presented across the portfolio. The active CV is streamed privately and never exposed as a direct file path."
    >
        <x-slot:aside>
            <div class="flex flex-col gap-3" data-no-print>
                @if ($resume)
                    <a href="{{ route('resume.download') }}" class="btn-primary">
                        <x-ui-icon name="download" class="h-4 w-4" />
                        Download CV
                    </a>
                    <button type="button" onclick="window.print()" class="btn-secondary">
                        <x-ui-icon name="monitor" class="h-4 w-4" />
                        Print this page
                    </button>
                    <p class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">
                        {{ $resume->label }} · Updated {{ $resume->uploaded_at?->format('M j, Y') ?? 'recently' }}
                    </p>
                @else
                    <a href="{{ route('contact') }}" wire:navigate class="btn-primary">Request the CV</a>
                    <p class="text-xs leading-relaxed text-faint">No active CV has been uploaded yet. An administrator can add one under Resumes.</p>
                @endif
            </div>
        </x-slot:aside>
    </x-page-hero>

    <section class="section" data-no-print>
        <div class="container-editorial">
            @if ($resume)
                <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p class="eyebrow mb-2">Document preview</p>
                        <h2 class="display-3">Active CV</h2>
                    </div>
                    <a href="{{ route('resume.preview') }}" target="_blank" rel="noopener noreferrer" class="link-underline text-[0.6875rem] font-medium uppercase tracking-[0.14em]">
                        Open in new tab
                        <x-ui-icon name="arrow-up-right" class="h-3.5 w-3.5" />
                    </a>
                </div>

                <div class="border border-line bg-elevated">
                    <iframe
                        src="{{ route('resume.preview') }}#toolbar=0&navpanes=0&view=FitH"
                        title="Preview of the active CV PDF"
                        class="h-[70vh] w-full"
                        loading="lazy"
                    ></iframe>
                </div>

                <p class="mt-4 text-xs text-faint">
                    If the preview does not render in your browser,
                    <a href="{{ route('resume.download') }}" class="text-accent underline underline-offset-4">download the PDF</a>
                    instead.
                </p>
            @else
                <x-empty-state
                    icon="download"
                    title="The CV is not available yet"
                    description="Upload an active PDF resume from the admin panel to enable the preview and download links."
                />
            @endif
        </div>
    </section>

    <section class="section border-t border-line print:border-0">
        <div class="container-editorial mx-auto max-w-5xl">
            <header class="flex flex-col justify-between gap-8 border-b border-line pb-8 sm:flex-row sm:items-end">
                <div>
                    <p class="eyebrow mb-3">Curriculum vitae</p>
                    <h1 class="font-display text-4xl font-medium tracking-[-0.04em] sm:text-5xl">{{ $profile?->full_name ?: 'Your Name' }}</h1>
                    <p class="mt-2 text-lg text-secondary">{{ $profile?->headline }}</p>
                </div>

                <div class="space-y-1 text-sm text-secondary sm:text-end">
                    @if ($profile?->email)
                        <p>{{ $profile->email }}</p>
                    @endif
                    @if ($profile?->phone)
                        <p>{{ $profile->phone }}</p>
                    @endif
                    @if ($profile?->location)
                        <p>{{ $profile->location }}</p>
                    @endif
                    @if ($siteSocialLinks->isNotEmpty())
                        <p class="text-xs text-faint">{{ $siteSocialLinks->pluck('url')->implode(' · ') }}</p>
                    @endif
                </div>
            </header>

            @if ($profile?->bio_short)
                <div class="mt-8 max-w-3xl">
                    <p class="eyebrow mb-2">Profile</p>
                    <p class="text-sm leading-relaxed text-secondary">{{ $profile->bio_short }}</p>
                </div>
            @endif

            @if ($experiences->isNotEmpty())
                <div class="mt-12">
                    <div class="flex items-end justify-between gap-4 border-b border-line pb-3">
                        <h2 class="font-display text-2xl font-medium tracking-[-0.025em]">Experience</h2>
                        <span class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">Selected</span>
                    </div>

                    <div class="divide-y divide-line">
                        @foreach ($experiences as $experience)
                            <article class="print-block py-6">
                                <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-baseline">
                                    <div>
                                        <h3 class="font-display text-lg font-medium tracking-[-0.02em]">{{ $experience->title }}</h3>
                                        <p class="mt-0.5 text-sm text-secondary">{{ collect([$experience->position, $experience->organization])->filter()->implode(' · ') }}</p>
                                    </div>
                                    <p class="font-mono text-xs text-faint">{{ $experience->period }}</p>
                                </div>
                                @if ($experience->description)
                                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-secondary">{{ $experience->description }}</p>
                                @endif
                                @if ($experience->responsibilities)
                                    <div class="mt-2 max-w-3xl text-sm leading-relaxed text-secondary">{!! nl2br(e($experience->responsibilities)) !!}</div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($educations->isNotEmpty())
                <div class="mt-12">
                    <div class="flex items-end justify-between gap-4 border-b border-line pb-3">
                        <h2 class="font-display text-2xl font-medium tracking-[-0.025em]">Education</h2>
                    </div>

                    <div class="divide-y divide-line">
                        @foreach ($educations as $education)
                            <article class="print-block flex flex-col justify-between gap-2 py-5 sm:flex-row sm:items-baseline">
                                <div>
                                    <h3 class="font-display text-lg font-medium tracking-[-0.02em]">{{ $education->degree ?: $education->institution }}</h3>
                                    <p class="mt-0.5 text-sm text-secondary">{{ collect([$education->institution, $education->field_of_study, $education->level])->filter()->implode(' · ') }}</p>
                                </div>
                                <p class="font-mono text-xs text-faint">{{ $education->period }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($skillCategories->isNotEmpty())
                <div class="mt-12">
                    <div class="border-b border-line pb-3">
                        <h2 class="font-display text-2xl font-medium tracking-[-0.025em]">Capabilities</h2>
                    </div>

                    <div class="mt-6 grid gap-8 sm:grid-cols-2">
                        @foreach ($skillCategories as $category)
                            <div class="print-block">
                                <p class="eyebrow mb-3">{{ $category->name }}</p>
                                <ul class="flex flex-wrap gap-x-4 gap-y-2">
                                    @foreach ($category->skills as $skill)
                                        <li class="text-sm text-secondary">{{ $skill->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <footer class="mt-12 border-t border-line pt-5 text-[0.625rem] uppercase tracking-[0.14em] text-faint">
                {{ $siteSettings['site_title'] ?? 'Digital Dimension' }} · {{ url('/') }} · Generated {{ now()->format('F j, Y') }}
            </footer>
        </div>
    </section>
@endsection
