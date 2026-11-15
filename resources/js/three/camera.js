import * as THREE from 'three';

export function createCamera() {
    const camera = new THREE.PerspectiveCamera(38, 1, 0.1, 100);
    camera.position.set(0, 0, 4.6);

    return camera;
}
