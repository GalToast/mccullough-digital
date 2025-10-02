import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    RichText,
    BlockControls,
    URLInputButton,
    InspectorControls,
} from '@wordpress/block-editor';
import {
    PanelBody,
    ToggleControl,
    Notice,
    ToolbarGroup,
} from '@wordpress/components';

import metadata from './block.json';

registerBlockType( metadata.name, {
    ...metadata,
    edit( { attributes, setAttributes } ) {
        const { buttonText, buttonLink, opensInNewTab } = attributes;
        const blockProps = useBlockProps( {
            className: 'mcd-button-block',
        } );

        const buttonBaseClass = 'cta-button hero__cta-button';
        const commonButtonProps = {
            className: buttonBaseClass,
        };

        if ( buttonLink ) {
            commonButtonProps.href = buttonLink;
            if ( opensInNewTab ) {
                commonButtonProps.target = '_blank';
                commonButtonProps.rel = 'noopener';
            }
        }

        const ButtonTag = buttonLink ? 'a' : 'button';

        return (
            <>
                <BlockControls>
                    <ToolbarGroup>
                        <URLInputButton
                            url={ buttonLink }
                            onChange={ ( url ) =>
                                setAttributes( { buttonLink: url ?? '' } )
                            }
                        />
                    </ToolbarGroup>
                </BlockControls>
                <InspectorControls>
                    <PanelBody title={ __('Button Settings', 'mccullough-digital') }>
                        <ToggleControl
                            label={ __('Open in new tab', 'mccullough-digital') }
                            checked={ opensInNewTab }
                            onChange={ (value) => setAttributes({ opensInNewTab: value }) }
                            help={ __('Adds target="_blank" and rel="noopener" when a URL is set.', 'mccullough-digital') }
                            disabled={ ! buttonLink }
                        />
                    </PanelBody>
                </InspectorControls>
                <div { ...blockProps }>
                    <ButtonTag { ...commonButtonProps } type={ buttonLink ? undefined : 'button' }>
                        <RichText
                            tagName="span"
                            className="hero__cta-button-label"
                            value={ buttonText }
                            onChange={ (value) => setAttributes({ buttonText: value }) }
                            allowedFormats={ [] }
                            placeholder={ __('Add button text…', 'mccullough-digital') }
                        />
                    </ButtonTag>
                    { ! buttonLink && (
                        <Notice
                            status="info"
                            isDismissible={ false }
                            className="mcd-button-block__link-notice"
                        >
                            { __('Add a link from the toolbar to output an anchor element on the front end.', 'mccullough-digital') }
                        </Notice>
                    ) }
                </div>
            </>
        );
    },
    save() {
        return null;
    },
});
