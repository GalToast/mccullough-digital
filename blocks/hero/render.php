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

$headline    = isset( $attributes['headline'] ) ? $attributes['headline'] : '';
$subheading  = isset( $attributes['subheading'] ) ? $attributes['subheading'] : '';
$button_text = isset( $attributes['buttonText'] ) ? $attributes['buttonText'] : '';
$button_link = isset( $attributes['buttonLink'] ) ? $attributes['buttonLink'] : '';
?>

<section <?php echo $wrapper_attributes; ?>>
    <canvas id="particle-canvas"></canvas>
    <div class="hero-content">
        <?php if ( ! empty( $headline ) ) : ?>
            <h1 id="interactive-headline" class="wp-block-heading">
                <?php echo wp_kses_post( $headline ); ?>
            </h1>
        <?php endif; ?>

        <?php if ( ! empty( $subheading ) ) : ?>
            <p>
                <?php echo wp_kses_post( $subheading ); ?>
            </p>
        <?php endif; ?>

        <?php if ( ! empty( $button_text ) ) : ?>
            <a href="<?php echo esc_url( $button_link ); ?>" class="cta-button">
                <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
            </a>
        <?php endif; ?>
    </div>
</section>