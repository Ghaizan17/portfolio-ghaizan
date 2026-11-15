@extends('layouts.app')

@section('title', 'Testimonials')

@section('description', 'Permissioned feedback from supervisors, teachers, project users, and peers.')

@section('content')
    <x-page-hero
        eyebrow="Testimonials"
        title="In their words."
        description="Feedback from people who have supervised, taught, used, or worked alongside the projects. Published only with permission."
    >
        <x-slot:aside>
            <div class="grid grid-cols-2 gap-4 border-t border-line pt-4">
                <div>
                    <p class="eyebrow">Published</p>
                    <p class="stat-value mt-2 text-[2.6rem]">{{ str_pad((string) $testimonials->count(), 2, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <p class="eyebrow">Relationships</p>
                    <p class="stat-value mt-2 text-[2.6rem]">{{ str_pad((string) $groupedTestimonials->count(), 2, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </x-slot:aside>
    </x-page-hero>

    <section class="section">
        <div class="container-editorial">
            @if ($testimonials->isEmpty())
                <x-empty-state
                    icon="quote"
                    title="No testimonials published yet"
                    description="Collect permissioned feedback and publish it from the admin panel."
                    action-route="contact"
                    action-label="Get in touch"
                />
            @else
                <div class="space-y-20">
                    @foreach (\App\Models\Testimonial::relationships() as $key => $label)
                        @if ($groupedTestimonials->has($key))
                            <section data-reveal>
                                <div class="mb-8 flex items-end justify-between gap-4 border-b border-line pb-4">
                                    <h2 class="display-3">{{ $label }}</h2>
                                    <span class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">{{ str_pad((string) $groupedTestimonials->get($key)->count(), 2, '0', STR_PAD_LEFT) }}</span>
                                </div>

                                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($groupedTestimonials->get($key) as $testimonial)
                                        <x-testimonial-card :testimonial="$testimonial" />
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
