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

    // --- Mobile Sub-menu Toggle ---
    const menuItemsWithChildren = document.querySelectorAll('.menu-item-has-children');
    const menuToggle = document.querySelector('.menu-toggle'); // Used to check for mobile view

    menuItemsWithChildren.forEach(item => {
        // Create and insert the toggle button
        const toggleButton = document.createElement('button');
        toggleButton.classList.add('submenu-toggle');
        toggleButton.setAttribute('aria-expanded', 'false');
        toggleButton.setAttribute('aria-label', 'Toggle submenu');
        item.appendChild(toggleButton);

        // Add event listener to the new button
        toggleButton.addEventListener('click', (e) => {
            // Only run this logic if the mobile menu button is visible
            if (getComputedStyle(menuToggle).display !== 'none') {
                e.preventDefault(); // Prevent the parent link from navigating

                // Toggle the class on the parent <li>
                item.classList.toggle('submenu-open');

                // Update aria-expanded attribute
                const isExpanded = item.classList.contains('submenu-open');
                toggleButton.setAttribute('aria-expanded', isExpanded);
            }
        });

        // Also add accessibility attributes to the main link
        const link = item.querySelector('a');
        if (link) {
            link.setAttribute('aria-haspopup', 'true');
        }
    });
});