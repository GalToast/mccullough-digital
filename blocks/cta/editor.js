import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    RichText,
    InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

import metadata from './block.json';

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, setAttributes }) {
        const { headline, buttonText, buttonLink } = attributes;
        const blockProps = useBlockProps({
            id: 'contact',
            className: 'cta-section',
        });

        return (
            <>
                <InspectorControls>
                    <PanelBody title={ __('Button Settings', 'mccullough-digital') }>
                        <TextControl
                            label={ __('Button Text', 'mccullough-digital') }
                            value={ buttonText }
                            onChange={ (value) => setAttributes({ buttonText: value }) }
                        />
                        <TextControl
                            label={ __('Button Link', 'mccullough-digital') }
                            value={ buttonLink }
                            onChange={ (value) => setAttributes({ buttonLink: value }) }
                        />
                    </PanelBody>
                </InspectorControls>
                <section {...blockProps}>
                    <div className="container">
                        <RichText
                            tagName="h2"
                            className="section-title"
                            value={ headline }
                            onChange={ (value) => setAttributes({ headline: value }) }
                            placeholder={ __('Add headline…', 'mccullough-digital') }
                        />
                        <a
                            className="cta-button"
                            href={ buttonLink || '#' }
                            onClick={ (event) => event.preventDefault() }
                        >
                            <span className="btn-text">
                                { buttonText || __('Add button text…', 'mccullough-digital') }
                            </span>
                        </a>
                    </div>
                </section>
            </>
        );
    },
    save() {
        return null;
    },
});
