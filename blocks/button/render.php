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

// Debug: Log what we're receiving (remove this after debugging)
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    error_log( 'Button Block Attributes: ' . print_r( $attributes, true ) );
}

$admin_debug_comment = '';
$button_text         = isset( $attributes['buttonText'] ) ? trim( wp_strip_all_tags( (string) $attributes['buttonText'] ) ) : '';
$button_link = isset( $attributes['buttonLink'] ) ? trim( (string) $attributes['buttonLink'] ) : '';
$open_new_tab = isset( $attributes['opensInNewTab'] ) ? (bool) $attributes['opensInNewTab'] : false;

if ( '' === $button_text ) {
    $default_button_text = '';

    if ( class_exists( 'WP_Block_Type_Registry' ) ) {
        $block_type = WP_Block_Type_Registry::get_instance()->get_registered( 'mccullough-digital/button' );

        if ( $block_type && isset( $block_type->attributes['buttonText']['default'] ) ) {
            $default_button_text = trim( wp_strip_all_tags( (string) $block_type->attributes['buttonText']['default'] ) );
        }
    }

    if ( '' === $default_button_text ) {
        $block_metadata_path = trailingslashit( __DIR__ ) . 'block.json';

        if ( file_exists( $block_metadata_path ) ) {
            if ( function_exists( 'wp_json_file_decode' ) ) {
                $metadata = wp_json_file_decode( $block_metadata_path, array( 'associative' => true ) );
                if ( is_wp_error( $metadata ) ) {
                    $metadata = null;
                }
            } else {
                $metadata = json_decode( file_get_contents( $block_metadata_path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
            }

            if ( is_array( $metadata ) && isset( $metadata['attributes']['buttonText']['default'] ) ) {
                $default_button_text = trim( wp_strip_all_tags( (string) $metadata['attributes']['buttonText']['default'] ) );
            }
        }
    }

    if ( '' !== $default_button_text ) {
        $button_text = $default_button_text;
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG && current_user_can( 'manage_options' ) ) {
        $admin_debug_comment = '<!-- Neon Button Block: No buttonText attribute received -->';
    }
}

// get_block_wrapper_attributes() will add the correct alignment class based on the `align` attribute.
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
    $link_attrs = array(
        'class' => $button_classes,
        'href'  => esc_url( $button_link ),
    );

    if ( $open_new_tab ) {
        $link_attrs['target'] = '_blank';
        $link_attrs['rel']    = 'noopener';
    }

    $attrs_str = '';
    foreach ( $link_attrs as $key => $value ) {
        $attrs_str .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
    }

    return $admin_debug_comment . sprintf(
        '<div %1$s><a%2$s>%3$s</a></div>',
        $wrapper_attributes,
        $attrs_str,
        $button_contents
    );
}

return $admin_debug_comment . sprintf(
    '<div %1$s><button class="%2$s" type="button">%3$s</button></div>',
    $wrapper_attributes,
    esc_attr( $button_classes ),
    $button_contents
);
