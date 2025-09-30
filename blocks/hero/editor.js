import { registerBlockType, createBlock } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useEffect, useRef } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import {
    useBlockProps,
    InnerBlocks,
    InspectorControls,
    MediaUpload,
    MediaUploadCheck,
} from '@wordpress/block-editor';
import {
    PanelBody,
    Button,
    SelectControl,
    RangeControl,
    ToggleControl,
} from '@wordpress/components';

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
    edit({ attributes, setAttributes, clientId }) {
        const { 
            headline, 
            subheading, 
            buttonText, 
            buttonLink,
            heroImageId,
            heroImageUrl,
            heroImageAlt,
            imagePosition,
            imageSize,
            imageOpacity,
            imageVerticalOffset,
            hideImageOnMobile,
        } = attributes;
        
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

        const onSelectImage = (media) => {
            setAttributes({
                heroImageId: media.id,
                heroImageUrl: media.url,
                heroImageAlt: media.alt || '',
            });
        };

        const onRemoveImage = () => {
            setAttributes({
                heroImageId: 0,
                heroImageUrl: '',
                heroImageAlt: '',
            });
        };

        return (
            <>
                <InspectorControls>
                    <PanelBody 
                        title={__('Hero Image', 'mccullough-digital')} 
                        initialOpen={true}
                    >
                        <MediaUploadCheck>
                            <MediaUpload
                                onSelect={onSelectImage}
                                allowedTypes={['image']}
                                value={heroImageId}
                                render={({ open }) => (
                                    <div style={{ marginBottom: '16px' }}>
                                        {!heroImageUrl ? (
                                            <Button
                                                onClick={open}
                                                variant="secondary"
                                                style={{ width: '100%' }}
                                            >
                                                {__('Select Hero Image', 'mccullough-digital')}
                                            </Button>
                                        ) : (
                                            <>
                                                <img
                                                    src={heroImageUrl}
                                                    alt={heroImageAlt}
                                                    style={{
                                                        width: '100%',
                                                        height: 'auto',
                                                        marginBottom: '8px',
                                                        borderRadius: '4px',
                                                    }}
                                                />
                                                <div style={{ display: 'flex', gap: '8px' }}>
                                                    <Button
                                                        onClick={open}
                                                        variant="secondary"
                                                        style={{ flex: 1 }}
                                                    >
                                                        {__('Replace Image', 'mccullough-digital')}
                                                    </Button>
                                                    <Button
                                                        onClick={onRemoveImage}
                                                        variant="secondary"
                                                        isDestructive
                                                    >
                                                        {__('Remove', 'mccullough-digital')}
                                                    </Button>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                )}
                            />
                        </MediaUploadCheck>

                        {heroImageUrl && (
                            <>
                                <SelectControl
                                    label={__('Image Position', 'mccullough-digital')}
                                    value={imagePosition}
                                    options={[
                                        { label: __('Bottom Right', 'mccullough-digital'), value: 'bottom-right' },
                                        { label: __('Bottom Left', 'mccullough-digital'), value: 'bottom-left' },
                                        { label: __('Bottom Center', 'mccullough-digital'), value: 'bottom-center' },
                                        { label: __('Center Right', 'mccullough-digital'), value: 'center-right' },
                                        { label: __('Center Left', 'mccullough-digital'), value: 'center-left' },
                                        { label: __('Center', 'mccullough-digital'), value: 'center' },
                                    ]}
                                    onChange={(value) => setAttributes({ imagePosition: value })}
                                />

                                <RangeControl
                                    label={__('Image Size (%)', 'mccullough-digital')}
                                    value={imageSize}
                                    onChange={(value) => setAttributes({ imageSize: value })}
                                    min={10}
                                    max={100}
                                    step={5}
                                />

                                <RangeControl
                                    label={__('Opacity (%)', 'mccullough-digital')}
                                    value={imageOpacity}
                                    onChange={(value) => setAttributes({ imageOpacity: value })}
                                    min={0}
                                    max={100}
                                    step={5}
                                />

                                <RangeControl
                                    label={__('Vertical Offset (px)', 'mccullough-digital')}
                                    value={imageVerticalOffset}
                                    onChange={(value) => setAttributes({ imageVerticalOffset: value })}
                                    min={-200}
                                    max={200}
                                    step={10}
                                    help={__('Positive moves down, negative moves up', 'mccullough-digital')}
                                />

                                <ToggleControl
                                    label={__('Hide on Mobile', 'mccullough-digital')}
                                    checked={hideImageOnMobile}
                                    onChange={(value) => setAttributes({ hideImageOnMobile: value })}
                                    help={__('Hide the image on screens smaller than 768px', 'mccullough-digital')}
                                />
                            </>
                        )}
                    </PanelBody>
                </InspectorControls>

                <section {...blockProps}>
                    <div
                        className="hero-canvas-placeholder"
                        aria-hidden="true"
                        role="presentation"
                    />
                    {heroImageUrl && (
                        <div 
                            className="hero__image-container"
                            style={{
                                opacity: imageOpacity / 100,
                            }}
                        >
                            <img
                                src={heroImageUrl}
                                alt={heroImageAlt}
                                className="hero__decorative-image"
                            />
                        </div>
                    )}
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
        return <InnerBlocks.Content />;
    },
});
