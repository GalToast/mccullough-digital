# Bug Sweep Report - McCullough Digital Theme

**Date:** 2025-09-27

This report documents ten issues uncovered during the latest review of the McCullough Digital WordPress theme. Each item below notes the affected code, the incorrect behaviour that was observed, and the potential impact on editors or visitors.

---

## Confirmed Bugs (Incorrect Behavior)

These problems directly affected usability, accessibility, or runtime stability.

### 1. Header Offset Broke After Layout Changes
- **Files:** `style.css`, `js/header-scripts.js`
- **Description:** The fixed header height was hard-coded to `80px`. When the header grew taller (e.g., due to responsive wrapping or the mobile menu opening), page content slipped underneath it and became unreadable.
- **Impact:** Visitors could no longer see the top of the page content after resizing or opening the mobile navigation.

### 2. Header Script Crashed Without `matchMedia`
- **File:** `js/header-scripts.js`
- **Description:** The header behaviour relied on `window.matchMedia` without guarding for browsers where the API is missing.
- **Impact:** Older browsers threw a runtime error, preventing the rest of the theme JavaScript from executing.

### 3. Header Hid While Navigating With the Keyboard
- **File:** `js/header-scripts.js`
- **Description:** The hide-on-scroll logic ignored keyboard focus. Tabbing into the navigation while scrolled down left the header hidden off-screen.
- **Impact:** Keyboard users were forced to interact with invisible controls, creating an accessibility failure.

### 4. Hero Animation Crashed on Legacy Browsers
- **File:** `blocks/hero/view.js`
- **Description:** The hero animation also assumed `window.matchMedia` was available.
- **Impact:** On browsers without that API the script halted before initialising the headline animation or the particle field.

### 5. Hero Headline Animation Broke Screen Readers
- **Files:** `blocks/hero/view.js`, `blocks/hero/render.php`
- **Description:** Splitting every headline character into individual `<span>` elements caused screen readers to announce each letter separately, and the decorative canvas lacked a presentational role.
- **Impact:** Assistive technology delivered nonsensical output, harming accessibility.

### 6. SVG Sanitiser Removed Legitimate Icons
- **File:** `functions.php`
- **Description:** `mcd_sanitize_svg()` only allowed a small set of tags and attributes. Icons that used gradients, symbols, or `<use>` references were stripped to empty markup.
- **Impact:** Social and block icons that relied on gradients or shared symbols failed to render.

### 7. Service Card Block Emitted Inaccessible Markup
- **File:** `blocks/service-card/render.php`
- **Description:** Decorative SVGs were exposed to assistive tech and an anchor tag was rendered even when no link text was provided.
- **Impact:** Screen reader users heard stray “graphic” announcements, and empty links created WCAG violations.

### 8. CTA Block Rendered Empty Buttons
- **File:** `blocks/cta/render.php`
- **Description:** The call-to-action button appeared even when the author left the label blank.
- **Impact:** Visitors encountered focusable controls with no name, failing accessibility guidelines and confusing users.

### 9. Standalone Preview Missed Font Optimisations
- **File:** `standalone.html`
- **Description:** The static preview page contained malformed `<link>` elements and depended on `overflow-x: hidden` to mask layout issues.
- **Impact:** Previewing the theme locally showed fallback fonts and could still hide underlying layout bugs.

---

## Code Quality & Performance Issues

### 1. Services Block Re-read Metadata on Every Render
- **File:** `blocks/services/render.php`
- **Description:** The render callback decoded `block.json` on every request even though the data was not used.
- **Impact:** The extra file I/O slowed page generation and complicated future maintenance.

---

## Additional Confirmed Bugs (2025-09-27)

These issues were discovered during the latest sweep and have not yet been addressed.

### 1. Dynamic Blocks Override Custom Anchors and Duplicate IDs
- **Files:** `blocks/about/block.json`, `blocks/about/render.php`, `build/blocks/about/editor.js`, `blocks/services/block.json`, `blocks/services/render.php`, `build/blocks/services/editor.js`, `blocks/cta/block.json`, `blocks/cta/render.php`, `build/blocks/cta/editor.js`
- **Description:** The About, Services, and CTA blocks advertise anchor support but hard-code wrapper IDs such as `about`, `services`, and `contact`. The editor scripts also force these IDs. As a result, user-defined anchors are ignored and multiple instances of a block render duplicate IDs on the same page.
- **Impact:** Editors cannot set unique anchors, in-page links target the wrong section, and duplicate IDs break accessibility expectations for assistive technology.

### 2. About Block Headline Escapes Legitimate Formatting
- **File:** `blocks/about/render.php`
- **Description:** The About block renders its headline with `esc_html()`, stripping inline markup supplied by the RichText editor (for example emphasis or links).
- **Impact:** Content creators lose visual and semantic formatting in headings, reducing expressiveness and accessibility.

### 3. CTA Block Headline Escapes Legitimate Formatting
- **File:** `blocks/cta/render.php`
- **Description:** The CTA block also uses `esc_html()` for its headline, removing inline formatting choices from authors.
- **Impact:** Important text styling such as emphasis or inline links cannot persist to the front end.

### 4. Service Card Block Removes Links and Styling from Body Copy
- **File:** `blocks/service-card/render.php`
- **Description:** Both the service title and description are passed through `esc_html()`, which strips markup created in the editor, including links.
- **Impact:** Editors cannot highlight keywords or link to service details, harming usability and SEO.

### 5. CTA Buttons Render Dead `#` Links When No URL Is Provided
- **Files:** `blocks/hero/render.php`, `blocks/cta/render.php`, `blocks/service-card/render.php`
- **Description:** When authors omit a URL, the blocks fall back to `href="#"` while still showing an active button.
- **Impact:** Visitors encounter focusable elements that do nothing, confusing keyboard and screen reader users and failing accessibility guidelines.

### 6. Home Pattern Seeder Can Promote Unpublished Pages to the Front Page
- **File:** `functions.php`
- **Description:** When a site already has a page with the slug `home`, the seeding routine assigns it as the front page without verifying that it is published.
- **Impact:** Draft or private pages can become the designated front page, producing front-end errors or exposing unfinished content.

### 7. Home Pattern Seeder Skips Populating Existing Empty Pages
- **File:** `functions.php`
- **Description:** If a `home` page exists, the function sets it as the front page and exits before checking its content, so empty pages remain blank instead of receiving the landing layout.
- **Impact:** New theme installs can ship with an empty homepage, forcing manual intervention.

### 8. Section Blocks Ignore Align Settings Because of Fixed `.container` Widths
- **Files:** `blocks/about/render.php`, `blocks/services/render.php`, `blocks/cta/render.php`, `style.css`
- **Description:** Each block wraps its content in a `.container` div that enforces a 1200px max width and 90% viewport margin, negating the advertised `alignwide` and `alignfull` options.
- **Impact:** Editors cannot create edge-to-edge sections or wider layouts, limiting design flexibility.

### 9. Post Card Pattern Styles the Wrapper Instead of the Read More Link
- **Files:** `patterns/post-card-grid.php`, `style.css`
- **Description:** The pattern adds the `.cta-button` class to the `core/read-more` wrapper paragraph, but the styling targets anchor elements.
- **Impact:** The “Read more” link looks unstyled because the anchor does not receive the button styles, degrading visual hierarchy and affordance.

### 10. Default Patterns Ship with Placeholder `#` Links
- **Files:** `patterns/home-landing.php`, `blocks/service-card/block.json`, `blocks/hero/block.json`
- **Description:** The bundled homepage pattern and block defaults populate CTA URLs with `#`, so a fresh install exposes multiple dead links even before authors edit content.
- **Impact:** The live homepage contains non-functional buttons, confusing visitors and harming credibility until every URL is manually updated.
