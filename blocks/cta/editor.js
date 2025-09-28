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
const defaultButtonText = metadata?.attributes?.buttonText?.default
    ?? __('Let\'s Talk', 'mccullough-digital');
const defaultButtonLink = metadata?.attributes?.buttonLink?.default ?? '';

const {
    innerBlocks: {
        allowedBlocks: allowedCtaBlocks = [
            'core/heading',
            'core/paragraph',
            'core/list',
            'core/buttons',
            'core/group',
            'core/image',
        ],
        template: ctaTemplate = [
            [
                'core/heading',
                {
                    level: 2,
                    className: 'section-title',
                    placeholder: __('Add CTA headline…', 'mccullough-digital'),
                    content: defaultHeadline,
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
        templateLock: ctaTemplateLock = false,
    } = {},
} = metadata;

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, clientId }) {
        const { headline, buttonText, buttonLink } = attributes;
        const blockProps = useBlockProps({
            className: 'cta-section',
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
                        level: 2,
                        className: 'section-title',
                        content: headline,
                    })
                );
            }

            if (buttonText || buttonLink) {
                const button = createBlock('core/button', {
                    className: 'cta-button',
                    text: buttonText || defaultButtonText,
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
        ]);

        return (
            <>
                <section {...blockProps}>
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
        return null;
    },
});
