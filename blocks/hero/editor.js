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
            imageHorizontalOffset,
            heroImageWidth,
            hideImageOnMobile,
            contentAlignment,
            contentOffset,
        } = attributes;

        const alignmentOptions = ['top', 'center', 'bottom'];
        const normalizedAlignment = alignmentOptions.includes(contentAlignment)
            ? contentAlignment
            : 'center';
        const alignmentClass = `is-content-${normalizedAlignment}`;

        const parsedOffset = Number(contentOffset);
        const normalizedOffset = Number.isFinite(parsedOffset) ? parsedOffset : 0;

        const blockProps = useBlockProps({
            className: ['hero', alignmentClass].filter(Boolean).join(' '),
            style: {
                '--hero-content-offset': `${normalizedOffset}px`,
            },
        });

        const hasMigrated = useRef(false);
        const { innerBlocks = [] } = useSelect(
            (select) => ({
                innerBlocks: select('core/block-editor').getBlocks(clientId),
            }),
            [clientId]
        );
        const { replaceInnerBlocks } = useDispatch('core/block-editor');

        const extractNaturalWidth = (media) => {
            if (!media) {
                return 0;
            }

            const {
                width,
                media_details: mediaDetails = {},
                sizes = {},
            } = media;

            const detailsWidth = mediaDetails?.width;
            const fullSizeWidth = sizes?.full?.width;

            return (
                detailsWidth
                || fullSizeWidth
                || width
                || 0
            );
        };

        const heroMedia = useSelect(
            (select) =>
                heroImageId ? select('core').getMedia(heroImageId) : null,
            [heroImageId]
        );

        useEffect(() => {
            if (!heroImageId || !heroMedia) {
                return;
            }

            const naturalWidth = extractNaturalWidth(heroMedia);

            if (naturalWidth && naturalWidth !== heroImageWidth) {
                setAttributes({ heroImageWidth: naturalWidth });
            }
        }, [heroImageId, heroImageWidth, heroMedia, setAttributes]);

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
                heroImageWidth: extractNaturalWidth(media),
            });
        };

        const onRemoveImage = () => {
            setAttributes({
                heroImageId: 0,
                heroImageUrl: '',
                heroImageAlt: '',
                heroImageWidth: 0,
            });
        };

        const buildImagePresentation = () => {
            const baseTransforms = {
                'bottom-center': 'translateX(-50%)',
                'center-right': 'translateY(-50%)',
                'center-left': 'translateY(-50%)',
                center: 'translate(-50%, -50%)',
            };

            const classes = ['hero__image-container'];

            if (imagePosition) {
                classes.push(`hero__image-position--${imagePosition}`);
            }

            if (hideImageOnMobile) {
                classes.push('hero__image-hide-mobile');
            }

            const styles = {
                opacity: imageOpacity / 100,
            };

            if (heroImageWidth > 0) {
                styles.width = `calc(${imageSize} / 100 * ${heroImageWidth}px)`;
            } else {
                styles.width = `${imageSize}vw`;
            }

            const transformParts = [];

            if (baseTransforms[imagePosition]) {
                transformParts.push(baseTransforms[imagePosition]);
            }

            if (imageVerticalOffset !== 0) {
                transformParts.push(`translateY(${imageVerticalOffset}px)`);
            }

            if (imageHorizontalOffset !== 0) {
                transformParts.push(`translateX(${imageHorizontalOffset}px)`);
            }

            if (transformParts.length > 0) {
                styles.transform = transformParts.join(' ');
            }

            return {
                className: classes.join(' '),
                styles,
            };
        };

        const { className: imageContainerClassName, styles: imageStyles } =
            buildImagePresentation();

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
                                    max={200}
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

                                <RangeControl
                                    label={__('Horizontal Offset (px)', 'mccullough-digital')}
                                    value={imageHorizontalOffset}
                                    onChange={(value) => setAttributes({ imageHorizontalOffset: value })}
                                    min={-200}
                                    max={200}
                                    step={10}
                                    help={__('Positive moves right, negative moves left', 'mccullough-digital')}
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
                    <PanelBody
                        title={__('Content Layout', 'mccullough-digital')}
                        initialOpen={false}
                    >
                        <SelectControl
                            label={__('Vertical Alignment', 'mccullough-digital')}
                            value={normalizedAlignment}
                            options={[
                                {
                                    label: __('Top', 'mccullough-digital'),
                                    value: 'top',
                                },
                                {
                                    label: __('Center', 'mccullough-digital'),
                                    value: 'center',
                                },
                                {
                                    label: __('Bottom', 'mccullough-digital'),
                                    value: 'bottom',
                                },
                            ]}
                            onChange={(value) => {
                                if (alignmentOptions.includes(value)) {
                                    setAttributes({ contentAlignment: value });
                                }
                            }}
                        />
                        <RangeControl
                            label={__('Content Offset (px)', 'mccullough-digital')}
                            value={normalizedOffset}
                            onChange={(value) =>
                                setAttributes({ contentOffset: value ?? 0 })
                            }
                            min={0}
                            max={240}
                            step={4}
                            help={__('Adds extra top padding inside the content stack.', 'mccullough-digital')}
                        />
                    </PanelBody>
                </InspectorControls>

                <section {...blockProps}>
                    <div
                        className="hero-canvas-placeholder"
                        aria-hidden="true"
                        role="presentation"
                    />
                    {heroImageUrl && (
                        <div className={imageContainerClassName} style={imageStyles}>
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
