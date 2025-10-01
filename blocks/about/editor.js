import { registerBlockType, createBlock } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

import metadata from './block.json';

const defaultHeadline = metadata?.attributes?.headline?.default ?? '';
const defaultText = metadata?.attributes?.text?.default ?? '';

const {
    innerBlocks: {
        allowedBlocks: allowedAboutBlocks = [
            'core/heading',
            'core/paragraph',
            'core/list',
            'core/group',
            'core/image',
            'core/buttons',
            'core/button',
        ],
        template: aboutTemplate = [
            [
                'core/heading',
                {
                    level: 2,
                    className: 'section-title',
                    placeholder: __('Add about headline…', 'mccullough-digital'),
                    content: defaultHeadline,
                },
            ],
            [
                'core/paragraph',
                {
                    placeholder: __('Add about text…', 'mccullough-digital'),
                    content: defaultText,
                },
            ],
            [
                'core/paragraph',
                {
                    placeholder: __('Add founder introduction…', 'mccullough-digital'),
                    content:
                        "As the founder, I bring a passion for technology and a deep understanding of what it takes to get noticed online. We're not just a service provider; we're your dedicated digital launchpad.",
                },
            ],
            [
                'core/paragraph',
                {
                    placeholder: __('Add proof statement…', 'mccullough-digital'),
                    content:
                        'We integrate everything—from your website and social media to your Google Business Profile and ad campaigns—into one cohesive strategy for growth.',
                },
            ],
            [
                'core/buttons',
                {
                    layout: {
                        type: 'flex',
                        justifyContent: 'left',
                    },
                },
                [
                    [
                        'core/button',
                        {
                            className: 'is-style-fill',
                            text: 'Learn Our Story',
                            url: '/about/',
                        },
                    ],
                ],
            ],
        ],
        templateLock: aboutTemplateLock = false,
    } = {},
} = metadata;

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, clientId }) {
        const { headline, text } = attributes;
        const blockProps = useBlockProps();

        const hasMigrated = useRef(false);
        const { innerBlocks } = useSelect(
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

            if (text) {
                generatedBlocks.push(
                    createBlock('core/paragraph', {
                        content: text,
                    })
                );
            }

            if (generatedBlocks.length === 0) {
                return;
            }

            replaceInnerBlocks(clientId, generatedBlocks, false);
            hasMigrated.current = true;
        }, [clientId, headline, innerBlocks.length, replaceInnerBlocks, text]);

        return (
            <section {...blockProps}>
                <div className="container">
                    <InnerBlocks
                        allowedBlocks={ allowedAboutBlocks }
                        template={ aboutTemplate }
                        templateLock={ aboutTemplateLock }
                    />
                </div>
            </section>
        );
    },
    save() {
        return <InnerBlocks.Content />;
    },
});
