(() => {
    const init = () => {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

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

            registerMenuStateCallback((open) => {
                isMenuOpen = open;
                if (isMenuOpen) {
                    header.classList.remove('hide');
                }
            });

            dispatchMenuState(navBlock ? navBlock.classList.contains('is-menu-open') : false);

            const updateHeaderVisibility = () => {
                const y = window.scrollY;

                if (prefersReducedMotion.matches || isMenuOpen) {
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

            const handleScroll = () => {
                if (!ticking) {
                    window.requestAnimationFrame(updateHeaderVisibility);
                    ticking = true;
                }
            };

            const handleResize = () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    headerHeight = header.offsetHeight;
                    updateHeaderVisibility();
                }, 150);
            };

            window.addEventListener('scroll', handleScroll, { passive: true });
            window.addEventListener('resize', handleResize, { passive: true });

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

        const logoContainer = document.querySelector('.site-branding');
        if (logoContainer) {
            const logoLink = logoContainer.querySelector('.custom-logo-link');
            if (logoLink) {
                const maxRotate = 15;
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
                        const rotateY = maxRotate * ((x - width / 2) / (width / 2));
                        const rotateX = -maxRotate * ((y - height / 2) / (height / 2));
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
