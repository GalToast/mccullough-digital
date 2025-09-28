import { registerBlockType, createBlock } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import {
    useBlockProps,
    InnerBlocks,
} from '@wordpress/block-editor';

import metadata from './block.json';

const defaultHeadline = metadata?.attributes?.headline?.default ?? '';
const defaultSubheading = metadata?.attributes?.subheading?.default ?? '';
const defaultButtonText = metadata?.attributes?.buttonText?.default
    ?? __('Start a Project', 'mccullough-digital');
const defaultButtonLink = metadata?.attributes?.buttonLink?.default ?? '';

const {
    innerBlocks: {
        allowedBlocks: allowedHeroBlocks = [
            'core/heading',
            'core/paragraph',
            'core/list',
            'core/buttons',
            'core/group',
            'core/image',
            'core/media-text',
        ],
        template: heroTemplate = [
            [
                'core/heading',
                {
                    level: 1,
                    className: 'hero__headline',
                    placeholder: __('Add hero headline…', 'mccullough-digital'),
                    content: defaultHeadline,
                },
            ],
            [
                'core/paragraph',
                {
                    placeholder: __('Add supporting copy…', 'mccullough-digital'),
                    content: defaultSubheading,
                },
            ],
            [
                'core/buttons',
                {
                    layout: {
                        type: 'flex',
                        justifyContent: 'center',
                    },
                },
                [
                    [
                        'core/button',
                        {
                            className: 'cta-button',
                            text: defaultButtonText,
                            url: defaultButtonLink,
                        },
                    ],
                ],
            ],
        ],
        templateLock: heroTemplateLock = false,
    } = {},
} = metadata;

const DEFAULT_BUTTON_TEXT = defaultButtonText;

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, clientId }) {
        const { headline, subheading, buttonText, buttonLink } = attributes;
        const blockProps = useBlockProps({
            className: 'hero',
        });

        const hasMigrated = useRef(false);
        const { innerBlocks = [] } = useSelect(
            (select) => ({
                innerBlocks: select('core/block-editor').getBlocks(clientId),
            }),
            [clientId]
        );
        const { replaceInnerBlocks } = useDispatch('core/block-editor');

        useEffect(() => {
            if (hasMigrated.current) {
                return;
            }

            if (innerBlocks.length > 0) {
                hasMigrated.current = true;
                return;
            }

            const generatedBlocks = [];

            if (headline) {
                generatedBlocks.push(
                    createBlock('core/heading', {
                        level: 1,
                        className: 'hero__headline',
                        content: headline,
                    })
                );
            }

            if (subheading) {
                generatedBlocks.push(
                    createBlock('core/paragraph', {
                        content: subheading,
                    })
                );
            }

            if (buttonText || buttonLink) {
                const button = createBlock('core/button', {
                    className: 'cta-button',
                    text: buttonText || DEFAULT_BUTTON_TEXT,
                    url: buttonLink || undefined,
                });

                const buttonsWrapper = createBlock(
                    'core/buttons',
                    {
                        layout: {
                            type: 'flex',
                            justifyContent: 'center',
                        },
                    },
                    [button]
                );

                generatedBlocks.push(buttonsWrapper);
            }

            if (generatedBlocks.length === 0) {
                return;
            }

            replaceInnerBlocks(clientId, generatedBlocks, false);
            hasMigrated.current = true;
        }, [
            buttonLink,
            buttonText,
            clientId,
            headline,
            innerBlocks.length,
            replaceInnerBlocks,
            subheading,
        ]);

        return (
            <>
                <section {...blockProps}>
                    <div
                        className="hero-canvas-placeholder"
                        aria-hidden="true"
                        role="presentation"
                    />
                    <div className="hero-content">
                        <InnerBlocks
                            allowedBlocks={ allowedHeroBlocks }
                            template={ heroTemplate }
                            templateLock={ heroTemplateLock }
                        />
                    </div>
                </section>
            </>
        );
    },
    save() {
        return null;
    },
});
