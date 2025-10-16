import { registerBlockType, createBlock } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

import metadata from './block.json';

const defaultHeadline = metadata?.attributes?.headline?.default ?? '';
const defaultButtonText =
	metadata?.attributes?.buttonText?.default ??
	__( "Let's Talk", 'mccullough-digital' );

const {
	innerBlocks: {
		allowedBlocks: allowedCtaBlocks = [
			'core/heading',
			'core/paragraph',
			'core/list',
			'core/buttons',
			'core/group',
			'core/image',
			'mccullough-digital/button',
		],
		template: ctaTemplate = [
			[
				'core/heading',
				{
					level: 2,
					className: 'section-title',
					placeholder: __(
						'Add CTA headline…',
						'mccullough-digital'
					),
					content: defaultHeadline,
				},
			],
		],
		templateLock: ctaTemplateLock = false,
	} = {},
} = metadata;

registerBlockType( metadata.name, {
	...metadata,
	edit: function Edit( { attributes, clientId } ) {
		const { headline, buttonText, buttonLink } = attributes;
		const blockProps = useBlockProps( {
			className: 'cta-section',
		} );

		const hasMigrated = useRef( false );
		const { innerBlocks = [] } = useSelect(
			( select ) => ( {
				innerBlocks:
					select( 'core/block-editor' ).getBlocks( clientId ),
			} ),
			[ clientId ]
		);
		const { replaceInnerBlocks } = useDispatch( 'core/block-editor' );

		useEffect( () => {
			if ( hasMigrated.current ) {
				return;
			}

			if ( innerBlocks.length > 0 ) {
				hasMigrated.current = true;
				return;
			}

			const generatedBlocks = [];

			if ( headline ) {
				generatedBlocks.push(
					createBlock( 'core/heading', {
						level: 2,
						className: 'section-title',
						content: headline,
					} )
				);
			}

			if ( buttonText || buttonLink ) {
				const neonButton = createBlock( 'mccullough-digital/button', {
					buttonText: buttonText || defaultButtonText,
					buttonLink: buttonLink || '',
				} );

				generatedBlocks.push( neonButton );
			}

			if ( generatedBlocks.length === 0 ) {
				return;
			}

			replaceInnerBlocks( clientId, generatedBlocks, false );
			hasMigrated.current = true;
		}, [
			buttonLink,
			buttonText,
			clientId,
			headline,
			innerBlocks.length,
			replaceInnerBlocks,
		] );

		return (
			<>
				<section { ...blockProps }>
					<div className="container">
						<InnerBlocks
							allowedBlocks={ allowedCtaBlocks }
							template={ ctaTemplate }
							templateLock={ ctaTemplateLock }
						/>
					</div>
				</section>
			</>
		);
	},
	save() {
		return <InnerBlocks.Content />;
	},
} );
