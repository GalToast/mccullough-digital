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

$icon      = isset( $attributes['icon'] ) ? $attributes['icon'] : '';
$title     = isset( $attributes['title'] ) ? $attributes['title'] : '';
$text      = isset( $attributes['text'] ) ? $attributes['text'] : '';
$link_text = isset( $attributes['linkText'] ) ? $attributes['linkText'] : '';
$link_url  = isset( $attributes['linkUrl'] ) ? $attributes['linkUrl'] : '';
?>

<div <?php echo $wrapper_attributes; ?>>
   <div class="service-card-content">
        <div>
            <?php if ( ! empty( $icon ) ) : ?>
                <div class="icon">
                    <?php echo $icon; // Note: This will be raw SVG content from trusted admin input. ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $title ) ) : ?>
                <h3>
                    <?php echo esc_html( $title ); ?>
                </h3>
            <?php endif; ?>

            <?php if ( ! empty( $text ) ) : ?>
                <p>
                    <?php echo esc_html( $text ); ?>
                </p>
            <?php endif; ?>
        </div>
        <?php if ( ! empty( $link_text ) ) : ?>
            <a href="<?php echo esc_url( $link_url ); ?>" class="learn-more">
                <?php echo esc_html( $link_text ); ?>
            </a>
        <?php endif; ?>
    </div>
</div>