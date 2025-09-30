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
            const rootElement = document.documentElement;
            const rootStyle = rootElement && rootElement.style;

            const setHeaderOffset = () => {
                headerHeight = header.offsetHeight;

                if (rootStyle) {
                    const roundedHeight = Math.max(Math.round(headerHeight), 0);
                    rootStyle.setProperty('--mcd-header-offset', `${roundedHeight}px`);
                }
            };

            // Register callback only after navBlock has been checked
            if (navBlock) {
                registerMenuStateCallback((open) => {
                    isMenuOpen = open;
                    if (isMenuOpen) {
                        header.classList.remove('hide');
                    }
                    setHeaderOffset();
                });

                // Dispatch initial state
                dispatchMenuState(navBlock.classList.contains('is-menu-open'));
            }

            setHeaderOffset();
            window.addEventListener('load', setHeaderOffset);

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
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
})();
