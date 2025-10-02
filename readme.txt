=== McCullough Digital ===
Contributors: McCullough Digital
Requires at least: 5.9
Tested up to: 6.9
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

= 1.2.40 - Unreleased =
* **Hero Image Accessibility:** Let the hero image container expose descriptive alt text, pull attachment metadata when available, and render via `wp_get_attachment_image()` so responsive sources and lazy-loading are applied automatically.
* **Static CTA States:** Keep CTA and service card copy visible to assistive technology and restyle static variants as neutral text so empty links are no longer implied when no URL is stored.
* **Home Pattern Seeding:** Ignore trashed "Home" pages while seeding the landing layout to ensure a published page always receives the starter content.
* **WordPress Requirement Bump:** Raised the documented minimum WordPress version to 5.9 to match the block theme features the codebase already relies on.
* **Header Logo Clamp:** Scoped the masthead logo styling to outrank WordPress core selectors and added max constraints so oversize uploads respect `--logo-size-header` without inflating the fixed header or Site Editor preview.
* **Neon Blog Archive Template:** Rebuilt the archive and index templates around a radial hero, live search, pill-style category filters, and a featured post grid with matching editor styles so the blog listing mirrors the new mockup without duplicating markup in patterns.
* **Blog Hero Glitch Parity:** Extended the header enhancement script and blog hero styles so the archive title now splits into interactive glitch letters with proper reduced-motion fallbacks, matching the front-page hero treatment.
* **Latest Badge Query Guard:** Limited the “Most Recent” badge to the main posts query on its first page, preventing paged archives and secondary loops from mislabeling older entries while keeping the grid markup clean elsewhere.
* **Blog Archive Loop Block:** Registered a `mccullough-digital/blog-archive-loop` dynamic block that renders curated category pills, a dedicated latest-post hero, the remaining `.post-grid` layout, and empty-state messaging with synchronized front-end/editor styling.

= 1.2.38 - 2025-11-06 =
* **Hero CTA Alignment Controls:** Freed the hero wrapper overflow and centring overrides so the neon button's URL popover opens fully while align and spacing controls can park the CTA left, centre, or right with extra breathing room.
* **Section CTA Allowlist:** Updated the hero, services, about, and CTA sections to allow the `mccullough-digital/button` block while keeping the neon CTA auto-seeded only in the hero so other sections stay optional by default.
* **Standalone Neon Button Block:** Added a reusable `mccullough-digital/button` block so editors can drop the hero's gradient sweep CTA anywhere, complete with toolbar link control and optional new-tab toggle.
* **Hero CTA Gradient Sweep:** Lighten the `.hero__cta-button` resting tint and flip the sweep animation to originate from the left, ensuring the neon gradient floods the pill left-to-right in both the front end and Site Editor while preserving the glow and focus outline.
* **Shared CTA Label Wrapper:** Wrap hero CTA text in `.hero__cta-button-label` so the hero and standalone button share identical inline-flex label styling.

= 1.2.37 - 2025-10-31 =
* **About Story Defaults:** Seed the About block and its companion patterns with mission, founder, proof, and CTA copy so new installs and reinserts surface the full narrative without manual editing.

= 1.2.36 - 2025-10-30 =
* **Hero CTA Simplified:** Retired the neon React bundle and `.hero-neon-button-mount` wrapper so the hero block now renders a standard `.cta-button.hero__cta-button` anchor (or `<button>` when no link is stored) with automatic bottom spacing in both the front end and Site Editor.
* **Hero Script Cleanup:** Removed the CTA magnetism and neon layer orchestration from `blocks/hero/view.js`, leaving only the starfield and decorative canvas logic to run on load.

= 1.2.35 - 2025-10-29 =
* **Hero CTA Hydration Everywhere:** Always enqueue the neon jelly CTA script and wrap saved buttons in a `.hero-neon-button-mount` so Site Editor and front-end renders hydrate even when authors edit the inner `core/button`, while the original markup stays in place for no-JS fallbacks.

= 1.2.34 - 2025-10-28 =
* **Link Semantics Restored:** Allow the neon hero CTA to hydrate as a `<motion.a>` when a URL exists, preserving native link behaviours like new-tab navigation, context menus, and assistive tech exposure while keeping the button fallback for empty links.
* **Responsive Label Layout:** Size the jelly button label and padding from the measured orb diameter and relax word breaking so default copy such as "Start a Project" stays horizontal instead of stacking each letter inside the sphere.

= 1.2.33 - 2025-10-27 =
* **Magnetic Hit Target Realignment:** Routed the Framer Motion springs onto the outer `<motion.button>` so the jelly sphere and its clickable hit area move together, restoring the magnetism effect for mouse and pen users.
* **Neon Ring Mask Fallback:** Wrapped the spinning conic ring in a CSS mask support check and provided a radial halo fallback so browsers without `mask-image` support keep the label readable instead of flooding it with the gradient.

= 1.2.32 - 2025-10-26 =
* **Neon Ring Mask Compatibility:** Rewrote the hero CTA's conic ring mask to use Safari- and Chromium-safe radial gradients with explicit mask sizing so the neon band stays hollow instead of flooding the button interior.

= 1.2.31 - 2025-10-24 =
* **Hero React CTA:** Replaced the vanilla neon button with a React-powered jelly sphere that mounts once per hero instance, mirrors reduced-motion preferences, and leaves a styled fallback link for no-JS contexts.
* **Hydration Guardrails:** Added dataset guards, MutationObserver cleanup, and fallback restoration so repeated script loads or block rerenders never double-initialise the hero CTA.

= 1.2.30 - 2025-10-23 =
* **Hero CTA 3D Tilt:** Extended the magnetic button script with Framer Motion-style rotateX/rotateY easing and a `transform-style: preserve-3d` surface so the neon shell leans toward the pointer while reduced-motion users stay on the flat fallback.

= 1.2.29 - 2025-10-22 =
* **Neon Hero CTA Orbiters:** Layered a sheen pass, orbiting sparks, and ripple feedback on the hero call-to-action by rebuilding the vanilla enhancement script to inject reusable surface shells, respect reduced-motion, and keep the label accessible while the button leans toward the pointer.
* **Editor Preview Sync:** Mirrored the refreshed CTA gradients, glow timings, and typography inside `editor-style.css` so Site Editor previews show the same neon sphere even without the runtime script.

= 1.2.28 - 2025-10-21 =
* **Hero CTA Jelly Redesign:** Rebuilt the hero call-to-action around a dedicated `.hero__cta-button` and lightweight CSS-variable animation so the circular jelly surface stays smooth, the label remains visible, and the editor plus standalone preview mirror the front-end magnetic behaviour without GSAP.

= 1.2.27 - 2025-10-20 =
* **Hero CTA Magnetic Guard:** Prevented repeated hero script loads from duplicating button glow/border layers by reusing existing wrappers, wrapping only raw text nodes, and rebinding GSAP listeners without mutating the DOM.

= 1.2.26 - 2025-10-19 =
* **Hero Top Alignment:** Replaced the duplicated masthead offset with a gentle clamp so top-aligned hero layouts hug the fixed header without reopening the gap on desktop.
* **Editor Parity:** Mirrored the hero alignment classes, content stack layout, and CTA offset variable inside `editor-style.css` so authors preview the same vertical behaviour as the front end while adjusting the offset slider.
* **Decorative Glow:** Layered stronger drop shadows around the hero artwork to mask the light halo left by background removal against dark canvases.

= 1.2.25 - 2025-10-18 =
* **Hero Header Alignment:** Removed the extra masthead offset from the hero block and anchored the CTA buttons to a bottom off
set so the section now sits flush beneath the fixed header while the desktop button rests lower on the canvas.

= 1.2.24 - 2025-10-17 =
* **Hero Content Layout Controls:** Added vertical alignment presets and a clamped 0–240px padding offset to the hero block, persisting the `is-content-*` class and `--hero-content-offset` variable so authors can pin copy to the top, middle, or bottom while nudging the stack without custom CSS.

= 1.2.23 - 2025-10-16 =
* **Hero Image Size Slider Reliability:** Ensured the hero block falls back to responsive viewport scaling and backfills intrinsic media widths so legacy content without stored pixel data still honours the size control in both the editor and on the front end.

= 1.2.22 - 2025-10-15 =
* **Hero Artwork Precision:** Added horizontal offset and intrinsic-width scaling to the hero image controls so decorative art can align precisely and scale beyond its native size from inside the editor.

= 1.2.21 - 2025-10-14 =
* **Hero Preview Parity:** Matched the editor's decorative image classes and inline styles with the front-end render so position, size, opacity, and offset controls now update instantly while hide-on-mobile states stay accurate.
* **Offset Alignment Fix:** Composed vertical offset transforms with the base centring transforms so offsetting artwork no longer breaks `bottom-center`, `center-right`, `center-left`, or `center` placements.

= 1.2.18 - 2025-10-13 =
* **Neon Footer Flattening:** Moved the gradient glow and padding onto the site footer itself, removed the inner shell wrapper, and swapped the chunky dividers for a slim separator so the section now stretches edge-to-edge without losing the neon wash.

= 1.2.17 - 2025-10-12 =
* **Footer Shell Refresh:** Removed the standalone CTA card, re-centred the headline/description inside a single gradient shell, and tightened spacing so the neon footer feels intentional without leaving a huge black void beneath the legal line.

= 1.2.16 - 2025-10-11 =
* **Footer Refresh:** Versioned the neon footer template part to `footer-neon` and updated every template plus the PHP fallback so sites previously customised in the Site Editor now load the CTA-led grid without manual resets.
* **Template Registration:** Registered the header and neon footer template parts in `theme.json` to keep them grouped correctly inside the Site Editor.
* **Compatibility Update:** Verified compatibility with WordPress 6.9 and updated project documentation to reflect the supported version.

= 1.2.15 - 2025-10-10 =
* **Footer Glow-Up:** Rebuilt the footer into a CTA-led neon grid with Caveat headlines, quick links, and contact details so the closing section mirrors the hero/header energy on both the front end and in the Site Editor.
* **Starfield Twinkle:** Layered faster parallax drift and brightness pulses across the footer starfield while adding motion-reduction fallbacks and freeing the footer logo to scale via new shared size tokens.
* **Standalone Preview Sync:** Updated `standalone.html` to mirror the CTA-first footer grid and starfield so offline demos reflect the production WordPress experience.

= 1.2.14 - 2025-10-09 =
* **Header Hover Highlight:** Reintroduced the transparent baseline border on the fixed header so the neon cyan underline and hover glow animate as intended without showing a divider at rest.

= 1.2.13 - 2025-10-08 =
* **CTA Gradient Sync:** Matched the public CTA, hero, and read-more buttons with the editor flood-fill treatment so the cyan-to-magenta gradient, halo glow, and hover text swap animate identically across every surface.

 = 1.2.12 - 2025-10-07 =
* **Navigation Palette Reset:** Kept primary menu links white at rest and removed the cyan sweep underline so the hover wobble now transitions into a neon-blue glow only when links are engaged.
* **Hover-Fill CTA Pills:** Rebuilt CTA, hero, and read-more buttons to rest on a single dark pill that floods with a cyan-to-magenta gradient on hover while the lettering pulses and spaces out for extra flair.

 = 1.2.11 - 2025-10-06 =
* **Navigation Glow:** Locked the primary menu links to a solid cyan neon treatment with a stronger pulse so the wobble effect
stays bright and legible.
* **CTA Surface Cleanup:** Rebuilt the CTA, hero, and read-more buttons around a single gradient surface with a halo glow, remo
ving the unwanted inner pill across front-end, editor, and standalone previews.

 = 1.2.10 - 2025-10-05 =
* **Navigation Hover Contrast:** Replaced the gradient text fill with a neon underline sweep so the wobble and pulse animations remain while the link copy stays readable.
* **CTA Polish:** Recentred the "Ready to Create?" heading within the CTA block and kept the gradient pill layer hidden until interaction so the buttons no longer display a second inner pill at rest.

= 1.2.9 - 2025-10-04 =
* **Navigation:** Reinstated the neon wobble-and-pulse hover treatment with a pink-to-blue gradient sweep that inverses the header animation while honouring reduced-motion preferences.
* **Calls to Action:** Centered the CTA layout and kept the gradient fill layer hidden until interaction so the buttons no longer show a second pill inside the outline.
* **Layout Polish:** Removed unintended divider borders from the header and section blocks to eliminate stray white lines across the page chrome.

= 1.2.8 - 2025-10-03 =
* **Editor Controls:** Enabled global spacing support flags and unit options in `theme.json` so the Dimensions panel exposes pa
dding, margin, and block gap controls across custom marketing sections and core blocks using spacing presets.

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