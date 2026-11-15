@extends('layouts.app')

@section('title', 'Contact')

@section('description', $siteSettings['contact_intro'] ?? 'Have a project, an opportunity, or a question? Send a message.')

@section('content')
    <x-page-hero
        eyebrow="Contact"
        title="Let's talk."
        :description="$siteSettings['contact_intro'] ?? 'Have a project, an opportunity, or a question? Send a message and I will get back to you.'"
    >
        <x-slot:aside>
            <div class="flex items-center gap-3 text-sm text-secondary lg:justify-end">
                <span class="inline-block h-2 w-2 rounded-full bg-accent"></span>
                {{ $profile?->availability ?: 'Open to new conversations' }}
            </div>
        </x-slot:aside>
    </x-page-hero>

    <section class="section">
        <div class="container-editorial grid gap-14 lg:grid-cols-[0.75fr_1.25fr] lg:gap-20">
            <div class="space-y-10" data-reveal>
                <div>
                    <p class="eyebrow mb-4">Direct</p>
                    @if ($profile?->email)
                        <a href="mailto:{{ $profile->email }}" class="group inline-flex flex-wrap items-baseline gap-3 font-display text-[clamp(1.4rem,3vw,2.2rem)] font-medium tracking-[-0.03em] transition-colors hover:text-accent">
                            {{ $profile->email }}
                            <x-ui-icon name="arrow-up-right" class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                        </a>
                    @else
                        <p class="font-display text-2xl font-medium tracking-[-0.03em]">Use the form.</p>
                        <p class="mt-2 text-sm text-secondary">An email address has not been published yet. Messages sent through the form are stored safely in the admin inbox.</p>
                    @endif
                </div>

                <dl class="grid gap-5 border-t border-line pt-6 text-sm sm:grid-cols-2">
                    @if ($profile?->location)
                        <div>
                            <dt class="eyebrow mb-1.5">Location</dt>
                            <dd class="text-secondary">{{ $profile->location }}</dd>
                        </div>
                    @endif
                    @if ($profile?->phone)
                        <div>
                            <dt class="eyebrow mb-1.5">Phone</dt>
                            <dd class="text-secondary">{{ $profile->phone }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="eyebrow mb-1.5">Response</dt>
                        <dd class="text-secondary">Usually within a few days</dd>
                    </div>
                    <div>
                        <dt class="eyebrow mb-1.5">Best for</dt>
                        <dd class="text-secondary">Projects, internships, collaborations</dd>
                    </div>
                </dl>

                @if ($siteSocialLinks->isNotEmpty())
                    <div class="border-t border-line pt-6">
                        <p class="eyebrow mb-3">Elsewhere</p>
                        <x-social-links :links="$siteSocialLinks" :show-labels="true" />
                    </div>
                @endif

                <div class="border border-line p-5">
                    <p class="eyebrow mb-2">Before you send</p>
                    <ul class="space-y-2 text-xs leading-relaxed text-secondary">
                        <li class="flex gap-2"><span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-accent"></span> A short note on the problem is more useful than a long brief.</li>
                        <li class="flex gap-2"><span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-accent"></span> Include timelines if the work is time-sensitive.</li>
                        <li class="flex gap-2"><span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-accent"></span> Links to references or context are welcome.</li>
                    </ul>
                </div>
            </div>

            <div data-reveal>
                <div class="border border-line bg-surface p-6 sm:p-9">
                    <div class="mb-8">
                        <p class="eyebrow mb-2">Send a message</p>
                        <h2 class="font-display text-2xl font-medium tracking-[-0.025em]">What are you working on?</h2>
                    </div>

                    <livewire:contact-form />
                </div>
            </div>
        </div>
    </section>
@endsection
