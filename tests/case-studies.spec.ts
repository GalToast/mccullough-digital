import { test, expect } from '@playwright/test';

test.describe( 'Case Studies index experience', () => {
	test( 'spotlights the OnMark case study card', async ( { page } ) => {
		await page.goto( '/case-studies/', {
			waitUntil: 'domcontentloaded',
			timeout: 60000,
		} );

		const cards = page.locator( '.case-study-card' );
		await expect( cards ).toHaveCount( 1 );

		const card = cards.first();
		await expect( card ).toBeVisible();

		const segmentChip = card.getByRole( 'link', {
			name: /Growth Sprint/i,
		} );
		await expect( segmentChip ).toBeVisible();

		const title = card.getByRole( 'heading', {
			level: 3,
			name: 'OnMark (Houston) Digital Transformation',
		} );
		await expect( title ).toBeVisible();

		const excerpt = card.getByText(
			'Houston fabricator with zero digital footprint to commercial market leader that paused paid ads in six months.',
			{ exact: true }
		);
		await expect( excerpt ).toBeVisible();

		const viewCaseStudy = card.getByRole( 'link', {
			name: /View case study/i,
		} );
		await expect( viewCaseStudy ).toBeVisible();
		await expect( viewCaseStudy ).toHaveAttribute(
			'href',
			/onmark-houston-digital-transformation\/?$/
		);
	} );
} );
