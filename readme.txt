=== McCullough Digital ===
Contributors: McCullough Digital
Requires at least: 5.0
Tested up to: 6.0
Requires PHP: 7.4
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom theme scaffold with fixed header, mobile menu, and simple template hierarchy.

== Description ==

Custom theme scaffold with fixed header, mobile menu, and simple template hierarchy.

== Installation ==

1. Upload the `mccullough-digital` directory to the `/wp-content/themes/` directory.
2. Activate the theme through the 'Themes' menu in WordPress.

== Frequently Asked Questions ==

= Does this theme support widgets? =

This theme does not have any widget areas registered by default.

== Changelog ==

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
