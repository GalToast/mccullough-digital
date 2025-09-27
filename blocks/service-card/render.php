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
?>

<div <?php echo $wrapper_attributes; ?>>
   <div class="service-card-content">
        <div>
            <div class="icon">
                <?php echo $attributes['icon']; // Note: This will be raw SVG content. It's ok since it's from an admin. ?>
            </div>
            <h3>
                <?php echo esc_html( $attributes['title'] ); ?>
            </h3>
            <p>
                <?php echo esc_html( $attributes['text'] ); ?>
            </p>
        </div>
        <a href="<?php echo esc_url( $attributes['linkUrl'] ); ?>" class="learn-more">
            <?php echo esc_html( $attributes['linkText'] ); ?>
        </a>
    </div>
</div>