=== McCullough Digital ===
Contributors: McCullough Digital
Requires at least: 5.0
Tested up to: 6.0
Requires PHP: 7.4
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom theme scaffold with fixed header, mobile menu, and simple template hierarchy.

== Description ==

McCullough Digital delivers a performant block theme with a fixed, auto-hiding header, animated hero canvas, and accessible CTA and service patterns. Every interactive element honours reduced motion preferences, preserves semantic markup, and avoids placeholder links so new installations ship production-ready content.

== Key Features ==

* Dynamic header offsetting that keeps page content visible regardless of menu height or viewport changes.
* Animated hero block with particle field, keyboard-friendly headline animation, and graceful fallbacks for legacy browsers.
* Sanitised SVG pipeline that preserves gradients, symbols, and `<use>` references while blocking unsafe attributes.
* Custom CTA, Service Card, and Services blocks that respect author formatting, avoid empty links, and expose accessible markup by default.
* Standalone HTML preview that mirrors production assets, preloads fonts, and demonstrates block styling without dead links.

== Installation ==

1. Upload the `mccullough-digital` directory to the `/wp-content/themes/` directory.
2. Activate the theme through the 'Themes' menu in WordPress.

== Frequently Asked Questions ==

= Does this theme support widgets? =

This theme does not have any widget areas registered by default.

== Changelog ==

= 1.2.7 - 2025-10-02 =
* **Editor Persistence:** Updated all InnerBlocks-based custom blocks to save their nested content so edits to hero, CTA, about, services, and service card layouts now persist when templates or template parts are customized in the site editor.

= 1.2.6 - 2025-10-01 =
* **WYSIWYG Blocks:** Rebuilt the hero, CTA, about, services, and service card blocks around InnerBlocks templates with automated migrations so editors can manipulate every headline, paragraph, and button directly in the canvas without breaking legacy content.
* **Styling:** Extended front-end and editor button styles so core button markup inherits the neon CTA treatment and aligned service card button stacks with the existing layout.

= 1.2.5 - 2025-09-30 =
* **Hero Headline Animation:** Wrapped glitch letters per word so spaces render as real whitespace, restoring natural line breaks while keeping the hover distortion effect responsive.

= 1.2.4 - 2025-09-30 =
* **Hero CTA Accessibility:** Delayed the hover text color swap until the gradient fill completes so the "Start a Project" button stays legible while animating and added an explicit focus outline for keyboard users.

= 1.2.3 - 2025-09-29 =
* **Editor Experience:** Restored the page-wide template wrapper so `site-content` padding and the container helper classes work in the editor and the front end, and added matching wide/full container utilities to the block editor stylesheet.
* **Customization:** Enabled spacing, color, and typography design tools plus text domains across all custom blocks so they can be styled and translated like core blocks, and registered the page-wide template for selection in the template picker.
* **Design Tokens:** Introduced reusable surface color tokens, spacing presets, and a wide content size so align-wide/full and preset spacing controls produce valid CSS in templates and patterns.
* **Content Hygiene:** Rebuilt the 404 page layout, removed placeholder `#` links from the services pattern and footer social menu, and prevented the legacy bootstrapper from outputting duplicate headers and footers.

= 1.2.2 - 2025-09-28 =
* **Accessibility:** Removed duplicated IDs from editor components, restored hero block alignment support, and suppressed empty hero subheadings.
* **Stability:** Added the missing services block registration import, corrected its dynamic save implementation, and ensured service CTA links escape URLs.
* **Styling:** Introduced the missing z-index tokens, replaced remaining hard-coded header colors with CSS variables, fixed the navigation alignment control, and scoped hero block styles to prevent global leakage.
* **Performance:** Honoured reduced motion preferences by disabling the service card glow animation when requested and tightened the SVG sanitizer while keeping inline styles intact.
* **Content:** Corrected social icon targeting so default footer icons receive hover styling and ensured CTA buttons render safe URLs.

= 1.2.1 - 2025-09-28 =
* **Enhancement:** Seed the Home landing layout into the Home page content on activation, convert the front-page template to render editable content, and expose the landing layout as an optional custom template.

= 1.2.0 - 2025-09-27 =
* **Enhancement:** Made the social icon retrieval function extensible via a new `mcd_social_link_svg_patterns` filter.
* **Fix:** Corrected placeholder links in the default home page pattern.
* **Fix:** Improved performance and visual appeal of CSS animations for the footer starfield, main navigation hover, and post card hover effects.
* **Fix:** Replaced hardcoded colors and `z-index` values with CSS variables for improved maintainability.
* **Fix:** Hardened the theme activation logic to prevent duplicate "Home" page creation.
* **Fix:** Adjusted the SVG sanitizer to prevent it from stripping necessary inline `style` attributes.
* **Fix:** Corrected the hamburger menu animation for a smoother visual transition.
* **Fix:** Fixed flawed regex in the social icon logic to ensure correct URL matching.

= 1.1.2 - 2025-09-28 =
* Track the fixed header height with a `ResizeObserver`, font loading callbacks, and bfcache restores so content never slides underneath the masthead.
* Rebuild the hero headline animation to duplicate screen-reader text, guard against missing browser APIs, and share reusable `.screen-reader-text` utilities.
* Preload Google Fonts correctly in the standalone preview, reuse the production header/hero scripts, and convert decorative service card links into static spans.
* Silence PHP 8 deprecation warnings by only disabling the libxml entity loader when the function is available.

= 1.1.1 - 2025-09-27 =
* Respect custom anchors, alignment options, and inline formatting across the About, Services, CTA, and Service Card blocks.
* Remove placeholder `#` links from blocks, defaults, and patterns while rendering static buttons when URLs are missing.
* Harden the home pattern seeding routine so it ignores unpublished pages and fills empty front pages with the default layout.

= 1.1.0 - 2025-09-27 =
* Dynamically sync the fixed header height, keep it visible during keyboard navigation, and guard scripts that rely on `matchMedia`.
* Restore hero headline accessibility, expand SVG sanitisation to preserve gradients, and improve decorative canvas semantics.
* Prevent blocks from emitting empty links, streamline services block rendering, and fix the standalone preview font preload markup.

= 1.0.0 - 2025-09-25 =
* Initial release.

== Copyright ==

McCullough Digital © 2025
This theme is licensed under the GNU General Public License v2 or later.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

== Development ==

This theme ships with a build pipeline powered by `@wordpress/scripts`.

1. Run `npm install` to set up the toolchain.
2. Use `npm run build` to compile block assets for production.
3. Use `npm run start` during development to watch and rebuild block scripts.

Compiled files are written to `build/blocks/*/editor.js` and referenced automatically by each block's `block.json`.