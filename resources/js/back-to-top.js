let scrollHandler = null;

export function initBackToTop() {
    const button = document.querySelector('[data-back-to-top]');

    if (scrollHandler) {
        window.removeEventListener('scroll', scrollHandler);
        scrollHandler = null;
    }

    if (!button) {
        return;
    }

    const toggle = () => {
        const visible = window.scrollY > 640;

        button.classList.toggle('is-visible', visible);
        button.setAttribute('aria-hidden', visible ? 'false' : 'true');
        button.tabIndex = visible ? 0 : -1;
    };

    scrollHandler = toggle;
    toggle();
    window.addEventListener('scroll', toggle, { passive: true });

    button.addEventListener('click', () => {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
}

export function cleanupBackToTop() {
    if (scrollHandler) {
        window.removeEventListener('scroll', scrollHandler);
        scrollHandler = null;
    }
}
