# Bug Fix Report — 2025-09-29

This report tracks all production-impacting fixes and continuous improvements in the McCullough Digital theme from 2025-09-27 through 2025-09-29. Each item lists the affected files, the observed problem, and the implemented remedy.

## Fixed Bugs

### 2025-10-22 Sweep
1. **Neon Hero CTA Orbiters & Ripples**
   *Files:* `blocks/hero/view.js`, `blocks/hero/style.css`, `editor-style.css`, `style.css`, `readme.txt`, `AGENTS.md`
   *Issue:* The hero CTA redesign shipped as a static jelly sphere without the neon orbiters, ripple feedback, or sheen requested in the latest creative, and the Site Editor still previewed the older glow treatment.
   *Resolution:* Extended the vanilla enhancement script to inject a reusable neon shell with orbiting sparks and ripple pools that honour reduced motion, refreshed the front-end and editor styles with the new gradients and animations, bumped the theme to 1.2.29, and documented the upgrade across project notes.

### 2025-10-21 Sweep
1. **Hero CTA Jelly Rebuild**
   *Files:* `blocks/hero/render.php`, `blocks/hero/block.json`, `blocks/hero/editor.js`, `blocks/hero/style.css`, `blocks/hero/view.js`, `build/blocks/hero/editor.js`, `editor-style.css`, `style.css`, `standalone.html`
   *Issue:* The hero reused the global `.cta-button` pill while the GSAP script injected extra glow layers, causing stacked pseudo elements, jittery stretching, and text hidden beneath overlays so the CTA appeared glitchy instead of smooth.
   *Resolution:* Introduced a dedicated `.hero__cta-button` wrapper with single-layer markup, replaced the GSAP routine with a lightweight pointer tracker that feeds CSS variables for translation/stretch/glow, and mirrored the circular jelly styling across front-end, editor, and standalone previews while keeping reduced-motion fallbacks intact.

### 2025-10-20 Sweep
1. **Hero CTA Magnetic Layers Duplicating**
   *Files:* `blocks/hero/view.js`, `style.css`, `readme.txt`, `AGENTS.md`, `bug-report.md`
   *Issue:* Reinitialising the hero script wrapped CTA buttons repeatedly, duplicating the glow/border layers and breaking the GSAP jelly effect when the script loaded more than once.
   *Resolution:* Added a dataset guard so previously enhanced buttons bail before DOM mutations, reuse existing glow/border/text wrappers, and rebind GSAP listeners idempotently so repeated loads leave a single glow/border pair while keeping the cursor tracking intact.

### 2025-10-19 Sweep
1. **Hero Top Alignment Still Overpadded**
   *Files:* `blocks/hero/style.css`, `style.css`, `readme.txt`, `AGENTS.md`, `bug-report.md`
   *Issue:* Setting the hero content to `top` reinstated the full masthead offset, reopening a visible gap between the fixed header and the section on the live site.
   *Resolution:* Replaced the header-height variable with a gentle clamp so the hero now tucks beneath the masthead while keeping a modest buffer for the content stack.

2. **Hero Alignment Preview Drift in Editor**
   *Files:* `editor-style.css`
   *Issue:* The Site Editor ignored the hero alignment modifiers and offset variable, so top- or bottom-aligned layouts still appeared centred and the offset slider had no visible effect while authoring.
   *Resolution:* Mirrored the front-end alignment classes, stack layout, and CTA offset styles inside the editor stylesheet so previews honour each vertical alignment and reflect the offset translation.

3. **Decorative Hero Art Halo Visible**
   *Files:* `blocks/hero/style.css`
   *Issue:* The background-removed hero illustration retained a faint white fringe that bled through on dark canvases despite the existing drop shadow.
   *Resolution:* Layered denser, tinted drop shadows to envelop the artwork edges and mask the halo without flattening the glow effect.

### 2025-10-18 Sweep
1. **Hero Offset & CTA Elevation**
   *Files:* `blocks/hero/style.css`, `editor-style.css`
   *Issue:* The hero block stacked the site-wide header offset on top of the layout padding, leaving a visible gap beneath the 
masthead, and the CTA button was vertically centred so it rose and fell with the headline instead of sitting lower on the canva
s.
   *Resolution:* Dropped the redundant top padding from the hero wrapper and anchored the CTA to a consistent bottom offset, ke
eping the top-aligned layout option while freeing the button to rest lower on desktop without affecting the mobile layout.

### 2025-10-17 Sweep
1. **Hero Content Stack Locked to Middle**
   *Files:* `blocks/hero/block.json`, `blocks/hero/editor.js`, `blocks/hero/render.php`, `blocks/hero/style.css`, `build/blocks/hero/editor.js`
   *Issue:* The hero block always centred its copy vertically and lacked spacing controls, forcing authors to resort to custom CSS or spacer blocks to align the content near the top or bottom of the viewport.
   *Resolution:* Added saved block attributes for vertical alignment and a clamped 0–240px content padding offset, surfaced matching editor controls, and wired the `is-content-*` class plus `--hero-content-offset` CSS variable through the PHP render so both the editor and front end respect the new layout options without manual code.

### 2025-10-16 Sweep
1. **Hero Image Width Fallback Ignored Size Slider**
   *Files:* `blocks/hero/editor.js`, `blocks/hero/render.php`, `build/blocks/hero/editor.js`
   *Issue:* Legacy hero blocks without a stored `heroImageWidth` defaulted to a hard-clamped 800px editor preview and front-end render, so the image size slider couldn't enlarge or shrink artwork based on the chosen percentage.
   *Resolution:* Retrieve the selected media's intrinsic width on load and fall back to percentage-based viewport sizing when pixels are unavailable, keeping the size slider effective across both the editor and public output.

### 2025-10-15 Sweep
1. **Hero Artwork Controls Needed Horizontal Offset & Natural Scaling**
   *Files:* `blocks/hero/block.json`, `blocks/hero/editor.js`, `blocks/hero/render.php`, `blocks/hero/style.css`, `build/blocks/hero/editor.js`
   *Issue:* Authors could only nudge the decorative hero image vertically, and the size slider topped out at a clamped 800px width, preventing precise horizontal placement or scaling to the asset's native resolution.
   *Resolution:* Added a dedicated horizontal offset control, captured the media library's intrinsic width, and rebuilt the width calculation to honour the stored pixel size so the slider now reaches (and optionally exceeds) the original dimensions while keeping editor and front-end transforms in sync.

### 2025-09-29 Sweep (Code Quality & WordPress Compatibility)
1. **Deprecated get_page_by_title() Function**
   *Files:* `functions.php`
   *Issue:* The theme used `get_page_by_title()` which was deprecated in WordPress 6.2, potentially causing PHP warnings or errors in newer WordPress installations.
   *Resolution:* Replaced with `get_posts()` query using the 'title' parameter for WordPress 6.2+ compatibility.

2. **Invalid get_block_wrapper_attributes() Usage**
   *Files:* `blocks/hero/render.php`, `blocks/cta/render.php`, `blocks/services/render.php`, `blocks/about/render.php`, `blocks/service-card/render.php`
   *Issue:* All render callbacks passed an invalid second `$block` parameter to `get_block_wrapper_attributes()`, which only accepts a single array parameter, potentially causing PHP warnings.
   *Resolution:* Removed the second parameter from all `get_block_wrapper_attributes()` calls to match the correct WordPress API signature.

3. **Missing Webpack Source Maps**
   *Files:* `webpack.config.js`
   *Issue:* The build configuration didn't generate source maps, making debugging difficult during development.
   *Resolution:* Added `devtool` configuration to generate `eval-source-map` for development and regular `source-map` for production builds.

### 2025-10-14 Sweep
1. **Hero Image Controls Ignored in Editor Preview**
   *Files:* `blocks/hero/editor.js`, `build/blocks/hero/editor.js`
   *Issue:* The Site Editor only applied opacity to the decorative hero image, so changes to size, position, vertical offset, and the hide-on-mobile toggle never appeared during authoring even though the published front end responded.
   *Resolution:* Mirrored the PHP render logic inside the editor script to append the same modifier classes and inline width/transform styles, ensuring every control now updates instantly within the canvas.

2. **Vertical Offset Broke Centred Hero Artwork**
   *Files:* `blocks/hero/render.php`
   *Issue:* Applying a vertical offset replaced the hero image transform entirely, removing the base centring translate values and causing the artwork to drift off-target for `bottom-center`, `center-right`, `center-left`, and `center` positions.
   *Resolution:* Composed the offset with the existing base transforms so adjustments stack instead of overwrite, preserving alignment across both the editor preview and live markup.

### 2025-10-13 Sweep
1. **Footer Shell Caused Redundant Padding & Dividers**
   *Files:* `parts/footer-neon.html`, `style.css`, `editor-style.css`
   *Issue:* The neon footer still wrapped its content in an inner `.footer-shell`, leaving duplicate padding, rounded edges, and gradient borders that stopped the section from spanning edge-to-edge. Legacy divider blocks also forced extra vertical spacing that made the legal line feel detached.
   *Resolution:* Moved the gradient glow and padding directly onto `#colophon`, removed the shell wrapper, trimmed the layout gap, and replaced the thick dividers with a single slim separator so the footer breathes without an inner card.

### 2025-10-12 Sweep
1. **Footer CTA Card Stuck Above Site Info**
   *Files:* `parts/footer-neon.html`, `style.css`, `editor-style.css`
   *Issue:* The neon footer always rendered a standalone CTA card with duplicate copy and buttons the client no longer wanted, and the surrounding layout left an oversized block of empty space beneath the legal text.
   *Resolution:* Removed the CTA block, rebuilt the footer template around a single gradient "shell" that carries the neon glow into the core columns, added a compact headline/description treatment, and tightened spacing so the footer collapses cleanly without the unwanted black void.

### 2025-10-11 Sweep
1. **Neon Footer Still Showing Legacy Layout**
   *Files:* `parts/footer-neon.html`, `templates/404.html`, `templates/archive.html`, `templates/front-page.html`, `templates/home-landing.html`, `templates/index.html`, `templates/page-wide.html`, `templates/search.html`, `templates/singular.html`, `index.php`, `theme.json`
   *Issue:* Sites that had previously customised the `footer` template part kept loading the legacy single-column footer even after the neon CTA grid shipped because the database-stored slug overrode the file version.
   *Resolution:* Versioned the footer template part to `footer-neon`, updated every template reference plus the PHP fallback, and registered the new slug in `theme.json` so fresh installs and existing sites immediately render the revamped footer.

2. **Services Heading Reappeared After Deletion**
   *Files:* `blocks/services/editor.js`, `build/blocks/services/editor.js`
   *Issue:* The Services block automatically reinserted its heading whenever authors deleted it, overriding intentional edits and preventing alternate layouts.
   *Resolution:* Limited the legacy heading migration to a single run so new InnerBlocks edits take precedence and the heading stays removed when users intentionally delete it.

### 2025-10-10 Sweep
1. **Footer Starfield Felt Static**
   *Files:* `style.css`
   *Issue:* The footer's star layers only drifted slowly on long loops, so there was no parallax or brightness variation to make the twinkle visible.
   *Resolution:* Replaced the single `move-stars` loop with layered drift/twinkle animations at staggered speeds and delays, added blend/blur treatments per layer, and ensured the motion disables cleanly for reduced-motion users.

2. **Footer Logo Was Still Squished**
   *Files:* `style.css`
   *Issue:* The global `.custom-logo` height locked the footer mark to the header's 60px cap, so the footer override never gained enough height to respect the logo's aspect ratio.
   *Resolution:* Introduced shared logo size variables, reset the footer logo to `height: auto` with a dedicated max height, and preserved the header sizing via the new `--logo-size-header` token.

3. **Standalone Preview Footer Stayed Minimal**
   *Files:* `standalone.html`
   *Issue:* The standalone demo continued to show the legacy single-column footer with dated copy, so local previews missed the CTA grid, contact details, and layered starfield now shipping in the block theme.
   *Resolution:* Rebuilt the standalone footer to mirror the block template, including the CTA headline, quick links, stylised social icons, animated starfield layers, and responsive alignments.

4. **Footer Layout Didn't Match the Hero Vibe**
   *Files:* `parts/footer.html`, `style.css`, `editor-style.css`
   *Issue:* The footer stacked a logo, title, and social icons with minimal styling, lacking the neon gradients, CTA energy, and typography established by the hero/header.
   *Resolution:* Rebuilt the footer into a CTA-first grid with glowing dividers, Caveat headlines, quick-link and contact panels, and mirrored the styling in the editor preview so the closing section carries the hero's neon tone.

### 2025-10-09 Sweep
1. **Header Hover Highlight Missing**
   *Files:* `style.css`, `standalone.html`
   *Issue:* Removing the static header divider also stripped the transparent base border, so the neon cyan accent never appeared on hover because there was no border to recolor.
   *Resolution:* Restored the header border as a transparent baseline and kept the hover state swapping the color, preserving the clean resting state while allowing the requested neon underline to return.

### 2025-10-08 Sweep
1. **Front-End CTA Gradient Desync**
   *Files:* `style.css`, `blocks/cta/style.css`, `standalone.html`
   *Issue:* The home-page CTA buttons still rendered a dark pill with the gradient halo trapped underneath, so the hover flood and glow effects never appeared on the public site even though the editor preview was correct.
   *Resolution:* Replaced the conic-gradient border trick with the editor's gradient flood treatment, added the matching halo animation, and updated the CTA block override plus standalone preview so every surface uses the same hover/focus transitions.

### 2025-10-07 Sweep
1. **Navigation Contrast Request**
   *Files:* `style.css`
   *Issue:* The primary menu labels defaulted to neon cyan with an animated underline, so the hover wobble never transitioned from the requested resting white state and the sweep underline remained visible.
   *Resolution:* Reset the base link color to the primary text token, removed the sweep underline pseudo-element, and limited the wobble/pulse animation to hover and focus so links stay white until interaction.

2. **CTA Pill Gradient Fill Timing**
   *Files:* `style.css`, `editor-style.css`, `blocks/cta/style.css`, `standalone.html`
   *Issue:* CTA buttons still rendered as fully gradient-filled pills at rest with halo effects that implied an inner pill, and the hover text lacked the requested playful treatment.
   *Resolution:* Rebuilt the button styling to rest on a single dark pill that floods with a cyan-to-magenta gradient on hover, added glowing letter spacing animations, synced the editor and standalone previews, and updated CTA block overrides to avoid reintroducing the double-pill look.

### 2025-10-06 Sweep
1. **Navigation Glow Regression**
   *Files:* `style.css`
   *Issue:* The primary navigation reverted to muted white labels with a gradient underline, leaving the wobble animation visually inconsistent with the neon palette and lacking the requested cyan glow.
   *Resolution:* Set the default link color to the neon cyan token, tightened the underline to a solid cyan bar, and paired the wobble hover animation with a cyan-only pulse so the links stay readable while delivering the requested glow.

2. **Persistent Inner CTA Pills**
   *Files:* `style.css`, `editor-style.css`, `blocks/cta/style.css`, `standalone.html`
   *Issue:* CTA buttons still rendered a dark secondary pill beneath the gradient layer, producing the unwanted double-pill look in both the hero and CTA sections.
   *Resolution:* Redesigned the button styling to use a single gradient surface with a blurred halo, removed the inner outline, and synced the editor and standalone previews so the neon pill renders as one cohesive element.

### 2025-10-05 Sweep
1. **Navigation Hover Contrast Loss**
   *Files:* `style.css`
   *Issue:* The navigation wobble and pulse returned, but the gradient text fill rendered the links unreadable as it swept across each label.
   *Resolution:* Replaced the gradient text fill with a neon underline animation that preserves the wobble/pulse motion while keeping the text color solid for consistent legibility.

2. **CTA Alignment & Double-Pill Regression**
   *Files:* `blocks/cta/style.css`, `style.css`, `editor-style.css`
   *Issue:* The "Ready to Create?" heading drifted off-centre and the CTA buttons showed a second inner pill from the dormant gradient layer resting inside the outline.
   *Resolution:* Reset the CTA heading positioning in both front-end and editor styles and hid the gradient layer until interaction, eliminating the extra pill while keeping the hover sweep intact.

### 2025-10-04 Sweep
1. **Navigation Hover Animation Regression**
   *Files:* `style.css`
   *Issue:* The primary navigation lost its intended hover treatment—the links no longer pulsed or wobbled, and the gradient fill swept in the same blue-to-pink direction as the header instead of the requested reverse.
   *Resolution:* Restored the wobble and pulse animations, swapped the gradient to sweep pink-to-blue, and added a reduced-motion fallback so the effect respects accessibility preferences.

2. **CTA Layout Drift & Double-Pill Button**
   *Files:* `blocks/cta/style.css`, `style.css`
   *Issue:* The hero and CTA buttons displayed a second inner pill from the hover layer resting inside the outline, and the CTA section's content stack sat off-center with inconsistent vertical spacing.
   *Resolution:* Hid the gradient layer until interaction, re-centered the CTA container with flexible alignment, and synced spacing so the call-to-action headline and button align cleanly.

3. **Unwanted Section Dividers**
   *Files:* `style.css`
   *Issue:* Newly introduced borders on the header and generic `section` elements surfaced as stark white lines across the layout and at the bottom of the fixed header.
   *Resolution:* Removed the default section divider and header border, relying on existing background and glow effects for separation to eliminate the stray lines.

### 2025-10-03 Sweep
1. **Missing Dimensions Controls**
   *Files:* `theme.json`
   *Issue:* The theme's spacing presets existed, but global padding, margin, block gap, and unit flags were absent, so Gutenberg hid the Dimensions panel for custom sections and core blocks opting into spacing support.
   *Resolution:* Enabled the spacing feature flags and unit list in `settings.spacing` so the editor exposes padding, margin, and block gap controls backed by the defined presets.

### 2025-10-02 Sweep
1. **Template Edits Lost After Saving**
   *Files:* `blocks/about/editor.js`, `blocks/cta/editor.js`, `blocks/hero/editor.js`, `blocks/services/editor.js`, `blocks/service-card/editor.js`, `build/blocks/about/editor.js`, `build/blocks/cta/editor.js`, `build/blocks/hero/editor.js`, `build/blocks/services/editor.js`, `build/blocks/service-card/editor.js`
   *Issue:* All InnerBlocks-driven custom blocks returned `null` from their `save` implementation, so WordPress never stored the nested markup. Any edits made in the site editor appeared to save but were wiped on reload because the PHP render callbacks fell back to the original attributes.
   *Resolution:* Taught every block to return `<InnerBlocks.Content />` during save, ensuring nested content is serialized and replayed by the render callbacks so template, template part, and block edits persist.

### 2025-10-01 Sweep
1. **InnerBlocks Migration for Marketing Sections**
   *Files:* `blocks/hero/block.json`, `blocks/hero/editor.js`, `blocks/hero/render.php`, `blocks/hero/view.js`, `blocks/cta/block.json`, `blocks/cta/editor.js`, `blocks/cta/render.php`, `blocks/about/block.json`, `blocks/about/editor.js`, `blocks/about/render.php`, `blocks/services/block.json`, `blocks/services/editor.js`, `blocks/services/render.php`, `blocks/service-card/block.json`, `blocks/service-card/editor.js`, `blocks/service-card/render.php`, `blocks/cta/style.css`, `blocks/services/style.css`, `editor-style.css`, `style.css`
   *Issue:* The hero, CTA, about, services, and service-card blocks still relied on fixed attributes, leaving headings, paragraphs, and buttons locked to inspector fields while the services grid couldn't mix headings with cards without manual HTML edits.
   *Resolution:* Rebuilt each block on top of InnerBlocks templates with automated migrations that translate legacy attributes into nested core blocks, updated PHP renders to consume nested content while preserving fallbacks, and refreshed the global/editor styles so core button markup inherits the existing neon CTA treatment.

### 2025-09-30 Sweep
1. **Hero CTA Hover Contrast**
   *Files:* `style.css`
   *Issue:* The hero and post card CTA buttons turned their text dark as soon as hover started, but the gradient fill animation lagged behind, so the copy became unreadable on the dark background and there was no visible keyboard focus cue.
   *Resolution:* Delayed the text color swap until the gradient completes, extended the gradient layer so it matches the pill radius, and added a high-contrast focus outline so both mouse and keyboard interactions remain accessible.

2. **Hero Headline Word Breaks**
   *Files:* `blocks/hero/view.js`, `blocks/hero/style.css`
   *Issue:* The interactive glitch animation replaced spaces with non-breaking spans, so long headlines couldn't wrap between words and would overflow at smaller viewports.
   *Resolution:* Rebuilt the letter span logic to group characters per word and emit real whitespace/text nodes, then updated the hover selectors to target the new markup so the glitch effect and reduced-motion fallbacks continue to function.

### 2025-09-27 Sweep
1. **Placeholder Links on Homepage**
   *Files:* `patterns/home-landing.php`
   *Issue:* The service cards on the homepage contained placeholder links pointing to "example.com", which were not functional.
   *Resolution:* Removed the `linkUrl` attribute from the service card blocks, causing the links to render as static text and preventing user confusion.

2. **Inefficient Starfield Animation**
   *Files:* `style.css`
   *Issue:* The footer's starfield animation used the `transform` property, which is less performant for this type of continuous background animation.
   *Resolution:* Changed the `@keyframes` to animate `background-position` instead, resulting in a smoother and more efficient animation.

3. **Jarring Navigation Hover Animation**
   *Files:* `style.css`
   *Issue:* The main navigation links had multiple, conflicting, and infinite animations on hover, creating a visually jarring effect.
   *Resolution:* Simplified the hover effect to a single, subtle `text-shadow`, providing a more professional and polished user experience.

4. **Post Card Hover Layout Shift**
   *Files:* `style.css`
   *Issue:* Hovering over a post card caused a layout shift because the `transition: all` property was inefficient and affected layout-related properties.
   *Resolution:* Changed the transition to only affect `transform` and `border-color`, preventing any layout shifts on hover.

5. **Flawed Social Media Icon Logic**
   *Files:* `functions.php`
   *Issue:* The regex for matching social media URLs (e.g., for X/Twitter) was incorrect and would fail to identify some valid URLs.
   *Resolution:* Corrected the regular expressions to be more precise and handle all variations of the social media domains.

6. **Overly Aggressive SVG Sanitizer**
   *Files:* `functions.php`
   *Issue:* The SVG sanitizer was too strict and removed the `style` attribute from SVGs, which could break legitimate inline styling.
   *Resolution:* Modified the sanitizer to allow the `style` attribute, ensuring that SVGs render correctly while maintaining security.

7. **Duplicate "Home" Page Creation**
   *Files:* `functions.php`
   *Issue:* The theme's activation logic could create a duplicate "Home" page if one already existed.
   *Resolution:* Added a check to the page seeding logic to first search for an existing "Home" page by title before creating a new one.

8. **Inconsistent Color Usage**
   *Files:* `style.css`
   *Issue:* The stylesheet used a mix of hardcoded colors (e.g., `#222`) and CSS variables, making the theme difficult to skin and maintain.
   *Resolution:* Replaced all hardcoded color values with the appropriate CSS variables from the theme's color palette for consistency.

9. **Disorganized Z-Index Values**
   *Files:* `style.css`
   *Issue:* The CSS used arbitrary "magic numbers" for `z-index`, which made the stacking order of elements difficult to manage.
   *Resolution:* Defined a set of `z-index` variables in the `:root` and applied them throughout the stylesheet for better organization.

10. **Broken Hamburger Menu Animation**
    *Files:* `style.css`
    *Issue:* The mobile hamburger menu icon's middle bar disappeared instantly during the transition to a close icon, making the animation look broken.
    *Resolution:* Modified the CSS to animate the `box-shadow` property, creating a smooth transition where the middle bar appears to merge into the top bar.

### 2025-09-28 Sweep
1. **Undefined Z-Index Tokens**
   *Files:* `style.css`
   *Issue:* The stylesheet referenced `--z-index-background` and `--z-index-content` without defining them, producing invalid CSS variables.
   *Resolution:* Declared both tokens in `:root` alongside the existing z-index scale.

2. **Header Alignment Control Ignored**
   *Files:* `parts/header.html`
   *Issue:* The navigation block attempted to justify content using the invalid value `right`, so the menu never aligned properly.
   *Resolution:* Switched the setting to the supported `flex-end` value.

3. **Theme Version Out of Sync**
   *Files:* `style.css`, `readme.txt`
   *Issue:* The theme stylesheet still reported version 1.1.2 even though new fixes warranted a new release entry.
   *Resolution:* Bumped the version to 1.2.19 and documented the changes in the changelog.

4. **Hero Block Lost Alignment Support**
   *Files:* `blocks/hero/render.php`
   *Issue:* The render callback never passed the block instance to `get_block_wrapper_attributes()`, so alignment and anchor support were dropped on the front end.
   *Resolution:* Removed invalid second parameter to maintain proper WordPress API compatibility.

5. **Hero CTA Output Emitted Empty Elements**
   *Files:* `blocks/hero/render.php`
   *Issue:* Empty hero headings, descriptions, and CTA URLs rendered blank markup and unsafe `href` attributes.
   *Resolution:* Added empty checks for the headline/subheading and escaped button URLs before output.

6. **Hero Block Styles Leaked Globally**
   *Files:* `blocks/hero/style.css`
   *Issue:* Styles targeted the generic `.hero` selector, affecting any element with that class across the site.
   *Resolution:* Scoped the stylesheet to `.wp-block-mccullough-digital-hero`.

7. **Hero Editor Duplicated IDs**
   *Files:* `blocks/hero/editor.js`
   *Issue:* The block hard-coded `id="interactive-headline"`, producing duplicate IDs whenever multiple heroes were added.
   *Resolution:* Removed the unused `id` attribute from the editor component.

8. **About Block Editor Duplicated IDs**
   *Files:* `blocks/about/editor.js`
   *Issue:* The editor forced every block instance to share the `id="about"`, violating HTML uniqueness.
   *Resolution:* Let `useBlockProps()` manage the wrapper without a static ID.

9. **CTA Block Editor Duplicated IDs**
   *Files:* `blocks/cta/editor.js`
   *Issue:* Similar to the About block, the CTA editor hard-coded `id="contact"`.
   *Resolution:* Removed the static ID while preserving the contextual class name.

10. **Services Block Editor Missing Registration Import**
    *Files:* `blocks/services/editor.js`
    *Issue:* `registerBlockType` was used but never imported, breaking the build when the file was compiled.
    *Resolution:* Added the missing import.

11. **Services Block Editor Duplicated IDs**
    *Files:* `blocks/services/editor.js`
    *Issue:* Every services block rendered with the same `id="services"` attribute.
    *Resolution:* Removed the static ID by relying on `useBlockProps()` defaults.

12. **Services Block Saved Markup Twice**
    *Files:* `blocks/services/editor.js`
    *Issue:* The dynamic block returned `<InnerBlocks.Content />`, duplicating inner markup alongside the PHP render output.
    *Resolution:* Updated `save()` to return `null` as expected for dynamic blocks.

13. **Service CTA Links Not Escaped**
    *Files:* `blocks/cta/render.php`, `blocks/hero/render.php`, `blocks/service-card/render.php`
    *Issue:* CTA and service card links echoed pre-sanitized URLs without escaping, leaving edge cases unprotected.
    *Resolution:* Wrapped all outgoing URLs in `esc_url()`.

14. **Footer Social Icons Unstyled**
    *Files:* `style.css`
    *Issue:* CSS targeted `.social-link`, but WordPress outputs `.wp-social-link`, so hover styles never triggered.
    *Resolution:* Updated selectors to match core markup.

15. **Header Colors Ignored Theme Palette**
    *Files:* `style.css`
    *Issue:* Header and mobile menu text still used hard-coded `#fff`, preventing palette customisation.
    *Resolution:* Replaced them with the existing `--text-primary` variable.

16. **Hero CTA Span Rendered When Empty**
    *Files:* `blocks/hero/render.php`
    *Issue:* Even when no subheading existed, the block still output an empty paragraph.
    *Resolution:* Suppressed the paragraph unless meaningful content is present.

17. **Service Card Animation Ignores Motion Preferences**
    *Files:* `blocks/services/style.css`
    *Issue:* The glowing border animation ran even with `prefers-reduced-motion` enabled.
    *Resolution:* Disabled the animation when reduced motion is requested.

18. **SVG Sanitizer Removed Inline Styles**
    *Files:* `functions.php`
    *Issue:* The SVG sanitizer still stripped `style` attributes despite previous fixes.
    *Resolution:* Allowed `style` within the global attribute whitelist.

19. **Missing Hero CTA Escaping**
    *Files:* `blocks/hero/render.php`
    *Issue:* Hero CTAs used sanitized variables but never escaped during output.
    *Resolution:* Ensured links are properly escaped before rendering.

20. **Theme Header Z-Index Values Misapplied**
    *Files:* `style.css`
    *Issue:* Footer elements referenced `--z-index-content`, but the value was undefined, undermining stacking order.
    *Resolution:* Defined the token in the global variable set.

### 2025-09-29 Sweep
1. **Service Card Block Missing Inserter Support**
   *Files:* `blocks/service-card/block.json`
   *Issue:* The service card block was hidden from the inserter and could not be converted to reusable patterns, preventing authors from adding new cards in the editor.
   *Resolution:* Enabled the inserter and reusable block support so cards can be created and managed from list view.

2. **Service Card Block Lacked Design Controls**
   *Files:* `blocks/service-card/block.json`
   *Issue:* The block exposed no spacing, color, or typography controls, forcing authors to edit code for simple layout tweaks.
   *Resolution:* Added margin, padding, color, gradient, link, and typography support to match core block capabilities.

3. **Hero Block Lacked Design Controls**
   *Files:* `blocks/hero/block.json`
   *Issue:* The hero block ignored spacing and color tools, making it impossible to adjust padding or palette choices from the editor.
   *Resolution:* Enabled spacing, color, gradient, and typography support on the hero metadata.

4. **Services Block Lacked Design Controls**
   *Files:* `blocks/services/block.json`
   *Issue:* The services container could not be restyled without editing CSS because spacing and color controls were missing.
   *Resolution:* Opted the block into margin, padding, color, gradient, and typography tools.

5. **CTA Block Lacked Design Controls**
   *Files:* `blocks/cta/block.json`
   *Issue:* Editors could not tweak CTA spacing or text styling from the inspector.
   *Resolution:* Added full spacing, color, gradient, and typography support to the CTA metadata.

6. **About Block Lacked Design Controls**
   *Files:* `blocks/about/block.json`
   *Issue:* The about block was locked to its default spacing and palette, hindering layout customization.
   *Resolution:* Enabled margin, padding, color, gradient, and typography controls.

7. **Custom Blocks Missing Text Domains**
   *Files:* `blocks/about/block.json`, `blocks/cta/block.json`, `blocks/hero/block.json`, `blocks/service-card/block.json`, `blocks/services/block.json`
   *Issue:* None of the custom blocks declared the theme text domain, so translations for titles and descriptions could not load.
   *Resolution:* Added the `mccullough-digital` text domain to every block manifest.

8. **Surface Palette Tokens Missing**
   *Files:* `theme.json`
   *Issue:* No palette entries existed for surface backgrounds or borders, so components relied on hard-coded hex values.
   *Resolution:* Introduced `surface-dark` and `surface-border` tokens in the global palette.

9. **Hard-Coded Surface Colors in Front-End Styles**
   *Files:* `style.css`, `blocks/services/style.css`
   *Issue:* Service cards, post cards, tags, comment forms, and search inputs used fixed hex colors that ignored palette changes.
   *Resolution:* Swapped those declarations to the new surface tokens and text variables for consistent theming.

10. **CTA Button Text Ignored Palette**
    *Files:* `style.css`
    *Issue:* CTA buttons forced white text regardless of palette adjustments.
    *Resolution:* Switched the button text color to use `--text-primary`.

11. **Spacing Presets Missing for Page-Wide Template**
    *Files:* `theme.json`
    *Issue:* The template referenced `var(--wp--preset--spacing--50)` and `--40`, but no spacing presets existed, producing invalid CSS variables.
    *Resolution:* Added a spacing scale with the required preset sizes.

12. **Wide Align Width Undefined**
    *Files:* `theme.json`
    *Issue:* Align-wide controls defaulted to the content width because `layout.wideSize` was absent.
    *Resolution:* Declared a 1600px wide size to give wide/full blocks extra breathing room.

13. **Page-Wide Template Missing Site Content Wrapper**
    *Files:* `templates/page-wide.html`
    *Issue:* The layout omitted the `site-content` class and inner container, so header offsets and container helpers failed on the front end.
    *Resolution:* Wrapped the template in the expected `site-content` structure with a constrained inner container.

14. **Page-Wide Template Not Registered**
    *Files:* `theme.json`
    *Issue:* The wide layout could not be selected from the template picker because it lacked a `customTemplates` entry.
    *Resolution:* Registered the template with a title and description so editors can assign it to pages.

15. **404 Template Rendered Archive Loop**
    *Files:* `templates/404.html`
    *Issue:* The 404 template used the archive query loop and title, showing empty archive messaging instead of a proper not-found page.
    *Resolution:* Replaced it with a focused 404 layout, search prompt, and optional recent posts grid.

16. **Services Pattern Contained Placeholder Links**
    *Files:* `patterns/services-section.php`
    *Issue:* Each service card shipped with `linkUrl="#"`, generating non-functional anchors.
    *Resolution:* Removed the placeholder URLs so cards render as static text until authors supply real links.

17. **Footer Social Menu Used Placeholder Links**
    *Files:* `parts/footer.html`
    *Issue:* Footer social icons pointed to `#`, breaking navigation and harming accessibility.
    *Resolution:* Replaced the placeholders with real profile URLs for production-ready defaults.

18. **Legacy Bootstrap Duplicated Header/Footer**
    *Files:* `index.php`
    *Issue:* The PHP fallback called both `block_header_area()` and `block_template_part('header')`, rendering duplicate headers (and the same for footers).
    *Resolution:* Updated the fallback to prefer the block areas and only call template parts when those functions are unavailable.

19. **Editor Preview Lacked Palette & Alignment Helpers**
    *Files:* `editor-style.css`
    *Issue:* Editor styles still used hard-coded surface colors and offered no wide/full container utilities, so previews diverged from the front end.
    *Resolution:* Mapped editor surfaces to the new tokens and added the wide/full container helper classes.

20. **Editor CTA Button Border Ignored Palette**
    *Files:* `editor-style.css`
    *Issue:* The editor-side CTA button retained a white border regardless of palette changes.
    *Resolution:* Updated the border color to use the primary text token.

## Improvements
1. **Extensible Social Icon Function**
   *Files:* `functions.php`
   *Issue:* The function for retrieving social media icons was rigid and could not be easily extended to include new social networks.
   *Resolution:* Introduced a new filter (`mcd_social_link_svg_patterns`) that allows developers to add new social icons and their matching logic without modifying the theme's core files.
2. **Home Landing Layout Now Seeds Page Content**
   *Files:* `functions.php`, `templates/front-page.html`, `templates/home-landing.html`, `patterns/home-landing.php`, `theme.json`
   *Issue:* The home landing template was automatically applied through `front-page.html`, preventing authors from editing the landing layout within the standard page editor.
   *Resolution:* Converted the front-page template to render page content, registered an optional "Home Landing" template, and seeded the landing pattern into the Home page content during activation so it stays fully editable from the page editor.
3. **Enhanced Developer Experience**
   *Files:* `webpack.config.js`
   *Issue:* Missing source maps made debugging compiled JavaScript difficult during development.
   *Resolution:* Added source map generation for both development (`eval-source-map`) and production (`source-map`) builds to improve debugging capabilities.

## Documentation Updates
- `readme.txt` now includes the 1.2.19 changelog with all recent fixes.
- `AGENTS.md` summarizes both previous and latest bug sweeps with clear categorization.
- This `bug-report.md` consolidates all fixes from 2025-09-27 through 2025-09-29.
