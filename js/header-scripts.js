document.addEventListener('DOMContentLoaded', function() {

    // --- Mobile Menu Toggle ---
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-navigation');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('toggled');
            menuToggle.classList.toggle('is-active');

            // Toggle aria-expanded attribute for accessibility
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
        });
    }

    // --- Hide header on scroll down, reveal on scroll up ---
    const header = document.querySelector('#masthead.site-header');
    let lastY = window.scrollY;

    const onScrollDir = () => {
      const y = window.scrollY;
      // Hides after scrolling 120px down and only if moving down
      if (y > 120 && y > lastY) {
          header?.classList.add('hide');
      } else {
          header?.classList.remove('hide');
      }
      lastY = y;
    };

    if(header) {
        window.addEventListener('scroll', onScrollDir, { passive: true });
    }

    // --- Add accessibility attributes to menu items with dropdowns ---
    const menuItemsWithChildren = document.querySelectorAll('.menu-item-has-children');

    menuItemsWithChildren.forEach(item => {
        const link = item.querySelector('a');
        if (link) {
            link.setAttribute('aria-haspopup', 'true');
        }
    });

});