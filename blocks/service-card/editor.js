import { registerBlockType, createBlock } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import {
    useBlockProps,
    InnerBlocks,
} from '@wordpress/block-editor';

import metadata from './block.json';

const {
    innerBlocks: {
        allowedBlocks: allowedCardBlocks = [
            'core/group',
            'core/heading',
            'core/paragraph',
            'core/list',
            'core/html',
            'core/buttons',
            'core/button',
            'core/image',
        ],
        template: cardTemplate = [
            [
                'core/group',
                {
                    className: 'service-card__body',
                },
                [
                    [
                        'core/group',
                        {
                            className: 'icon',
                        },
                    ],
                    [
                        'core/heading',
                        {
                            level: 3,
                            placeholder: __('Add service title…', 'mccullough-digital'),
                            content: metadata?.attributes?.title?.default ?? __('Service title', 'mccullough-digital'),
                        },
                    ],
                    [
                        'core/paragraph',
                        {
                            placeholder: __('Describe the value of this service…', 'mccullough-digital'),
                            content: metadata?.attributes?.text?.default ?? '',
                        },
                    ],
                ],
            ],
            [
                'core/buttons',
                {
                    className: 'service-card__cta',
                    layout: {
                        type: 'flex',
                        justifyContent: 'center',
                    },
                },
                [
                    [
                        'core/button',
                        {
                            className: 'learn-more',
                            text: metadata?.attributes?.linkText?.default ?? __('Learn More →', 'mccullough-digital'),
                        },
                    ],
                ],
            ],
        ],
        templateLock: cardTemplateLock = false,
    } = {},
} = metadata;

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, clientId }) {
        const { icon, title, text, linkText, linkUrl } = attributes;
        const blockProps = useBlockProps({
            className: 'service-card',
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

            const bodyChildren = [];

            const iconGroupChildren = [];

            if (icon) {
                iconGroupChildren.push(
                    createBlock('core/html', {
                        content: icon,
                    })
                );
            }

            const iconGroup = createBlock(
                'core/group',
                {
                    className: 'icon',
                },
                iconGroupChildren
            );

            bodyChildren.push(iconGroup);

            if (title) {
                bodyChildren.push(
                    createBlock('core/heading', {
                        level: 3,
                        content: title,
                    })
                );
            }

            if (text) {
                bodyChildren.push(
                    createBlock('core/paragraph', {
                        content: text,
                    })
                );
            }

            const bodyGroup = createBlock(
                'core/group',
                {
                    className: 'service-card__body',
                },
                bodyChildren
            );

            const newBlocks = [bodyGroup];

            if (linkText || linkUrl) {
                const button = createBlock('core/button', {
                    className: 'learn-more',
                    text: linkText || (metadata?.attributes?.linkText?.default ?? ''),
                    url: linkUrl || undefined,
                });

                const buttons = createBlock(
                    'core/buttons',
                    {
                        className: 'service-card__cta',
                        layout: {
                            type: 'flex',
                            justifyContent: 'center',
                        },
                    },
                    [button]
                );

                newBlocks.push(buttons);
            }

            if (newBlocks.length === 0) {
                return;
            }

            replaceInnerBlocks(clientId, newBlocks, false);
            hasMigrated.current = true;
        }, [
            clientId,
            icon,
            innerBlocks.length,
            linkText,
            linkUrl,
            replaceInnerBlocks,
            text,
            title,
        ]);

        return (
            <>
                <article {...blockProps}>
                    <div className="service-card-content">
                        <InnerBlocks
                            allowedBlocks={ allowedCardBlocks }
                            template={ cardTemplate }
                            templateLock={ cardTemplateLock }
                        />
                    </div>
                </article>
            </>
        );
    },
    save() {
        return null;
    },
});
