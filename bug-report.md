# Bug Fix Report — 2025-09-28

This report now tracks both the 2025-09-27 and 2025-09-28 sweeps, covering thirty production-impacting fixes and one code quality improvement in the McCullough Digital theme. Each item below lists the affected files, the observed problem, and the implemented remedy.

## Fixed Bugs

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
   *Resolution:* Bumped the version to 1.2.2 and documented the changes in the changelog.

4. **Hero Block Lost Alignment Support**
   *Files:* `blocks/hero/render.php`
   *Issue:* The render callback never passed the block instance to `get_block_wrapper_attributes()`, so alignment and anchor support were dropped on the front end.
   *Resolution:* Forwarded the `$block` context when building wrapper attributes.

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

## Improvements
1. **Extensible Social Icon Function**
   *Files:* `functions.php`
   *Issue:* The function for retrieving social media icons was rigid and could not be easily extended to include new social networks.
   *Resolution:* Introduced a new filter (`mcd_social_link_svg_patterns`) that allows developers to add new social icons and their matching logic without modifying the theme's core files.
2. **Home Landing Layout Now Seeds Page Content**
   *Files:* `functions.php`, `templates/front-page.html`, `templates/home-landing.html`, `patterns/home-landing.php`, `theme.json`
   *Issue:* The home landing template was automatically applied through `front-page.html`, preventing authors from editing the landing layout within the standard page editor.
   *Resolution:* Converted the front-page template to render page content, registered an optional "Home Landing" template, and seeded the landing pattern into the Home page content during activation so it stays fully editable from the page editor.

## Documentation Updates
- `readme.txt` now includes the 1.2.2 changelog alongside earlier notes.
- `AGENTS.md` summarises both the previous and latest bug sweeps.
- This `bug-report.md` consolidates the 2025-09-27 and 2025-09-28 fixes.