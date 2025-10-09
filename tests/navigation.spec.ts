import { test, expect } from '@playwright/test';

test.describe.configure({ mode: 'serial' });

test.describe('Primary navigation', () => {
  test('Blog link routes from home to blog and back', async ({ page }) => {
    await page.goto('/', { waitUntil: 'networkidle' });
    const nav = page.locator('nav').first();
    await nav.getByRole('link', { name: 'Blog', exact: true }).click();
    await expect(page).toHaveURL(/\/blog\/$/);
    await nav.getByRole('link', { name: 'Home', exact: true }).click();
    await expect(page).toHaveURL(/\/$/);
    await expect(page.locator('.wp-block-mccullough-digital-hero .hero__headline')).toContainText(/Digital Vision/i);
  });

  test('About link routes to the About Us page', async ({ page }) => {
    await page.goto('/', { waitUntil: 'networkidle' });
    const nav = page.locator('nav').first();
    await nav.getByRole('link', { name: /About/i }).click();
    await expect(page).toHaveURL(/\/about-us\/$/);
    await expect(page.getByRole('heading', { name: /About/i })).toBeVisible();
  });
});
