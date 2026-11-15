import '../css/app.css';

import { initTheme } from './theme';
import { cleanupNavigation, initNavigation } from './navigation';
import { cleanupBackToTop, initBackToTop } from './back-to-top';
import { initLightbox } from './lightbox';
import { highlightCodeBlocks } from './highlight';
import { initPageAnimations, cleanupPageAnimations } from './animations/index';
import { initPageTransitions } from './animations/transitions';
import { cleanupThree, initChromeSphere } from './three/index';

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

function bootPage() {
    initTheme();
    initNavigation();
    initBackToTop();
    initLightbox();
    highlightCodeBlocks();

    try {
        initPageAnimations();
    } catch (error) {
        document.documentElement.classList.add('animations-failed');
        console.error('Animation initialization failed', error);
    }

    initChromeSphere();
}

function teardownPage() {
    cleanupPageAnimations();
    cleanupThree();
    cleanupNavigation();
    cleanupBackToTop();
}

document.addEventListener('DOMContentLoaded', () => {
    initPageTransitions();
    bootPage();
});

document.addEventListener('livewire:navigating', () => {
    teardownPage();
});

document.addEventListener('livewire:navigated', () => {
    bootPage();
});

reducedMotion.addEventListener('change', () => {
    initPageAnimations();
});
