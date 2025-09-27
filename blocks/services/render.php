<?php
/**
 * Services Block Template.
 *
 * @param   array $attributes - The block attributes.
 * @param   string $content - The block inner content (from InnerBlocks).
 * @param   WP_Block $block - The block instance.
 *
 * @package McCullough_Digital
 */

$section_id         = ! empty( $attributes['anchor'] ) ? $attributes['anchor'] : 'services';
$wrapper_attributes = get_block_wrapper_attributes(
    [
        'id' => $section_id,
    ]
);

// The InnerBlocks template defines the allowed blocks and their default state.
$allowed_blocks = [ 'mccullough-digital/service-card' ];
$template = [
    [ 'mccullough-digital/service-card' ],
    [ 'mccullough-digital/service-card' ],
    [ 'mccullough-digital/service-card' ],
];

?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="container">
        <h2 class="section-title">
            <?php echo wp_kses_post( $attributes['headline'] ); ?>
        </h2>
        <div class="services-grid">
            <?php echo $content; ?>
        </div>
    </div>
</section>