<?php
/**
 * Services Block Template.
 *
 * @param   array    $attributes - The block attributes.
 * @param   string   $content    - The block inner content (from InnerBlocks).
 * @param   WP_Block $block      - The block instance.
 *
 * @package McCullough_Digital
 */

$metadata = wp_json_file_decode(
    __DIR__ . '/block.json',
    [
        'associative' => true,
    ]
);

$inner_blocks    = $metadata['innerBlocks'] ?? [];
$allowed_blocks  = $inner_blocks['allowedBlocks'] ?? [ 'mccullough-digital/service-card' ];
$template        = $inner_blocks['template'] ?? [
    [ 'mccullough-digital/service-card' ],
    [ 'mccullough-digital/service-card' ],
    [ 'mccullough-digital/service-card' ],
];

// The $allowed_blocks and $template variables mirror the editor configuration for future use.

$wrapper_attributes = get_block_wrapper_attributes(
    [
        'id' => 'services', // Keep the ID for anchor links.
    ]
);

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
