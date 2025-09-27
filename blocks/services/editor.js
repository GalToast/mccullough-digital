import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InnerBlocks } from '@wordpress/block-editor';

import metadata from './block.json';

const {
    innerBlocks: {
        allowedBlocks: allowedServiceBlocks = [
            'mccullough-digital/service-card',
        ],
        template: servicesTemplate = [
            ['mccullough-digital/service-card'],
            ['mccullough-digital/service-card'],
            ['mccullough-digital/service-card'],
        ],
    } = {},
} = metadata;

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, setAttributes }) {
        const { headline } = attributes;
        const blockProps = useBlockProps({
            id: 'services',
        });

        return (
            <section {...blockProps}>
                <div className="container">
                    <RichText
                        tagName="h2"
                        className="section-title"
                        value={ headline }
                        onChange={ (value) => setAttributes({ headline: value }) }
                        placeholder={ __('Add services headline…', 'mccullough-digital') }
                    />
                    <div className="services-grid">
                        <InnerBlocks
                            allowedBlocks={ allowedServiceBlocks }
                            template={ servicesTemplate }
                        />
                    </div>
                </div>
            </section>
        );
    },
    save() {
        return null;
    },
});
