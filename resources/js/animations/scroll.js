import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

export function initScrollReveals() {
    document.querySelectorAll('[data-reveal]').forEach((element) => {
        element.classList.add('reveal-target');

        gsap.fromTo(
            element,
            { y: 30, opacity: 0 },
            {
                y: 0,
                opacity: 1,
                duration: 0.95,
                ease: 'power3.out',
                clearProps: 'transform',
                scrollTrigger: {
                    trigger: element,
                    start: 'top 88%',
                    once: true,
                },
                onComplete: () => element.classList.add('reveal-visible'),
            },
        );
    });

    document.querySelectorAll('[data-reveal-group]').forEach((group) => {
        const children = Array.from(group.children).filter((child) => ! child.hasAttribute('data-reveal'));

        if (! children.length) {
            return;
        }

        children.forEach((child) => child.classList.add('reveal-target'));

        gsap.fromTo(
            children,
            { y: 26, opacity: 0 },
            {
                y: 0,
                opacity: 1,
                duration: 0.85,
                stagger: 0.08,
                ease: 'power3.out',
                clearProps: 'transform',
                scrollTrigger: {
                    trigger: group,
                    start: 'top 86%',
                    once: true,
                },
                onComplete: () => children.forEach((child) => child.classList.add('reveal-visible')),
            },
        );
    });

    document.querySelectorAll('[data-reveal-line]').forEach((line) => {
        gsap.fromTo(
            line,
            { scaleX: 0, transformOrigin: 'left center' },
            {
                scaleX: 1,
                duration: 1.2,
                ease: 'power3.inOut',
                scrollTrigger: {
                    trigger: line,
                    start: 'top 92%',
                    once: true,
                },
            },
        );
    });

    ScrollTrigger.refresh();
}

export function initProgressBars() {
    document.querySelectorAll('[data-progress]').forEach((element) => {
        const value = Math.min(100, Math.max(0, Number(element.dataset.progress) || 0));
        const bar = element.querySelector('[data-progress-bar]');

        if (! bar) {
            return;
        }

        gsap.fromTo(
            bar,
            { scaleX: 0 },
            {
                scaleX: value / 100,
                duration: 1.25,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: element,
                    start: 'top 92%',
                    once: true,
                },
            },
        );
    });
}
