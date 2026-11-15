import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { initHero } from './hero';
import { initProgressBars, initScrollReveals } from './scroll';

gsap.registerPlugin(ScrollTrigger);

let pageContext = null;

export function prefersReducedMotion() {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export function initPageAnimations(root = document) {
    cleanupPageAnimations();

    if (prefersReducedMotion()) {
        document.querySelectorAll('[data-reveal]').forEach((element) => {
            element.classList.add('reveal-visible');
        });

        return;
    }

    pageContext = gsap.context(() => {
        initHero();
        initScrollReveals();
        initProgressBars();
    }, root);

    requestAnimationFrame(() => ScrollTrigger.refresh());
}

export function cleanupPageAnimations() {
    pageContext?.revert();
    pageContext = null;
}

export { gsap, ScrollTrigger };
