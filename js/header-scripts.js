document.addEventListener('DOMContentLoaded', function() {
    // --- Mobile Menu Toggle (classic header markup) ---
    const initClassicMenu = () => {
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.querySelector('#site-navigation');
        const primaryMenu = document.getElementById('primary-menu');

        if (!menuToggle || !mainNav) {
            return;
        }

        const setMenuState = (isOpen) => {
            menuToggle.classList.toggle('is-active', isOpen);
            menuToggle.setAttribute('aria-expanded', isOpen.toString());
            mainNav.classList.toggle('toggled', isOpen);
        };

        setMenuState(false);

        menuToggle.addEventListener('click', () => {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            setMenuState(!isExpanded);
        });

        if (primaryMenu) {
            primaryMenu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (mainNav.classList.contains('toggled')) {
                        setMenuState(false);
                    }
                });
            });
        }
    };

    // --- Mobile Menu Toggle (navigation block) ---
    const initBlockMenu = () => {
        const menuToggle = document.querySelector('.wp-block-navigation__responsive-container-open');
        const navBlock = document.querySelector('.wp-block-navigation');

        if (!menuToggle || !navBlock) {
            return;
        }

        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('is-active');
        });

        const syncToggleState = () => {
            if (!navBlock.classList.contains('is-menu-open')) {
                menuToggle.classList.remove('is-active');
            }
        };

        const observer = new MutationObserver(syncToggleState);
        observer.observe(navBlock, { attributes: true, attributeFilter: ['class'] });

        navBlock.querySelectorAll('.wp-block-navigation-item a').forEach((link) => {
            link.addEventListener('click', () => {
                if (navBlock.classList.contains('is-menu-open')) {
                    const menuClose = navBlock.querySelector('.wp-block-navigation__responsive-container-close');
                    if (menuClose) {
                        menuClose.click();
                    }
                    menuToggle.classList.remove('is-active');
                }
            });
        });
    };

    initClassicMenu();
    initBlockMenu();

    // --- Hide header on scroll down, reveal on scroll up ---
    const header = document.querySelector('#masthead.site-header');
    let lastY = window.scrollY;

    if (header) {
        window.addEventListener('scroll', function() {
            const y = window.scrollY;
            if (y > 120 && y > lastY) {
                header.classList.add('hide');
            } else {
                header.classList.remove('hide');
            }
            lastY = y;
        }, { passive: true });
    }

    // --- 3D Logo Tilt Effect ---
    const logoContainer = document.querySelector('.site-branding');
    if (logoContainer) {
        const logoLink = logoContainer.querySelector('.custom-logo-link');
        if (logoLink) {
            const maxRotate = 15; // Max rotation in degrees

            logoContainer.addEventListener('mousemove', (e) => {
                const rect = logoContainer.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const { width, height } = rect;

                const rotateY = maxRotate * ((x - width / 2) / (width / 2));
                const rotateX = -maxRotate * ((y - height / 2) / (height / 2));

                logoLink.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });

            logoContainer.addEventListener('mouseleave', () => {
                logoLink.style.transform = 'rotateX(0deg) rotateY(0deg)';
            });
        }
    }
});
