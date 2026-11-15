import { gsap } from 'gsap';
import { prefersReducedMotion } from './index';

let overlay = null;
let initialized = false;

export function initPageTransitions() {
    overlay = document.querySelector('[data-page-transition]');

    if (! overlay || initialized || prefersReducedMotion()) {
        return;
    }

    initialized = true;
    gsap.set(overlay, { autoAlpha: 0 });

    document.addEventListener('livewire:navigating', () => {
        gsap.set(overlay, { autoAlpha: 1, opacity: 0 });
        gsap.to(overlay, { opacity: 1, duration: 0.22, ease: 'power2.out' });
    });

    document.addEventListener('livewire:navigated', () => {
        gsap.to(overlay, {
            opacity: 0,
            duration: 0.38,
            delay: 0.05,
            ease: 'power2.out',
            onComplete: () => gsap.set(overlay, { autoAlpha: 0 }),
        });
    });
}
