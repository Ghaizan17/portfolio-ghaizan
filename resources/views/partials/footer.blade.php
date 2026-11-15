@php
    $profile = $siteProfile ?? null;
    $footerColumns = config('navigation.footer', []);
    $contactUrl = route('contact');
@endphp

<footer class="border-t border-line">
    <section class="container-editorial section-tight">
        <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr] lg:items-end">
            <div data-reveal>
                <p class="eyebrow mb-5">Next step</p>
                <h2 class="display-2 max-w-3xl">
                    Have an idea worth<br>
                    <span class="text-accent">building?</span>
                </h2>
            </div>

            <div class="flex flex-col items-start gap-6 lg:items-end" data-reveal>
                <p class="prose-editorial max-w-sm lg:text-end">
                    Open to thoughtful projects, internships, and collaborations. Tell me what you are working on.
                </p>
                <a href="{{ $contactUrl }}" wire:navigate class="group inline-flex items-center gap-4">
                    <span class="grid h-16 w-16 place-items-center rounded-full border border-line transition-colors duration-300 group-hover:border-accent group-hover:bg-accent group-hover:text-accent-ink">
                        <x-ui-icon name="arrow-up-right" class="h-6 w-6" />
                    </span>
                    <span class="text-[0.75rem] font-medium uppercase tracking-[0.18em]">Start a conversation</span>
                </a>
            </div>
        </div>
    </section>

    <div class="border-t border-line">
        <div class="container-editorial grid gap-12 py-14 md:grid-cols-2 lg:grid-cols-[1.4fr_0.7fr_0.7fr_1fr]">
            <div>
                <a href="{{ route('home') }}" wire:navigate class="inline-flex items-center gap-3">
                    <span class="brand-mark" aria-hidden="true">GZ</span>
                    <span class="font-display text-base font-medium tracking-[-0.02em]">{{ $siteSettings['site_title'] ?? 'Digital Dimension' }}</span>
                </a>
                <p class="mt-5 max-w-xs text-sm leading-relaxed text-secondary">
                    {{ $profile?->personal_branding ?: $siteSettings['site_tagline'] ?? 'Beyond Code, Into Experience.' }}
                </p>
                <div class="mt-6">
                    <x-social-links :links="$siteSocialLinks" />
                </div>
            </div>

            @foreach ($footerColumns as $heading => $links)
                <nav aria-label="{{ $heading }}">
                    <p class="eyebrow mb-5">{{ $heading }}</p>
                    <ul class="space-y-3">
                        @foreach ($links as $item)
                            <li>
                                <a href="{{ route($item['route']) }}" wire:navigate class="site-footer-link">{{ $item['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endforeach

            <div>
                <p class="eyebrow mb-5">Contact</p>
                <ul class="space-y-3 text-sm text-secondary">
                    @if ($profile?->email)
                        <li>
                            <a href="mailto:{{ $profile->email }}" class="link-underline">{{ $profile->email }}</a>
                        </li>
                    @else
                        <li>
                            <a href="{{ $contactUrl }}" wire:navigate class="link-underline">Send a message</a>
                        </li>
                    @endif
                    @if ($profile?->location)
                        <li>{{ $profile->location }}</li>
                    @endif
                    @if ($profile?->availability)
                        <li class="flex items-center gap-2">
                            <span class="inline-block h-1.5 w-1.5 rounded-full bg-accent"></span>
                            {{ $profile->availability }}
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="container-editorial overflow-hidden pb-8 pt-4" aria-hidden="true">
        <p class="footer-wordmark">
            GHAIZAN<br>
            <span>DEVELOPER</span>
        </p>
    </div>

    <div class="border-t border-line">
        <div class="container-editorial flex flex-col gap-4 py-6 text-[0.6875rem] uppercase tracking-[0.14em] text-faint sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $siteSettings['site_title'] ?? 'Digital Dimension' }}. All rights reserved.</p>
            <p>{{ $siteSettings['footer_text'] ?? 'Designed and built with Laravel, Livewire, GSAP, and Three.js.' }}</p>
        </div>
    </div>
</footer>
