# Bug Fix Report — Opened 2025-09-27 (Last updated 2025-11-13)

This rolling QA log tracks production-impacting fixes and follow-up checks for the McCullough Digital theme. Use it to understand **when** a regression was addressed, what still needs verification, and where to find the detailed release notes.

- Canonical changelog: see the `= 1.2.x =` entries in [`readme.txt`](readme.txt).
- Contributor workflow guidelines: refer to [`AGENTS.md`](AGENTS.md).

## Monitoring Scope
- WordPress core version: 6.9 (verified).
- PHP baseline: 7.4.
- Focus areas: fixed masthead offsets, blog archive loop experience, reusable neon CTA components.

## Recent Sweeps (November 2025)
- **2025-11-13 — Admin toolbar alignment**
  - Result: CSS token and runtime script keep logged-in views clear of the toolbar across front end, editor, and standalone preview.
  - Follow-up: Re-test after any header height adjustments.
- **2025-11-12 — Header fallback offset**
  - Result: Increased base `--header-height` to 100px so content clears the fixed masthead even before JavaScript loads.
  - Follow-up: Confirm on slower devices or when scripts are blocked.
- **2025-11-11 — Blog archive loop & hero padding**
  - Result: Dynamic loop block now previews inside the Site Editor and the archive hero spacing matches the fixed header offset.
  - Follow-up: Monitor pagination and category pill filters for regressions.
- **2025-11-09 — Blog hero glitch parity & badge guard**
  - Result: Archive title mirrors the front-page glitch treatment and the "Most Recent" badge only appears on the first page of the main query.
  - Follow-up: Check badge logic after query-related changes.
- **2025-11-08 — Masthead logo clamp**
  - Result: Oversized uploads respect `--logo-size-header` without expanding the masthead.
  - Follow-up: Validate after any logo upload workflow updates.
- **2025-11-07 — Neon blog archive template**
  - Result: Archive and index templates now match the approved neon layout with aligned editor styles.
  - Follow-up: Ensure template overrides created before 1.2.38 receive the update notes.

## QA Checklist
- [x] Rebuilt assets with `npm run build` after each sweep listed above.
- [x] Confirmed Site Editor parity for header offsets and neon CTA treatments.
- [ ] Pending: regression sweep for category pill keyboard focus states (scheduled next release).

## Historical Reference
Sweeps prior to November 2025, including the hero CTA rebuild and footer refresh work completed between 2025-09-27 and 2025-11-06, are archived in the changelog section of `readme.txt`. Review that file for the full narrative when auditing older bugs.
