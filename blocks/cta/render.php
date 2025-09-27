<?php
/**
 * CTA Block Template.
 *
 * @param   array $attributes - The block attributes.
 * @param   string $content - The block inner content (empty).
 * @param   WP_Block $block - The block instance.
 *
 * @package McCullough_Digital
 */

$wrapper_attributes = get_block_wrapper_attributes(
    [
        'id'    => 'contact', // Keep the ID for anchor links
        'class' => 'cta-section',
    ]
);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <h2 class="section-title">
            <?php echo esc_html( $attributes['headline'] ?? '' ); ?>
        </h2>
        <?php
        $button_text = isset( $attributes['buttonText'] ) ? trim( (string) $attributes['buttonText'] ) : '';

        if ( '' !== $button_text ) :
            $button_link = isset( $attributes['buttonLink'] ) ? esc_url( $attributes['buttonLink'] ) : '';
            if ( '' === $button_link ) {
                $button_link = '#';
            }
            ?>
            <a href="<?php echo $button_link; ?>" class="cta-button">
                <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
            </a>
        <?php endif; ?>
    </div>
</section>