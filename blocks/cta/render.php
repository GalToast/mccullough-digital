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

$headline    = isset( $attributes['headline'] ) ? $attributes['headline'] : '';
$button_text = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : '';
$button_link = isset( $attributes['buttonLink'] ) ? $attributes['buttonLink'] : '';
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <?php if ( ! empty( $headline ) ) : ?>
            <h2 class="section-title">
                <?php echo esc_html( $headline ); ?>
            </h2>
        <?php endif; ?>

        <?php if ( ! empty( $button_text ) ) : ?>
            <a href="<?php echo esc_url( $button_link ); ?>" class="cta-button">
                <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
            </a>
        <?php endif; ?>
    </div>
</section>