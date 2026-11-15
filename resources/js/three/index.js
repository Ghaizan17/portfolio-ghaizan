let sceneInstance = null;

function webglAvailable() {
    try {
        const canvas = document.createElement('canvas');

        return Boolean(window.WebGLRenderingContext && (canvas.getContext('webgl2') || canvas.getContext('webgl')));
    } catch {
        return false;
    }
}

export function initChromeSphere() {
    const container = document.querySelector('[data-chrome-sphere]');

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
        .then(({ ChromeSphereScene }) => {
            sceneInstance = new ChromeSphereScene(container);

            return sceneInstance.init();
        })
        .catch((error) => {
            container.classList.add('is-fallback');
            container.dataset.threeFailed = 'true';
            console.warn('Chrome sphere scene could not initialize.', error);
        });
}

export function cleanupThree() {
    sceneInstance?.destroy();
    sceneInstance = null;

    document.querySelectorAll('[data-chrome-sphere]').forEach((container) => {
        delete container.dataset.initialized;
    });
}
