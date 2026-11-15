@extends('layouts.app')

@section('title', 'Skills')

@section('description', 'Technical capabilities and working habits, with honest self-assessment and linked evidence.')

@section('content')
    <x-page-hero
        eyebrow="Capabilities"
        title="The skill matrix."
        description="A working inventory of technical tools and the habits that support delivery. Every percentage is editable in the admin panel and should be backed by evidence."
    >
        <x-slot:aside>
            <div class="border border-line p-5">
                <p class="eyebrow">A note on numbers</p>
                <p class="mt-2 text-sm leading-relaxed text-secondary">
                    Percentages are self-assessments, not certifications. When demonstration mode is enabled, they are explicit placeholders.
                </p>
            </div>
        </x-slot:aside>
    </x-page-hero>

    @if ($skillCategories->isEmpty())
        <section class="section">
            <div class="container-editorial">
                <x-empty-state
                    icon="layers"
                    title="No skills published yet"
                    description="Add skill categories and skills from the admin panel to build the matrix."
                    action-route="contact"
                    action-label="Get in touch"
                />
            </div>
        </section>
    @else
        <section class="section">
            <div class="container-editorial grid gap-14 lg:grid-cols-[0.32fr_0.68fr] lg:gap-20">
                <aside class="lg:sticky lg:top-28 lg:self-start" data-reveal>
                    <p class="eyebrow mb-4">Jump to</p>
                    <nav aria-label="Skill categories" class="space-y-1">
                        @foreach ($skillCategories as $category)
                            <a href="#category-{{ $category->slug }}" class="flex items-baseline justify-between gap-4 border-b border-line py-3 text-sm text-secondary transition-colors hover:text-ink">
                                <span>{{ $category->name }}</span>
                                <span class="text-[0.625rem] text-faint">{{ str_pad((string) $category->skills->count(), 2, '0', STR_PAD_LEFT) }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="mt-8 border border-line p-5">
                        <p class="eyebrow mb-2">Evidence first</p>
                        <p class="text-xs leading-relaxed text-secondary">
                            Skills are more convincing when they point to shipped work. Find the project archive below.
                        </p>
                        <a href="{{ route('projects.index') }}" wire:navigate class="link-underline mt-3 inline-flex text-[0.6875rem] font-medium uppercase tracking-[0.12em]">
                            Projects
                            <x-ui-icon name="arrow-right" class="h-3.5 w-3.5" />
                        </a>
                    </div>
                </aside>

                <div class="space-y-20">
                    @foreach ($skillCategories as $category)
                        <section id="category-{{ $category->slug }}" class="scroll-mt-28" data-reveal>
                            <div class="flex flex-col justify-between gap-4 border-b border-line pb-5 sm:flex-row sm:items-end">
                                <div>
                                    <p class="eyebrow mb-3">Category {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                                    <h2 class="display-3">{{ $category->name }}</h2>
                                    @if ($category->description)
                                        <p class="prose-editorial mt-3 max-w-xl">{{ $category->description }}</p>
                                    @endif
                                </div>
                                <p class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">{{ $category->skills->count() }} entries</p>
                            </div>

                            <div class="mt-8 grid gap-x-12 gap-y-8 sm:grid-cols-2">
                                @foreach ($category->skills as $skill)
                                    <x-skill-bar :skill="$skill" />
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
