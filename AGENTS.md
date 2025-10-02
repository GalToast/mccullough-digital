# Agent Notes (Updated 2025-11-13)

This repository contains the McCullough Digital block theme. Use these notes as an onboarding primer and quick reference while working on the theme.

## Build & QA Checklist
1. Run `npm install` to install block tooling.
2. Use `npm run start` while developing blocks so assets rebuild automatically.
3. Finish with `npm run build` before committing production bundles.
4. Document shipped work by updating the changelog in `readme.txt` and noting QA outcomes in `bug-report.md`.

## Documentation Sources
- **`readme.txt`** — Canonical changelog and release notes. Every merged fix or enhancement should land here first.
- **`bug-report.md`** — Rolling QA log that captures dates, affected areas, and any follow-up checks still pending.
- **`Web and Graphic Design 101.md`** — Shared design guidance covering typography, layout, accessibility, and visual direction.

Keep these three documents in sync conceptually: add detailed release bullets to `readme.txt`, summarize QA verification in `bug-report.md`, and reference the latest areas of focus below for active investigations.

## Current Focus (November 2025)
- Verify the admin toolbar offset token keeps logged-in views aligned across front end, editor, and standalone previews.
- Monitor the new blog archive loop block for editor preview regressions or pagination edge cases.
- Ensure masthead logo uploads respect the sizing clamp and do not reintroduce header offset jumps.

## Development Notes
- The theme requires WordPress 5.0+ and PHP 7.4+.
- Build assets are tracked in `build/blocks/*/editor.js`.
- Block registrations are handled automatically via `functions.php`.
- Custom blocks support InnerBlocks for flexible content composition.
- All animations respect `prefers-reduced-motion` user preferences.
- Theme uses CSS custom properties for consistent theming.
- SVG sanitization allows safe inline styles while preventing XSS.

Consult the canonical changelog in `readme.txt` whenever you need the full history of recent changes.
