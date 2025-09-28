# Agent Notes (2025-09-27)

This repository contains the McCullough Digital block theme. The notes below summarise the current workflow and the defects resolved during the latest sweep.

## Build & QA Checklist
1. Run `npm install` to install block tooling.
2. Use `npm run start` while developing blocks so assets rebuild automatically.
3. Finish with `npm run build` before committing production bundles.
4. **Always** update `AGENTS.md`, `bug-report.md`, and `readme.txt` to reflect any bug fixes or improvements.

## Bug Fix & Improvement Highlights
- **Functionality:**
    - Corrected placeholder links in the `home-landing.php` pattern that were pointing to example.com.
    - Fixed social media icon logic in `functions.php` to correctly identify all variations of `x.com` and other social URLs by using proper regex.
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

Keep documentation (this file, `readme.txt`, and `bug-report.md`) in sync with any future bug sweeps so downstream contributors understand the latest fixes.