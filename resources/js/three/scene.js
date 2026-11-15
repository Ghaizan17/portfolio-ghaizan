import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { createCamera } from './camera';
import { addLighting } from './lighting';
import { createChromeSphere, disposeChromeSphere } from './objects/chrome-sphere';
import { createRenderer, resizeRenderer } from './renderer';

gsap.registerPlugin(ScrollTrigger);

export class ChromeSphereScene {
    constructor(container) {
        this.container = container;
        this.canvas = container.querySelector('canvas');
        this.reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.pointer = { x: 0, y: 0 };
        this.smoothedPointer = { x: 0, y: 0 };
        this.scrollProgress = 0;
        this.visible = true;
        this.frame = null;
        this.started = false;
        this.destroyed = false;

        this.handlePointerMove = this.handlePointerMove.bind(this);
        this.handleResize = this.handleResize.bind(this);
        this.handleVisibility = this.handleVisibility.bind(this);
        this.animate = this.animate.bind(this);
        this.render = this.render.bind(this);
    }

    async init() {
        this.renderer = createRenderer(this.canvas);
        this.scene = new THREE.Scene();
        this.camera = createCamera();
        this.lights = addLighting(this.scene);

        this.pmrem = new THREE.PMREMGenerator(this.renderer);
        this.environmentRenderTarget = this.pmrem.fromScene(new RoomEnvironment(), 0.04);
        this.scene.environment = this.environmentRenderTarget.texture;
        this.pmrem.dispose();

        this.sphere = createChromeSphere();
        this.group = new THREE.Group();
        this.group.add(this.sphere);
        this.scene.add(this.group);

        this.resizeObserver = new ResizeObserver(this.handleResize);
        this.resizeObserver.observe(this.container);

        this.intersectionObserver = new IntersectionObserver(
            (entries) => {
                this.visible = entries[0]?.isIntersecting ?? true;
            },
            { threshold: 0.01 },
        );
        this.intersectionObserver.observe(this.container);

        window.addEventListener('pointermove', this.handlePointerMove, { passive: true });
        document.addEventListener('visibilitychange', this.handleVisibility);

        if (! this.reducedMotion) {
            this.scrollTrigger = ScrollTrigger.create({
                trigger: this.container.closest('[data-hero]') ?? this.container,
                start: 'top top',
                end: 'bottom top',
                scrub: true,
                onUpdate: (self) => {
                    this.scrollProgress = self.progress;
                },
            });
        }

        this.handleResize();
        this.container.classList.add('is-ready');
        this.started = true;

        this.render();

        if (! this.reducedMotion) {
            gsap.fromTo(this.group.scale, { x: 0.82, y: 0.82, z: 0.82 }, {
                x: 1,
                y: 1,
                z: 1,
                duration: 1.6,
                ease: 'power3.out',
            });

            this.animate();
        }
    }

    handlePointerMove(event) {
        if (this.reducedMotion) {
            return;
        }

        this.pointer.x = (event.clientX / window.innerWidth) * 2 - 1;
        this.pointer.y = (event.clientY / window.innerHeight) * 2 - 1;
    }

    handleResize() {
        if (! this.renderer) {
            return;
        }

        const { width, height } = resizeRenderer(this.renderer, this.container);

        if (! width || ! height) {
            return;
        }

        this.camera.aspect = width / height;
        this.camera.updateProjectionMatrix();

        if (this.reducedMotion) {
            this.render();
        }
    }

    handleVisibility() {
        if (document.hidden) {
            cancelAnimationFrame(this.frame);
            this.frame = null;

            return;
        }

        if (! this.reducedMotion && this.started && ! this.frame) {
            this.animate();
        }
    }

    animate() {
        this.frame = requestAnimationFrame(this.animate);

        if (document.hidden || ! this.visible) {
            return;
        }

        this.render();
    }

    render() {
        if (! this.renderer || this.destroyed) {
            return;
        }

        const time = performance.now() * 0.001;

        if (! this.reducedMotion) {
            this.smoothedPointer.x += (this.pointer.x - this.smoothedPointer.x) * 0.045;
            this.smoothedPointer.y += (this.pointer.y - this.smoothedPointer.y) * 0.045;

            this.group.rotation.y = this.smoothedPointer.x * 0.42 + time * 0.06;
            this.group.rotation.x = this.smoothedPointer.y * 0.22 + this.scrollProgress * 0.8;
            this.group.position.y = Math.sin(time * 0.75) * 0.07 - this.scrollProgress * 0.45;
            this.group.position.x = this.smoothedPointer.x * 0.08;

            const scrollScale = 1 - this.scrollProgress * 0.18;
            this.group.scale.setScalar(scrollScale);

            this.camera.position.z = 4.6 + this.scrollProgress * 0.6;
        } else {
            this.group.rotation.set(0.12, 0.45, -0.03);
        }

        this.renderer.render(this.scene, this.camera);
    }

    destroy() {
        this.destroyed = true;
        cancelAnimationFrame(this.frame);

        window.removeEventListener('pointermove', this.handlePointerMove);
        document.removeEventListener('visibilitychange', this.handleVisibility);

        this.resizeObserver?.disconnect();
        this.intersectionObserver?.disconnect();
        this.scrollTrigger?.kill();

        if (this.sphere) {
            disposeChromeSphere(this.sphere);
        }

        this.environmentRenderTarget?.dispose();
        this.renderer?.dispose();
        this.renderer?.forceContextLoss();
        this.renderer = null;
        this.container.classList.remove('is-ready');
    }
}
