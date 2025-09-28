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
    [],
    $block
);

$inner_classes = [ 'container' ];
$align         = isset( $attributes['align'] ) ? $attributes['align'] : '';

if ( in_array( $align, [ 'wide', 'full' ], true ) ) {
    $inner_classes[] = 'container--align-' . $align;
}

$headline      = isset( $attributes['headline'] ) ? $attributes['headline'] : '';
$text          = isset( $attributes['text'] ) ? $attributes['text'] : '';
$inner_content = trim( (string) $content );

if ( '' === $inner_content ) {
    ob_start();

    if ( '' !== trim( (string) $headline ) ) {
        ?>
        <h2 class="section-title">
            <?php echo wp_kses_post( $headline ); ?>
        </h2>
        <?php
    }

    if ( '' !== trim( wp_strip_all_tags( (string) $text ) ) ) {
        ?>
        <p>
            <?php echo wp_kses_post( $text ); ?>
        </p>
        <?php
    }

    $inner_content = trim( (string) ob_get_clean() );
}
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="<?php echo esc_attr( implode( ' ', $inner_classes ) ); ?>">
        <?php echo $inner_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks already sanitized. ?>
    </div>
</section>