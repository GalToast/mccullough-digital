import { registerBlockType, createBlock } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

import metadata from './block.json';

const {
    innerBlocks: {
        allowedBlocks: allowedServiceBlocks = [
            'core/heading',
            'mccullough-digital/service-card',
            'mccullough-digital/button',
        ],
        template: servicesTemplate = [
            [
                'core/heading',
                {
                    level: 2,
                    className: 'section-title',
                    placeholder: __('Add services headline…', 'mccullough-digital'),
                    content: metadata?.attributes?.headline?.default ?? __('What We Do', 'mccullough-digital'),
                },
            ],
            ['mccullough-digital/service-card'],
            ['mccullough-digital/service-card'],
            ['mccullough-digital/service-card'],
        ],
        templateLock: servicesTemplateLock = false,
    } = {},
} = metadata;

registerBlockType(metadata.name, {
    ...metadata,
    edit({ attributes, clientId }) {
        const { headline } = attributes;
        const blockProps = useBlockProps();
        const hasSeededHeading = useRef(false);

        const { innerBlocks = [] } = useSelect(
            (select) => ({
                innerBlocks: select('core/block-editor').getBlocks(clientId),
            }),
            [clientId]
        );

        const { insertBlocks } = useDispatch('core/block-editor');
        const defaultHeadline = metadata?.attributes?.headline?.default ?? '';

        useEffect(() => {
            if (hasSeededHeading.current) {
                return;
            }

            if (!Array.isArray(innerBlocks)) {
                return;
            }

            const hasHeading = innerBlocks.some((block) => block.name === 'core/heading');

            if (hasHeading) {
                hasSeededHeading.current = true;
                return;
            }

            const resolvedHeadline = headline || defaultHeadline;

            if (!resolvedHeadline) {
                hasSeededHeading.current = true;
                return;
            }

            const headingBlock = createBlock('core/heading', {
                level: 2,
                className: 'section-title',
                content: resolvedHeadline,
            });

            insertBlocks(headingBlock, 0, clientId, false);
            hasSeededHeading.current = true;
        }, [clientId, defaultHeadline, headline, innerBlocks, insertBlocks]);

        return (
            <section {...blockProps}>
                <div className="container">
                    <InnerBlocks
                        allowedBlocks={ allowedServiceBlocks }
                        template={ servicesTemplate }
                        templateLock={ servicesTemplateLock }
                    />
                </div>
            </section>
        );
    },
    save() {
        return <InnerBlocks.Content />;
    },
});
