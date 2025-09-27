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
        const { headline, subheading, buttonText, buttonLink } = attributes;
        const blockProps = useBlockProps({
            className: 'hero',
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
                    <div
                        className="hero-canvas-placeholder"
                        aria-hidden="true"
                        role="presentation"
                    />
                    <div className="hero-content">
                        <RichText
                            tagName="h1"
                            id="interactive-headline"
                            className="wp-block-heading"
                            value={ headline }
                            onChange={ (value) => setAttributes({ headline: value }) }
                            placeholder={ __('Add headline…', 'mccullough-digital') }
                        />
                        <RichText
                            tagName="p"
                            value={ subheading }
                            onChange={ (value) => setAttributes({ subheading: value }) }
                            placeholder={ __('Add subheading…', 'mccullough-digital') }
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
