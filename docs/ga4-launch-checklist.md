# GA4 Verification Checklist (Pre-Launch)

1. **Create/Confirm Property**
   - Create a GA4 property (or use existing) for McCullough Digital.
   - Note the Measurement ID (`G-XXXXXXX`).

2. **Connect via GTM or Direct Tag**
   - Preferred: add the Measurement ID to your Google Tag Manager container and publish a new version.
   - Direct option: use Site Kit → Analytics to paste the Measurement ID if GTM is not available.

3. **Enable Debugging**
   - Open the site with GA DebugView (GA4 Debugger extension) or GTM Preview mode to watch events in real time.

4. **Validate Core Events**
   - Load `/services/` and `/contact/`; confirm the dataLayer records `lead_form_view` on render.
   - Submit each form and watch for `mcd_contact_success` (success) or `mcd_contact_error` (failure) in DebugView.

5. **Document Results**
   - Capture DebugView screenshots and note the verification date in `bug-report.md`.

6. **Post-Launch Monitoring**
   - Build a GA4 Exploration filtered by `event_name` in (`lead_form_view`, `mcd_contact_success`).
   - Optional: configure an alert for 24-hour periods with zero form events.
