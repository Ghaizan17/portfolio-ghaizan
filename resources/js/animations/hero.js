import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

export function initHero() {
    const hero = document.querySelector('[data-hero]');

    if (!hero) {
        return;
    }

    const timeline = gsap.timeline({
        defaults: { duration: 0.95, ease: 'power3.out' },
        delay: 0.15,
    });

    const eyebrow = hero.querySelector('[data-hero-eyebrow]');
    const lines = hero.querySelectorAll('[data-hero-line]');
    const copy = hero.querySelector('[data-hero-copy]');
    const ctas = hero.querySelector('[data-hero-cta]');
    const meta = hero.querySelector('[data-hero-meta]');
    const visual = hero.querySelector('[data-hero-visual]');
    const indicator = hero.querySelector('[data-scroll-indicator]');

    if (eyebrow) {
        timeline.from(eyebrow, { y: 18, opacity: 0 }, 0);
    }

    if (lines.length) {
        timeline.from(lines, { yPercent: 110, opacity: 0, stagger: 0.09 }, 0.08);
    }

    if (copy) {
        timeline.from(copy, { y: 24, opacity: 0 }, 0.45);
    }

    if (ctas) {
        timeline.from(ctas, { y: 18, opacity: 0 }, 0.6);
    }

    if (meta) {
        timeline.from(meta, { y: 14, opacity: 0 }, 0.72);
    }

    if (visual) {
        timeline.from(visual, { scale: 0.94, opacity: 0, duration: 1.4 }, 0.25);
    }

    if (indicator) {
        timeline.from(indicator, { opacity: 0, y: -8 }, 1);

        gsap.to(indicator.querySelector('[data-scroll-dot]') ?? indicator, {
            y: 10,
            repeat: -1,
            yoyo: true,
            duration: 1.2,
            ease: 'sine.inOut',
        });
    }

    const content = hero.querySelector('[data-hero-content]');

    if (content) {
        gsap.to(content, {
            yPercent: 8,
            opacity: 0.55,
            ease: 'none',
            scrollTrigger: {
                trigger: hero,
                start: 'top top',
                end: 'bottom top',
                scrub: true,
            },
        });
    }

    ScrollTrigger.refresh();
}
