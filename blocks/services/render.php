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

$wrapper_attributes = get_block_wrapper_attributes(
    []
);

$inner_classes = [ 'container' ];
$align         = isset( $attributes['align'] ) ? $attributes['align'] : '';

if ( in_array( $align, [ 'wide', 'full' ], true ) ) {
    $inner_classes[] = 'container--align-' . $align;
}

$headline = isset( $attributes['headline'] ) ? $attributes['headline'] : '';

$parsed_inner_blocks = [];

if ( $block instanceof WP_Block && isset( $block->parsed_block['innerBlocks'] ) ) {
    $parsed_inner_blocks = $block->parsed_block['innerBlocks'];
}

$rendered_heading = '';
$rendered_cards   = '';
$additional_html  = '';

if ( ! empty( $parsed_inner_blocks ) ) {
    foreach ( $parsed_inner_blocks as $inner_block ) {
        if ( ! is_array( $inner_block ) || ! isset( $inner_block['blockName'] ) ) {
            continue;
        }

        $block_name = $inner_block['blockName'];

        if ( '' === $rendered_heading && 'core/heading' === $block_name ) {
            $rendered_heading = render_block( $inner_block );
            continue;
        }

        if ( 'mccullough-digital/service-card' === $block_name ) {
            $rendered_cards .= render_block( $inner_block );
            continue;
        }

        $additional_html .= render_block( $inner_block );
    }
}

if ( '' === $rendered_heading && '' !== trim( (string) $headline ) ) {
    $rendered_heading = sprintf(
        '<h2 class="section-title">%s</h2>',
        wp_kses_post( $headline )
    );
}

if ( '' === $rendered_cards ) {
    $rendered_cards = trim( (string) $content );
}
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="<?php echo esc_attr( implode( ' ', $inner_classes ) ); ?>">
        <?php if ( '' !== trim( (string) $rendered_heading ) ) : ?>
            <?php echo $rendered_heading; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped via render_block/wp_kses_post. ?>
        <?php endif; ?>
        <?php if ( '' !== trim( (string) $rendered_cards ) ) : ?>
            <div class="services-grid">
                <?php echo $rendered_cards; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks output. ?>
            </div>
        <?php endif; ?>
        <?php if ( '' !== trim( (string) $additional_html ) ) : ?>
            <?php echo $additional_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks output. ?>
        <?php endif; ?>
    </div>
</section>
