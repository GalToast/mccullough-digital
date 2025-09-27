import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

import metadata from './block.json';

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, setAttributes }) {
        const { headline } = attributes;
        const blockProps = useBlockProps();

        return (
            <section {...blockProps}>
                <RichText
                    tagName="h2"
                    value={ headline }
                    onChange={ (value) => setAttributes({ headline: value }) }
                    placeholder={ __('Add services headline…', 'mccullough-digital') }
                />
            </section>
        );
    },
    save() {
        return null;
    },
});
