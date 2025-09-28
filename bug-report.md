# Bug Fix Report — 2025-09-27

This sweep resolved ten production-impacting defects and introduced one code quality improvement in the McCullough Digital theme. Each item below lists the affected files, the observed problem, and the implemented remedy.

## Fixed Bugs
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
- `readme.txt` now highlights key features and logs version 1.2.0 of the theme.
- `AGENTS.md` has been updated with the latest bug fixes and a new instruction to always keep documentation in sync.
- This `bug-report.md` has been updated to reflect the latest changes.