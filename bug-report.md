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

## Resolved Bugs (2025-09-27)

The following issues identified during the latest sweep have been fixed:

- **Dynamic Blocks Overrode Custom Anchors and Duplicated IDs:** Updated About, Services, and CTA block renderers and editor scripts to stop forcing hard-coded IDs and respect author-provided anchors.
- **Inline Formatting Removed from Block Headings and Copy:** The About, CTA, and Service Card blocks now preserve rich text markup when rendering headings and body text.
- **Blocks Emitted Dead `#` Links:** Hero, CTA, and Service Card blocks no longer emit placeholder `href="#"` attributes. Buttons without URLs render as static visuals instead of focusable controls.
- **Home Pattern Seeder Reliability Issues:** The seeding routine now ignores unpublished `home` pages, creates a published placeholder when needed, and populates existing empty front pages with the default layout.
- **Section Alignment Constraints:** Section blocks add alignment-aware wrapper classes and styles so `alignwide` and `alignfull` layouts expand as expected.
- **Post Card Pattern Styling:** Button styles now target the actual Read More anchor, restoring the intended appearance.
- **Placeholder Links in Defaults:** Block defaults and the home landing pattern use meaningful URLs or static content, eliminating dead links on fresh installs.
