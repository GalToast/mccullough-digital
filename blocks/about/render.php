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

$section_id         = ! empty( $attributes['anchor'] ) ? $attributes['anchor'] : 'about';
$wrapper_attributes = get_block_wrapper_attributes(
    [
        'id' => $section_id,
    ]
);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <h2 class="section-title">
            <?php echo esc_html( $attributes['headline'] ); ?>
        </h2>
        <p>
            <?php echo wp_kses_post( $attributes['text'] ); ?>
        </p>
    </div>
</section>