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
        const { icon, title, text, linkText, linkUrl } = attributes;
        const blockProps = useBlockProps();

        return (
            <>
                <InspectorControls>
                    <PanelBody title={ __('Link Settings', 'mccullough-digital') }>
                        <TextControl
                            label={ __('Link Text', 'mccullough-digital') }
                            value={ linkText }
                            onChange={ (value) => setAttributes({ linkText: value }) }
                        />
                        <TextControl
                            label={ __('Link URL', 'mccullough-digital') }
                            value={ linkUrl }
                            onChange={ (value) => setAttributes({ linkUrl: value }) }
                        />
                    </PanelBody>
                </InspectorControls>
                <article { ...blockProps }>
                    <RichText
                        tagName="div"
                        className="icon"
                        value={ icon }
                        onChange={ (value) => setAttributes({ icon: value }) }
                        placeholder={ __('Add icon…', 'mccullough-digital') }
                        allowedFormats={ [] }
                    />
                    <RichText
                        tagName="h3"
                        value={ title }
                        onChange={ (value) => setAttributes({ title: value }) }
                        placeholder={ __('Add title…', 'mccullough-digital') }
                    />
                    <RichText
                        tagName="p"
                        value={ text }
                        onChange={ (value) => setAttributes({ text: value }) }
                        placeholder={ __('Add description…', 'mccullough-digital') }
                    />
                    <div className="service-card-link-preview">
                        <span className="service-card-link-text">{ linkText }</span>
                        <span className="service-card-link-url">{ linkUrl }</span>
                    </div>
                </article>
            </>
        );
    },
    save() {
        return null;
    },
});
