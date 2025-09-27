# Agent Notes (2025-09-28)

This repository contains the McCullough Digital block theme. The notes below summarise the current workflow and the defects resolved during the latest sweep.

## Build & QA Checklist
1. Run `npm install` to install block tooling.
2. Use `npm run start` while developing blocks so assets rebuild automatically.
3. Finish with `npm run build` before committing production bundles.

## Bug Fix Highlights
- Header behaviour (`js/header-scripts.js`, `style.css`) now uses a `ResizeObserver`, font loading events, and bfcache restores to keep `--mcd-header-offset` in sync with the masthead height.
- The same header script guards reduced-motion checks against missing `matchMedia`, keeps focus-visible navigation expanded, and disconnects observers during unload.
- Hero markup and scripts (`blocks/hero/render.php`, `blocks/hero/view.js`, `blocks/hero/style.css`) preserve screen-reader content, build animations from `.hero__headline-text`, and skip span generation when legacy DOM APIs are unavailable.
- Shared `.screen-reader-text` utilities (`style.css`, `standalone.html`) support accessible duplicates across blocks.
- SVG sanitisation (`functions.php`) only disables the libxml entity loader on PHP < 8 to avoid deprecation warnings while still whitelisting gradients, symbols, and `<use>` attributes.
- The standalone preview (`standalone.html`) preloads Google Fonts correctly, reuses the production header/hero scripts, honours reduced-motion scroll behaviour, and removes placeholder `#` URLs from service cards.

Keep documentation (this file, `readme.txt`, and `bug-report.md`) in sync with any future bug sweeps so downstream contributors understand the latest fixes.
