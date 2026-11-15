import * as THREE from 'three';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';

const ACCENT = '#c8102e';

const tokenColors = {
    comment: '#7c7c85',
    keyword: '#ff6b7f',
    string: '#9ae6b4',
    number: '#f6ad55',
    property: '#7dd3fc',
    function: '#a5b4fc',
    plain: '#e8e8ee',
    punctuation: '#8b8f9a',
    accent: '#ff5068',
};

const tokenPatterns = [
    { type: 'comment', pattern: /\/\/.*$/ },
    { type: 'string', pattern: /"(?:[^"\\]|\\.)*"|'(?:[^'\\]|\\.)*'/ },
    { type: 'keyword', pattern: /\b(?:const|let|var|export|default|function|return|true|false|null|new|if|else|import|from)\b/ },
    { type: 'number', pattern: /\b\d+(?:\.\d+)?\b/ },
    { type: 'property', pattern: /\b[A-Za-z_$][\w$]*(?=\s*:)/ },
    { type: 'function', pattern: /\b[A-Za-z_$][\w$]*(?=\s*\()/ },
];

const codeLines = [
    'const developer = {',
    '  name: "Your Name",',
    '  role: "Full-Stack Engineer",',
    '  focus: ["craft", "clarity", "motion"],',
    '  stack: ["Laravel", "Livewire", "Three.js"],',
    '  available: true,',
    '};',
    '',
    'export function buildExperience() {',
    '  return developer.focus.map((value) => ({',
    '    value,',
    '    accent: "#C8102E",',
    '  }));',
    '}',
];

function tokenize(line) {
    const tokens = [];
    let cursor = 0;

    while (cursor < line.length) {
        const rest = line.slice(cursor);
        let best = null;

        for (const { type, pattern } of tokenPatterns) {
            const match = rest.match(pattern);

            if (match && match.index !== undefined && (best === null || match.index < best.index)) {
                best = { type, index: match.index, value: match[0] };
            }
        }

        if (best === null) {
            tokens.push({ type: 'plain', value: rest });
            break;
        }

        if (best.index > 0) {
            tokens.push({ type: 'plain', value: rest.slice(0, best.index) });
        }

        tokens.push({ type: best.type, value: best.value });
        cursor += best.index + best.value.length;
    }

    return tokens.map((token) => ({
        ...token,
        color: token.value === '"#C8102E"' ? tokenColors.accent : tokenColors[token.type] ?? tokenColors.plain,
    }));
}

function createCodeTexture(isMobile) {
    const logicalWidth = 960;
    const logicalHeight = 600;
    const canvas = document.createElement('canvas');
    canvas.width = isMobile ? 720 : logicalWidth;
    canvas.height = isMobile ? 450 : logicalHeight;

    const context = canvas.getContext('2d');
    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.minFilter = THREE.LinearFilter;
    texture.magFilter = THREE.LinearFilter;
    texture.generateMipmaps = false;

    const lines = codeLines.map((line) => ({
        value: line,
        tokens: tokenize(line),
    }));

    const totalCharacters = codeLines.reduce((total, line) => total + Math.max(1, line.length), 0);
    const startedAt = { value: null };
    const lastFrame = { value: -1 };
    const activeLine = { value: 11 };
    const nextActiveAt = { value: 0 };
    let staticDrawn = false;

    const drawRoundedRect = (x, y, width, height, radius, fill) => {
        context.beginPath();
        context.roundRect(x, y, width, height, radius);
        context.fillStyle = fill;
        context.fill();
    };

    const draw = (time, reveal) => {
        context.setTransform(canvas.width / logicalWidth, 0, 0, canvas.height / logicalHeight, 0, 0);
        context.clearRect(0, 0, logicalWidth, logicalHeight);

        const background = context.createLinearGradient(0, 0, logicalWidth, logicalHeight);
        background.addColorStop(0, '#0c0d11');
        background.addColorStop(0.55, '#090a0d');
        background.addColorStop(1, '#101116');
        context.fillStyle = background;
        context.fillRect(0, 0, logicalWidth, logicalHeight);

        const glow = context.createRadialGradient(150, 40, 0, 150, 40, 520);
        glow.addColorStop(0, 'rgba(200, 16, 46, 0.16)');
        glow.addColorStop(1, 'rgba(200, 16, 46, 0)');
        context.fillStyle = glow;
        context.fillRect(0, 0, logicalWidth, logicalHeight);

        drawRoundedRect(0, 0, logicalWidth, 56, 0, '#101116');
        context.fillStyle = '#1b1c22';
        context.fillRect(0, 55, logicalWidth, 1);

        const dotColors = ['#ff5f57', '#febc2e', '#28c840'];
        dotColors.forEach((color, index) => {
            context.beginPath();
            context.arc(28 + index * 24, 28, 7, 0, Math.PI * 2);
            context.fillStyle = color;
            context.fill();
        });

        drawRoundedRect(108, 13, 210, 30, 6, '#16171d');
        context.fillStyle = '#e8e8ee';
        context.font = '500 16px ui-monospace, SFMono-Regular, Menlo, Consolas, monospace';
        context.fillText('hero.tsx', 128, 34);
        context.fillStyle = ACCENT;
        context.fillRect(112, 16, 3, 24);

        context.fillStyle = '#7c7c85';
        context.font = '500 15px ui-monospace, SFMono-Regular, Menlo, Consolas, monospace';
        context.textAlign = 'right';
        context.fillText('main', logicalWidth - 32, 34);
        context.textAlign = 'left';

        const gutterX = 64;
        const codeX = 92;
        const codeTop = 92;
        const lineHeight = 33;
        const fontSize = 23;
        const font = `500 ${fontSize}px ui-monospace, SFMono-Regular, Menlo, Consolas, monospace`;
        context.font = font;
        context.textBaseline = 'alphabetic';

        const visibleCharacters = Math.floor(totalCharacters * reveal);
        const fullyRevealed = visibleCharacters >= totalCharacters;
        let remaining = visibleCharacters;
        let typedLine = 0;

        lines.forEach((line, index) => {
            const y = codeTop + index * lineHeight;

            context.fillStyle = '#565963';
            context.font = font;
            context.textAlign = 'right';
            context.fillText(String(index + 1).padStart(2, '0'), gutterX, y);
            context.textAlign = 'left';

            if (! fullyRevealed && remaining >= 0) {
                typedLine = index;
            }

            remaining -= Math.max(1, line.value.length);
        });

        const highlighted = fullyRevealed ? activeLine.value : typedLine;
        const highlightY = codeTop + highlighted * lineHeight - 24;

        if (highlighted >= 0 && highlighted < lines.length) {
            context.fillStyle = 'rgba(200, 16, 46, 0.10)';
            context.fillRect(76, highlightY, logicalWidth - 108, lineHeight);
            context.fillStyle = ACCENT;
            context.fillRect(76, highlightY, 3, lineHeight);
        }

        let charactersLeft = visibleCharacters;
        let cursor = null;

        lines.forEach((line, index) => {
            if (charactersLeft <= 0) {
                return;
            }

            const y = codeTop + index * lineHeight;
            let x = codeX;
            let drawn = 0;

            for (const token of line.tokens) {
                if (charactersLeft <= 0) {
                    break;
                }

                const available = Math.max(0, charactersLeft);
                const value = token.value.slice(0, available);

                if (value.length) {
                    context.fillStyle = token.color;
                    context.font = font;
                    context.fillText(value, x, y);
                    x += context.measureText(value).width;
                    drawn += value.length;
                    charactersLeft -= value.length;
                } else {
                    break;
                }
            }

            if (drawn >= line.value.length) {
                charactersLeft -= 1;
            }

            cursor = { x, y, index };
        });

        if (cursor) {
            const blinkOn = Math.floor(time * 1.8) % 2 === 0;
            const isTypingLine = ! fullyRevealed;

            if (isTypingLine || blinkOn) {
                context.fillStyle = ACCENT;
                context.fillRect(cursor.x + 2, cursor.y - 19, 2, 23);
            }
        }

        const statusY = logicalHeight - 34;
        context.fillStyle = '#0d0e12';
        context.fillRect(0, statusY, logicalWidth, 34);
        context.fillStyle = '#1b1c22';
        context.fillRect(0, statusY, logicalWidth, 1);

        context.font = '500 14px ui-monospace, SFMono-Regular, Menlo, Consolas, monospace';
        context.fillStyle = '#8b8f9a';
        context.fillText('main*', 24, statusY + 23);
        context.fillStyle = '#7dd3fc';
        context.fillText('TypeScript', 90, statusY + 23);
        context.fillStyle = '#8b8f9a';
        context.fillText(`Ln ${highlighted + 1}, Col 12`, 196, statusY + 23);

        const pulse = 0.4 + Math.abs(Math.sin(time * 2.2)) * 0.6;
        context.beginPath();
        context.arc(logicalWidth - 96, statusY + 17, 4, 0, Math.PI * 2);
        context.fillStyle = `rgba(40, 200, 64, ${pulse})`;
        context.fill();
        context.fillStyle = '#8b8f9a';
        context.textAlign = 'right';
        context.fillText('live preview', logicalWidth - 24, statusY + 23);
        context.textAlign = 'left';

        const gloss = context.createLinearGradient(0, 0, logicalWidth, 300);
        gloss.addColorStop(0, 'rgba(255, 255, 255, 0.045)');
        gloss.addColorStop(0.5, 'rgba(255, 255, 255, 0.012)');
        gloss.addColorStop(1, 'rgba(255, 255, 255, 0)');
        context.fillStyle = gloss;
        context.fillRect(0, 56, logicalWidth, 300);

        texture.needsUpdate = true;
    };

    const update = (time, reducedMotion) => {
        if (reducedMotion) {
            if (! staticDrawn) {
                draw(time, 1);
                staticDrawn = true;
            }

            return;
        }

        if (startedAt.value === null) {
            startedAt.value = time;
        }

        if (time - lastFrame.value < 0.09) {
            return;
        }

        lastFrame.value = time;

        if (time > nextActiveAt.value) {
            activeLine.value = 9 + Math.floor(Math.random() * 5);
            nextActiveAt.value = time + 2.6;
        }

        const reveal = Math.min(1, Math.max(0, (time - startedAt.value) / 2.1));
        draw(time, reveal);
    };

    draw(0, 0);

    return {
        texture,
        update,
        dispose: () => texture.dispose(),
    };
}

function createMaterials() {
    return {
        body: new THREE.MeshPhysicalMaterial({
            color: 0xdfe3e8,
            metalness: 0.96,
            roughness: 0.22,
            envMapIntensity: 1.45,
            clearcoat: 1,
            clearcoatRoughness: 0.12,
        }),
        dark: new THREE.MeshStandardMaterial({
            color: 0x101114,
            metalness: 0.6,
            roughness: 0.42,
        }),
        key: new THREE.MeshStandardMaterial({
            color: 0x24262c,
            metalness: 0.55,
            roughness: 0.36,
        }),
        trackpad: new THREE.MeshStandardMaterial({
            color: 0x181a20,
            metalness: 0.7,
            roughness: 0.24,
        }),
        accent: new THREE.MeshStandardMaterial({
            color: 0xc8102e,
            emissive: 0xc8102e,
            emissiveIntensity: 0.35,
            metalness: 0.4,
            roughness: 0.3,
        }),
    };
}

function createContactShadow() {
    const canvas = document.createElement('canvas');
    canvas.width = 256;
    canvas.height = 256;
    const context = canvas.getContext('2d');
    const gradient = context.createRadialGradient(128, 128, 16, 128, 128, 126);
    gradient.addColorStop(0, 'rgba(0, 0, 0, 0.62)');
    gradient.addColorStop(0.55, 'rgba(0, 0, 0, 0.26)');
    gradient.addColorStop(1, 'rgba(0, 0, 0, 0)');
    context.fillStyle = gradient;
    context.fillRect(0, 0, 256, 256);

    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;

    const material = new THREE.MeshBasicMaterial({
        map: texture,
        transparent: true,
        depthWrite: false,
        opacity: 0.85,
    });

    const shadow = new THREE.Mesh(new THREE.PlaneGeometry(3.6, 2.6), material);
    shadow.rotation.x = -Math.PI / 2;
    shadow.position.y = -0.1;

    return { shadow, texture };
}

export function createChromeLaptop() {
    const isMobile = window.matchMedia('(max-width: 767px)').matches;
    const group = new THREE.Group();
    const model = new THREE.Group();
    const materials = createMaterials();
    const screen = createCodeTexture(isMobile);
    const { shadow, texture: shadowTexture } = createContactShadow();
    const geometries = [];

    const addMesh = (geometry, material) => {
        geometries.push(geometry);

        return new THREE.Mesh(geometry, material);
    };

    const base = addMesh(new RoundedBoxGeometry(2.7, 0.14, 1.85, 4, 0.035), materials.body);
    base.position.y = 0;
    model.add(base);

    const deck = addMesh(new RoundedBoxGeometry(2.34, 0.018, 1.08, 3, 0.012), materials.dark);
    deck.position.set(0, 0.08, 0.03);
    model.add(deck);

    const keyboardRows = 5;
    const keyboardColumns = 14;
    const keyGeometry = new RoundedBoxGeometry(0.118, 0.012, 0.098, 2, 0.012);
    geometries.push(keyGeometry);

    const keys = new THREE.InstancedMesh(keyGeometry, materials.key, keyboardRows * keyboardColumns);
    const matrix = new THREE.Matrix4();
    let keyIndex = 0;

    for (let row = 0; row < keyboardRows; row += 1) {
        for (let column = 0; column < keyboardColumns; column += 1) {
            matrix.makeTranslation(
                -0.945 + column * 0.145,
                0.092,
                -0.34 + row * 0.125,
            );
            keys.setMatrixAt(keyIndex, matrix);
            keyIndex += 1;
        }
    }

    keys.instanceMatrix.needsUpdate = true;
    model.add(keys);

    const trackpad = addMesh(new RoundedBoxGeometry(0.74, 0.012, 0.4, 3, 0.02), materials.trackpad);
    trackpad.position.set(0, 0.086, 0.63);
    model.add(trackpad);

    const hinge = addMesh(new THREE.CylinderGeometry(0.048, 0.048, 2.2, 24), materials.dark);
    hinge.rotation.z = Math.PI / 2;
    hinge.position.set(0, 0.075, -0.93);
    model.add(hinge);

    const lid = new THREE.Group();
    lid.position.set(0, 0.08, -0.92);
    lid.rotation.x = -0.2;

    const lidBody = addMesh(new RoundedBoxGeometry(2.7, 1.82, 0.1, 4, 0.035), materials.body);
    lidBody.position.y = 0.91;
    lid.add(lidBody);

    const bezel = addMesh(new RoundedBoxGeometry(2.46, 1.6, 0.022, 3, 0.025), materials.dark);
    bezel.position.set(0, 0.91, 0.056);
    lid.add(bezel);

    const screenGeometry = new THREE.PlaneGeometry(2.3, 1.42);
    geometries.push(screenGeometry);
    const screenMaterial = new THREE.MeshBasicMaterial({
        map: screen.texture,
        toneMapped: false,
    });
    const screenMesh = new THREE.Mesh(screenGeometry, screenMaterial);
    screenMesh.position.set(0, 0.91, 0.076);
    lid.add(screenMesh);

    const cameraDot = addMesh(new THREE.CircleGeometry(0.018, 16), materials.dark);
    cameraDot.position.set(0, 1.66, 0.08);
    lid.add(cameraDot);

    const logo = addMesh(new THREE.PlaneGeometry(0.16, 0.16), materials.accent);
    logo.position.set(0, 0.91, -0.055);
    logo.rotation.y = Math.PI;
    lid.add(logo);

    model.add(lid);
    model.add(shadow);
    model.position.y = -0.92;
    group.add(model);

    return {
        group,
        update: (time, reducedMotion) => screen.update(time, reducedMotion),
        dispose: () => {
            screen.dispose();
            shadowTexture.dispose();
            shadow.geometry.dispose();
            shadow.material.dispose();

            group.traverse((object) => {
                if (object.isMesh) {
                    object.geometry.dispose();
                    object.material.dispose();
                }
            });

            geometries.forEach((geometry) => geometry.dispose());
            Object.values(materials).forEach((material) => material.dispose());
            keys.dispose();
        },
    };
}
