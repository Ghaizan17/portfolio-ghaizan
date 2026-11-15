@extends('layouts.app')

@section('title', 'Projects')

@section('description', 'A searchable archive of published projects, case studies, and development stories.')

@section('content')
    <x-page-hero
        eyebrow="Work archive"
        title="Projects & case studies."
        description="Selected work documented end to end: the problem, the constraints, the decisions, and what was actually built."
    >
        <x-slot:aside>
            <dl class="grid grid-cols-2 gap-4 border-t border-line pt-4">
                <div>
                    <dt class="eyebrow">Published</dt>
                    <dd class="stat-value mt-2 text-[2.6rem]">{{ str_pad((string) $total, 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
                <div>
                    <dt class="eyebrow">Categories</dt>
                    <dd class="stat-value mt-2 text-[2.6rem]">{{ str_pad((string) $categories->count(), 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
            </dl>
        </x-slot:aside>

        <x-slot:meta>
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-[0.625rem] uppercase tracking-[0.14em] text-faint">
                <span class="inline-flex items-center gap-2">
                    <x-ui-icon name="filter" class="h-3 w-3" />
                    Filter by category, technology, or keyword
                </span>
                <span class="hidden sm:inline">Every project links to a full case study</span>
            </div>
        </x-slot:meta>
    </x-page-hero>

    <section class="section">
        <div class="container-editorial">
            <livewire:project-explorer />
        </div>
    </section>
@endsection
