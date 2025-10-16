# Launch Readiness Checklist

## Status (2025-10-13)
- ✅ Services/contact intake blocks verified locally (REST submission, email notification, lead storage).
- ✅ Masthead CTA now points to `/contact/#project-intake`; navigation overlay restored.
- ✅ Playwright suite (Chromium/Firefox/WebKit) passing; Chromium navigation spec marked flaky once and noted in `bug-report.md`.
- ✅ Theme version aligned to `1.2.44` across `style.css`, `readme.txt`, and `package.json`.
- ⏳ GA4 wiring and staging verification remain pending (see sections below).

## 1. Content & UX
- ❑ Review homepage, About, Services, Case Studies, Contact for final copy/CTA approvals.
- ❑ Confirm Services CTA buttons point to `/contact/#project-intake` and pricing copy matches latest packages.
- ❑ Ensure primary CTAs (masthead, Services hero, intake submit) use the burnt neon gradient, include focus outlines, and remain legible against dark backgrounds.
- ❑ Validate footer quick links/social URLs (Facebook, Instagram, LinkedIn, YouTube, Support) are correct.
- ❑ Capture updated screenshots for handoff documentation.

## 2. Forms & Analytics
- ❑ Staging: submit Services and Contact forms; confirm `mcd_lead` entry, email notification, and GA4 DebugView events (`lead_form_view`, `mcd_contact_success`).
- ❑ Confirm inline validation appears on required fields, clears on correction, and the success notice displays after submission.
- ❑ Verify opt-in copy states the newsletter is optional and does not affect estimates.
- ❑ Configure GA4 Measurement ID or GTM container using `docs/ga4-launch-checklist.md`.
- ❑ Verify Mail delivery to hello@mccullough.digital via production SMTP.

## 3. QA & Testing
- ❑ Run `npm run build` and `npm run test:e2e -- --project=chromium` on staging build (Playwright base URL: `https://mcculloug-digital-2.local/`).
- ❑ Perform manual responsive check (desktop, tablet, mobile) for masthead overlay, Services form, footer.
- ❑ Clear WordPress/Local caches and re-test hero animations with `prefers-reduced-motion`.

## 4. Deployment
- ❑ Create production backup (db + wp-content) before theme deploy.
- ❑ Deploy theme bundle (exclude `node_modules/`, `test-results/`, `docs/` if not needed).
- ❑ Flush permalinks, regenerate assets if required, and re-run smoke tests post-deploy.
- ❑ Update `readme.txt` changelog version/date prior to tagging release.

## 5. Post-Launch
- ❑ Monitor GA4 Exploration for form events; set 24-hour alert on zero counts.
- ❑ Check hello@mccullough.digital for real submissions and follow SLA.
- ❑ Update `bug-report.md` with launch date + any hotfix follow-ups.

