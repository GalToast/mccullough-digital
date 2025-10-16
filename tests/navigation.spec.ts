import { test, expect } from '@playwright/test';

test.describe.configure( { mode: 'serial', timeout: 90000 } );

test.describe( 'Primary navigation', () => {
	test( 'Blog link routes from home to blog and back', async ( { page } ) => {
		await page.goto( '/', {
			waitUntil: 'domcontentloaded',
			timeout: 60000,
		} );

		const headerNav = page.locator( 'header .main-navigation' );
		const blogLink = headerNav.getByRole( 'link', {
			name: 'Blog',
			exact: true,
		} );
		await expect( blogLink ).toBeVisible();

		await blogLink.click();
		await expect( page ).toHaveURL( /\/blog\/?$/, { timeout: 60000 } );
		await expect(
			page.getByRole( 'heading', {
				level: 1,
				name: /Ideas with a neon edge/i,
			} )
		).toBeVisible( { timeout: 10000 } );

		const homeLink = page
			.locator( 'header .main-navigation' )
			.getByRole( 'link', { name: 'Home', exact: true } );
		await expect( homeLink ).toBeVisible();

		await homeLink.click();
		await expect( page ).toHaveURL( /\/$/, { timeout: 60000 } );
		await expect(
			page.getByRole( 'heading', {
				level: 1,
				name: /Websites that purr/i,
			} )
		).toBeVisible( { timeout: 10000 } );
	} );

	test( 'About link routes to the About Us page', async ( { page } ) => {
		await page.goto( '/', {
			waitUntil: 'domcontentloaded',
			timeout: 60000,
		} );
		const aboutLink = page
			.locator( 'header .main-navigation' )
			.getByRole( 'link', { name: /^About Us$/i } );
		await expect( aboutLink ).toBeVisible();
		await aboutLink.click();
		await expect( page ).toHaveURL( /\/about-us\/$/, { timeout: 60000 } );
		await expect(
			page.getByRole( 'heading', { name: /Small by design/i } )
		).toBeVisible( { timeout: 10000 } );
	} );
} );
