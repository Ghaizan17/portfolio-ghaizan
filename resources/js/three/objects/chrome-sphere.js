import * as THREE from 'three';

export function createChromeSphere() {
    const isMobile = window.matchMedia('(max-width: 767px)').matches;
    const geometry = new THREE.SphereGeometry(1.28, isMobile ? 48 : 96, isMobile ? 32 : 64);
    const material = new THREE.MeshPhysicalMaterial({
        color: 0xffffff,
        metalness: 1,
        roughness: 0.12,
        envMapIntensity: 1.55,
        clearcoat: 1,
        clearcoatRoughness: 0.08,
    });

    const sphere = new THREE.Mesh(geometry, material);

    return sphere;
}

export function disposeChromeSphere(sphere) {
    sphere.geometry.dispose();
    sphere.material.dispose();
}
