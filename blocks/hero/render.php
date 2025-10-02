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
$hero_image_id          = isset( $attributes['heroImageId'] ) ? intval( $attributes['heroImageId'] ) : 0;
$hero_image_url         = isset( $attributes['heroImageUrl'] ) ? esc_url_raw( $attributes['heroImageUrl'] ) : '';
$hero_image_alt_input   = isset( $attributes['heroImageAlt'] ) ? sanitize_text_field( $attributes['heroImageAlt'] ) : '';
$hero_image_width       = isset( $attributes['heroImageWidth'] ) ? intval( $attributes['heroImageWidth'] ) : 0;
$image_position         = isset( $attributes['imagePosition'] ) ? $attributes['imagePosition'] : 'bottom-right';
$image_size             = isset( $attributes['imageSize'] ) ? intval( $attributes['imageSize'] ) : 40;
$image_opacity          = isset( $attributes['imageOpacity'] ) ? intval( $attributes['imageOpacity'] ) : 40;
$image_vertical_offset  = isset( $attributes['imageVerticalOffset'] ) ? intval( $attributes['imageVerticalOffset'] ) : 0;
$image_horizontal_offset = isset( $attributes['imageHorizontalOffset'] ) ? intval( $attributes['imageHorizontalOffset'] ) : 0;
$hide_image_on_mobile   = isset( $attributes['hideImageOnMobile'] ) && $attributes['hideImageOnMobile'] === true;

$hero_image_alt = $hero_image_alt_input;

if ( 0 !== $hero_image_id && '' === $hero_image_alt ) {
    $attachment_alt = get_post_meta( $hero_image_id, '_wp_attachment_image_alt', true );
    if ( is_string( $attachment_alt ) ) {
        $hero_image_alt = sanitize_text_field( $attachment_alt );
    }
}

$hero_image_alt = trim( $hero_image_alt );

$inner_content = trim( (string) $content );

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

    if ( '' !== $button_text ) {
        $button_label = sprintf(
            '<span class="hero__cta-button-label">%s</span>',
            esc_html( $button_text )
        );

        if ( '' !== $button_link ) {
            ?>
            <a class="cta-button hero__cta-button" href="<?php echo esc_url( $button_link ); ?>">
                <?php echo $button_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>
            </a>
            <?php
        } else {
            ?>
            <button class="cta-button hero__cta-button" type="button">
                <?php echo $button_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. ?>
            </button>
            <?php
        }
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
$image_style_attr = trim( implode( ' ', $image_styles ) );

$hero_image_markup = '';

if ( 0 !== $hero_image_id ) {
    $image_attributes = array(
        'class'   => 'hero__decorative-image',
        'loading' => 'lazy',
        'alt'     => $hero_image_alt,
    );

    if ( '' === $hero_image_alt ) {
        $image_attributes['role'] = 'presentation';
    }

    $hero_image_markup = wp_get_attachment_image( $hero_image_id, 'full', false, $image_attributes );

    if ( ! $hero_image_markup && '' !== $hero_image_url ) {
        $fallback_attributes = $image_attributes;
        $fallback_attributes['src'] = $hero_image_url;

        $hero_image_markup = '<img';

        foreach ( $fallback_attributes as $attr_name => $attr_value ) {
            $escaped_value    = 'src' === $attr_name ? esc_url( $attr_value ) : esc_attr( $attr_value );
            $hero_image_markup .= sprintf( ' %s="%s"', esc_attr( $attr_name ), $escaped_value );
        }

        $hero_image_markup .= ' />';
    }
} elseif ( '' !== $hero_image_url ) {
    $attr_pairs = array(
        'src'     => $hero_image_url,
        'class'   => 'hero__decorative-image',
        'loading' => 'lazy',
        'alt'     => $hero_image_alt,
    );

    if ( '' === $hero_image_alt ) {
        $attr_pairs['role'] = 'presentation';
    }

    $hero_image_markup = '<img';

    foreach ( $attr_pairs as $attr_name => $attr_value ) {
        $escaped_value     = 'src' === $attr_name ? esc_url( $attr_value ) : esc_attr( $attr_value );
        $hero_image_markup .= sprintf( ' %s="%s"', esc_attr( $attr_name ), $escaped_value );
    }

    $hero_image_markup .= ' />';
}

$image_container_attributes = array(
    'class' => $image_container_class,
);

if ( '' !== $image_style_attr ) {
    $image_container_attributes['style'] = $image_style_attr;
}

if ( '' === $hero_image_alt ) {
    $image_container_attributes['aria-hidden'] = 'true';
}

$image_container_attr_string = '';

foreach ( $image_container_attributes as $attr_name => $attr_value ) {
    $image_container_attr_string .= sprintf( ' %s="%s"', esc_attr( $attr_name ), esc_attr( $attr_value ) );
}
?>

<section <?php echo $wrapper_attributes; ?>>
    <canvas class="hero__particle-canvas" aria-hidden="true" role="presentation"></canvas>
    <?php if ( '' !== $hero_image_markup ) : ?>
        <div<?php echo $image_container_attr_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Individually escaped above. ?>>
            <?php echo $hero_image_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup escaped through wp_get_attachment_image or attribute assembly. ?>
        </div>
    <?php endif; ?>
    <div class="hero-content">
        <?php echo $inner_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Content contains sanitized inner blocks/fallback markup. ?>
    </div>
</section>
