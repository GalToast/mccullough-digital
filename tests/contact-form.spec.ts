import { test, expect } from '@playwright/test';

test.describe( 'Contact intake form', () => {
	test( 'form renders with required fields', async ( { page } ) => {
		await page.goto( '/contact/#project-intake', {
			waitUntil: 'domcontentloaded',
			timeout: 60000,
		} );
		const form = page.locator( 'form.mcd-contact-form' );
		await expect( form ).toBeVisible();

		await expect( form.locator( 'input[name="name"]' ) ).toBeVisible();
		await expect( form.locator( 'input[name="email"]' ) ).toBeVisible();
		await expect( form.locator( 'textarea[name="goals"]' ) ).toBeVisible();

		const submitButton = form.getByRole( 'button', {
			name: /Send my project details/i,
		} );
		await expect( submitButton ).toBeVisible();
	} );
} );
