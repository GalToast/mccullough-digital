<?php
/**
 * Hero Block Template.
 *
 * @param   array $attributes - The block attributes.
 * @param   string $content - The block inner content (empty).
 * @param   WP_Block $block - The block instance.
 *
 * @package McCullough_Digital
 */

if ( ! function_exists( 'mcd_hero_enqueue_button_script' ) ) {
    /**
     * Ensure the neon hero button script is loaded when a CTA is present.
     *
     * @return void
     */
    function mcd_hero_enqueue_button_script() {
        $script_path = get_template_directory() . '/build/blocks/hero/hero-button.js';

        if ( ! file_exists( $script_path ) ) {
            return;
        }

        wp_enqueue_script(
            'mccullough-digital-hero-button',
            get_template_directory_uri() . '/build/blocks/hero/hero-button.js',
            array(),
            filemtime( $script_path ),
            true
        );
    }
}

if ( ! function_exists( 'mcd_hero_wrap_cta_markup' ) ) {
    /**
     * Wrap the saved CTA markup with the neon button mount so React can hydrate it.
     *
     * @param string $inner_content The saved InnerBlocks markup.
     *
     * @return array {
     *     @type string $content        Updated markup with mount wrappers.
     *     @type string $button_text    CTA label.
     *     @type string $button_link    CTA URL if present.
     *     @type string $fallback_type  Either "link" or "static".
     *     @type bool   $found          Whether a CTA button was located.
     * }
     */
    function mcd_hero_wrap_cta_markup( $inner_content ) {
        $result = array(
            'content'       => $inner_content,
            'button_text'   => '',
            'button_link'   => '',
            'fallback_type' => 'static',
            'found'         => false,
        );

        if ( '' === $inner_content ) {
            return $result;
        }

        $previous_state = libxml_use_internal_errors( true );
        $document       = new DOMDocument();

        $content_to_load = '<div id="mcd-hero-inner-root">' . $inner_content . '</div>';
        $loaded          = $document->loadHTML(
            '<?xml encoding="utf-8" ?>' . $content_to_load,
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        if ( ! $loaded ) {
            libxml_clear_errors();
            libxml_use_internal_errors( $previous_state );
            return $result;
        }

        $container = $document->getElementById( 'mcd-hero-inner-root' );

        if ( ! $container ) {
            libxml_clear_errors();
            libxml_use_internal_errors( $previous_state );
            return $result;
        }

        $xpath     = new DOMXPath( $document );
        $cta_nodes = $xpath->query(
            '//*[contains(concat(" ", normalize-space(@class), " "), " hero__cta-button ")]'
        );

        if ( ! $cta_nodes || 0 === $cta_nodes->length ) {
            libxml_clear_errors();
            libxml_use_internal_errors( $previous_state );
            return $result;
        }

        foreach ( $cta_nodes as $cta_node ) {
            $parent            = $cta_node->parentNode;
            $already_wrapped   = false;
            $ancestor          = $parent;

            while ( $ancestor instanceof DOMElement ) {
                $class_attribute = $ancestor->getAttribute( 'class' );

                if ( false !== strpos( ' ' . $class_attribute . ' ', ' hero-neon-button-mount ' ) ) {
                    $already_wrapped = true;
                    break;
                }

                $ancestor = $ancestor->parentNode;
            }

            if ( $already_wrapped ) {
                continue;
            }

            $cta_text = trim( wp_strip_all_tags( $cta_node->textContent ) );
            $cta_href = '';

            if ( $cta_node->hasAttribute( 'href' ) ) {
                $candidate_href = trim( $cta_node->getAttribute( 'href' ) );
                $sanitized_href = esc_url_raw( $candidate_href );

                if ( '' !== $sanitized_href ) {
                    $cta_href = $sanitized_href;
                }
            }

            $fallback_type = '' === $cta_href ? 'static' : 'link';
            $mount         = $document->createElement( 'div' );
            $mount->setAttribute( 'class', 'hero-neon-button-mount' );

            if ( '' !== $cta_text ) {
                $mount->setAttribute( 'data-button-text', $cta_text );
            }

            if ( '' !== $cta_href ) {
                $mount->setAttribute( 'data-button-link', $cta_href );
            }

            $mount->setAttribute( 'data-fallback-type', $fallback_type );

            $parent->replaceChild( $mount, $cta_node );
            $mount->appendChild( $cta_node );

            $updated_markup = '';

            foreach ( $container->childNodes as $child_node ) {
                $updated_markup .= $document->saveHTML( $child_node );
            }

            $result = array(
                'content'       => $updated_markup,
                'button_text'   => $cta_text,
                'button_link'   => $cta_href,
                'fallback_type' => $fallback_type,
                'found'         => ( '' !== $cta_text || '' !== $cta_href ),
            );

            break;
        }

        libxml_clear_errors();
        libxml_use_internal_errors( $previous_state );

        return $result;
    }
}

$headline   = isset( $attributes['headline'] ) ? $attributes['headline'] : '';
$subheading = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';

$content_alignment = isset( $attributes['contentAlignment'] ) ? $attributes['contentAlignment'] : 'center';
$allowed_alignments = array( 'top', 'center', 'bottom' );
if ( ! in_array( $content_alignment, $allowed_alignments, true ) ) {
    $content_alignment = 'center';
}
$alignment_class = 'is-content-' . $content_alignment;

$content_offset = isset( $attributes['contentOffset'] ) ? floatval( $attributes['contentOffset'] ) : 0;
$min_offset     = -300;
$max_offset     = 240;
$content_offset = max( $min_offset, min( $max_offset, $content_offset ) );
$content_offset = round( $content_offset, 2 );
$offset_style   = sprintf( '--hero-content-offset: %spx;', $content_offset );

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class' => trim( 'hero ' . $alignment_class ),
        'style' => $offset_style,
    )
);

// Image attributes
$hero_image_url         = isset( $attributes['heroImageUrl'] ) ? esc_url( $attributes['heroImageUrl'] ) : '';
$hero_image_alt         = isset( $attributes['heroImageAlt'] ) ? esc_attr( $attributes['heroImageAlt'] ) : '';
$hero_image_width       = isset( $attributes['heroImageWidth'] ) ? intval( $attributes['heroImageWidth'] ) : 0;
$image_position         = isset( $attributes['imagePosition'] ) ? $attributes['imagePosition'] : 'bottom-right';
$image_size             = isset( $attributes['imageSize'] ) ? intval( $attributes['imageSize'] ) : 40;
$image_opacity          = isset( $attributes['imageOpacity'] ) ? intval( $attributes['imageOpacity'] ) : 40;
$image_vertical_offset  = isset( $attributes['imageVerticalOffset'] ) ? intval( $attributes['imageVerticalOffset'] ) : 0;
$image_horizontal_offset = isset( $attributes['imageHorizontalOffset'] ) ? intval( $attributes['imageHorizontalOffset'] ) : 0;
$hide_image_on_mobile   = isset( $attributes['hideImageOnMobile'] ) && $attributes['hideImageOnMobile'] === true;

$inner_content = trim( (string) $content );

if ( '' !== $inner_content ) {
    $cta_wrapped = mcd_hero_wrap_cta_markup( $inner_content );

    if ( ! empty( $cta_wrapped['found'] ) ) {
        $inner_content = $cta_wrapped['content'];
        mcd_hero_enqueue_button_script();
    }
}

if ( '' === $inner_content ) {
    ob_start();

    if ( '' !== trim( wp_strip_all_tags( (string) $headline ) ) ) {
        ?>
        <h1 class="wp-block-heading hero__headline">
            <span class="hero__headline-text">
                <?php echo wp_kses_post( $headline ); ?>
            </span>
        </h1>
        <?php
    }

    if ( '' !== trim( wp_strip_all_tags( (string) $subheading ) ) ) {
        ?>
        <p>
            <?php echo wp_kses_post( $subheading ); ?>
        </p>
        <?php
    }

    $button_text = isset( $attributes['buttonText'] ) ? trim( wp_strip_all_tags( (string) $attributes['buttonText'] ) ) : '';
    $raw_link    = isset( $attributes['buttonLink'] ) ? trim( (string) $attributes['buttonLink'] ) : '';
    $button_link = '' !== $raw_link ? esc_url( $raw_link ) : '';

    // React Neon Button Mount Point
    if ( '' !== $button_text ) {
        // Enqueue the React button script
        mcd_hero_enqueue_button_script();

        $mount_attributes = array(
            'class'              => 'hero-neon-button-mount',
            'data-button-text'   => $button_text,
            'data-button-link'   => $button_link,
            'data-fallback-type' => '' === $button_link ? 'static' : 'link',
        );

        $attributes_string = '';
        foreach ( $mount_attributes as $attr_key => $attr_value ) {
            if ( '' === $attr_value && 'data-button-link' === $attr_key ) {
                // Avoid printing empty href-like attribute to keep markup tidy.
                continue;
            }

            $attributes_string .= sprintf( ' %1$s="%2$s"', esc_attr( $attr_key ), esc_attr( $attr_value ) );
        }

        ?>
        <div<?php echo $attributes_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>>
            <?php if ( '' !== $button_link ) : ?>
                <a class="hero-neon-button-fallback hero__cta-button"
                   data-hero-fallback="link"
                   href="<?php echo esc_url( $button_link ); ?>">
                    <span class="hero-neon-button-fallback__label"><?php echo esc_html( $button_text ); ?></span>
                </a>
            <?php else : ?>
                <span class="hero-neon-button-fallback hero__cta-button"
                      data-hero-fallback="static"
                      role="button"
                      tabindex="0">
                    <span class="hero-neon-button-fallback__label"><?php echo esc_html( $button_text ); ?></span>
                </span>
            <?php endif; ?>
        </div>
        <?php
    }

    $inner_content = trim( (string) ob_get_clean() );
}

// Build image container classes
$image_container_classes = array( 'hero__image-container' );
$image_container_classes[] = 'hero__image-position--' . $image_position;
if ( $hide_image_on_mobile ) {
    $image_container_classes[] = 'hero__image-hide-mobile';
}
$image_container_class = implode( ' ', $image_container_classes );

// Build inline styles for image container
$image_styles = array();
$image_styles[] = 'opacity: ' . ( $image_opacity / 100 ) . ';';
$image_styles[] = $hero_image_width > 0
    ? 'width: calc(' . $image_size . ' / 100 * ' . $hero_image_width . 'px);'
    : 'width: ' . $image_size . 'vw;';

$base_transforms = array(
    'bottom-center' => 'translateX(-50%)',
    'center-right'  => 'translateY(-50%)',
    'center-left'   => 'translateY(-50%)',
    'center'        => 'translate(-50%, -50%)',
);

$transform_parts = array();

if ( isset( $base_transforms[ $image_position ] ) ) {
    $transform_parts[] = $base_transforms[ $image_position ];
}

if ( 0 !== $image_vertical_offset ) {
    $transform_parts[] = 'translateY(' . $image_vertical_offset . 'px)';
}

if ( 0 !== $image_horizontal_offset ) {
    $transform_parts[] = 'translateX(' . $image_horizontal_offset . 'px)';
}

if ( ! empty( $transform_parts ) ) {
    $image_styles[] = 'transform: ' . implode( ' ', $transform_parts ) . ';';
}
$image_style_attr = implode( ' ', $image_styles );
?>

<section <?php echo $wrapper_attributes; ?>>
    <canvas class="hero__particle-canvas" aria-hidden="true" role="presentation"></canvas>
    <?php if ( '' !== $hero_image_url ) : ?>
        <div class="<?php echo esc_attr( $image_container_class ); ?>" aria-hidden="true" style="<?php echo esc_attr( $image_style_attr ); ?>">
            <img src="<?php echo $hero_image_url; ?>" alt="<?php echo $hero_image_alt; ?>" class="hero__decorative-image" />
        </div>
    <?php endif; ?>
    <div class="hero-content">
        <?php echo $inner_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content contains sanitized inner blocks/fallback markup. ?>
    </div>
</section>
