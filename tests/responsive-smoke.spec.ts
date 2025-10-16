import { test, expect } from '@playwright/test';

const mobileViewport = { width: 375, height: 812 };

const assertNoHorizontalOverflow = async ( page: Parameters<typeof test>[ 0 ][ 0 ] ) => {
	const overflow = await page.evaluate( () => {
		const doc = document.scrollingElement ?? document.documentElement;
		return Math.max( 0, doc.scrollWidth - document.documentElement.clientWidth );
	} );

	expect.soft( overflow, 'Horizontal overflow detected' ).toBeLessThanOrEqual( 1 );
};

test.describe( 'Narrow viewport smoke', () => {
	test.use( { viewport: mobileViewport } );

	test( 'services cards stack cleanly', async ( { page } ) => {
		await page.goto( '/services/?cache-bust=4', {
			waitUntil: 'networkidle',
			timeout: 60_000,
		} );

		await assertNoHorizontalOverflow( page );

		const gridColumnCount = await page.evaluate( () => {
			const grid = document.querySelector<HTMLElement>(
				'#mdl-services .grid.grid-cols-3'
			);
			if ( ! grid ) {
				return null;
			}
			const template = window
				.getComputedStyle( grid )
				.getPropertyValue( 'grid-template-columns' );
			return template
				.split( ' ' )
				.filter( ( token ) => token.trim().length > 0 ).length;
		} );

		expect.soft( gridColumnCount, 'Service grid did not collapse to single column' ).toBe( 1 );

		const cardOverflow = await page.evaluate( () => {
			const cards = Array.from(
				document.querySelectorAll<HTMLElement>( '#mdl-services .card' )
			);
			if ( ! cards.length ) {
				return null;
			}
			const maxRight = Math.max(
				...cards.map( ( card ) => card.getBoundingClientRect().right )
			);
			return Math.max( 0, maxRight - window.innerWidth );
		} );

		expect
			.soft( cardOverflow, 'Service card content leaks horizontally' )
			.toBeLessThanOrEqual( 1 );
	} );

	test( 'contact intake fits narrow view', async ( { page } ) => {
		await page.goto( '/contact/?cache-bust=4#project-intake', {
			waitUntil: 'networkidle',
			timeout: 60_000,
		} );

		await assertNoHorizontalOverflow( page );

		const formOverflow = await page.evaluate( () => {
			const form = document.querySelector<HTMLElement>( '.mcd-contact-form' );
			if ( ! form ) {
				return null;
			}
			return Math.max( 0, form.scrollWidth - form.clientWidth );
		} );

		expect.soft( formOverflow, 'Contact form overflows horizontally' ).toBeLessThanOrEqual( 1 );

		const labelOverflow = await page.evaluate( () => {
			const labels = Array.from(
				document.querySelectorAll<HTMLElement>( '.mcd-contact-form__label' )
			);
			if ( ! labels.length ) {
				return null;
			}
			const maxRight = Math.max(
				...labels.map( ( label ) => label.getBoundingClientRect().right )
			);
			return Math.max( 0, maxRight - window.innerWidth );
		} );

		expect
			.soft( labelOverflow, 'Contact labels overflow their column' )
			.toBeLessThanOrEqual( 1 );
	} );

	test( 'homepage FAQ stays contained', async ( { page } ) => {
		await page.goto( '/?cache-bust=4', {
			waitUntil: 'networkidle',
			timeout: 60_000,
		} );

		await assertNoHorizontalOverflow( page );

		const faqOverflow = await page.evaluate( () => {
			const elements = Array.from(
				document.querySelectorAll<HTMLElement>(
					'.mcd-home__faq-items details, .mcd-home__faq-items summary'
				)
			);
			if ( ! elements.length ) {
				return null;
			}
			const maxRight = Math.max(
				...elements.map( ( element ) => element.getBoundingClientRect().right )
			);
			return Math.max( 0, maxRight - window.innerWidth );
		} );

		expect
			.soft( faqOverflow, 'FAQ accordions overflow horizontally' )
			.toBeLessThanOrEqual( 1 );
	} );
} );
