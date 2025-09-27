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
        <a href="<?php echo esc_url( $attributes['buttonLink'] ?? '#' ); ?>" class="cta-button">
            <span class="btn-text"><?php echo esc_html( $attributes['buttonText'] ?? '' ); ?></span>
        </a>
    </div>
</section>