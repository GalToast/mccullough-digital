# McCullough Digital Delivery Platform

AI-first client delivery system and custom WordPress block theme for McCullough Digital.

This repository is the public code surface for a real client-delivery workflow: strategy, custom frontend implementation, analytics-aware lead capture, accessibility, QA, and AI-assisted content/search positioning. The WordPress package details remain in `readme.txt`; this README explains the product and hiring signal.

## What This Demonstrates

- **AI-first delivery workflow:** AI-assisted planning, copy, QA, search positioning, and implementation review wrapped around a real business website.
- **Client product judgment:** Market segmentation, conversion paths, services pages, contact intake, case-study content, and measurable campaign outcomes.
- **Custom WordPress engineering:** Block theme architecture, custom blocks, editor/front-end parity, template hierarchy, and production build tooling.
- **Lead capture automation:** Contact intake validates and rate-limits submissions, stores leads, fires GA4-friendly events, and notifies the team.
- **Quality and accessibility discipline:** Reduced-motion support, semantic markup, SVG sanitization, placeholder-link cleanup, responsive smoke tests, and visual QA notes.
- **Search and AI visibility:** Technical SEO, schema/content structure, and generative-engine optimization work intended to make business content legible to both search engines and AI assistants.

## Client Proof Points

### ARES Construction: 7-Day Digital Foundation

ARES is represented here as sanitized delivery proof only. Raw client assets, backups, staging exports, and private handoff files are intentionally not published.

Reported outcomes from that delivery lane include:

- 0-to-production launch in under 7 days, from first build work to live client handoff.
- 81+ project/media items organized, optimized, and made usable for a construction portfolio.
- AI-assisted asset cleanup and visual refinement used to make real project imagery presentation-ready.
- Custom gallery/admin workflow for managing project priority and ongoing site updates.
- 13-document handoff package covering security, SEO, runbooks, maintenance, and ownership transfer.
- Written client feedback praised the look, speed, and customer service of the delivery.

### Onmark LLC: Growth and Visibility System

The project also includes an Onmark LLC case study in `docs/onmark-case-study.md`. Reported outcomes from that delivery lane include:

- Page 1 organic ranking for "solid surface countertops houston" within 120 days.
- Top 5 ChatGPT result for core Houston solid-surface intent.
- Frequent Google AI Overview visibility for related service searches.
- Paid ads paused by September 2025 after organic and referral demand improved.
- July paid-launch metrics: $19.02 average CPL, $14.87 Facebook Lead Ads CPL, and $0.21 average CPC across 2,305 paid clicks.

## Architecture

```text
AI-assisted strategy and content
  -> custom WordPress block theme
  -> accessible service and CTA surfaces
  -> analytics-aware contact intake
  -> QA and visual verification
  -> client-facing business outcomes
```

## Repository Map

| Path | Purpose |
| --- | --- |
| `functions.php` | Theme setup, assets, block registration, lead/contact plumbing, and platform behavior |
| `blocks/` | Custom block source for hero, CTA, services, blog loop, and related UI surfaces |
| `src/` | Block editor/source assets compiled into production bundles |
| `templates/`, `parts/`, `patterns/` | WordPress full-site editing templates and reusable content structures |
| `style.css`, `editor-style.css` | Front-end and editor styling |
| `tests/`, `playwright.config.ts` | Browser and UI verification scaffolding |
| `docs/onmark-case-study.md` | Client outcome and campaign proof points |
| `readme.txt` | WordPress package readme and changelog |

## Local Development

```bash
npm install
npm run build
npm run start
```

Compiled block assets are written to `build/blocks/*/editor.js` and referenced by each block's `block.json`.

## Why It Matters

For AI-adjacent roles, this repo shows the practical side of AI-assisted delivery: not a demo prompt, but a shipped business surface with custom code, measurement, QA, lead capture, and client outcomes.
