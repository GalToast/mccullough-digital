# Bug Fix Report — Opened 2025-09-27 (Last updated 2025-11-23 — CTA block hero fallback)

This rolling QA log tracks production-impacting fixes and follow-up checks for the McCullough Digital theme. Use it to understand **when** a regression was addressed, what still needs verification, and where to find the detailed release notes.

- Canonical changelog: see the `= 1.2.x =` entries in [`readme.txt`](readme.txt).
- Contributor workflow guidelines: refer to [`AGENTS.md`](AGENTS.md).

## Monitoring Scope
- WordPress core version: 6.9 (verified).
- PHP baseline: 7.4.
- Focus areas: fixed masthead offsets, blog archive loop experience, reusable neon CTA components.

## Recent Sweeps (November 2025)
- **2025-11-23 — CTA block hero fallback**
  - Result: Fallback markup now renders the `.cta-button.hero__cta-button` anchor/span pair and CTA block gradients skip the hero class so default buttons inherit the neon sweep styling instead of reverting to the legacy pill.
  - Follow-up: Re-test the CTA block in the Site Editor after rebuilding assets to confirm the hero sweep loads and legacy `.cta-button` variants keep their gradient.
- **2025-11-22 — Hero CTA gradient sweep sync**
  - Result: Updated the hero block, reusable CTA button block, and editor styles to use the slimmer gradient slide pill without the extra glow layer so both contexts render the provided hover animation consistently.
  - Follow-up: Re-test the hero block in a fresh template and confirm the Site Editor loads the revised CSS after the next build deploy.
- **2025-11-21 — Neon button child-theme parity**
  - Result: Block registration now scans both child and parent theme `blocks/` directories and enqueues button assets via `get_theme_file_*()` so the neon CTA renders with styling on front-end views when a child theme is active.
  - Follow-up: Smoke-test on a production child theme after the next deploy to confirm the button outputs markup + CSS and that child overrides continue to win when present.
- **2025-11-20 — Footer column spacing + separator removal**
  - Result: Let the neon footer grid keep the branding column snug, centred the navigation and contact columns, and removed the decorative separator so the logo/tagline stack no longer shows a faint horizontal line.
  - Follow-up: Reconfirm layout balance after adding or removing footer columns in the Site Editor and ensure custom separators elsewhere remain unaffected.
- **2025-11-19 — Footer starfield overlay restore**
  - Result: Reintroduced the `stars` layer markup and inner `.footer-container` wrapper so the neon footer regains its animated background and centred layout.
  - Follow-up: Spot-check the Site Editor to confirm the HTML block persists and verify `prefers-reduced-motion` continues to disable the twinkle.
- **2025-11-18 — Footer Plan A streamlining**
  - Result: Removed the hero-style headline from the neon footer, rebuilt the layout into balanced company, quick links, and connect columns, and synced editor/front-end styles for the slimmer structure.
  - Follow-up: Re-test footer navigation hover/focus states after the next design polish pass and confirm the contact list adapts if a phone number is added.
- **2025-11-17 — About slug helper + mobile nav + archive polish**
  - Result: Added an `mcd_get_about_page_url()` helper so the home CTA and neon footer follow renamed About pages, restored the core navigation responsive toggles on ≤768px screens, aligned blog card thumbnails to a 16:9 frame, and expanded service card CTAs to 44px touch targets.
  - Follow-up: Confirm translations extend the About helper candidate lists and re-test the hamburger toggle after future header layout tweaks.
- **2025-11-16 — Home seeding retry + dynamic footer links**
  - Result: Home-page seeding now resolves the intended page deterministically, only clears the option after successful updates/inserts, and the neon footer/about patterns generate quick links with `home_url()` so subdirectory installs avoid broken slugs.
  - Follow-up: Re-test on a fresh activation once translations/localized slugs are available to confirm the helpers cover non-English installations.
- **2025-11-15 — Hero fallback neon CTA label**
  - Result: Hero block PHP fallback now pulls the neon button's metadata default when the saved label is blank, so legacy hero content and cached renders surface the CTA with "Start a Project" even before inner blocks resave.
  - Follow-up: Confirm after translating the default label or when patterns change their seeded CTA copy.
- **2025-11-14 — Neon button editor regression sweep**
  - Result: Editor now keeps the neon button clickable without leaving the editor when a link is set, and it restores the default "Start a Project" label whenever previously saved buttons load with an empty attribute.
  - Follow-up: Re-check after translating the default label or updating block editor RichText behaviour around empty strings.
- **2025-11-13 — Neon button default label fallback**
  - Result: Dynamic render now loads the block metadata to backfill CTA text, so published neon buttons always surface the default "Start a Project" label when authors save an empty field.
  - Follow-up: Re-test after translating block strings or changing the default label in `block.json`.
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
