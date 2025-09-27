<?php
/**
 * CTA Block Template.
 *
 * @param   array $attributes - The block attributes.
 * @param   string $content - The block inner content (empty).
 * @param   WP_Block $block - The block instance.
 *
 * @package McCullough_Digital
 */

$wrapper_attributes = get_block_wrapper_attributes(
    [
        'class' => 'cta-section',
    ],
    $block
);

$inner_classes = [ 'container' ];
$align         = isset( $attributes['align'] ) ? $attributes['align'] : '';

if ( in_array( $align, [ 'wide', 'full' ], true ) ) {
    $inner_classes[] = 'container--align-' . $align;
}

$headline     = isset( $attributes['headline'] ) ? $attributes['headline'] : '';
$button_text  = isset( $attributes['buttonText'] ) ? trim( (string) $attributes['buttonText'] ) : '';
$raw_link     = isset( $attributes['buttonLink'] ) ? trim( (string) $attributes['buttonLink'] ) : '';
$button_link  = '' !== $raw_link ? esc_url( $raw_link ) : '';
$has_link_url = '' !== $button_link;
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="<?php echo esc_attr( implode( ' ', $inner_classes ) ); ?>">
        <?php if ( '' !== trim( (string) $headline ) ) : ?>
            <h2 class="section-title">
                <?php echo wp_kses_post( $headline ); ?>
            </h2>
        <?php endif; ?>

        <?php if ( '' !== $button_text ) : ?>
            <?php if ( $has_link_url ) : ?>
                <a href="<?php echo $button_link; ?>" class="cta-button">
                    <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
                </a>
            <?php else : ?>
                <span class="cta-button is-static" aria-hidden="true">
                    <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
                </span>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
