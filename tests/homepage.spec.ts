import { test, expect } from '@playwright/test';

test.describe( 'Homepage smoke checks', () => {
	test( 'hero renders neon headline and CTA', async ( { page } ) => {
		await page.goto( '/', {
			waitUntil: 'domcontentloaded',
			timeout: 60000,
		} );
		const hero = page.locator( '.mcd-home__hero' );
		await expect( hero ).toBeVisible();
		const headline = hero.getByRole( 'heading', {
			level: 1,
			name: /Websites that purr/i,
		} );
		await expect( headline ).toBeVisible();
		const primaryCta = hero.getByRole( 'link', {
			name: 'Get Pricing & Timeline',
			exact: true,
		} );
		await expect( primaryCta ).toBeVisible();
		await expect( primaryCta ).toHaveAttribute(
			'href',
			'/contact/#project-intake'
		);
		const secondaryCta = hero.getByRole( 'link', {
			name: /^See Packages$/i,
		} );
		await expect( secondaryCta ).toBeVisible();
	} );

	test( 'services section visible with cards', async ( { page } ) => {
		await page.goto( '/', {
			waitUntil: 'domcontentloaded',
			timeout: 60000,
		} );
		const packagesSection = page.locator( '#packages' );
		await expect( packagesSection ).toBeVisible();
		const packageCards = packagesSection.locator( '.mcd-package-card' );
		await expect( packageCards ).toHaveCount( 3 );
		await expect(
			packageCards.first().getByRole( 'heading', { level: 3 } )
		).toBeVisible();
	} );
} );
