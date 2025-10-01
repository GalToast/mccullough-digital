# Agent Notes (2025-09-29)

This repository contains the McCullough Digital block theme. The notes below summarize the current workflow and the defects resolved during the latest sweep.

## Build & QA Checklist
1. Run `npm install` to install block tooling.
2. Use `npm run start` while developing blocks so assets rebuild automatically.
3. Finish with `npm run build` before committing production bundles.
4. **Always** update `AGENTS.md`, `bug-report.md`, and `readme.txt` to reflect any bug fixes or improvements.

## Bug Fix & Improvement Highlights

### Latest (2025-10-31) - About Story Defaults
- **Richer Defaults:** Updated the About block metadata to prefill mission, founder, and proof paragraphs plus a CTA button so every new insertion surfaces the full storytelling stack.
- **Pattern Sync:** Rebuilt the Home Landing and standalone About section patterns to embed the same copy structure, ensuring seeded pages and manual inserts stay consistent.

### Latest (2025-10-30) - Hero CTA Simplification
- **Static CTA:** Removed the neon React button bundle and `.hero-neon-button-mount` wrapper. The PHP render now outputs the saved CTA markup directly as a `.cta-button.hero__cta-button` anchor (or `<button>` when no link is stored) so the hero falls back gracefully without JavaScript.
- **Layout Parity:** Updated the front-end and editor hero styles to let `.hero-content` push button groups to the bottom with `margin-top: auto`, reusing the global `.cta-button` treatment instead of bespoke neon layers.
- **JS Cleanup:** Trimmed `blocks/hero/view.js` down to the starfield and decorative logic so no magnetism helpers or CTA layer orchestration run on load, matching the simplified markup and styles.

> Older hero CTA notes below (2025-10-29 through 2025-10-21) describe the retired neon implementation and are kept for historical context only.

### Latest (2025-10-29) - Hero CTA Mount Reliability
- **Hydration Everywhere:** The PHP render now wraps saved hero CTA buttons in a `.hero-neon-button-mount` wrapper and enqueues `build/blocks/hero/hero-button.js` whenever a call-to-action exists, so React hydrates in both the Site Editor preview and the published front end.
- **Graceful Fallbacks:** The wrapper preserves the original `<a>` or `<button>` markup inside the mount, keeping the no-JavaScript experience accessible while exposing the CTA text/link to the React bundle.

### Latest (2025-10-28) - Hero CTA Link Semantics & Label Layout
- **Accessibility:** The React jelly button now renders a `<motion.a>` whenever a link is provided, so keyboard modifiers, conte
xt menus, and assistive technology see a real anchor while the motion-driven `<motion.button>` fallback remains for empty URLs.
- **Legibility:** The CTA label sizing, padding, and wrapping derive from the measured sphere diameter, keeping phrases like “St
art a Project” on one or two readable lines instead of stacking single letters vertically inside the orb.

### Latest (2025-10-27) - Hero CTA Magnetism & Mask Fallbacks
- **Critical:** Rebound the Framer Motion springs to the `<motion.button>` wrapper so the actual click target now follows the jelly sphere. Any future tweaks to magnetism or press squish must stay on the outer button to keep pointer math and hit testing aligned.
- **Compatibility:** Added a runtime CSS mask support check around the neon ring and a radial halo fallback so browsers that ignore `mask-image` (or its WebKit variant) no longer flood the label with the conic gradient. Preserve this guard when iterating on the ring visuals.

### Latest (2025-09-29) - Code Quality & WordPress Compatibility
- **Critical:** Replaced deprecated `get_page_by_title()` function with `get_posts()` query for WordPress 6.2+ compatibility, eliminating potential PHP warnings.
- **Critical:** Fixed invalid `get_block_wrapper_attributes()` usage across all block render files by removing the unsupported second parameter, ensuring proper WordPress API compliance.
- **Enhancement:** Added source map generation to webpack config for improved debugging in both development and production environments.
- **Theme Version:** Bumped to 1.2.19 to reflect code quality improvements and WordPress API compliance fixes.

-### Latest (2025-10-26) - Neon Ring Mask Compatibility
- Safari and Chromium were discarding the jelly ring's `calc()`-based radial mask, so the conic gradient filled the entire button. We replaced the shorthand mask with explicit `mask-image`/`mask-repeat` pairs that use plain percentage stops and a no-repeat sizing band. Keep any future tweaks within that mask structure so Safari, Chrome, and Edge all preserve the hollow center.
- Bumped the theme to v1.2.32 and documented the mask fix across `readme.txt` and `bug-report.md`.

### Latest (2025-10-24) - Hero React CTA Mount
- Implemented the React-based neon jelly CTA and MutationObserver guard so each hero block hydrates once, restores the fallback link when scripts fail, and mirrors reduced-motion preferences in the mounted experience.
- Ensure any future adjustments keep the fallback markup in `render.php` aligned with the React props and leave the `.hero-neon-button-mount--hydrated` class intact for CSS targeting.

### Latest (2025-10-23) - Hero CTA Tilt Depth
- Added a Framer Motion-inspired 3D tilt to the hero CTA by extending the pointer tracking script with rotateX/rotateY easing, updating the surface wrapper to respect `transform-style: preserve-3d`, and zeroing the effect for reduced-motion users.
- Documented the 3D tilt upgrade alongside the existing neon CTA notes so future sweeps know the rotation variables must stay in sync across JS and CSS fallbacks.

### Latest (2025-10-22) - Neon CTA Orbiters
- Added a reusable enhancement layer in `blocks/hero/view.js` that builds the neon sphere shell, orbiting sparks, and ripple pool exactly once per button, reuses them on subsequent initialisations, and still respects reduced-motion plus keyboard activation.
- Rebuilt the hero CTA styling in `blocks/hero/style.css` and `editor-style.css` so the sheen sweep, scanline, halo, and label glow all match the new circular neon treatment while keeping accessible fallbacks for static markup.
- Bumped the theme to v1.2.29 and documented the neon CTA upgrades across `readme.txt` and `bug-report.md`.

### Latest (2025-10-21) - Hero Magnetic CTA Redesign
- Replaced the hero CTA markup with a dedicated `.hero__cta-button` wrapper so it no longer inherits the global pill surface, keeps the label on its own layer, and supports static fallbacks without extra spans.
- Rebuilt the magnetic hover effect without GSAP by driving translation, stretch, and glow through CSS custom properties that follow pointer events, clamp deformation, and respect reduced-motion and touch input.
- Synced the editor preview, standalone HTML demo, and global CTA rules with the new hero styling so every surface renders the circular jelly design consistently.

### Latest (2025-10-20) - Magnetic CTA Idempotency
- Hardened the hero CTA magnetic button enhancement so repeated script loads reuse the existing glow/border layers, skip rewrapping nested markup, and simply rebind the GSAP listeners while leaving the DOM untouched.

### Latest (2025-10-19) - Hero Alignment Parity
- Replaced the hero's top-alignment padding with a modest clamp so the section tucks beneath the fixed header without reopening a visible gap on desktop.
- Mirrored the hero alignment classes, offset transform, and CTA positioning rules inside the editor stylesheet so Site Editor previews now match the live layouts.
- Intensified the decorative hero image glow with layered drop shadows to mask the light halo left by background removal.

### Latest (2025-10-18) - Hero Layout Polish
- Removed the duplicate masthead offset from the hero block so the section now sits flush beneath the fixed header while keepin
g the top-aligned layout option intact.
- Anchored the hero CTA to a bottom offset instead of the heading's vertical midpoint, freeing the button to live lower on the 
canvas regardless of copy length.

### Latest (2025-10-17)
- Introduced hero content layout controls that save vertical alignment (`top`, `center`, `bottom`) and a clamped 0–240px padding offset in block attributes. The editor now emits the matching `is-content-*` class plus a `--hero-content-offset` CSS variable so both the live render and dynamic fallback stay in sync.

### Latest (2025-10-16)
- Expanded the hero image fallback width handling to honour the size slider even when legacy content lacks stored media dimensions by retrieving intrinsic widths and defaulting to responsive viewport scaling.

### Latest (2025-10-15)
- Added horizontal offset and natural-size scaling to the hero decorative image so authors can fine-tune artwork placement and enlarge assets beyond their defaults without leaving the block editor.

### Latest (2025-10-14)
- Synced the hero block editor preview with the front-end decorative image logic so size, position, opacity, and vertical offset controls now respond live while keeping hide-on-mobile classes in step.
- Preserved centred hero image transforms when applying a vertical offset so the artwork stays aligned in both the editor and on the front end.

### Latest (2025-10-13)
- Flattened the neon footer wrapper so the gradient glow now lives on the footer container, removed redundant dividers, and tightened spacing so the colophon spans edge-to-edge without an inner shell.

### Latest (2025-10-12)
- Replaced the standalone footer CTA card with a gradient-wrapped shell that keeps the neon glow, centres the headline/description, and tightens spacing so the legal line no longer floats above a vast black void.

### Latest (2025-10-11)
- Renamed the neon footer template part to `footer-neon` and updated every template plus the PHP fallback so installs bypass stale Site Editor overrides and immediately load the rebuilt CTA grid.
- Registered the header and neon footer template parts in `theme.json` for clearer organisation inside the Site Editor.
- Limited the Services block's legacy heading migration to a single run so authors can intentionally remove the section title without it being reinstated.

### Latest (2025-10-10)
- Intensified the footer starfield with layered parallax drift and brightness pulses that respect `prefers-reduced-motion` while keeping the neon skyline alive.
- Freed the footer logo from the header lock and rebuilt the footer into a CTA-led, neon-accented grid that mirrors the hero's typography and gradients.
- Synced the standalone preview's footer with the CTA-led neon grid so local demos match the block theme output, complete with the refreshed starfield.

### Latest (2025-10-09)
- Restored the transparent header border baseline so the neon hover underline returns without reintroducing a visible divider at rest in both the theme CSS and standalone preview.

### Latest (2025-10-08)
- Synced the front-end CTA, hero, and read more buttons with the editor treatment so the gradient flood, halo glow, and hover color swap all animate identically across the theme, block styles, and standalone preview.

### Latest (2025-10-07)
- Kept the primary navigation labels white at rest, removed the sweep underline, and reserved the neon cyan wobble-and-pulse animation for hover/focus states only.
- Reimagined the CTA button styling across the theme so the single pill base fills with a cyan-to-magenta gradient on hover while the lettering gains a pulsing glow.

### Latest (2025-10-06)
- Locked the primary navigation links to a solid cyan treatment with an intensified glow-and-pulse hover so the wobble animation stays legible and energetic without reverting to gradient fills.
- Rebuilt the CTA button styling to render a single neon surface with a bloom halo, eliminating the nested dark pill while keeping the hover lift consistent across hero, CTA, and read more links.

### Latest (2025-10-05)
- Centered the CTA headline, kept the gradient pill layer hidden until interaction so CTA buttons render a single surface, and swapped the navigation hover gradient text fill for a neon underline that stays legible while wobbling.

### Latest (2025-10-04)
- Restored the neon navigation hover wobble with a reversed gradient sweep, re-centered the CTA layout, hid the standby gradient pill layer, and removed unintended header/section borders that introduced bright divider lines.

### Latest (2025-10-03)
- Exposed global padding, margin, and block gap controls with unit selection in `theme.json` so Gutenberg surfaces the Dimensions panel for custom and core blocks that opt into spacing support.

### Latest (2025-10-02)
- Restored persistence for all InnerBlocks-based custom blocks by saving their nested content so Site Editor changes to templates, template parts, and marketing sections stick after reload.

### Latest (2025-10-01)
- Rebuilt the hero, CTA, about, services, and service-card blocks on top of InnerBlocks templates so every headline, paragraph, and button is edited in place while migrations and styling keep legacy attribute-driven content intact.

### Latest (2025-09-30)
- Kept CTA button text readable by delaying the hover color swap until the gradient animation finishes and added a neon focus outline so keyboard users get the same visual feedback.
- Reworked the hero headline glitch markup so words wrap naturally while preserving the hover distortion and reduced-motion fallbacks.

### Latest (2025-09-29)
- Enabled padding, margin, color, and typography design tools across every custom block, added missing text domains, and unhid the service card block in the inserter so authors can freely compose sections from the editor.
- Added reusable surface color tokens, spacing presets, and a defined wide content width to `theme.json`, updated both front-end and editor styles to consume the new palette, and wired up wide/full container helpers for accurate previews.
- Reworked the page-wide and 404 templates, registered the wide template in `theme.json`, and replaced placeholder URLs in the services pattern and footer to keep default content production ready while preventing duplicate headers in the PHP bootstrap.

### Latest (2025-09-28)
- Defined the missing `--z-index-background` and `--z-index-content` variables and switched lingering header colours to CSS tokens so palette changes propagate everywhere.
- Corrected the navigation block alignment setting and scoped hero block styles to avoid leaking `.hero` rules across the site.
- Hardened all CTA links by escaping URLs and suppressing empty hero elements while restoring wrapper alignment support.
- Removed duplicate IDs from the hero, CTA, about, and services editors, added the missing services block import, and ensured the dynamic block no longer saves rendered markup.
- Disabled the service card glow animation when `prefers-reduced-motion` is active and allowed the SVG sanitizer to retain safe `style` attributes.
- Updated footer social icon selectors so WordPress' `.wp-social-link` markup inherits the intended hover styling.

### Functionality
- Corrected placeholder links in the `home-landing.php` pattern that were pointing to example.com.
- Fixed social media icon logic in `functions.php` to correctly identify all variations of `x.com` and other social URLs by using proper regex.
- Seed the Home landing layout into the "Home" page content on activation so editors can manage it directly from the page editor while keeping the dedicated template optional.

### Visual & Performance
- Improved the footer's starfield animation in `style.css` to use `background-position` for better performance.
- Refined the main navigation hover animation in `style.css` to be a smoother, more professional `text-shadow` effect.
- Fixed a layout shift on post cards in `style.css` by changing the hover transition to only affect `transform` and `border-color`.
- Corrected the mobile hamburger menu animation in `style.css` for a smoother transition.

### Code Quality & Extensibility
- Made the `mcd_get_social_link_svg()` function in `functions.php` extensible with a filter, allowing new social icons to be added easily.
- Hardened the theme activation logic in `functions.php` to prevent the creation of duplicate "Home" pages.
- Fixed the over-aggressive SVG sanitizer in `functions.php` to no longer strip out necessary `style` attributes.
- Replaced hardcoded `z-index` values and colors in `style.css` with CSS variables for better maintainability.
- Added webpack source maps for improved debugging during development and production troubleshooting.
- Ensured all WordPress API calls use correct signatures to prevent PHP warnings and maintain forward compatibility.

## Development Notes
- The theme requires WordPress 5.0+ and PHP 7.4+
- Build assets are tracked in `build/blocks/*/editor.js`
- Block registrations are handled automatically via `functions.php`
- Custom blocks support InnerBlocks for flexible content composition
- All animations respect `prefers-reduced-motion` user preferences
- Theme uses CSS custom properties for consistent theming
- SVG sanitization allows safe inline styles while preventing XSS

## Design Resources
- **Web and Graphic Design 101.md**: A comprehensive guide covering foundational design principles, advanced techniques, UI/UX best practices, responsive design, accessibility (WCAG), information architecture, design psychology, and emerging technologies. This resource serves as a reference for design decisions, best practices, and industry standards when working on the theme.

Keep documentation (this file, `readme.txt`, and `bug-report.md`) in sync with every bug sweep so downstream contributors understand the latest fixes.
