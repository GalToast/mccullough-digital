# Agent Instructions & Bug Sweep Log

This document outlines the process for working with the McCullough Digital theme and logs the findings of a bug sweep.

## Development Process

1.  **Dependency Installation**: Run `npm install` to set up the build toolchain.
2.  **Development**: Use `npm run start` to watch for changes and automatically rebuild assets.
3.  **Production Build**: Use `npm run build` to create optimized, production-ready assets.

## Bug Sweep Findings (2025-09-27)

I have investigated 10 potential issues. The final list of confirmed bugs and code quality issues is below.

### Confirmed Bugs & Code Quality Issues

-   **[BUG] Social Icon Domain Matching is Fragile:**
    -   **File:** `functions.php`
    -   **Function:** `mcd_get_social_link_svg()`
    -   **Issue:** The logic to determine the domain (`implode('.', array_slice(explode('.', $host), -2))`) is not robust. It incorrectly processes domains like `twitter.co.uk` as `co.uk`, failing to find the correct icon.
    -   **Impact:** Social icons will not appear for websites with complex top-level domains.

-   **[BUG] Header Visibility Breaks on Window Resize:**
    -   **File:** `js/header-scripts.js`
    -   **Issue:** The `headerHeight` variable is calculated only once on page load. If the browser window is resized, this height can change, but the script continues to use the old, incorrect value for its hide/show calculations.
    -   **Impact:** The header hides at the wrong time or not at all after the browser is resized.

-   **[BUG] Block-based Mobile Menu Icon State is Unreliable:**
    -   **File:** `js/header-scripts.js`
    -   **Function:** `initBlockMenu()`
    -   **Issue:** The `is-active` class on the hamburger toggle button is only changed on click. It is not synchronized with the actual menu's open/closed state (`is-menu-open` on the parent block). If the menu is closed by other means (e.g., clicking a link), the icon does not revert to its "hamburger" state.
    -   **Impact:** A minor visual glitch where the user sees a "close" icon when the menu is already closed.

-   **[QUALITY] Insufficient SVG Sanitization:**
    -   **File:** `functions.php`
    -   **Function:** `mcd_sanitize_svg()`
    -   **Issue:** The sanitization logic is very basic, only removing `<script>` tags and `on*` attributes. It does not protect against more advanced XSS vectors within SVGs.
    -   **Impact:** Potential security vulnerability if users with upload permissions were to upload a malicious SVG file.

-   **[QUALITY] `overflow-x: hidden` Masks Layout Issues:**
    -   **File:** `style.css`
    -   **Selector:** `body`
    -   **Issue:** Applying `overflow-x: hidden` to the body prevents horizontal scrollbars, but it does so by hiding the problem, not fixing the root cause. There is likely an element somewhere that is wider than the viewport.
    -   **Impact:** Can make the site difficult to debug and may cause content to be clipped on certain screen sizes.

-   **[QUALITY] Hardcoded SVGs in CSS:**
    -   **File:** `style.css`
    -   **Selectors:** `.stars`, `.stars2`, `.stars3`
    -   **Issue:** The starfield background in the footer uses large, hardcoded data-URI SVGs. This increases the stylesheet size and makes the assets impossible to cache separately and difficult to maintain.
    -   **Impact:** Slower page load and poor maintainability.

### Issues Investigated and Confirmed as NOT Bugs

-   **Social Icon Fallback:** The `Mcd_Social_Nav_Menu_Walker` correctly checks if the SVG is empty and displays the text title as a fallback.
-   **Logo Animation Memory Leak:** The `requestAnimationFrame` calls in `js/header-scripts.js` are correctly preceded by `cancelAnimationFrame`, preventing memory issues.
-   **Social Link Accessibility:** The social menu walker correctly adds a `.screen-reader-text` span, making the links accessible to screen readers. This is a standard WordPress pattern.
-   **Classic Menu JS Errors:** The `initClassicMenu` function in JavaScript correctly checks if the `#primary-menu` element exists before attempting to attach event listeners to its children, thus avoiding errors.