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

$headline = isset( $attributes['headline'] ) ? $attributes['headline'] : '';
$text     = isset( $attributes['text'] ) ? $attributes['text'] : '';
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="<?php echo esc_attr( implode( ' ', $inner_classes ) ); ?>">
        <?php if ( '' !== trim( (string) $headline ) ) : ?>
            <h2 class="section-title">
                <?php echo wp_kses_post( $headline ); ?>
            </h2>
        <?php endif; ?>

        <?php if ( '' !== trim( wp_strip_all_tags( (string) $text ) ) ) : ?>
            <p>
                <?php echo wp_kses_post( $text ); ?>
            </p>
        <?php endif; ?>
    </div>
</section>