@extends('layouts.app')

@section('title', 'Experience')

@section('description', 'Internships, organizations, committees, competitions, and the education behind them.')

@section('content')
    <x-page-hero
        eyebrow="Experience"
        title="A timeline of the work."
        description="Internships, organizations, committees, and competitions — documented as they happened, not embellished after the fact."
    >
        <x-slot:aside>
            <nav aria-label="Experience categories" class="flex flex-wrap gap-2 lg:justify-end">
                @foreach (\App\Models\Experience::categories() as $key => $label)
                    @if ($groupedExperiences->has($key))
                        <a href="#experience-{{ $key }}" class="filter-pill">{{ $label }}</a>
                    @endif
                @endforeach
            </nav>
        </x-slot:aside>
    </x-page-hero>

    <section class="section border-b border-line">
        <div class="container-editorial">
            @if ($experiences->isEmpty())
                <x-empty-state
                    icon="briefcase"
                    title="No experience published yet"
                    description="Add internship, organization, committee, or competition records from the admin panel."
                    action-route="contact"
                    action-label="Get in touch"
                />
            @else
                <div class="space-y-20">
                    @foreach (\App\Models\Experience::categories() as $key => $label)
                        @if ($groupedExperiences->has($key))
                            <section id="experience-{{ $key }}" class="scroll-mt-28" data-reveal>
                                <div class="mb-8 flex items-end justify-between gap-6 border-b border-line pb-4">
                                    <h2 class="display-3">{{ $label }}</h2>
                                    <span class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">{{ str_pad((string) $groupedExperiences->get($key)->count(), 2, '0', STR_PAD_LEFT) }} records</span>
                                </div>

                                <div class="timeline">
                                    @foreach ($groupedExperiences->get($key) as $experience)
                                        <x-experience-item :experience="$experience" />
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($educations->isNotEmpty())
        <section class="section">
            <div class="container-editorial grid gap-14 lg:grid-cols-[0.7fr_1.3fr] lg:gap-20">
                <div data-reveal>
                    <p class="eyebrow mb-5 flex items-center gap-3">
                        <span class="inline-block h-px w-6 bg-accent"></span>
                        Education
                    </p>
                    <h2 class="display-2">The formal base.</h2>
                </div>

                <div>
                    @foreach ($educations as $education)
                        <article class="border-b border-line py-7 first:pt-0" data-reveal>
                            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-baseline">
                                <div>
                                    <h3 class="font-display text-xl font-medium tracking-[-0.02em]">{{ $education->degree ?: $education->institution }}</h3>
                                    <p class="mt-1 text-sm text-secondary">{{ collect([$education->institution, $education->field_of_study])->filter()->implode(' · ') }}</p>
                                </div>
                                <p class="font-mono text-xs text-faint">{{ $education->period }}</p>
                            </div>
                            @if ($education->description)
                                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-secondary">{{ $education->description }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
