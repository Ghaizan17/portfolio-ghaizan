<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        (function () {
            try {
                var stored = localStorage.getItem('dd-theme');
                var fallback = @json($siteSettings['default_theme'] ?? 'dark');
                var dark = stored ? stored === 'dark' : fallback !== 'light';

                document.documentElement.classList.toggle('dark', dark);
                document.documentElement.dataset.theme = dark ? 'dark' : 'light';
            } catch (error) {
                document.documentElement.classList.add('dark');
            }

            document.documentElement.classList.add('js');
        })();
    </script>

    @php
        $pageTitle = trim($__env->yieldContent('title'));
        $pageDescription = trim($__env->yieldContent('description'));
        $pageImage = trim($__env->yieldContent('og_image'));
        $pageType = trim($__env->yieldContent('og_type')) ?: 'website';
        $pageRobots = trim($__env->yieldContent('robots')) ?: 'index,follow';
    @endphp

    <x-seo
        :title="$pageTitle ?: null"
        :description="$pageDescription ?: null"
        :image="$pageImage ?: null"
        :type="$pageType"
        :robots="$pageRobots"
    />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="@yield('body_class') min-h-screen bg-bg font-sans text-ink antialiased">
    <a href="#main" class="skip-link">Skip to content</a>

    <div class="page-transition" data-page-transition aria-hidden="true"></div>

    @if ($siteDemoMode ?? false)
        <div class="demo-banner" role="status">
            <x-ui-icon name="alert-circle" class="h-3.5 w-3.5" />
            <span>Demonstration content — every identity, project, and testimonial is an editable placeholder.</span>
            <a href="{{ url('/admin') }}" class="underline decoration-accent-ink/40 underline-offset-2 hover:decoration-accent-ink">Manage in admin</a>
        </div>
    @endif

    @include('partials.header')

    <main id="main" tabindex="-1">
        @yield('content')
    </main>

    @include('partials.footer')

    <button
        type="button"
        class="back-to-top"
        data-back-to-top
        aria-label="Back to top"
        aria-hidden="true"
        tabindex="-1"
    >
        <x-ui-icon name="arrow-up" class="h-4 w-4" />
    </button>

    <dialog class="lightbox-dialog" data-lightbox aria-label="Image viewer">
        <div class="relative flex max-h-[94dvh] flex-col">
            <div class="relative flex items-center justify-center bg-black/40">
                <img data-lightbox-image src="" alt="" class="mx-auto max-h-[76dvh] w-auto object-contain">
            </div>
            <div class="flex items-center justify-between gap-4 border-t border-white/10 bg-black/60 px-4 py-3">
                <div class="min-w-0">
                    <p data-lightbox-caption class="truncate text-sm text-white/80"></p>
                    <p data-lightbox-counter class="mt-0.5 text-[0.6875rem] uppercase tracking-[0.16em] text-white/40"></p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" data-lightbox-prev class="grid h-10 w-10 place-items-center border border-white/15 text-white transition-colors hover:border-white hover:bg-white hover:text-black" aria-label="Previous image">
                        <x-ui-icon name="chevron-left" class="h-4 w-4" />
                    </button>
                    <button type="button" data-lightbox-next class="grid h-10 w-10 place-items-center border border-white/15 text-white transition-colors hover:border-white hover:bg-white hover:text-black" aria-label="Next image">
                        <x-ui-icon name="chevron-right" class="h-4 w-4" />
                    </button>
                    <button type="button" data-lightbox-close class="grid h-10 w-10 place-items-center border border-white/15 text-white transition-colors hover:border-white hover:bg-white hover:text-black" aria-label="Close image viewer">
                        <x-ui-icon name="x" class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>
    </dialog>

    @stack('scripts')
</body>
</html>
