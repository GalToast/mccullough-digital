import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';
import fs from 'node:fs';
import path from 'node:path';

type AuditTarget = {
	name: string;
	url: string;
};

const auditTargets: AuditTarget[] = [
	{ name: 'Homepage', url: '/?cache-bust=3' },
	{ name: 'Services', url: '/services/?cache-bust=3' },
	{ name: 'Contact', url: '/contact/?cache-bust=3' },
];

const outputDir = path.resolve(
	__dirname,
	'../../../../../../logs/accessibility'
);

test.describe('Color contrast audits', () => {
	for ( const { name, url } of auditTargets ) {
		test(`axe color-contrast: ${ name }`, async ( { page }, testInfo ) => {
			await page.goto( url, {
				waitUntil: 'networkidle',
				timeout: 60_000,
			} );

			const axe = new AxeBuilder( { page } ).withRules( [
				'color-contrast',
			] );

			const results = await axe.analyze();
			const violations = results.violations ?? [];

			await testInfo.attach( `axe-color-contrast-${ name }`, {
				body: JSON.stringify( results, null, 2 ),
				contentType: 'application/json',
			} );

			fs.mkdirSync( outputDir, { recursive: true } );
			const filename = `color-contrast-${ name
				.toLowerCase()
				.replace( /\s+/g, '-' ) }.json`;
			fs.writeFileSync(
				path.join( outputDir, filename ),
				JSON.stringify( results, null, 2 ),
				'utf8'
			);

			console.log(
				`[axe][${ name }] ${ violations.length } color-contrast violation(s)`
			);
			for ( const violation of violations ) {
				const targets = violation.nodes
					.map( ( node ) => node.target.join( ' ' ) )
					.join( ' | ' );
				console.log(
					`[axe][${ name }] ${ violation.id }: ${ violation.help } (impact: ${ violation.impact }) => ${ targets }`
				);
			}

			expect
				.soft(
					violations,
					`${ name } contains color-contrast violations`
				)
				.toEqual( [] );
		} );
	}
} );
