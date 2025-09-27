document.addEventListener('DOMContentLoaded', function() {
    // --- Mobile Menu Toggle (navigation block) ---
    const initBlockMenu = (menuToggle, navBlock) => {
        // This MutationObserver keeps the toggle button's state (`is-active`)
        // perfectly in sync with the navigation block's state (`is-menu-open`).
        const syncToggleState = () => {
            const isOpen = navBlock.classList.contains('is-menu-open');
            menuToggle.classList.toggle('is-active', isOpen);
        };

        // Run the sync function once on initialization to set the correct initial state.
        syncToggleState();

        const observer = new MutationObserver(syncToggleState);
        observer.observe(navBlock, { attributes: true, attributeFilter: ['class'] });

        // This ensures that when a user clicks a link in the mobile menu,
        // the menu closes automatically. The MutationObserver will then handle the icon state.
        navBlock.querySelectorAll('.wp-block-navigation-item a').forEach((link) => {
            link.addEventListener('click', () => {
                if (navBlock.classList.contains('is-menu-open')) {
                    const menuClose = navBlock.querySelector('.wp-block-navigation__responsive-container-close');
                    if (menuClose) {
                        menuClose.click();
                    }
                }
            });
        });
    };

    // Initialize block menu if it exists
    const blockMenuToggle = document.querySelector('.wp-block-navigation__responsive-container-open');
    const navBlock = document.querySelector('.wp-block-navigation');
    if (blockMenuToggle && navBlock) {
        initBlockMenu(blockMenuToggle, navBlock);
    }

    // --- Hide header on scroll down, reveal on scroll up ---
    const header = document.querySelector('#masthead.site-header');
    if (header) {
        let lastY = window.scrollY;
        let ticking = false;
        let headerHeight = header.offsetHeight;
        let resizeTimeout;

        const updateHeaderVisibility = () => {
            const y = window.scrollY;
            // Use the dynamically updated headerHeight
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
            }, 150); // Debounce resize event
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
        window.addEventListener('resize', handleResize, { passive: true });
    }

    // --- 3D Logo Tilt Effect ---
    const logoContainer = document.querySelector('.site-branding');
    if (logoContainer) {
        const logoLink = logoContainer.querySelector('.custom-logo-link');
        if (logoLink) {
            const maxRotate = 15; // Max rotation in degrees
            let animationFrameId = null;

            logoContainer.addEventListener('mousemove', (e) => {
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
                    logoLink.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
                });
            });
        }
    }
});
