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
        };

        heroBlocks.forEach((hero) => {
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
            } else {
                updateMotionPreference();
            }
        });

        window.addEventListener('unload', () => {
            heroState.forEach((instance) => instance.destroy());
            heroState.clear();

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
