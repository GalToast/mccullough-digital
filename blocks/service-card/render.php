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
    ]
);
?>

<div <?php echo $wrapper_attributes; ?>>
   <div class="service-card-content">
        <div>
            <?php
            $icon_svg = isset( $attributes['icon'] ) ? mcd_sanitize_svg( $attributes['icon'] ) : '';

            if ( '' !== $icon_svg ) :
                ?>
                <div class="icon" aria-hidden="true" role="presentation">
                    <?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG markup. ?>
                </div>
            <?php endif; ?>
            <h3>
                <?php echo esc_html( $attributes['title'] ?? '' ); ?>
            </h3>
            <p>
                <?php echo esc_html( $attributes['text'] ?? '' ); ?>
            </p>
        </div>
        <?php
        $link_text = isset( $attributes['linkText'] ) ? trim( (string) $attributes['linkText'] ) : '';

        if ( '' !== $link_text ) :
            $link_url = isset( $attributes['linkUrl'] ) ? esc_url( $attributes['linkUrl'] ) : '';
            if ( '' === $link_url ) {
                $link_url = '#';
            }
            ?>
            <a href="<?php echo $link_url; ?>" class="learn-more">
                <?php echo esc_html( $link_text ); ?>
            </a>
        <?php endif; ?>
    </div>
</div>