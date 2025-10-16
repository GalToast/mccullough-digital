# Staging Verification Notes

1. **Masthead**
   - Confirm navigation overlay opens/closes on ≤768px.
   - Ensure “Start a Project” CTA jumps to `/contact/#project-intake` and page scrolls correctly.

2. **Services / Contact Forms**
   - Submit both forms; verify lead entry + email + GA4 DebugView events per `docs/ga4-launch-checklist.md`.
   - Confirm required-field inline validation surfaces immediately (name, email, project goals) and clears once the field is corrected.
   - After a successful submission, confirm the thank-you status message appears and the neon card keeps its success highlight.
   - Check the opt-in copy reads “Optional — … Skipping this does not impact my project estimate.”

3. **Footer**
   - Test each quick link (About, Blog, Case Studies, Contact, Home, Services) and social URL to ensure staging or production targets are correct.

4. **Regression Suite**
   - Run `npm run build` (if not already) and `npm run test:e2e -- --project=chromium` with `https://mcculloug-digital-2.local/` set as the Playwright base URL.
   - Manual responsive sweep: hero animations, Services pricing band, footer starfield, contact intake (include smooth-scroll + focus behavior from Services/About CTAs).

5. **Bug Log**
   - Update `bug-report.md` with staging verification date + outstanding items.
