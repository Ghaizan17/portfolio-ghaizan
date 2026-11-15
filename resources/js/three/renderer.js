import * as THREE from 'three';

export function createRenderer(canvas) {
    const renderer = new THREE.WebGLRenderer({
        canvas,
        alpha: true,
        antialias: window.devicePixelRatio < 2,
        powerPreference: 'high-performance',
    });

    renderer.setClearColor(0x000000, 0);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.05;

    return renderer;
}

export function resizeRenderer(renderer, container) {
    const width = container.clientWidth;
    const height = container.clientHeight;
    const isMobile = window.matchMedia('(max-width: 767px)').matches;
    const maxPixelRatio = isMobile ? 1.5 : 2;

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, maxPixelRatio));
    renderer.setSize(width, height, false);

    return { width, height };
}
