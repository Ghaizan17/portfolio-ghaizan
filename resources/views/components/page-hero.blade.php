@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'index' => null,
])

<section class="page-hero grain" data-hero>
    <div class="container-editorial">
        <div class="grid gap-10 lg:grid-cols-[1.55fr_0.45fr] lg:items-end">
            <div data-reveal>
                @if ($eyebrow)
                    <p class="eyebrow mb-5 flex items-center gap-3">
                        <span class="inline-block h-px w-8 bg-accent"></span>
                        {{ $eyebrow }}
                    </p>
                @endif

                <h1 class="display-1">{{ $title }}</h1>

                @if ($description)
                    <p class="body-lg mt-6 max-w-2xl text-secondary">{{ $description }}</p>
                @endif
            </div>

            @isset($aside)
                <div class="lg:pb-2" data-reveal>
                    {{ $aside }}
                </div>
            @endisset
        </div>

        @isset($meta)
            <div class="mt-12 border-t border-line pt-4" data-reveal>
                {{ $meta }}
            </div>
        @endisset
    </div>
</section>
