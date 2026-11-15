@php
    $primaryNavigation = config('navigation.primary', []);
    $indexNavigation = config('navigation.index', []);
    $profile = $siteProfile ?? null;
@endphp

<header class="site-header" data-site-header>
    <div class="container-editorial flex h-[4.5rem] items-center justify-between gap-6 sm:h-20">
        <a href="{{ route('home') }}" wire:navigate class="group flex items-center gap-3" aria-label="{{ $siteSettings['site_title'] ?? 'GHAIZAN DEVELOPER' }} — home">
            <span class="brand-mark" aria-hidden="true">GZ</span>
            <span class="hidden flex-col leading-none sm:flex">
                <span class="font-display text-[0.9375rem] font-medium tracking-[-0.02em]">{{ $siteSettings['site_title'] ?? 'GHAIZAN DEVELOPER' }}</span>
                <span class="mt-1 text-[0.5625rem] font-medium uppercase tracking-[0.22em] text-faint">Portfolio / {{ now()->year }}</span>
            </span>
        </a>

        <nav class="hidden items-center gap-8 lg:flex" aria-label="Primary">
            @foreach ($primaryNavigation as $item)
                <a
                    href="{{ route($item['route']) }}"
                    wire:navigate
                    @class(['nav-link', 'text-ink' => request()->routeIs($item['active'] ?? $item['route'])])
                    @if (request()->routeIs($item['active'] ?? $item['route'])) aria-current="page" @endif
                >{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <button
                type="button"
                data-theme-toggle
                class="grid h-10 w-10 place-items-center border border-line text-secondary transition-colors hover:border-ink hover:text-ink"
                aria-label="Switch theme"
                aria-pressed="false"
            >
                <x-ui-icon name="moon" class="theme-icon-dark h-4 w-4" />
                <x-ui-icon name="sun" class="theme-icon-light h-4 w-4" />
            </button>

            <button
                type="button"
                data-menu-open
                class="flex h-10 items-center gap-2.5 border border-line px-3.5 text-[0.6875rem] font-medium uppercase tracking-[0.16em] text-ink transition-colors hover:border-ink hover:bg-ink hover:text-bg"
                aria-controls="site-menu"
                aria-expanded="false"
            >
                <x-ui-icon name="menu" class="h-4 w-4" />
                <span class="hidden sm:inline">Index</span>
            </button>
        </div>
    </div>
</header>

<dialog class="menu-dialog" id="site-menu" data-mobile-menu aria-label="Site index">
    <div class="flex min-h-dvh flex-col">
        <div class="container-editorial flex h-[4.5rem] shrink-0 items-center justify-between border-b border-line sm:h-20">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3">
                <span class="brand-mark" aria-hidden="true">DD</span>
                <span class="font-display text-sm font-medium tracking-[-0.01em]">{{ $siteSettings['site_title'] ?? 'GHAIZAN DEVELOPER' }}</span>
            </a>

            <button type="button" data-menu-close class="flex h-10 items-center gap-2.5 border border-line px-3.5 text-[0.6875rem] font-medium uppercase tracking-[0.16em] transition-colors hover:border-ink hover:bg-ink hover:text-bg">
                <span>Close</span>
                <x-ui-icon name="x" class="h-4 w-4" />
            </button>
        </div>

        <div class="container-editorial flex flex-1 flex-col justify-center py-10">
            <p class="eyebrow mb-6">Navigate the dimension</p>
            <nav aria-label="Full site index" class="w-full">
                @foreach ($indexNavigation as $index => $item)
                    <a
                        href="{{ route($item['route']) }}"
                        wire:navigate
                        @class(['menu-link', 'text-accent' => request()->routeIs($item['route'])])
                        @if (request()->routeIs($item['route'])) aria-current="page" @endif
                    >
                        <span class="menu-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <span>{{ $item['label'] }}</span>
                        <x-ui-icon name="arrow-up-right" class="ms-auto h-5 w-5 opacity-40" />
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="container-editorial grid shrink-0 gap-6 border-t border-line py-8 sm:grid-cols-2 sm:items-end">
            <div class="space-y-2">
                @if ($profile?->email)
                    <a href="mailto:{{ $profile->email }}" class="link-underline text-sm">{{ $profile->email }}</a>
                @endif
                @if ($profile?->location)
                    <p class="flex items-center gap-2 text-sm text-secondary">
                        <x-ui-icon name="map-pin" class="h-4 w-4 text-faint" />
                        {{ $profile->location }}
                    </p>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-x-5 gap-y-3 sm:justify-end">
                <x-social-links :links="$siteSocialLinks" :show-labels="true" />
            </div>
        </div>
    </div>
</dialog>
