# Bug Sweep Report - McCullough Digital Theme

**Date:** 2025-09-27

This report details the findings of a comprehensive bug sweep conducted on the McCullough Digital WordPress theme. The investigation focused on identifying incorrect behavior, security vulnerabilities, and code quality issues.

Below is a list of confirmed bugs and code quality improvements that were identified.

---

## Confirmed Bugs (Incorrect Behavior)

These are issues where the theme does not behave as expected, leading to a flawed user experience.

### 1. Social Icon Domain Matching is Fragile

-   **File:** `functions.php`
-   **Function:** `mcd_get_social_link_svg()`
-   **Description:** The logic to extract a domain name from a URL is too simplistic. It assumes the domain is always the last two segments of the hostname (e.g., `example.com`). This fails for domains with country-code top-level domains like `twitter.co.uk`, which is incorrectly processed as `co.uk`.
-   **Impact:** Social media icons will not appear for any site that doesn't use a simple `.com`, `.org`, etc., domain structure.

### 2. Header Visibility Breaks on Window Resize

-   **File:** `js/header-scripts.js`
-   **Description:** The script that hides the header on scroll-down calculates the header's height only once when the page first loads. If the browser window is resized, the header's height might change, but the script continues to use the original, outdated height value.
-   **Impact:** After resizing the browser, the header may hide too early, too late, or not at all, creating a jarring visual effect.

### 3. Block-based Mobile Menu Icon State is Unreliable

-   **File:** `js/header-scripts.js`
-   **Function:** `initBlockMenu()`
-   **Description:** The visual state of the mobile menu's hamburger icon (changing from three lines to an "X") is not synchronized with the actual state of the menu. The icon's state is toggled on click, but if the menu is closed by another action (like clicking a navigation link), the icon does not reset.
-   **Impact:** The user might see a "close" icon even when the menu is already closed, which is confusing.

---

## Code Quality & Security Issues

These are not user-facing bugs but represent areas where the code is inefficient, hard to maintain, or potentially insecure.

### 1. Insufficient SVG Sanitization

-   **File:** `functions.php`
-   **Function:** `mcd_sanitize_svg()`
-   **Description:** The function to sanitize uploaded SVGs is too basic. It only removes `<script>` tags and `on*` event attributes. It does not protect against more sophisticated Cross-Site Scripting (XSS) vectors that can exist in SVGs.
-   **Impact:** This poses a potential security vulnerability. If a user with file upload permissions (like an author or editor) uploads a maliciously crafted SVG, it could execute malicious scripts in the browsers of visitors or administrators.

### 2. `overflow-x: hidden` Masks Layout Issues

-   **File:** `style.css`
-   **Selector:** `body`
-   **Description:** The stylesheet applies `overflow-x: hidden` to the main `<body>` tag. This is generally considered a "code smell" because it hides the symptom (a horizontal scrollbar) instead of fixing the root cause (an element that is wider than the viewport).
-   **Impact:** This can make the theme difficult to debug and may cause content to be clipped or hidden on certain screen sizes without any indication that something is wrong.

### 3. Hardcoded SVGs in CSS

-   **File:** `style.css`
-   **Selectors:** `.stars`, `.stars2`, `.stars3`
-   **Description:** The animated starfield background in the footer is created using large, data-URI-encoded SVGs placed directly within the CSS file.
-   **Impact:** This unnecessarily bloats the main stylesheet, increasing page load times. It also makes the star graphics impossible for browsers to cache separately and very difficult for developers to edit or maintain.