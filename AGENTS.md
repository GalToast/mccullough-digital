# Agent Instructions & Bug Sweep Log

This document outlines the process for working with the McCullough Digital theme and logs the findings of a bug sweep.

## Development Process

1.  **Dependency Installation**: Run `npm install` to set up the build toolchain.
2.  **Development**: Use `npm run start` to watch for changes and automatically rebuild assets.
3.  **Production Build**: Use `npm run build` to create optimized, production-ready assets.

## Bug Sweep Findings (2025-09-27)

Ten issues were confirmed during the latest review. The high-level summary is below.

### Confirmed Bugs
- **[BUG] Header offset failed to update after layout changes:** `style.css`, `js/header-scripts.js` — content slipped under the fixed header when its height changed.
- **[BUG] Header script assumed `window.matchMedia`:** `js/header-scripts.js` — missing guard caused runtime crashes in older browsers.
- **[BUG] Header hid during keyboard navigation:** `js/header-scripts.js` — the hide-on-scroll behaviour ignored focus state, hiding the menu while tabbing.
- **[BUG] Hero animation assumed `window.matchMedia`:** `blocks/hero/view.js` — the hero script aborted on browsers without the API.
- **[BUG] Hero headline animation harmed accessibility:** `blocks/hero/view.js`, `blocks/hero/render.php` — per-letter spans broke screen-reader output and the canvas lacked a presentational role.
- **[BUG] SVG sanitiser stripped legitimate gradients:** `functions.php` — gradients, symbols, and `<use>` references were removed, leaving blank icons.
- **[BUG] Service card block emitted inaccessible markup:** `blocks/service-card/render.php` — decorative icons were announced and empty links were rendered.
- **[BUG] CTA block rendered empty buttons:** `blocks/cta/render.php` — buttons appeared even when the label was blank.
- **[BUG] Standalone preview shipped malformed font hints:** `standalone.html` — incorrect `<link>` tags prevented font preloading and relied on `overflow-x: hidden` to mask layout issues.

### Code Quality & Performance Issues
- **[QUALITY] Services block re-read metadata unnecessarily:** `blocks/services/render.php` — decoding `block.json` on every render added avoidable I/O and complexity.

### Issues Investigated and Confirmed as NOT Bugs

- **Social Icon Fallback:** The `Mcd_Social_Nav_Menu_Walker` correctly checks if the SVG is empty and displays the text title as a fallback.
- **Logo Animation Memory Leak:** The `requestAnimationFrame` calls in `js/header-scripts.js` are correctly preceded by `cancelAnimationFrame`, preventing memory issues.
- **Social Link Accessibility:** The social menu walker correctly adds a `.screen-reader-text` span, making the links accessible to screen readers. This is a standard WordPress pattern.
- **Classic Menu JS Errors:** The `initClassicMenu` function in JavaScript correctly checks if the `#primary-menu` element exists before attempting to attach event listeners to its children, thus avoiding errors.
