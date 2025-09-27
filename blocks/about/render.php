<?php
/**
 * About Block Template.
 *
 * @param   array $attributes - The block attributes.
 * @param   string $content - The block inner content (empty).
 * @param   WP_Block $block - The block instance.
 *
 * @package McCullough_Digital
 */

$wrapper_attributes = get_block_wrapper_attributes(
    [
        'id' => 'about', // Keep the ID for anchor links
    ]
);

$headline = isset( $attributes['headline'] ) ? $attributes['headline'] : '';
$text     = isset( $attributes['text'] ) ? $attributes['text'] : '';
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <?php if ( ! empty( $headline ) ) : ?>
            <h2 class="section-title">
                <?php echo esc_html( $headline ); ?>
            </h2>
        <?php endif; ?>

        <?php if ( ! empty( $text ) ) : ?>
            <p>
                <?php echo wp_kses_post( $text ); ?>
            </p>
        <?php endif; ?>
    </div>
</section>