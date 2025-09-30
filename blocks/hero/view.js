(() => {
    // Configuration constants
    const CONFIG = {
        STAR_DENSITY: 2500,
        MAX_STARS: 600,
        MAX_SHOOTING_STARS: 3,
        STAR_SIZE_RANGE: { min: 0.5, max: 2.5 },
        STAR_VELOCITY_RANGE: { min: 0.05, max: 0.15 },
        TWINKLE_SPEED_RANGE: { min: 0.005, max: 0.02 },
        SHOOTING_STAR_LENGTH_RANGE: { min: 20, max: 80 },
        SHOOTING_STAR_SPEED_RANGE: { min: 6, max: 14 },
        SHOOTING_STAR_SIZE_RANGE: { min: 0.5, max: 2 },
    };

    const init = () => {
        const heroBlocks = document.querySelectorAll('.wp-block-mccullough-digital-hero');

        if (!heroBlocks.length) {
            return;
        }

        const createMotionPreferenceQuery = () => {
            if (typeof window.matchMedia !== 'function') {
                const noop = () => {};
                return {
                    matches: false,
                    addEventListener: noop,
                    removeEventListener: noop,
                    addListener: noop,
                    removeListener: noop,
                };
            }

            return window.matchMedia('(prefers-reduced-motion: reduce)');
        };

        const prefersReducedMotion = createMotionPreferenceQuery();
        const heroState = new Map();
        const magneticButtons = new Set();

        const createMagneticButton = (button) => {
            const restState = {
                x: 0,
                y: 0,
                scaleX: 1,
                scaleY: 1,
                glow: 0,
                press: 0,
                rotateX: 0,
                rotateY: 0,
            };

            const state = {
                element: button,
                prefersReducedMotion: false,
                pointerActive: false,
                coarsePointer: false,
                current: { ...restState },
                target: { ...restState },
                rafId: null,
                layers: {
                    surface: null,
                    ripples: null,
                },
            };

            const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

            const ensureLabelWrapper = () => {
                const existing = button.querySelector(':scope > .hero__cta-button-label');

                if (existing) {
                    return existing;
                }

                const legacy = button.querySelector(':scope > .btn-text');

                if (legacy) {
                    legacy.classList.add('hero__cta-button-label');
                    return legacy;
                }

                const label = document.createElement('span');
                label.className = 'hero__cta-button-label';

                const directTextNodes = Array.from(button.childNodes).filter((node) => node.nodeType === Node.TEXT_NODE);

                directTextNodes.forEach((node) => {
                    label.appendChild(node);
                });

                if (button.firstChild) {
                    button.insertBefore(label, button.firstChild);
                } else {
                    button.appendChild(label);
                }

                return label;
            };

            const ensureLayers = () => {
                const label = ensureLabelWrapper();

                if (!label) {
                    return;
                }

                let surface = button.querySelector(':scope > .hero__cta-button-surface');

                if (!surface) {
                    surface = document.createElement('span');
                    surface.className = 'hero__cta-button-surface';
                    surface.setAttribute('aria-hidden', 'true');
                    button.insertBefore(surface, label);
                }

                let core = surface.querySelector(':scope > .hero__cta-button-core');

                if (!core) {
                    core = document.createElement('span');
                    core.className = 'hero__cta-button-core';
                    core.setAttribute('aria-hidden', 'true');
                    surface.appendChild(core);
                }

                let sheen = surface.querySelector(':scope > .hero__cta-button-sheen');

                if (!sheen) {
                    sheen = document.createElement('span');
                    sheen.className = 'hero__cta-button-sheen';
                    sheen.setAttribute('aria-hidden', 'true');
                    surface.appendChild(sheen);
                }

                let scanline = surface.querySelector(':scope > .hero__cta-button-scanline');

                if (!scanline) {
                    scanline = document.createElement('span');
                    scanline.className = 'hero__cta-button-scanline';
                    scanline.setAttribute('aria-hidden', 'true');
                    surface.appendChild(scanline);
                }

                let orbiters = surface.querySelector(':scope > .hero__cta-button-orbiters');

                if (!orbiters) {
                    orbiters = document.createElement('span');
                    orbiters.className = 'hero__cta-button-orbiters';
                    orbiters.setAttribute('aria-hidden', 'true');
                    surface.appendChild(orbiters);
                }

                const desiredOrbiters = 4;
                const existingOrbiters = orbiters.querySelectorAll(':scope > .hero__cta-button-orb');

                if (existingOrbiters.length !== desiredOrbiters) {
                    existingOrbiters.forEach((node, index) => {
                        if (index >= desiredOrbiters) {
                            node.remove();
                        }
                    });

                    for (let index = existingOrbiters.length; index < desiredOrbiters; index += 1) {
                        const orb = document.createElement('span');
                        orb.className = 'hero__cta-button-orb';
                        orb.dataset.heroOrbIndex = String(index);
                        orbiters.appendChild(orb);
                    }
                }

                Array.from(orbiters.querySelectorAll(':scope > .hero__cta-button-orb')).forEach((orb, index) => {
                    orb.dataset.heroOrbIndex = String(index);
                });

                let ripples = button.querySelector(':scope > .hero__cta-button-ripples');

                if (!ripples) {
                    ripples = document.createElement('span');
                    ripples.className = 'hero__cta-button-ripples';
                    ripples.setAttribute('aria-hidden', 'true');
                    button.insertBefore(ripples, label);
                }

                button.appendChild(label);
                button.classList.add('hero__cta-button--enhanced');

                state.layers.surface = surface;
                state.layers.ripples = ripples;
            };

            ensureLayers();

            const applyStyles = () => {
                const { current } = state;

                button.style.setProperty('--hero-cta-translate-x', `${current.x.toFixed(3)}px`);
                button.style.setProperty('--hero-cta-translate-y', `${current.y.toFixed(3)}px`);
                button.style.setProperty('--hero-cta-scale-x', current.scaleX.toFixed(4));
                button.style.setProperty('--hero-cta-scale-y', current.scaleY.toFixed(4));
                button.style.setProperty('--hero-cta-glow', current.glow.toFixed(4));
                button.style.setProperty('--hero-cta-press', current.press.toFixed(4));
                button.style.setProperty('--hero-cta-rotate-x', `${current.rotateX.toFixed(3)}deg`);
                button.style.setProperty('--hero-cta-rotate-y', `${current.rotateY.toFixed(3)}deg`);
            };

            const cancelAnimation = () => {
                if (state.rafId) {
                    window.cancelAnimationFrame(state.rafId);
                    state.rafId = null;
                }
            };

            const step = () => {
                state.rafId = null;

                const { current, target } = state;
                let needsNextFrame = false;

                const easingActive = state.pointerActive ? 0.2 : 0.14;
                const easingScale = state.pointerActive ? 0.18 : 0.12;
                const easingGlow = state.pointerActive ? 0.16 : 0.1;
                const easingPress = 0.24;
                const easingRotate = state.pointerActive ? 0.18 : 0.12;

                const updateValue = (key, easing) => {
                    const difference = target[key] - current[key];

                    if (Math.abs(difference) > 0.001) {
                        current[key] += difference * easing;
                        needsNextFrame = true;
                    } else {
                        current[key] = target[key];
                    }
                };

                updateValue('x', easingActive);
                updateValue('y', easingActive);
                updateValue('scaleX', easingScale);
                updateValue('scaleY', easingScale);
                updateValue('glow', easingGlow);
                updateValue('press', easingPress);
                updateValue('rotateX', easingRotate);
                updateValue('rotateY', easingRotate);

                applyStyles();

                if (needsNextFrame) {
                    state.rafId = window.requestAnimationFrame(step);
                }
            };

            const requestFrame = () => {
                if (!state.rafId) {
                    state.rafId = window.requestAnimationFrame(step);
                }
            };

            const updateTarget = (values) => {
                let didUpdate = false;

                Object.keys(values).forEach((key) => {
                    if (Object.prototype.hasOwnProperty.call(state.target, key)) {
                        const nextValue = values[key];

                        if (state.target[key] !== nextValue) {
                            state.target[key] = nextValue;
                            didUpdate = true;
                        }
                    }
                });

                if (didUpdate) {
                    requestFrame();
                }
            };

            const reset = (immediate = false) => {
                state.pointerActive = false;
                state.coarsePointer = false;
                state.target = { ...restState };

                if (immediate) {
                    cancelAnimation();
                    state.current = { ...restState };
                    applyStyles();
                    return;
                }

                requestFrame();
            };

            const computeFromEvent = (event) => {
                const rect = button.getBoundingClientRect();
                const centerX = rect.left + rect.width / 2;
                const centerY = rect.top + rect.height / 2;

                const distanceX = event.clientX - centerX;
                const distanceY = event.clientY - centerY;
                const distance = Math.sqrt((distanceX * distanceX) + (distanceY * distanceY));

                const influenceRadius = Math.max(rect.width, rect.height) * 1.6;

                if (influenceRadius <= 0) {
                    updateTarget({
                        x: 0,
                        y: 0,
                        scaleX: 1,
                        scaleY: 1,
                        glow: 0,
                        rotateX: 0,
                        rotateY: 0,
                    });
                    return;
                }

                const falloff = clamp(1 - (distance / influenceRadius), 0, 1);

                if (falloff <= 0) {
                    updateTarget({
                        x: 0,
                        y: 0,
                        scaleX: 1,
                        scaleY: 1,
                        glow: 0,
                        rotateX: 0,
                        rotateY: 0,
                    });
                    return;
                }

                const influence = Math.pow(falloff, 1.1);
                const maxTranslation = Math.max(rect.width, rect.height) * 0.18;
                const translationStrength = 0.32;
                const rawX = clamp(distanceX * translationStrength, -maxTranslation, maxTranslation);
                const rawY = clamp(distanceY * translationStrength, -maxTranslation, maxTranslation);
                const targetX = rawX * influence;
                const targetY = rawY * influence;

                const angle = Math.atan2(distanceY, distanceX);
                const absCos = Math.abs(Math.cos(angle));
                const absSin = Math.abs(Math.sin(angle));
                const stretchBase = 0.05 + (influence * 0.09);
                const stretchX = stretchBase * absCos;
                const stretchY = stretchBase * absSin;
                const scaleX = clamp(1 + stretchX - stretchY * 0.35, 0.88, 1.18);
                const scaleY = clamp(1 + stretchY - stretchX * 0.35, 0.88, 1.18);

                const glow = clamp(0.2 + influence * 0.85, 0, 1);

                const normalizedX = clamp(distanceX / (rect.width / 2 || 1), -1, 1);
                const normalizedY = clamp(distanceY / (rect.height / 2 || 1), -1, 1);
                const rotationStrength = 14;
                const rotateX = clamp(-normalizedY * rotationStrength * influence, -rotationStrength, rotationStrength);
                const rotateY = clamp(normalizedX * rotationStrength * influence, -rotationStrength, rotationStrength);

                updateTarget({
                    x: targetX,
                    y: targetY,
                    scaleX,
                    scaleY,
                    glow,
                    rotateX,
                    rotateY,
                });
            };

            const handlePointerEnter = (event) => {
                state.coarsePointer = event.pointerType === 'touch';

                if (state.prefersReducedMotion || state.coarsePointer) {
                    reset();
                    return;
                }

                state.pointerActive = true;
                computeFromEvent(event);
            };

            const handlePointerMove = (event) => {
                if (state.prefersReducedMotion || state.coarsePointer) {
                    return;
                }

                state.pointerActive = true;
                computeFromEvent(event);
            };

            const handlePointerLeave = () => {
                reset();
            };

            const spawnRipple = (event) => {
                if (state.prefersReducedMotion || !state.layers.ripples) {
                    return;
                }

                if (event && 'isPrimary' in event && event.isPrimary === false) {
                    return;
                }

                const rect = button.getBoundingClientRect();
                const ripple = document.createElement('span');
                ripple.className = 'hero__cta-button-ripple';

                const x = event ? event.clientX - rect.left : rect.width / 2;
                const y = event ? event.clientY - rect.top : rect.height / 2;

                ripple.style.setProperty('--hero-cta-ripple-x', `${x}px`);
                ripple.style.setProperty('--hero-cta-ripple-y', `${y}px`);

                state.layers.ripples.appendChild(ripple);

                window.setTimeout(() => {
                    ripple.remove();
                }, 720);
            };

            const handlePointerDown = (event) => {
                if (event.pointerType === 'touch') {
                    state.coarsePointer = true;
                }

                spawnRipple(event);
                updateTarget({ press: 1 });
            };

            const handlePointerUp = () => {
                updateTarget({ press: 0 });
            };

            const handleBlur = () => {
                updateTarget({ press: 0 });
                reset();
            };

            const handleKeyDown = (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    updateTarget({ press: 1 });
                    spawnRipple(null);
                }
            };

            const handleKeyUp = (event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    updateTarget({ press: 0 });
                }
            };

            button.addEventListener('pointerenter', handlePointerEnter, { passive: true });
            button.addEventListener('pointermove', handlePointerMove, { passive: true });
            button.addEventListener('pointerleave', handlePointerLeave, { passive: true });
            button.addEventListener('pointerdown', handlePointerDown);
            button.addEventListener('pointerup', handlePointerUp);
            button.addEventListener('pointercancel', handlePointerUp);
            button.addEventListener('blur', handleBlur, true);
            button.addEventListener('keydown', handleKeyDown);
            button.addEventListener('keyup', handleKeyUp);

            applyStyles();

            const api = {
                element: button,
                setMotionPreference: (matches) => {
                    state.prefersReducedMotion = matches;

                    if (matches) {
                        if (state.layers.ripples) {
                            state.layers.ripples.innerHTML = '';
                        }
                        reset(true);
                    }
                },
                destroy: () => {
                    reset(true);
                    button.removeEventListener('pointerenter', handlePointerEnter);
                    button.removeEventListener('pointermove', handlePointerMove);
                    button.removeEventListener('pointerleave', handlePointerLeave);
                    button.removeEventListener('pointerdown', handlePointerDown);
                    button.removeEventListener('pointerup', handlePointerUp);
                    button.removeEventListener('pointercancel', handlePointerUp);
                    button.removeEventListener('blur', handleBlur, true);
                    button.removeEventListener('keydown', handleKeyDown);
                    button.removeEventListener('keyup', handleKeyUp);
                    magneticButtons.delete(api);
                    delete button.__heroMagneticInstance;
                },
                reset,
                updateTarget,
            };

            return api;
        };

        const setupMagneticButtons = (hero) => {
            const interactiveButtons = hero.querySelectorAll('.hero__cta-button:not(.is-static), .cta-button:not(.is-static)');

            interactiveButtons.forEach((button) => {
                if (button.__heroMagneticInstance) {
                    button.__heroMagneticInstance.setMotionPreference(prefersReducedMotion.matches);
                    magneticButtons.add(button.__heroMagneticInstance);
                    return;
                }

                const api = createMagneticButton(button);
                api.setMotionPreference(prefersReducedMotion.matches);
                button.__heroMagneticInstance = api;
                magneticButtons.add(api);
            });
        };

        const supportsResizeObserver = 'ResizeObserver' in window;
        const supportsIntersectionObserver = 'IntersectionObserver' in window;

        const createLetterSpans = (headline) => {
            if (!headline) {
                return;
            }

            let textWrapper = headline.querySelector('.hero__headline-text');

            if (!textWrapper) {
                textWrapper = document.createElement('span');
                textWrapper.classList.add('hero__headline-text');

                const children = Array.from(headline.childNodes);

                children.forEach((node) => {
                    if (node === textWrapper) {
                        return;
                    }

                    textWrapper.appendChild(node);
                });

                headline.appendChild(textWrapper);
            }

            const rawText = textWrapper.textContent ? textWrapper.textContent.trim() : '';

            if (!rawText) {
                return;
            }

            if (textWrapper.dataset.heroAnimated === 'true') {
                return;
            }

            textWrapper.dataset.heroAnimated = 'true';

            let srText = headline.querySelector('.hero__headline-text--sr');

            if (!srText) {
                srText = textWrapper.cloneNode(true);
                srText.classList.add('hero__headline-text--sr', 'screen-reader-text');
                srText.setAttribute('data-hero-sr-text', 'true');
                srText.setAttribute('aria-hidden', 'false');
                srText.setAttribute('role', 'text');

                srText.querySelectorAll('[id]').forEach((node) => {
                    node.removeAttribute('id');
                });

                headline.insertBefore(srText, textWrapper);
            }

            textWrapper.setAttribute('aria-hidden', 'true');
            textWrapper.classList.add('hero__headline-text--visual');

            if (
                typeof document.createTreeWalker !== 'function'
                || typeof window.NodeFilter === 'undefined'
            ) {
                return;
            }

            const walker = document.createTreeWalker(
                textWrapper,
                window.NodeFilter.SHOW_TEXT,
                null,
                false
            );
            const textNodes = [];

            while (walker.nextNode()) {
                textNodes.push(walker.currentNode);
            }

            textNodes.forEach((node) => {
                const text = node.textContent;

                if (!text) {
                    return;
                }

                const fragment = document.createDocumentFragment();
                let currentWord = null;

                const flushWord = () => {
                    if (currentWord && currentWord.childNodes.length) {
                        fragment.appendChild(currentWord);
                    }

                    currentWord = null;
                };

                const normalizedText = text.replace(/\r\n/g, '\n');

                for (const char of normalizedText) {
                    if (char === '\n' || char === '\r') {
                        flushWord();
                        fragment.appendChild(document.createElement('br'));
                        continue;
                    }

                    if (char.trim() === '') {
                        flushWord();
                        fragment.appendChild(document.createTextNode(char));
                        continue;
                    }

                    if (!currentWord) {
                        currentWord = document.createElement('span');
                        currentWord.classList.add('hero__headline-word');
                    }

                    const letter = document.createElement('span');

                    letter.classList.add('hero__headline-letter');
                    letter.dataset.char = char;
                    letter.textContent = char;
                    letter.setAttribute('aria-hidden', 'true');

                    currentWord.appendChild(letter);
                }

                flushWord();

                if (node.parentNode) {
                    node.parentNode.replaceChild(fragment, node);
                }
            });
        };

        const createStarField = (hero) => {
            const canvas = hero.querySelector('.hero__particle-canvas');

            if (!canvas) {
                if (console && console.warn) {
                    console.warn('Hero block: Canvas element not found for starfield');
                }
                return null;
            }

            const context = canvas.getContext('2d');

            if (!context) {
                if (console && console.error) {
                    console.error('Hero block: Failed to get 2D canvas context - canvas rendering not supported');
                }
                return null;
            }

            const state = {
                canvas,
                context,
                stars: [],
                shootingStars: [],
                frame: 0,
                rafId: null,
                resizeObserver: null,
                visibilityObserver: null,
                isVisible: true,
                resizeHandler: null,
            };

            const setCanvasSize = () => {
                const rect = hero.getBoundingClientRect();
                const width = Math.max(rect.width, 1);
                const height = Math.max(rect.height, 1);
                const dpr = window.devicePixelRatio || 1;

                canvas.width = width * dpr;
                canvas.height = height * dpr;
                canvas.style.width = `${width}px`;
                canvas.style.height = `${height}px`;

                context.setTransform(1, 0, 0, 1, 0, 0);
                context.scale(dpr, dpr);

                state.width = width;
                state.height = height;
            };

            const createStar = () => ({
                x: Math.random() * state.width,
                y: Math.random() * state.height,
                size: Math.random() * (CONFIG.STAR_SIZE_RANGE.max - CONFIG.STAR_SIZE_RANGE.min) + CONFIG.STAR_SIZE_RANGE.min,
                vy: Math.random() * (CONFIG.STAR_VELOCITY_RANGE.max - CONFIG.STAR_VELOCITY_RANGE.min) + CONFIG.STAR_VELOCITY_RANGE.min,
                twinkleSpeed: Math.random() * (CONFIG.TWINKLE_SPEED_RANGE.max - CONFIG.TWINKLE_SPEED_RANGE.min) + CONFIG.TWINKLE_SPEED_RANGE.min,
                twinkleOffset: Math.random() * 100,
            });

            const createShootingStar = () => ({
                x: Math.random() * state.width + 100,
                y: -(Math.random() * state.height * 0.5),
                len: Math.random() * (CONFIG.SHOOTING_STAR_LENGTH_RANGE.max - CONFIG.SHOOTING_STAR_LENGTH_RANGE.min) + CONFIG.SHOOTING_STAR_LENGTH_RANGE.min,
                speed: Math.random() * (CONFIG.SHOOTING_STAR_SPEED_RANGE.max - CONFIG.SHOOTING_STAR_SPEED_RANGE.min) + CONFIG.SHOOTING_STAR_SPEED_RANGE.min,
                size: Math.random() * (CONFIG.SHOOTING_STAR_SIZE_RANGE.max - CONFIG.SHOOTING_STAR_SIZE_RANGE.min) + CONFIG.SHOOTING_STAR_SIZE_RANGE.min,
            });

            const repopulate = () => {
                const starCount = Math.min(
                    CONFIG.MAX_STARS,
                    Math.max(50, Math.round((state.width * state.height) / CONFIG.STAR_DENSITY))
                );

                state.stars = Array.from({ length: starCount }, createStar);
                state.shootingStars = Array.from({ length: CONFIG.MAX_SHOOTING_STARS }, createShootingStar);
            };

            const drawStars = () => {
                state.stars.forEach((star) => {
                    star.y += star.vy;

                    if (star.y > state.height + star.size) {
                        star.y = -star.size;
                        star.x = Math.random() * state.width;
                    }

                    const opacity = Math.pow(
                        Math.abs(Math.sin(star.twinkleOffset + state.frame * star.twinkleSpeed)),
                        10
                    );

                    context.beginPath();
                    context.arc(star.x, star.y, star.size, 0, Math.PI * 2, false);
                    const color = Math.random() > 0.1
                        ? `rgba(0, 229, 255, ${opacity})`
                        : `rgba(255, 0, 224, ${opacity})`;
                    context.fillStyle = color;
                    context.fill();
                });
            };

            const drawShootingStars = () => {
                state.shootingStars.forEach((shootingStar, index) => {
                    shootingStar.x -= shootingStar.speed;
                    shootingStar.y += shootingStar.speed * 0.4;

                    if (
                        shootingStar.x < -shootingStar.len ||
                        shootingStar.y > state.height + shootingStar.len
                    ) {
                        state.shootingStars[index] = createShootingStar();
                    }

                    const grad = context.createLinearGradient(
                        shootingStar.x,
                        shootingStar.y,
                        shootingStar.x - shootingStar.len,
                        shootingStar.y + shootingStar.len * 0.4
                    );
                    grad.addColorStop(0, 'rgba(255, 255, 255, 0.8)');
                    grad.addColorStop(0.5, 'rgba(0, 229, 255, 0.6)');
                    grad.addColorStop(1, 'rgba(0, 229, 255, 0)');

                    context.strokeStyle = grad;
                    context.lineWidth = shootingStar.size;
                    context.lineCap = 'round';
                    context.beginPath();
                    context.moveTo(shootingStar.x, shootingStar.y);
                    context.lineTo(
                        shootingStar.x - shootingStar.len,
                        shootingStar.y + shootingStar.len * 0.4
                    );
                    context.stroke();
                });
            };

            const animate = () => {
                if (prefersReducedMotion.matches || !state.isVisible) {
                    state.rafId = null;
                    return;
                }

                state.rafId = window.requestAnimationFrame(animate);
                state.frame += 1;

                context.clearRect(0, 0, state.width, state.height);
                drawStars();
                drawShootingStars();
            };

            const start = () => {
                if (state.rafId || prefersReducedMotion.matches || !state.isVisible) {
                    return;
                }

                animate();
            };

            const stop = () => {
                if (state.rafId) {
                    window.cancelAnimationFrame(state.rafId);
                    state.rafId = null;
                }
            };

            const refresh = () => {
                stop();
                setCanvasSize();
                repopulate();
                start();
            };

            refresh();

            if (supportsResizeObserver) {
                state.resizeObserver = new ResizeObserver(refresh);
                state.resizeObserver.observe(hero);
            } else {
                state.resizeHandler = () => refresh();
                window.addEventListener('resize', state.resizeHandler, { passive: true });
            }

            if (supportsIntersectionObserver) {
                state.visibilityObserver = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        state.isVisible = entry.isIntersecting;

                        if (state.isVisible) {
                            start();
                        } else {
                            stop();
                        }
                    });
                });
                state.visibilityObserver.observe(hero);
            } else {
                state.isVisible = true;
                start();
            }

            return {
                start,
                stop,
                refresh,
                destroy: () => {
                    stop();

                    if (state.resizeObserver) {
                        state.resizeObserver.disconnect();
                    }

                    if (state.visibilityObserver) {
                        state.visibilityObserver.disconnect();
                    }

                    if (state.resizeHandler) {
                        window.removeEventListener('resize', state.resizeHandler);
                    }

                    context.clearRect(0, 0, state.width, state.height);
                },
            };
        };

        const updateMotionPreference = () => {
            heroState.forEach((instance) => {
                if (prefersReducedMotion.matches) {
                    instance.stop();
                } else {
                    instance.refresh();
                }
            });

            heroBlocks.forEach((hero) => {
                hero.classList.toggle('is-reduced-motion', prefersReducedMotion.matches);
            });

            magneticButtons.forEach((instance) => {
                instance.setMotionPreference(prefersReducedMotion.matches);
            });
        };

        heroBlocks.forEach((hero) => {
            setupMagneticButtons(hero);

            if (hero.dataset.heroInitialized === 'true') {
                return;
            }

            hero.dataset.heroInitialized = 'true';

            const headline = hero.querySelector('.hero__headline');

            if (headline) {
                createLetterSpans(headline);
            }

            const starField = createStarField(hero);

            if (starField) {
                heroState.set(hero, starField);
            }
        });

        updateMotionPreference();

        const motionListener = () => updateMotionPreference();
        const hasModernMotionListener = typeof prefersReducedMotion.addEventListener === 'function';
        const hasLegacyMotionListener = !hasModernMotionListener && typeof prefersReducedMotion.addListener === 'function';

        if (hasModernMotionListener) {
            prefersReducedMotion.addEventListener('change', motionListener);
        } else if (hasLegacyMotionListener) {
            prefersReducedMotion.addListener(motionListener);
        }

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                heroState.forEach((instance) => instance.stop());
                magneticButtons.forEach((instance) => instance.reset(true));
            } else {
                updateMotionPreference();
            }
        });

        window.addEventListener('unload', () => {
            heroState.forEach((instance) => instance.destroy());
            heroState.clear();

            magneticButtons.forEach((instance) => instance.destroy());
            magneticButtons.clear();

            if (hasModernMotionListener) {
                prefersReducedMotion.removeEventListener('change', motionListener);
            } else if (hasLegacyMotionListener) {
                prefersReducedMotion.removeListener(motionListener);
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            init();
        }, { once: true });
    } else {
        init();
    }
})();
