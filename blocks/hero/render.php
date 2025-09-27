<?php
/**
 * Hero Block Template.
 *
 * @param   array $attributes - The block attributes.
 * @param   string $content - The block inner content (empty).
 * @param   WP_Block $block - The block instance.
 *
 * @package McCullough_Digital
 */

$wrapper_attributes = get_block_wrapper_attributes(
    [
        'class' => 'hero',
    ]
);
?>

<section <?php echo $wrapper_attributes; ?>>
    <canvas class="hero__particle-canvas" aria-hidden="true" role="presentation"></canvas>
    <div class="hero-content">
        <h1 class="wp-block-heading hero__headline">
            <?php echo wp_kses_post( $attributes['headline'] ?? '' ); ?>
        </h1>
        <p>
            <?php echo wp_kses_post( $attributes['subheading'] ?? '' ); ?>
        </p>
        <?php
        $button_text = isset( $attributes['buttonText'] ) ? trim( (string) $attributes['buttonText'] ) : '';
        $raw_link    = isset( $attributes['buttonLink'] ) ? trim( (string) $attributes['buttonLink'] ) : '';
        $button_link = '' !== $raw_link ? esc_url( $raw_link ) : '';

        if ( '' !== $button_text ) :
            if ( '' !== $button_link ) :
                ?>
                <a href="<?php echo $button_link; ?>" class="cta-button">
                    <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
                </a>
                <?php
            else :
                ?>
                <span class="cta-button is-static" aria-hidden="true">
                    <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
                </span>
                <?php
            endif;
        endif;
        ?>
    </div>
</section>