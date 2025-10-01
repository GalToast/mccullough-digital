<?php
/**
 * Neon Button Block render callback.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block content.
 * @param WP_Block $block      Block instance.
 *
 * @package McCullough_Digital
 */

$button_text = isset( $attributes['buttonText'] ) ? trim( wp_strip_all_tags( (string) $attributes['buttonText'] ) ) : '';
$button_link = isset( $attributes['buttonLink'] ) ? trim( (string) $attributes['buttonLink'] ) : '';
$open_new_tab = isset( $attributes['opensInNewTab'] ) ? (bool) $attributes['opensInNewTab'] : false;

if ( '' === $button_text ) {
    return '';
}

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class' => 'mcd-button-block',
    )
);

$button_classes  = 'cta-button hero__cta-button';
$button_contents = sprintf(
    '<span class="hero__cta-button-label">%s</span>',
    esc_html( $button_text )
);

if ( '' !== $button_link ) {
    $link_attributes = array(
        'class' => $button_classes,
        'href'  => esc_url( $button_link ),
    );

    if ( $open_new_tab ) {
        $link_attributes['target'] = '_blank';
        $link_attributes['rel']    = 'noopener';
    }

    $attribute_pairs = array();
    foreach ( $link_attributes as $attribute => $value ) {
        if ( '' === $value ) {
            continue;
        }

        $attribute_pairs[] = sprintf(
            '%1$s="%2$s"',
            esc_attr( $attribute ),
            esc_attr( $value )
        );
    }

    $attributes_string = implode( ' ', $attribute_pairs );

    return sprintf(
        '<div %1$s><a %2$s>%3$s</a></div>',
        $wrapper_attributes,
        $attributes_string,
        $button_contents
    );
}

return sprintf(
    '<div %1$s><button class="%2$s" type="button">%3$s</button></div>',
    $wrapper_attributes,
    esc_attr( $button_classes ),
    $button_contents
);
