@extends('layouts.app')

@push('head')
    @php
        $personSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $profile?->full_name,
            'jobTitle' => $profile?->headline,
            'description' => $profile?->bio_short,
            'url' => route('home'),
            'email' => $profile?->email,
            'address' => $profile?->location,
            'sameAs' => $siteSocialLinks->pluck('url')->all(),
        ]);

        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteSettings['site_title'] ?? 'Digital Dimension',
            'url' => route('home'),
            'description' => $siteSettings['seo_default_description'] ?? null,
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($personSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <section class="relative overflow-hidden border-b border-line" data-hero>
        <div class="hero-grid pointer-events-none absolute inset-0" aria-hidden="true"></div>

        <div class="container-editorial relative">
            <div class="grid min-h-[clamp(34rem,86svh,58rem)] items-center gap-14 py-16 lg:grid-cols-[1.08fr_0.92fr] lg:gap-8 lg:py-20" data-hero-content>
                <div class="relative z-10">
                    <p class="eyebrow flex items-center gap-3" data-hero-eyebrow>
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-accent"></span>
                        {{ $profile?->headline ?: 'Creative Full-Stack Developer' }}
                    </p>

                    <h1 class="display-1 mt-7">
                        <span class="block overflow-hidden pb-1"><span class="block" data-hero-line>Beyond code,</span></span>
                        <span class="block overflow-hidden pb-1"><span class="block" data-hero-line>into <span class="text-accent">experience.</span></span></span>
                    </h1>

                    <p class="body-lg mt-8 max-w-xl text-secondary" data-hero-copy>
                        {{ $profile?->bio_short ?: 'A portfolio for a developer who cares about craft, clarity, and the feel of an interface. Replace this placeholder copy in the admin panel.' }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center gap-3" data-hero-cta>
                        <a href="{{ route('projects.index') }}" wire:navigate class="btn-primary group">
                            Explore selected work
                            <x-ui-icon name="arrow-right" class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" />
                        </a>
                        <a href="{{ route('contact') }}" wire:navigate class="btn-secondary">Start a conversation</a>
                    </div>

                    <div class="mt-12 grid max-w-xl grid-cols-2 gap-x-6 gap-y-5 border-t border-line pt-6 sm:grid-cols-3" data-hero-meta>
                        @if ($profile?->location)
                            <div>
                                <p class="eyebrow">Based in</p>
                                <p class="mt-1.5 text-sm text-ink">{{ $profile->location }}</p>
                            </div>
                        @endif
                        @if ($profile?->availability)
                            <div>
                                <p class="eyebrow">Availability</p>
                                <p class="mt-1.5 flex items-center gap-2 text-sm text-ink">
                                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-accent"></span>
                                    {{ $profile->availability }}
                                </p>
                            </div>
                        @endif
                        <div>
                            <p class="eyebrow">Portfolio</p>
                            <p class="mt-1.5 text-sm text-ink">{{ now()->year }} Edition</p>
                        </div>
                    </div>
                </div>

                <div class="relative" data-hero-visual>
                    <div class="chrome-sphere mx-auto aspect-square w-full max-w-[34rem]" data-chrome-sphere aria-hidden="true">
                        <canvas></canvas>
                        <div class="chrome-fallback"></div>
                        <div class="chrome-orbit h-[86%] w-[86%]"></div>
                        <div class="chrome-orbit h-[102%] w-[102%] border-dashed opacity-50"></div>
                        <div class="chrome-orbit h-[70%] w-[70%] opacity-30"></div>
                    </div>

                    <div class="pointer-events-none absolute inset-x-0 top-2 flex items-start justify-between text-[0.5625rem] uppercase tracking-[0.2em] text-faint" aria-hidden="true">
                        <span>Study 001</span>
                        <span>WebGL / Chrome</span>
                    </div>
                    <div class="pointer-events-none absolute inset-x-0 bottom-2 flex items-end justify-between text-[0.5625rem] uppercase tracking-[0.2em] text-faint" aria-hidden="true">
                        <span>Interactive sphere</span>
                        <span class="hidden sm:inline">Drag / Scroll</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-editorial relative hidden items-center justify-between border-t border-line py-4 sm:flex">
            <span class="flex items-center gap-3 text-[0.5625rem] uppercase tracking-[0.2em] text-faint" data-scroll-indicator>
                <span class="grid h-7 w-7 place-items-center border border-line">
                    <x-ui-icon name="arrow-down" class="h-3 w-3" data-scroll-dot />
                </span>
                Scroll to explore
            </span>
            <span class="text-[0.5625rem] uppercase tracking-[0.2em] text-faint">{{ str_pad((string) $stats['projects'], 2, '0', STR_PAD_LEFT) }} projects · {{ str_pad((string) $stats['articles'], 2, '0', STR_PAD_LEFT) }} journal entries</span>
        </div>
    </section>

    @if ($technologies->isNotEmpty())
        <div class="marquee" aria-label="Technologies used">
            <div class="marquee-track flex w-max items-center">
                @foreach ($technologies->concat($technologies) as $technology)
                    <span class="flex items-center gap-8 px-8 text-[0.6875rem] font-medium uppercase tracking-[0.18em] text-secondary">
                        {{ $technology->name }}
                        <span class="h-1 w-1 rounded-full bg-accent" aria-hidden="true"></span>
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <section class="section border-b border-line">
        <div class="container-editorial grid gap-14 lg:grid-cols-[0.9fr_1.1fr] lg:gap-20">
            <div data-reveal>
                <p class="eyebrow mb-5 flex items-center gap-3">
                    <span class="inline-block h-px w-6 bg-accent"></span>
                    The short version
                </p>
                <h2 class="display-2">
                    A developer with an<br>
                    editor's eye for detail.
                </h2>
                <p class="prose-editorial mt-6 max-w-md">
                    {{ $profile?->personal_branding ?: 'Replace this placeholder with a short personal statement that captures how you think about your work.' }}
                </p>

                <div class="mt-10 flex items-center gap-4">
                    <x-profile-avatar :profile="$profile" class="h-16 w-16" />
                    <div>
                        <p class="font-display text-lg font-medium tracking-[-0.02em]">{{ $profile?->full_name ?: 'Your Name' }}</p>
                        <p class="text-xs text-secondary">{{ $profile?->vocational_competency ?: $profile?->headline }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-y-10 sm:grid-cols-3" data-reveal-group>
                <div>
                    <p class="stat-value">{{ str_pad((string) $stats['projects'], 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="eyebrow mt-3">Projects published</p>
                </div>
                <div>
                    <p class="stat-value">{{ str_pad((string) $stats['certificates'], 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="eyebrow mt-3">Certificates</p>
                </div>
                <div>
                    <p class="stat-value">{{ str_pad((string) $stats['articles'], 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="eyebrow mt-3">Journal entries</p>
                </div>
                <div class="col-span-2 border-t border-line pt-6 sm:col-span-3">
                    <p class="prose-editorial max-w-xl">
                        {{ $profile?->career_goals ? strip_tags($profile->career_goals) : 'Replace this section with a short statement about the kind of work you want to do next.' }}
                    </p>
                    <a href="{{ route('about') }}" wire:navigate class="link-underline mt-4 text-[0.6875rem] font-medium uppercase tracking-[0.14em]">
                        More about the person behind the screen
                        <x-ui-icon name="arrow-right" class="h-3.5 w-3.5" />
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section border-b border-line">
        <div class="container-editorial">
            <x-section-heading
                eyebrow="Selected work"
                title="Projects with a point of view."
                description="A selection of case studies covering product thinking, interface craft, and the engineering underneath."
                action-route="projects.index"
                action-label="All projects"
            />

            @if ($featuredProjects->isEmpty())
                <x-empty-state
                    class="mt-12"
                    icon="folder"
                    title="No published projects yet"
                    description="Publish a project from the admin panel and it will appear here automatically."
                    action-route="contact"
                    action-label="Get in touch"
                />
            @else
                <div class="mt-12 grid gap-x-8 gap-y-14 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($featuredProjects as $project)
                        <x-project-card :project="$project" :index="$loop->iteration" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($skillCategories->isNotEmpty())
        <section class="section border-b border-line">
            <div class="container-editorial">
                <x-section-heading
                    eyebrow="Capabilities"
                    title="Tools are only half the story."
                    description="The technical stack, paired with the working habits that make it useful."
                    action-route="skills"
                    action-label="Full skill matrix"
                />

                <div class="mt-12 grid gap-12 md:grid-cols-2 lg:gap-20">
                    @foreach ($skillCategories->take(2) as $category)
                        <div data-reveal>
                            <div class="flex items-baseline justify-between gap-4 border-b border-line pb-4">
                                <h3 class="font-display text-xl font-medium tracking-[-0.02em]">{{ $category->name }}</h3>
                                <span class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">{{ $category->skills->count() }} skills</span>
                            </div>

                            @if ($category->description)
                                <p class="mt-4 text-sm leading-relaxed text-secondary">{{ $category->description }}</p>
                            @endif

                            <div class="mt-6 space-y-6">
                                @foreach ($category->skills->take(6) as $skill)
                                    <x-skill-bar :skill="$skill" />
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($experiences->isNotEmpty())
        <section class="section border-b border-line">
            <div class="container-editorial grid gap-14 lg:grid-cols-[0.75fr_1.25fr] lg:gap-20">
                <div class="lg:sticky lg:top-28 lg:self-start" data-reveal>
                    <x-section-heading
                        eyebrow="Experience"
                        title="Where the work has happened."
                        action-route="experience"
                        action-label="Full timeline"
                    />
                </div>

                <div class="timeline">
                    @foreach ($experiences as $experience)
                        <x-experience-item :experience="$experience" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($certificates->isNotEmpty())
        <section class="section border-b border-line">
            <div class="container-editorial">
                <x-section-heading
                    eyebrow="Credentials"
                    title="Proof of the learning."
                    description="Certificates, courses, and formal recognition. Every record is managed from the admin panel."
                    action-route="certificates"
                    action-label="All certificates"
                />

                <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($certificates as $certificate)
                        <x-certificate-card :certificate="$certificate" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($posts->isNotEmpty())
        <section class="section border-b border-line">
            <div class="container-editorial">
                <x-section-heading
                    eyebrow="Journal"
                    title="Notes from the process."
                    description="Working notes on Laravel, interface details, and the decisions that do not fit in a case study."
                    action-route="journal.index"
                    action-label="Read the journal"
                />

                <div class="mt-12 space-y-8">
                    @foreach ($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($testimonials->isNotEmpty())
        <section class="section">
            <div class="container-editorial">
                <x-section-heading
                    eyebrow="Testimonials"
                    title="Words from people in the room."
                    action-route="testimonials"
                    action-label="All testimonials"
                />

                <div class="mt-12 grid gap-6 lg:grid-cols-3">
                    @foreach ($testimonials as $testimonial)
                        <x-testimonial-card :testimonial="$testimonial" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
