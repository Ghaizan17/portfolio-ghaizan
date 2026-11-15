import * as THREE from 'three';

export function addLighting(scene) {
    const ambient = new THREE.AmbientLight(0xffffff, 0.25);
    const key = new THREE.DirectionalLight(0xffffff, 2.4);
    key.position.set(4, 4, 5);
    const rim = new THREE.PointLight(0xc8102e, 28, 12, 2);
    rim.position.set(-3, -1.5, 2.5);
    const fill = new THREE.DirectionalLight(0x93c5fd, 1.1);
    fill.position.set(-4, 2, -3);

    scene.add(ambient, key, rim, fill);

    return { ambient, key, rim, fill };
}
