# Agent Notes (2025-09-28)

This repository contains the McCullough Digital block theme. The notes below summarise the current workflow and the defects resolved during the latest sweep.

## Build & QA Checklist
1. Run `npm install` to install block tooling.
2. Use `npm run start` while developing blocks so assets rebuild automatically.
3. Finish with `npm run build` before committing production bundles.
4. **Always** update `AGENTS.md`, `bug-report.md`, and `readme.txt` to reflect any bug fixes or improvements.

## Bug Fix & Improvement Highlights
- **Latest (2025-09-28):**
    - Defined the missing `--z-index-background` and `--z-index-content` variables and switched lingering header colours to CSS tokens so palette changes propagate everywhere.
    - Corrected the navigation block alignment setting and scoped hero block styles to avoid leaking `.hero` rules across the site.
    - Hardened all CTA links by escaping URLs and suppressing empty hero elements while restoring wrapper alignment support.
    - Removed duplicate IDs from the hero, CTA, about, and services editors, added the missing services block import, and ensured the dynamic block no longer saves rendered markup.
    - Disabled the service card glow animation when `prefers-reduced-motion` is active and allowed the SVG sanitizer to retain safe `style` attributes.
    - Updated footer social icon selectors so WordPress' `.wp-social-link` markup inherits the intended hover styling.
- **Functionality:**
    - Corrected placeholder links in the `home-landing.php` pattern that were pointing to example.com.
    - Fixed social media icon logic in `functions.php` to correctly identify all variations of `x.com` and other social URLs by using proper regex.
    - Seed the Home landing layout into the "Home" page content on activation so editors can manage it directly from the page editor while keeping the dedicated template optional.
- **Visual & Performance:**
    - Improved the footer's starfield animation in `style.css` to use `background-position` for better performance.
    - Refined the main navigation hover animation in `style.css` to be a smoother, more professional `text-shadow` effect.
    - Fixed a layout shift on post cards in `style.css` by changing the hover transition to only affect `transform` and `border-color`.
    - Corrected the mobile hamburger menu animation in `style.css` for a smoother transition.
- **Code Quality & Extensibility:**
    - Made the `mcd_get_social_link_svg()` function in `functions.php` extensible with a filter, allowing new social icons to be added easily.
    - Hardened the theme activation logic in `functions.php` to prevent the creation of duplicate "Home" pages.
    - Fixed the over-aggressive SVG sanitizer in `functions.php` to no longer strip out necessary `style` attributes.
    - Replaced hardcoded `z-index` values and colors in `style.css` with CSS variables for better maintainability.

Keep documentation (this file, `readme.txt`, and `bug-report.md`) in sync with every bug sweep so downstream contributors understand the latest fixes.
