@extends('layouts.app')

@section('title', 'Certificates')

@section('description', 'Certificates, courses, and formal recognition with supporting documents.')

@section('content')
    <x-page-hero
        eyebrow="Credentials"
        title="Certificates & recognition."
        :description="'A record of structured learning and formal recognition. '.($certificates->count()).' published '.\Illuminate\Support\Str::plural('certificate', $certificates->count()).' with supporting files where available.'"
    >
        <x-slot:aside>
            <dl class="grid grid-cols-2 gap-4 border-t border-line pt-4">
                <div>
                    <dt class="eyebrow">Published</dt>
                    <dd class="stat-value mt-2 text-[2.6rem]">{{ str_pad((string) $certificates->count(), 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
                <div>
                    <dt class="eyebrow">Categories</dt>
                    <dd class="stat-value mt-2 text-[2.6rem]">{{ str_pad((string) $groupedCertificates->count(), 2, '0', STR_PAD_LEFT) }}</dd>
                </div>
            </dl>
        </x-slot:aside>
    </x-page-hero>

    <section class="section">
        <div class="container-editorial">
            @if ($certificates->isEmpty())
                <x-empty-state
                    icon="award"
                    title="No certificates published yet"
                    description="Add certificates from the admin panel. Images can open in the lightbox, and PDFs download directly."
                    action-route="contact"
                    action-label="Get in touch"
                />
            @else
                <div class="space-y-20">
                    @foreach ($groupedCertificates as $category => $group)
                        <section data-reveal>
                            <div class="flex items-end justify-between gap-6 border-b border-line pb-4">
                                <h2 class="display-3">{{ $category }}</h2>
                                <span class="text-[0.625rem] uppercase tracking-[0.14em] text-faint">{{ str_pad((string) $group->count(), 2, '0', STR_PAD_LEFT) }} records</span>
                            </div>

                            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                @foreach ($group as $certificate)
                                    <x-certificate-card :certificate="$certificate" />
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection
