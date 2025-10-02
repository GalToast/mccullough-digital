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
    ]
);

$inner_classes = [ 'container' ];
$align         = isset( $attributes['align'] ) ? $attributes['align'] : '';

if ( in_array( $align, [ 'wide', 'full' ], true ) ) {
    $inner_classes[] = 'container--align-' . $align;
}

$headline        = isset( $attributes['headline'] ) ? $attributes['headline'] : '';
$button_text     = isset( $attributes['buttonText'] ) ? trim( (string) $attributes['buttonText'] ) : '';
$raw_link        = isset( $attributes['buttonLink'] ) ? trim( (string) $attributes['buttonLink'] ) : '';
$button_link     = '' !== $raw_link ? esc_url( $raw_link ) : '';
$has_link_url    = '' !== $button_link;
$inner_content   = trim( (string) $content );
$has_inner_block = '' !== $inner_content;

if ( ! $has_inner_block ) {
    ob_start();

    if ( '' !== trim( (string) $headline ) ) {
        ?>
        <h2 class="section-title">
            <?php echo wp_kses_post( $headline ); ?>
        </h2>
        <?php
    }

    if ( '' !== $button_text ) {
        if ( $has_link_url ) {
            ?>
            <a href="<?php echo esc_url( $button_link ); ?>" class="cta-button">
                <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
            </a>
            <?php
        } else {
            ?>
            <span class="cta-button is-static">
                <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
            </span>
            <?php
        }
    }

    $inner_content = trim( (string) ob_get_clean() );
}
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="<?php echo esc_attr( implode( ' ', $inner_classes ) ); ?>">
        <?php echo $inner_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks already sanitized. ?>
    </div>
</section>
