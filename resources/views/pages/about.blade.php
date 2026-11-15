@extends('layouts.app')

@section('title', 'About')

@section('description', $profile?->bio_short ?: 'Background, education, and the working approach behind the portfolio.')

@section('content')
    <x-page-hero
        eyebrow="About"
        title="A practice built on curiosity."
        :description="$profile?->bio_short"
    >
        <x-slot:aside>
            <div class="flex items-end gap-5 lg:justify-end">
                <x-profile-avatar :profile="$profile" class="h-28 w-28" />
                <div class="pb-1">
                    <p class="font-display text-lg font-medium leading-tight tracking-[-0.02em]">{{ $profile?->full_name ?: 'Your Name' }}</p>
                    <p class="mt-1 text-xs text-secondary">{{ $profile?->headline }}</p>
                    @if ($profile?->availability)
                        <p class="mt-3 inline-flex items-center gap-2 text-[0.625rem] uppercase tracking-[0.14em] text-faint">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-accent"></span>
                            {{ $profile->availability }}
                        </p>
                    @endif
                </div>
            </div>
        </x-slot:aside>

        <x-slot:meta>
            <dl class="grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                @if ($profile?->location)
                    <div>
                        <dt class="eyebrow">Location</dt>
                        <dd class="mt-1.5">{{ $profile->location }}</dd>
                    </div>
                @endif
                @if ($profile?->vocational_competency)
                    <div>
                        <dt class="eyebrow">Competency</dt>
                        <dd class="mt-1.5">{{ $profile->vocational_competency }}</dd>
                    </div>
                @endif
                @if ($profile?->class)
                    <div>
                        <dt class="eyebrow">Class</dt>
                        <dd class="mt-1.5">{{ $profile->class }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="eyebrow">Certificates</dt>
                    <dd class="mt-1.5">{{ $certificateCount }} published</dd>
                </div>
            </dl>
        </x-slot:meta>
    </x-page-hero>

    <section class="section border-b border-line">
        <div class="container-editorial grid gap-14 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
            <div data-reveal>
                <p class="eyebrow mb-5 flex items-center gap-3">
                    <span class="inline-block h-px w-6 bg-accent"></span>
                    Biography
                </p>
                <h2 class="display-2">The longer story.</h2>

                @if ($profile?->personal_branding)
                    <blockquote class="pull-quote mt-8">{{ $profile->personal_branding }}</blockquote>
                @endif
            </div>

            <div data-reveal>
                <div class="prose-editorial">
                    @if ($profile?->bio_long)
                        {!! $profile->bio_long !!}
                    @else
                        <p>Replace this placeholder biography in the admin panel. Describe your background, what you are learning, the work you care about, and how you approach a problem.</p>
                    @endif
                </div>

                @if ($profile?->career_goals)
                    <div class="mt-10 border-t border-line pt-8">
                        <p class="eyebrow mb-3">Career direction</p>
                        <div class="prose-editorial">{!! $profile->career_goals !!}</div>
                    </div>
                @endif

                <div class="mt-10 flex flex-wrap gap-3">
                    <a href="{{ route('projects.index') }}" wire:navigate class="btn-secondary">See the work</a>
                    <a href="{{ route('resume') }}" wire:navigate class="btn-ghost">
                        Open the resume
                        <x-ui-icon name="arrow-right" class="h-4 w-4" />
                    </a>
                </div>
            </div>
        </div>
    </section>

    @if ($educations->isNotEmpty())
        <section class="section border-b border-line">
            <div class="container-editorial grid gap-14 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
                <div class="lg:sticky lg:top-28 lg:self-start" data-reveal>
                    <p class="eyebrow mb-5 flex items-center gap-3">
                        <span class="inline-block h-px w-6 bg-accent"></span>
                        Education
                    </p>
                    <h2 class="display-2">Formal foundations.</h2>
                    <p class="prose-editorial mt-5 max-w-sm">
                        The classrooms, programs, and formal training that shaped the technical base.
                    </p>
                </div>

                <div>
                    @foreach ($educations as $education)
                        <article class="border-b border-line py-8 first:pt-0" data-reveal>
                            <div class="grid gap-4 sm:grid-cols-[7.5rem_1fr]">
                                <div>
                                    <p class="font-mono text-xs text-faint">{{ $education->period }}</p>
                                    @if ($education->is_current)
                                        <p class="mt-2 inline-flex items-center gap-1.5 text-[0.625rem] uppercase tracking-[0.14em] text-accent">
                                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-accent"></span>
                                            In progress
                                        </p>
                                    @endif
                                </div>

                                <div>
                                    <h3 class="font-display text-xl font-medium leading-tight tracking-[-0.02em]">
                                        {{ $education->degree ?: $education->institution }}
                                    </h3>
                                    <p class="mt-1 text-sm text-secondary">
                                        {{ collect([$education->institution, $education->field_of_study, $education->level])->filter()->implode(' · ') }}
                                    </p>
                                    @if ($education->description)
                                        <p class="mt-3 max-w-xl text-sm leading-relaxed text-secondary">{{ $education->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($experiences->isNotEmpty())
        <section class="section">
            <div class="container-editorial grid gap-14 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
                <div data-reveal>
                    <p class="eyebrow mb-5 flex items-center gap-3">
                        <span class="inline-block h-px w-6 bg-accent"></span>
                        Recent experience
                    </p>
                    <h2 class="display-2">Learning by doing.</h2>
                    <a href="{{ route('experience') }}" wire:navigate class="link-underline mt-6 inline-flex text-[0.6875rem] font-medium uppercase tracking-[0.14em]">
                        Full experience timeline
                        <x-ui-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </div>

                <div class="timeline">
                    @foreach ($experiences as $experience)
                        <x-experience-item :experience="$experience" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
