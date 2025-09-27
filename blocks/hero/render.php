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
?>

<section <?php echo $wrapper_attributes; ?>>
    <canvas id="particle-canvas"></canvas>
    <div class="hero-content">
        <h1 id="interactive-headline" class="wp-block-heading">
            <?php echo wp_kses_post( $attributes['headline'] ); ?>
        </h1>
        <p>
            <?php echo wp_kses_post( $attributes['subheading'] ); ?>
        </p>
        <?php if ( ! empty( $attributes['buttonText'] ) ) : ?>
            <a href="<?php echo esc_url( $attributes['buttonLink'] ); ?>" class="cta-button">
                <span class="btn-text"><?php echo esc_html( $attributes['buttonText'] ); ?></span>
            </a>
        <?php endif; ?>
    </div>
</section>