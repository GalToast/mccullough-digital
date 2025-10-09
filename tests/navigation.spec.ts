import { test, expect } from '@playwright/test';

test.describe('Primary navigation', () => {
  test('logo link navigates home', async ({ page }) => {
    await page.goto('/blog');
    await page.locator('.site-branding a').first().click();
    await expect(page).toHaveURL(/\/$/);
    await expect(page.locator('.wp-block-mccullough-digital-hero .hero__headline')).toContainText(/Digital Vision/i);
  });

  test('services link jumps to section', async ({ page }) => {
    await page.goto('/');
    await page.locator('a[href="#services"]').first().click();
    await page.waitForTimeout(500); // allow scroll
    const servicesHeading = page.locator('.services-section-v2 .section-title');
    await expect(servicesHeading).toBeVisible();
    await expect(page).toHaveURL(/#services/);
  });
});
