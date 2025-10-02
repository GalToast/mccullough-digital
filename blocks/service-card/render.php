<?php
/**
 * Service Card Block Template.
 *
 * @param   array $attributes - The block attributes.
 * @param   string $content - The block inner content (empty).
 * @param   WP_Block $block - The block instance.
 *
 * @package McCullough_Digital
 */

$wrapper_attributes = get_block_wrapper_attributes(
    [
        'class' => 'service-card',
    ]
);

$title = isset( $attributes['title'] ) ? $attributes['title'] : '';
$text  = isset( $attributes['text'] ) ? $attributes['text'] : '';

$icon_svg = isset( $attributes['icon'] ) ? mcd_sanitize_svg( $attributes['icon'] ) : '';

$link_text = isset( $attributes['linkText'] ) ? trim( (string) $attributes['linkText'] ) : '';
$link_raw  = isset( $attributes['linkUrl'] ) ? trim( (string) $attributes['linkUrl'] ) : '';
$link_url  = '' !== $link_raw ? esc_url( $link_raw ) : '';
$has_link  = '' !== $link_url;

$parsed_inner_blocks = [];

if ( $block instanceof WP_Block && isset( $block->parsed_block['innerBlocks'] ) ) {
    $parsed_inner_blocks = $block->parsed_block['innerBlocks'];
}

$body_html       = '';
$cta_html        = '';
$additional_html = '';

if ( ! empty( $parsed_inner_blocks ) ) {
    foreach ( $parsed_inner_blocks as $inner_block ) {
        if ( ! is_array( $inner_block ) || ! isset( $inner_block['blockName'] ) ) {
            continue;
        }

        $block_name = $inner_block['blockName'];

        if ( '' === $body_html && 'core/group' === $block_name ) {
            $body_html = render_block( $inner_block );
            continue;
        }

        if ( 'core/buttons' === $block_name ) {
            $cta_html .= render_block( $inner_block );
            continue;
        }

        $additional_html .= render_block( $inner_block );
    }
}

if ( '' === $body_html ) {
    ob_start();
    ?>
    <div>
        <?php if ( '' !== $icon_svg ) : ?>
            <div class="icon" aria-hidden="true" role="presentation">
                <?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitized SVG markup. ?>
            </div>
        <?php endif; ?>

        <?php if ( '' !== trim( wp_strip_all_tags( (string) $title ) ) ) : ?>
            <h3>
                <?php echo wp_kses_post( $title ); ?>
            </h3>
        <?php endif; ?>

        <?php if ( '' !== trim( wp_strip_all_tags( (string) $text ) ) ) : ?>
            <p>
                <?php echo wp_kses_post( $text ); ?>
            </p>
        <?php endif; ?>
    </div>
    <?php
    $body_html = ob_get_clean();
}

if ( '' === $cta_html && '' !== $link_text ) {
    ob_start();

    if ( $has_link ) {
        ?>
        <a href="<?php echo esc_url( $link_url ); ?>" class="learn-more">
            <?php echo esc_html( $link_text ); ?>
        </a>
        <?php
    } else {
        ?>
        <span class="learn-more is-static">
            <?php echo esc_html( $link_text ); ?>
        </span>
        <?php
    }

    $cta_html = ob_get_clean();
}
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="service-card-content">
        <?php if ( '' !== trim( (string) $body_html ) ) : ?>
            <?php echo $body_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner block/fallback output. ?>
        <?php endif; ?>
        <?php if ( '' !== trim( (string) $cta_html ) ) : ?>
            <?php echo $cta_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner block/fallback output. ?>
        <?php endif; ?>
        <?php if ( '' !== trim( (string) $additional_html ) ) : ?>
            <?php echo $additional_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner block output. ?>
        <?php endif; ?>
    </div>
</div>
