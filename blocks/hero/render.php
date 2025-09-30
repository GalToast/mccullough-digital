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

$wrapper_attributes = get_block_wrapper_attributes(
    [
        'class' => 'hero',
    ]
);

$headline   = isset( $attributes['headline'] ) ? $attributes['headline'] : '';
$subheading = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';

// Image attributes
$hero_image_url         = isset( $attributes['heroImageUrl'] ) ? esc_url( $attributes['heroImageUrl'] ) : '';
$hero_image_alt         = isset( $attributes['heroImageAlt'] ) ? esc_attr( $attributes['heroImageAlt'] ) : '';
$image_position         = isset( $attributes['imagePosition'] ) ? $attributes['imagePosition'] : 'bottom-right';
$image_size             = isset( $attributes['imageSize'] ) ? intval( $attributes['imageSize'] ) : 40;
$image_opacity          = isset( $attributes['imageOpacity'] ) ? intval( $attributes['imageOpacity'] ) : 40;
$image_vertical_offset  = isset( $attributes['imageVerticalOffset'] ) ? intval( $attributes['imageVerticalOffset'] ) : 0;
$hide_image_on_mobile   = isset( $attributes['hideImageOnMobile'] ) && $attributes['hideImageOnMobile'] === true;

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

    $button_text = isset( $attributes['buttonText'] ) ? trim( (string) $attributes['buttonText'] ) : '';
    $raw_link    = isset( $attributes['buttonLink'] ) ? trim( (string) $attributes['buttonLink'] ) : '';
    $button_link = '' !== $raw_link ? esc_url( $raw_link ) : '';

    if ( '' !== $button_text ) {
        if ( '' !== $button_link ) {
            ?>
            <a href="<?php echo esc_url( $button_link ); ?>" class="cta-button">
                <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
            </a>
            <?php
        } else {
            ?>
            <span class="cta-button is-static" aria-hidden="true">
                <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
            </span>
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
$image_styles[] = 'width: clamp(200px, ' . $image_size . 'vw, 800px);';

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
