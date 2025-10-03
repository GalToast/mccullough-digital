(() => {
    // Configuration constants
    const CONFIG = {
        MAX_LOGO_ROTATE: 15, // Maximum rotation angle for 3D tilt effect
        HEADER_RESIZE_DEBOUNCE: 150, // Debounce delay for resize events (ms)
    };

    const init = () => {
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

        const handleMenuStateChangeCallbacks = [];

        const registerMenuStateCallback = (callback) => {
            if (typeof callback === 'function') {
                handleMenuStateChangeCallbacks.push(callback);
            }
        };

        const dispatchMenuState = (isOpen) => {
            handleMenuStateChangeCallbacks.forEach((callback) => callback(isOpen));
        };

        const initBlockMenu = (menuToggle, navBlock) => {
            if (!menuToggle || !navBlock) {
                if (console && console.warn) {
                    console.warn('Header: Menu toggle or navigation block not found');
                }
                return;
            }

            const syncToggleState = () => {
                const isOpen = navBlock.classList.contains('is-menu-open');
                menuToggle.classList.toggle('is-active', isOpen);
                
                // Add class to header for neon sweep effect
                if (header) {
                    header.classList.toggle('has-modal-open', isOpen);
                }
                
                dispatchMenuState(isOpen);
            };

            syncToggleState();

            const observer = new MutationObserver(syncToggleState);
            observer.observe(navBlock, { attributes: true, attributeFilter: ['class'] });

            navBlock.querySelectorAll('.wp-block-navigation-item a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (navBlock.classList.contains('is-menu-open')) {
                        const menuClose = navBlock.querySelector(
                            '.wp-block-navigation__responsive-container-close'
                        );
                        if (menuClose) {
                            menuClose.click();
                        }
                    }
                });
            });
        };

        const blockMenuToggle = document.querySelector(
            '.site-header .wp-block-navigation__responsive-container-open'
        );
        const navBlock = document.querySelector('.site-header .wp-block-navigation');

        // Initialize menu only if both elements exist
        if (blockMenuToggle && navBlock) {
            initBlockMenu(blockMenuToggle, navBlock);
        }

        const header = document.querySelector('#masthead.site-header');

        if (header) {
            let lastY = window.scrollY;
            let ticking = false;
            let headerHeight = header.offsetHeight;
            let resizeTimeout;
            let isMenuOpen = false;
            let isFocusWithin = false;
            let headerResizeObserver = null;
            let adminBarResizeObserver = null;
            let observedAdminBar = null;
            const rootElement = document.documentElement;
            const rootStyle = rootElement && rootElement.style;

            const setHeaderOffset = () => {
                headerHeight = header.offsetHeight;

                const adminBar = document.querySelector('#wpadminbar');
                const adminBarHeight = adminBar ? adminBar.offsetHeight : 0;

                if (rootStyle) {
                    const roundedHeight = Math.max(Math.round(headerHeight), 0);
                    const roundedAdminHeight = Math.max(Math.round(adminBarHeight), 0);
                    rootStyle.setProperty('--mcd-header-offset', `${roundedHeight}px`);
                    rootStyle.setProperty('--mcd-admin-bar-offset', `${roundedAdminHeight}px`);
                }
            };

            const syncAdminBarObserver = () => {
                if (typeof window.ResizeObserver !== 'function') {
                    return;
                }

                const adminBar = document.querySelector('#wpadminbar');

                if (!adminBarResizeObserver) {
                    adminBarResizeObserver = new ResizeObserver(() => {
                        setHeaderOffset();
                    });
                }

                if (adminBar === observedAdminBar) {
                    return;
                }

                if (observedAdminBar) {
                    adminBarResizeObserver.unobserve(observedAdminBar);
                }

                if (adminBar) {
                    adminBarResizeObserver.observe(adminBar);
                }

                observedAdminBar = adminBar || null;
            };

            // Register callback only after navBlock has been checked
            if (navBlock) {
                registerMenuStateCallback((open) => {
                    isMenuOpen = open;
                    if (isMenuOpen) {
                        header.classList.remove('hide');
                    }
                    setHeaderOffset();
                    syncAdminBarObserver();
                });

                // Dispatch initial state
                dispatchMenuState(navBlock.classList.contains('is-menu-open'));
            }

            setHeaderOffset();
            syncAdminBarObserver();
            window.addEventListener('load', () => {
                setHeaderOffset();
                syncAdminBarObserver();
            });

            const updateHeaderVisibility = () => {
                const y = window.scrollY;

                if (prefersReducedMotion.matches || isMenuOpen || isFocusWithin) {
                    header.classList.remove('hide');
                    lastY = y;
                    ticking = false;
                    return;
                }

                if (y > headerHeight && y > lastY) {
                    header.classList.add('hide');
                } else if (y < lastY) {
                    header.classList.remove('hide');
                }

                lastY = y;
                ticking = false;
            };

            const refreshHeaderMetrics = () => {
                setHeaderOffset();
                syncAdminBarObserver();
                updateHeaderVisibility();
            };

            const handleScroll = () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateHeaderVisibility);
                    ticking = true;
                }
            };

            const handleResize = () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    refreshHeaderMetrics();
                }, CONFIG.HEADER_RESIZE_DEBOUNCE);
            };

            window.addEventListener('scroll', handleScroll, { passive: true });
            window.addEventListener('resize', handleResize, { passive: true });

            if (typeof window.ResizeObserver === 'function') {
                headerResizeObserver = new ResizeObserver(() => {
                    setHeaderOffset();
                    syncAdminBarObserver();
                });
                headerResizeObserver.observe(header);
            }

            if (document.fonts && typeof document.fonts.addEventListener === 'function') {
                document.fonts.addEventListener('loadingdone', refreshHeaderMetrics);
            }

            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    refreshHeaderMetrics();
                }
            });

            window.addEventListener('unload', () => {
                if (headerResizeObserver) {
                    headerResizeObserver.disconnect();
                }

                if (adminBarResizeObserver) {
                    adminBarResizeObserver.disconnect();
                }

                if (document.fonts && typeof document.fonts.removeEventListener === 'function') {
                    document.fonts.removeEventListener('loadingdone', refreshHeaderMetrics);
                }
            });

            header.addEventListener('focusin', () => {
                isFocusWithin = true;
                header.classList.remove('hide');
            });

            header.addEventListener('focusout', (event) => {
                if (!header.contains(event.relatedTarget)) {
                    isFocusWithin = false;
                    updateHeaderVisibility();
                }
            });

            const onMotionPreferenceChange = () => {
                if (prefersReducedMotion.matches) {
                    header.classList.remove('hide');
                } else {
                    updateHeaderVisibility();
                }
            };

            if (typeof prefersReducedMotion.addEventListener === 'function') {
                prefersReducedMotion.addEventListener('change', onMotionPreferenceChange);
            } else if (typeof prefersReducedMotion.addListener === 'function') {
                prefersReducedMotion.addListener(onMotionPreferenceChange);
            }

            updateHeaderVisibility();
        }

        // Logo 3D tilt effect
        const logoContainer = document.querySelector('.site-branding');
        if (logoContainer) {
            const logoLink = logoContainer.querySelector('.custom-logo-link');
            if (logoLink) {
                let animationFrameId = null;

                const shouldAnimate = () => !prefersReducedMotion.matches;

                const resetLogo = () => {
                    logoLink.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
                };

                logoContainer.addEventListener('mousemove', (e) => {
                    if (!shouldAnimate()) {
                        resetLogo();
                        return;
                    }

                    cancelAnimationFrame(animationFrameId);
                    animationFrameId = requestAnimationFrame(() => {
                        const rect = logoContainer.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        const { width, height } = rect;
                        const rotateY = CONFIG.MAX_LOGO_ROTATE * ((x - width / 2) / (width / 2));
                        const rotateX = -CONFIG.MAX_LOGO_ROTATE * ((y - height / 2) / (height / 2));
                        logoLink.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
                    });
                });

                logoContainer.addEventListener('mouseleave', () => {
                    cancelAnimationFrame(animationFrameId);
                    animationFrameId = requestAnimationFrame(() => {
                        resetLogo();
                    });
                });

                const handleMotionPreferenceChange = () => {
                    if (!shouldAnimate()) {
                        resetLogo();
                    }
                };

                if (typeof prefersReducedMotion.addEventListener === 'function') {
                    prefersReducedMotion.addEventListener('change', handleMotionPreferenceChange);
                } else if (typeof prefersReducedMotion.addListener === 'function') {
                    prefersReducedMotion.addListener(handleMotionPreferenceChange);
                }
            }
        }

        const initBlogHeroGlitch = () => {
            const blogHero = document.querySelector('.blog-hero');

            if (!blogHero) {
                return;
            }

            const title = blogHero.querySelector('.blog-hero__title');

            if (!title) {
                return;
            }

            if (title.dataset.blogHeroGlitchInitialized === 'true') {
                return;
            }

            title.dataset.blogHeroGlitchInitialized = 'true';

            const prepareHeadline = () => {
                let textWrapper = title.querySelector('.blog-hero__title-text');

                if (!textWrapper) {
                    textWrapper = document.createElement('span');
                    textWrapper.classList.add('blog-hero__title-text');

                    const children = Array.from(title.childNodes);

                    children.forEach((node) => {
                        if (node === textWrapper) {
                            return;
                        }

                        textWrapper.appendChild(node);
                    });

                    title.appendChild(textWrapper);
                }

                const rawText = textWrapper.textContent ? textWrapper.textContent.trim() : '';

                if (!rawText || textWrapper.dataset.blogHeroTitleAnimated === 'true') {
                    return;
                }

                textWrapper.dataset.blogHeroTitleAnimated = 'true';

                let srText = title.querySelector('.blog-hero__title-text--sr');

                if (!srText) {
                    srText = textWrapper.cloneNode(true);
                    srText.classList.add('blog-hero__title-text--sr', 'screen-reader-text');
                    srText.setAttribute('data-blog-hero-sr-text', 'true');
                    srText.setAttribute('aria-hidden', 'false');
                    srText.setAttribute('role', 'text');

                    srText.querySelectorAll('[id]').forEach((node) => {
                        node.removeAttribute('id');
                    });

                    title.insertBefore(srText, textWrapper);
                }

                textWrapper.setAttribute('aria-hidden', 'true');
                textWrapper.classList.add('blog-hero__title-text--visual');

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
                            currentWord.classList.add('blog-hero__word');
                        }

                        const letter = document.createElement('span');
                        letter.classList.add('blog-hero__letter');
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

            prepareHeadline();

            const applyMotionPreference = () => {
                blogHero.classList.toggle('is-reduced-motion', prefersReducedMotion.matches);
            };

            applyMotionPreference();

            const motionListener = () => {
                applyMotionPreference();
            };

            const hasModernMotionListener = typeof prefersReducedMotion.addEventListener === 'function';
            const hasLegacyMotionListener = !hasModernMotionListener && typeof prefersReducedMotion.addListener === 'function';

            if (hasModernMotionListener) {
                prefersReducedMotion.addEventListener('change', motionListener);
            } else if (hasLegacyMotionListener) {
                prefersReducedMotion.addListener(motionListener);
            }

            window.addEventListener('unload', () => {
                if (hasModernMotionListener) {
                    prefersReducedMotion.removeEventListener('change', motionListener);
                } else if (hasLegacyMotionListener) {
                    prefersReducedMotion.removeListener(motionListener);
                }
            }, { once: true });
        };

        initBlogHeroGlitch();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
