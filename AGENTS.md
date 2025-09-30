# Agent Notes (2025-09-30)

This repository contains the McCullough Digital block theme. The notes below summarise the current workflow and the defects resolved during the latest sweep.

## Build & QA Checklist
1. Run `npm install` to install block tooling.
2. Use `npm run start` while developing blocks so assets rebuild automatically.
3. Finish with `npm run build` before committing production bundles.
4. **Always** update `AGENTS.md`, `bug-report.md`, and `readme.txt` to reflect any bug fixes or improvements.

## Bug Fix & Improvement Highlights
- **Latest (2025-10-12):**
    - Replaced the standalone footer CTA card with a gradient-wrapped shell that keeps the neon glow, centres the headline/description, and tightens spacing so the legal line no longer floats above a vast black void.
- **Latest (2025-10-11):**
    - Renamed the neon footer template part to `footer-neon` and updated every template plus the PHP fallback so installs bypass stale Site Editor overrides and immediately load the rebuilt CTA grid.
    - Registered the header and neon footer template parts in `theme.json` for clearer organisation inside the Site Editor.
    - Limited the Services block's legacy heading migration to a single run so authors can intentionally remove the section title without it being reinstated.
- **Latest (2025-10-10):**
    - Intensified the footer starfield with layered parallax drift and brightness pulses that respect `prefers-reduced-motion` while keeping the neon skyline alive.
    - Freed the footer logo from the header lock and rebuilt the footer into a CTA-led, neon-accented grid that mirrors the hero's typography and gradients.
    - Synced the standalone preview's footer with the CTA-led neon grid so local demos match the block theme output, complete with the refreshed starfield.
- **Latest (2025-10-09):**
    - Restored the transparent header border baseline so the neon hover underline returns without reintroducing a visible divider at rest in both the theme CSS and standalone preview.
- **Latest (2025-10-08):**
    - Synced the front-end CTA, hero, and read more buttons with the editor treatment so the gradient flood, halo glow, and hover color swap all animate identically across the theme, block styles, and standalone preview.
- **Latest (2025-10-07):**
    - Kept the primary navigation labels white at rest, removed the sweep underline, and reserved the neon cyan wobble-and-pulse animation for hover/focus states only.
    - Reimagined the CTA button styling across the theme so the single pill base fills with a cyan-to-magenta gradient on hover while the lettering gains a pulsing glow.
- **Latest (2025-10-06):**
    - Locked the primary navigation links to a solid cyan treatment with an intensified glow-and-pulse hover so the wobble anim
      ation stays legible and energetic without reverting to gradient fills.
    - Rebuilt the CTA button styling to render a single neon surface with a bloom halo, eliminating the nested dark pill while
      keeping the hover lift consistent across hero, CTA, and read more links.
- **Latest (2025-10-05):**
    - Centered the CTA headline, kept the gradient pill layer hidden until interaction so CTA buttons render a single surface, and swapped the navigation hover gradient text fill for a neon underline that stays legible while wobbling.
- **Latest (2025-10-04):**
    - Restored the neon navigation hover wobble with a reversed gradient sweep, re-centered the CTA layout, hid the standby gradient pill layer, and removed unintended header/section borders that introduced bright divider lines.
- **Latest (2025-10-03):**
    - Exposed global padding, margin, and block gap controls with unit selection in `theme.json` so Gutenberg surfaces the Dimen
      sions panel for custom and core blocks that opt into spacing support.
- **Latest (2025-10-02):**
    - Restored persistence for all InnerBlocks-based custom blocks by saving their nested content so Site Editor changes to templates, template parts, and marketing sections stick after reload.
- **Latest (2025-10-01):**
    - Rebuilt the hero, CTA, about, services, and service-card blocks on top of InnerBlocks templates so every headline, paragraph, and button is edited in place while migrations and styling keep legacy attribute-driven content intact.
- **Latest (2025-09-30):**
    - Kept CTA button text readable by delaying the hover color swap until the gradient animation finishes and added a neon focus outline so keyboard users get the same visual feedback.
    - Reworked the hero headline glitch markup so words wrap naturally while preserving the hover distortion and reduced-motion fallbacks.
- **Latest (2025-09-29):**
    - Enabled padding, margin, color, and typography design tools across every custom block, added missing text domains, and unhid the service card block in the inserter so authors can freely compose sections from the editor.
    - Added reusable surface color tokens, spacing presets, and a defined wide content width to `theme.json`, updated both front-end and editor styles to consume the new palette, and wired up wide/full container helpers for accurate previews.
    - Reworked the page-wide and 404 templates, registered the wide template in `theme.json`, and replaced placeholder URLs in the services pattern and footer to keep default content production ready while preventing duplicate headers in the PHP bootstrap.
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
