document.addEventListener('DOMContentLoaded', function() {
    // --- Mobile Menu Toggle ---
    const menuToggle = document.querySelector('.wp-block-navigation__responsive-container-open');
    const mainNav = document.querySelector('.wp-block-navigation__responsive-container');
    const navBlock = document.querySelector('.wp-block-navigation');

    if (menuToggle && mainNav && navBlock) {
        menuToggle.addEventListener('click', function() {
            // The core block adds 'is-menu-open' to the main nav block
            // We just need to toggle our animation class on the button
            menuToggle.classList.toggle('is-active');
        });
    }

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

    // --- Close mobile menu when a link is clicked ---
    const menuLinks = document.querySelectorAll('.wp-block-navigation-item a');
    menuLinks.forEach(link => {
        link.addEventListener('click', () => {
            // Check if the mobile menu is open
            if (navBlock.classList.contains('is-menu-open')) {
                // We need to simulate a click on the close button
                const menuClose = document.querySelector('.wp-block-navigation__responsive-container-close');
                if(menuClose) {
                    menuClose.click();
                }
                // Also remove our animation class from the toggle
                if(menuToggle) {
                    menuToggle.classList.remove('is-active');
                }
            }
        });
    });

    // --- 3D Logo Tilt Effect ---
    const logoContainer = document.querySelector('.site-branding');
    if (logoContainer) {
        const logoLink = logoContainer.querySelector('.custom-logo-link');
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
});