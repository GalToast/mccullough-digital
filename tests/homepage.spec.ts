import { test, expect } from '@playwright/test';

test.describe('Homepage smoke checks', () => {
  test('hero renders neon headline and CTA', async ({ page }) => {
    await page.goto('/');
    const headline = page.locator('.wp-block-mccullough-digital-hero .hero__headline');
    await expect(headline).toContainText(/Digital Vision/i);
    const cta = page.locator('.wp-block-mccullough-digital-hero .hero__cta-button');
    await expect(cta).toBeVisible();
  });

  test('services section visible with cards', async ({ page }) => {
    await page.goto('/');
    const servicesSection = page.locator('.services-section-v2');
    await expect(servicesSection).toBeVisible();
    await expect(servicesSection.locator('.service-card-v2').first()).toBeVisible();
  });
});
