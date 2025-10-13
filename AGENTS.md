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

## Contextual Assets (2025-10-12)
- `AI ASSISTANT RESOURCES` is the canonical drop of user-provided collateral. Treat it as read/copy only -- do not add, delete, or edit files there.
- `AI CONTEXT FOR PROJECT` mirrors those resources in an organized structure with all archives already expanded for day-to-day use. Make edits against this workspace copy if adjustments are required.
- When bringing in new collateral, place copies inside `AI CONTEXT FOR PROJECT` while leaving `AI ASSISTANT RESOURCES` untouched so future updates can be diffed easily.

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

## Collaboration Notes (2025-10-07)
- Work is being reviewed locally in a browser; always list the steps the user should take to see or validate our changes (e.g., hard refresh, clear cache, navigate to section).
- When we change assets or CSS, remind the user to purge any WordPress caching layer they might have enabled (currently Local WP, no extra build steps required).
- `Shared-pad.txt` mirrors the homepage markup so we can quote snippets without shell paste issues.

## Database Operations (2025-10-08)
- Fresh exports: run `php ..\wp-cli.phar db export ..\sql\local-wp-export.sql --host=127.0.0.1 --port=10012 --user=root --pass=root --default-character-set=utf8mb4` from `app/public`. The command will overwrite the existing export, so we avoid piling up old `.sql` snapshots.
- Direct inspection: use `mysql.exe --host=127.0.0.1 --port=10012 --user=root --password=root --database=local --execute="SELECT ID, post_title, post_status, post_name FROM wp_posts WHERE post_type='page';"` to confirm live page rows. Use `--batch --raw --skip-column-names` to dump `post_content` when needed (e.g., saved to `app/sql/home-post-content.html`).
- Environment prerequisites: prepend `C:\Users\fredj\AppData\Roaming\Local\lightning-services\mysql-8.0.35+4\bin\win64\bin` to `PATH` before invoking WP-CLI so `mysqldump` is available. The PHP binary lives at `lightning-services\php-8.2.27+1\bin\win64\php.exe`.
- Clean-up: keep only the latest export in `app/sql` (currently `local-wp-export.sql`) and delete ad-hoc content dumps like `home-post-content.html` once they have been reviewed or inlined into documentation to prevent file bloat.

## Tooling Notes (2025-10-08)
- WP-CLI wrapper scripts live in `app\wp.cmd` and `app\wp.ps1`; both point at the Local PHP binary plus the custom ini at `conf\php\php.ini` (contains `mysqli`/`pdo_mysql`). Invoke from `app` (e.g., `.\wp.cmd post list`) and CLI will auto-target `app/public`.
- When adding new CLI automation, use the `DB_HOST` value `127.0.0.1:10012` (defined in `wp-config.php`) so commands hit the running Local MySQL instance.
- Playwright E2E scaffolding is checked in: config at `playwright.config.ts` and smoke/navigation specs under `tests/`. Run with `npm run test:e2e` (headless) or `npm run test:e2e:ui`. For a fast sanity cycle use `npm run test:e2e -- --project=chromium`.
- Playwright navigation specs assume the primary menu exposes “Home / Blog / About Us”. If you see flaky cross-browser runs, execute the stable subset with `npm run test:e2e -- --project=chromium` and confirm Local is serving over HTTP (`http://mcculloug-digital-2.local`). Clean up `test-results/` after runs to keep the tree tidy.
