<?php
/**
 * Service Card Block Template.
 *
 * @param   array $attributes - The block attributes.
 * @param   string $content - The block inner content (empty).
 * @param   WP_Block $block - The block instance.
 *
 * @package McCullough_Digital
 */

$wrapper_attributes = get_block_wrapper_attributes(
    [
        'class' => 'service-card',
    ],
    $block
);

$title = isset( $attributes['title'] ) ? $attributes['title'] : '';
$text  = isset( $attributes['text'] ) ? $attributes['text'] : '';

$icon_svg = isset( $attributes['icon'] ) ? mcd_sanitize_svg( $attributes['icon'] ) : '';

$link_text = isset( $attributes['linkText'] ) ? trim( (string) $attributes['linkText'] ) : '';
$link_raw  = isset( $attributes['linkUrl'] ) ? trim( (string) $attributes['linkUrl'] ) : '';
$link_url  = '' !== $link_raw ? esc_url( $link_raw ) : '';
$has_link  = '' !== $link_url;
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="service-card-content">
        <div>
            <?php if ( '' !== $icon_svg ) : ?>
                <div class="icon" aria-hidden="true" role="presentation">
                    <?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG markup. ?>
                </div>
            <?php endif; ?>

            <?php if ( '' !== trim( wp_strip_all_tags( (string) $title ) ) ) : ?>
                <h3>
                    <?php echo wp_kses_post( $title ); ?>
                </h3>
            <?php endif; ?>

            <?php if ( '' !== trim( wp_strip_all_tags( (string) $text ) ) ) : ?>
                <p>
                    <?php echo wp_kses_post( $text ); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php if ( '' !== $link_text ) : ?>
            <?php if ( $has_link ) : ?>
                <a href="<?php echo esc_url( $link_url ); ?>" class="learn-more">
                    <?php echo esc_html( $link_text ); ?>
                </a>
            <?php else : ?>
                <span class="learn-more is-static" aria-hidden="true">
                    <?php echo esc_html( $link_text ); ?>
                </span>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
