let sceneInstance = null;

function webglAvailable() {
    try {
        const canvas = document.createElement('canvas');

        return Boolean(window.WebGLRenderingContext && (canvas.getContext('webgl2') || canvas.getContext('webgl')));
    } catch {
        return false;
    }
}

export function initHeroScene() {
    const container = document.querySelector('[data-hero-object]');

    if (! container) {
        return;
    }

    if (sceneInstance || container.dataset.initialized === 'true') {
        return;
    }

    container.dataset.initialized = 'true';

    if (! webglAvailable()) {
        container.classList.add('is-fallback');
        container.dataset.threeFailed = 'true';

        return;
    }

    import('./scene')
        .then(({ HeroScene }) => {
            sceneInstance = new HeroScene(container);

            return sceneInstance.init();
        })
        .catch((error) => {
            container.classList.add('is-fallback');
            container.dataset.threeFailed = 'true';
            console.warn('Hero scene could not initialize.', error);
        });
}

export function cleanupHeroScene() {
    sceneInstance?.destroy();
    sceneInstance = null;

    document.querySelectorAll('[data-hero-object]').forEach((container) => {
        delete container.dataset.initialized;
    });
}
