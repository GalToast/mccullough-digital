# Bug Fix Report — 2025-09-28

This sweep resolved ten production-impacting defects in the McCullough Digital theme. Each item below lists the affected files, the observed problem, and the implemented remedy.

## Fixed Bugs
1. **Header offset failed after layout shifts**  
   *Files:* `js/header-scripts.js`, `style.css`  
   *Issue:* Opening the navigation, resizing the viewport, or loading web fonts changed the header height without updating the `--mcd-header-offset` custom property, causing content to slip under the masthead.  
   *Resolution:* Added a `ResizeObserver`, font loading listener, and bfcache `pageshow` hook to recalculate the offset and keep content aligned.

2. **Header observers leaked between navigations**  
   *Files:* `js/header-scripts.js`  
   *Issue:* The header script left mutation observers attached when navigating away, allowing callbacks to fire on detached nodes.  
   *Resolution:* Disconnect observers and remove font listeners during `unload` to prevent leaks in single-page navigation scenarios.

3. **Hero headline destroyed accessible text**  
   *Files:* `blocks/hero/render.php`, `blocks/hero/view.js`, `blocks/hero/style.css`  
   *Issue:* The animation replaced every character with `<span>` elements, causing screen readers to announce letters individually.  
   *Resolution:* Wrapped headline copy in `.hero__headline-text`, cloned a `.screen-reader-text` version for assistive tech, and generated decorative spans only for the visual layer.

4. **Hero animation crashed on legacy browsers**  
   *Files:* `blocks/hero/view.js`  
   *Issue:* Browsers without `NodeFilter` or `createTreeWalker` support threw runtime errors when the hero initialised.  
   *Resolution:* Guarded the animation against missing DOM APIs and skip span generation when the feature set is incomplete.

5. **Missing shared screen-reader utility**  
   *Files:* `style.css`, `standalone.html`  
   *Issue:* The theme duplicated ad-hoc visually hidden rules, leading to inconsistent behaviour across blocks.  
   *Resolution:* Introduced a reusable `.screen-reader-text` helper for both the WordPress theme and the standalone preview.

6. **PHP 8 libxml deprecation warnings**  
   *Files:* `functions.php`  
   *Issue:* Calling `libxml_disable_entity_loader()` triggered deprecation notices on PHP 8, polluting logs when sanitising SVGs.  
   *Resolution:* Only disable the entity loader on PHP versions where the function is supported, preserving security without warnings.

7. **Standalone preview font hints were malformed**  
   *Files:* `standalone.html`  
   *Issue:* Fonts were linked without preload hints, delaying hero rendering and ignoring reduced-motion scroll preferences.  
   *Resolution:* Added proper `preload`/`noscript` tags and wrapped `scroll-behavior` in a media query matching the production stylesheet.

8. **Standalone hero diverged from production markup**  
   *Files:* `standalone.html`  
   *Issue:* The preview used bespoke headline markup and particle code, leaving accessibility fixes unrepresented.  
   *Resolution:* Mirrored the production hero structure, loaded the shared hero script, and exposed the `.wp-block-mccullough-digital-hero` class so animations stay in sync.

9. **Standalone service cards exposed decorative icons and dead links**  
   *Files:* `standalone.html`  
   *Issue:* Icons were announced by assistive tech and `href="#"` placeholders created empty focus targets.  
   *Resolution:* Marked icons as presentational and rendered static `.is-static` text when no destination is available.

10. **Standalone navigation toggle lost ARIA state**  
    *Files:* `standalone.html`  
    *Issue:* The preview’s mobile menu left `aria-expanded` out of sync with the visual menu state.  
    *Resolution:* Centralised toggle logic that syncs the button state, CSS classes, and collapse behaviour, while delegating scroll handling to the production header script.

## Documentation Updates
- `readme.txt` now highlights key features and logs version 1.1.2 of the theme.
- `AGENTS.md` summarises the new workflow and the bug fixes above for future contributors.
