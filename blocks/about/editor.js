import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

import metadata from './block.json';

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, setAttributes }) {
        const { headline, text } = attributes;
        const blockProps = useBlockProps();

        return (
            <section {...blockProps}>
                <div className="container">
                    <RichText
                        tagName="h2"
                        className="section-title"
                        value={ headline }
                        onChange={ (value) => setAttributes({ headline: value }) }
                        placeholder={ __('Add headline…', 'mccullough-digital') }
                    />
                    <RichText
                        tagName="p"
                        value={ text }
                        onChange={ (value) => setAttributes({ text: value }) }
                        placeholder={ __('Add text…', 'mccullough-digital') }
                    />
                </div>
            </section>
        );
    },
    save() {
        return null;
    },
});
