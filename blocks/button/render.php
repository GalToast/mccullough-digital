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

// If there's no button text, there's nothing to render.
if ( empty( $button_text ) ) {
    return '';
}

// Set a default class and allow WordPress to add the alignment class.
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'mcd-button-block' ) );

$button_classes  = 'cta-button hero__cta-button';
$button_contents = sprintf(
    '<span class="hero__cta-button-label">%s</span>',
    esc_html( $button_text )
);

// If a link is set, render an anchor tag.
if ( ! empty( $button_link ) ) {
    $link_attributes = array(
        'class' => $button_classes,
        'href'  => esc_url( $button_link ),
    );

    if ( $open_new_tab ) {
        $link_attributes['target'] = '_blank';
        $link_attributes['rel']    = 'noopener noreferrer'; // Add noreferrer for security with target="_blank"
    }

    $attributes_string = '';
    foreach ( $link_attributes as $name => $value ) {
        if ( ! empty( $value ) ) {
            $attributes_string .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( $value ) );
        }
    }

    // Output the linked button within the wrapper.
    return sprintf(
        '<div %1$s><a%2$s>%3$s</a></div>',
        $wrapper_attributes,
        $attributes_string,
        $button_contents
    );
}

// If no link is set, render a button element.
return sprintf(
    '<div %1$s><button class="%2$s" type="button">%3$s</button></div>',
    $wrapper_attributes,
    esc_attr( $button_classes ),
    $button_contents
);