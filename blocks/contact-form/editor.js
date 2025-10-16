import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import {
	PanelBody,
	TextControl,
	ToggleControl,
	TextareaControl,
} from '@wordpress/components';

import metadata from './block.json';

registerBlockType( metadata.name, {
	...metadata,
	edit: function Edit( { attributes, setAttributes } ) {
		const { buttonText, successMessage, showOptIn } = attributes;
		const blockProps = useBlockProps();

		return (
			<>
				<InspectorControls>
					<PanelBody
						title={ __( 'Form Settings', 'mccullough-digital' ) }
						initialOpen={ true }
					>
						<TextControl
							label={ __(
								'Submit button text',
								'mccullough-digital'
							) }
							value={ buttonText }
							onChange={ ( value ) =>
								setAttributes( { buttonText: value } )
							}
							help={ __(
								'Keep it action oriented and specific.',
								'mccullough-digital'
							) }
						/>
						<TextareaControl
							label={ __(
								'Success message',
								'mccullough-digital'
							) }
							value={ successMessage }
							onChange={ ( value ) =>
								setAttributes( { successMessage: value } )
							}
							help={ __(
								'Displayed after the form submits successfully.',
								'mccullough-digital'
							) }
						/>
						<ToggleControl
							label={ __(
								'Show monthly tips opt-in',
								'mccullough-digital'
							) }
							checked={ !! showOptIn }
							onChange={ ( value ) =>
								setAttributes( { showOptIn: !! value } )
							}
							help={ __(
								'Adds a light-touch checkbox for newsletter tips.',
								'mccullough-digital'
							) }
						/>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<ServerSideRender
						block={ metadata.name }
						attributes={ attributes }
					/>
				</div>
			</>
		);
	},
	save() {
		return null;
	},
} );
