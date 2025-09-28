<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Theme setup
 */
function mcd_setup() {
  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
  add_theme_support(
    'custom-logo',
    array(
      'height'      => 120,
      'width'       => 120,
      'flex-width'  => true,
      'flex-height' => true,
    )
  );

  add_theme_support( 'wp-block-styles' );
  add_theme_support( 'responsive-embeds' );
  add_theme_support( 'editor-styles' );
  add_editor_style( 'editor-style.css' );
}
add_action( 'after_setup_theme', 'mcd_setup' );

/**
 * Assets
 */
function mcd_assets() {
  $theme_version = wp_get_theme()->get( 'Version' );

  wp_enqueue_style(
    'mcd-fonts',
    'https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Nunito:wght@300;400;700&display=swap',
    array(),
    null
  );

  // Enqueue main stylesheet
  wp_enqueue_style( 'mcd-style', get_stylesheet_uri(), array( 'mcd-fonts' ), $theme_version );

  // Cache-bust the theme interaction script by filemtime if possible
  $script_path = get_stylesheet_directory() . '/js/header-scripts.js';
  $ver         = file_exists( $script_path ) ? filemtime( $script_path ) : $theme_version;
  wp_enqueue_script( 'mcd-header-scripts', get_stylesheet_directory_uri() . '/js/header-scripts.js', array(), $ver, true );
}
add_action( 'wp_enqueue_scripts', 'mcd_assets' );

/**
 * Back-compat for wp_body_open (if very old WP)
 */
if ( ! function_exists( 'wp_body_open' ) ) {
  function wp_body_open() { do_action( 'wp_body_open' ); }
}

/**
 * Register Blocks
 */
function mcd_register_blocks() {
    $blocks_dir = get_stylesheet_directory() . '/blocks/';
    if ( ! file_exists( $blocks_dir ) || ! is_dir( $blocks_dir ) ) {
        return;
    }

    $block_folders = scandir( $blocks_dir );

    foreach ( $block_folders as $block_folder ) {
        if ( $block_folder === '.' || $block_folder === '..' ) {
            continue;
        }

        $block_path = $blocks_dir . $block_folder;

        if ( is_dir( $block_path ) && file_exists( $block_path . '/block.json' ) ) {
            register_block_type( $block_path );
        }
    }
}
add_action( 'init', 'mcd_register_blocks' );

/**
 * Custom Block Category
 */
function mcd_block_categories( $categories ) {
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'mcd-blocks',
                'title' => __( 'McCullough Digital Blocks', 'mccullough-digital' ),
                'icon'  => 'star-filled',
            ],
        ]
    );
}
add_action( 'block_categories_all', 'mcd_block_categories' );

/**
 * Register custom pattern category for theme patterns.
 */
function mcd_register_pattern_category() {
    if ( function_exists( 'register_block_pattern_category' ) ) {
        register_block_pattern_category(
            'mccullough-digital-sections',
            [
                'label' => __( 'McCullough Digital Sections', 'mccullough-digital' ),
            ]
        );
    }
}
add_action( 'init', 'mcd_register_pattern_category' );

/**
 * Mark that the home page content should be seeded with the landing pattern after theme switch.
 */
function mcd_schedule_home_pattern_seed() {
    update_option( 'mcd_seed_home_pattern', 1, false );
}
add_action( 'after_switch_theme', 'mcd_schedule_home_pattern_seed' );

/**
 * Populate the front page with the default landing layout if it's empty.
 */
function mcd_maybe_seed_home_page_content() {
    if ( ! get_option( 'mcd_seed_home_pattern' ) ) {
        return;
    }

    if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
        return;
    }

    $registry = WP_Block_Patterns_Registry::get_instance();
    if ( ! $registry->is_registered( 'mccullough-digital/home-landing' ) ) {
        return;
    }

    $pattern = $registry->get_registered( 'mccullough-digital/home-landing' );
    if ( empty( $pattern['content'] ) ) {
        return;
    }

    $front_page_id = (int) get_option( 'page_on_front' );

    if ( ! $front_page_id ) {
        $existing_home = get_page_by_path( 'home' );

        if ( $existing_home instanceof WP_Post && 'page' === $existing_home->post_type && 'publish' === $existing_home->post_status ) {
            $front_page_id = (int) $existing_home->ID;
        }

        if ( ! $front_page_id ) {
            // Check for existing page by title before creating a new one to avoid duplicates.
            $existing_home_by_title = get_page_by_title( 'Home', OBJECT, 'page' );

            if ( $existing_home_by_title instanceof WP_Post && 'page' === $existing_home_by_title->post_type ) {
                $front_page_id = (int) $existing_home_by_title->ID;
            } else {
                $new_page_args = [
                    'post_title'   => __( 'Home', 'mccullough-digital' ),
                    'post_name'    => 'home',
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_content' => $pattern['content'],
                ];

                $inserted_page = wp_insert_post( $new_page_args, true );

                if ( is_wp_error( $inserted_page ) ) {
                    delete_option( 'mcd_seed_home_pattern' );
                    return;
                }

                $front_page_id = (int) $inserted_page;
            }
        }

        if ( $front_page_id ) {
            update_option( 'page_on_front', $front_page_id );
            update_option( 'show_on_front', 'page' );
        }
    }

    if ( ! $front_page_id ) {
        delete_option( 'mcd_seed_home_pattern' );
        return;
    }

    $front_page = get_post( $front_page_id );

    if ( ! $front_page || 'page' !== $front_page->post_type ) {
        delete_option( 'mcd_seed_home_pattern' );
        return;
    }

    if ( 'publish' !== $front_page->post_status ) {
        delete_option( 'mcd_seed_home_pattern' );
        return;
    }

    if ( trim( (string) $front_page->post_content ) !== '' ) {
        delete_option( 'mcd_seed_home_pattern' );
        return;
    }

    $result = wp_update_post(
        [
            'ID'           => $front_page_id,
            'post_content' => $pattern['content'],
        ],
        true
    );

    if ( ! is_wp_error( $result ) ) {
        delete_option( 'mcd_seed_home_pattern' );
    }
}
add_action( 'init', 'mcd_maybe_seed_home_page_content', 20 );

/**
 * Sanitizes SVG code using a whitelist of allowed tags and attributes.
 *
 * @param string $svg The SVG code to sanitize.
 * @return string Sanitized SVG code.
 */
function mcd_sanitize_svg( $svg ) {
    if ( ! class_exists( 'DOMDocument' ) || ! is_string( $svg ) || '' === trim( $svg ) ) {
        return '';
    }

    // Disable network access when parsing XML to guard against XXE attacks.
    $previous_entity_loader = null;
    if ( function_exists( 'libxml_disable_entity_loader' ) && PHP_VERSION_ID < 80000 ) {
        $previous_entity_loader = libxml_disable_entity_loader( true );
    }

    libxml_use_internal_errors( true );

    $dom = new DOMDocument();

    $loaded = $dom->loadXML( $svg, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );

    libxml_clear_errors();

    if ( null !== $previous_entity_loader && function_exists( 'libxml_disable_entity_loader' ) ) {
        libxml_disable_entity_loader( $previous_entity_loader );
    }

    if ( ! $loaded || ! $dom->documentElement ) {
        return '';
    }

    $allowed_tags = [
        'svg'            => [ 'xmlns', 'viewbox', 'viewBox', 'fill', 'width', 'height', 'class', 'aria-hidden', 'focusable', 'role' ],
        'path'           => [ 'd', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'fill-rule', 'clip-rule', 'stroke-dasharray', 'stroke-miterlimit' ],
        'g'              => [ 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'transform', 'opacity', 'class', 'fill-rule', 'clip-path' ],
        'rect'           => [ 'x', 'y', 'width', 'height', 'rx', 'ry', 'fill', 'stroke', 'stroke-width', 'transform', 'opacity' ],
        'circle'         => [ 'cx', 'cy', 'r', 'fill', 'stroke', 'stroke-width', 'opacity' ],
        'ellipse'        => [ 'cx', 'cy', 'rx', 'ry', 'fill', 'stroke', 'stroke-width', 'opacity' ],
        'line'           => [ 'x1', 'y1', 'x2', 'y2', 'stroke', 'stroke-width', 'stroke-linecap', 'opacity' ],
        'polyline'       => [ 'points', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'opacity' ],
        'polygon'        => [ 'points', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'opacity' ],
        'defs'           => [],
        'symbol'         => [ 'id', 'viewbox', 'viewBox', 'preserveaspectratio' ],
        'use'            => [ 'href', 'xlink:href', 'x', 'y', 'width', 'height', 'transform' ],
        'lineargradient' => [ 'id', 'x1', 'y1', 'x2', 'y2', 'gradientunits', 'gradienttransform', 'href', 'xlink:href', 'spreadmethod' ],
        'radialgradient' => [ 'id', 'cx', 'cy', 'r', 'fx', 'fy', 'gradientunits', 'gradienttransform', 'href', 'xlink:href', 'spreadmethod' ],
        'stop'           => [ 'offset', 'stop-color', 'stop-opacity' ],
        'clippath'       => [ 'id', 'clippathunits' ],
        'mask'           => [ 'id', 'x', 'y', 'width', 'height', 'maskunits', 'maskcontentunits' ],
        'pattern'        => [ 'id', 'x', 'y', 'width', 'height', 'patternunits', 'patterntransform', 'viewbox', 'viewBox', 'href', 'xlink:href' ],
        'title'          => [],
        'desc'           => [],
    ];

    $allowed_global_attributes = [
        'id',
        'class',
        'aria-hidden',
        'focusable',
        'role',
        'aria-label',
        'aria-labelledby',
        'aria-describedby',
        'xmlns:xlink',
        'xml:space',
        'xlink:href',
    ];

    $nodes = [ $dom->documentElement ];

    while ( $nodes ) {
        /** @var DOMElement $node */
        $node = array_pop( $nodes );

        $tag_name = strtolower( $node->tagName );

        if ( ! isset( $allowed_tags[ $tag_name ] ) ) {
            $node->parentNode->removeChild( $node );
            continue;
        }

        if ( $node->hasAttributes() ) {
            foreach ( iterator_to_array( $node->attributes ) as $attr ) {
                $attr_name = strtolower( $attr->name );

                if ( in_array( $attr_name, $allowed_global_attributes, true ) ) {
                    continue;
                }

                $allowed_for_tag = $allowed_tags[ $tag_name ];
                if ( ! in_array( $attr_name, $allowed_for_tag, true ) ) {
                    $node->removeAttribute( $attr->name );
                    continue;
                }

                $attribute_value = trim( (string) $attr->value );

                $fragment_only_attributes = [ 'href', 'xlink:href' ];
                if ( in_array( $attr_name, $fragment_only_attributes, true ) ) {
                    if ( '' === $attribute_value || '#' !== $attribute_value[0] ) {
                        $node->removeAttribute( $attr->name );
                        continue;
                    }
                }

                $url_fragment_attributes = [ 'clip-path', 'filter', 'mask', 'fill', 'stroke' ];
                if (
                    in_array( $attr_name, $url_fragment_attributes, true )
                    && preg_match( '/^url\((?!\s*#)/i', $attribute_value )
                ) {
                    $node->removeAttribute( $attr->name );
                    continue;
                }
            }
        }

        foreach ( $node->childNodes as $child ) {
            if ( $child instanceof DOMElement ) {
                $nodes[] = $child;
            }
        }
    }

    return $dom->saveXML( $dom->documentElement );
}

/**
 * Get the SVG for a social link.
 *
 * @param string $url The URL to get the SVG for.
 * @return string The SVG markup or empty string.
 */
function mcd_get_social_link_svg( $url ) {
    $host = wp_parse_url( $url, PHP_URL_HOST );

    if ( ! $host ) {
        return '';
    }

    $host = strtolower( (string) $host );
    $host = preg_replace( '#^www\.#', '', $host );

    // Corrected regex patterns with escaped dots for accuracy.
    $patterns = [
        'twitter'  => [
            '/(^|\.)twitter\.[a-z0-9.-]+$/i',
            '/(^|\.)x\.com$/i',
        ],
        'linkedin' => [ '/(^|\.)linkedin\.[a-z0-9.-]+$/i' ],
        'github'   => [ '/(^|\.)github\.[a-z0-9.-]+$/i' ],
    ];

    /**
     * Filter the social link icon patterns. This allows child themes or plugins
     * to add support for more social networks without modifying theme code.
     *
     * @since 1.2.0
     *
     * @param array  $patterns An associative array of icon names to regex patterns.
     * @param string $host     The hostname being checked.
     */
    $patterns = apply_filters( 'mcd_social_link_svg_patterns', $patterns, $host );

    $icon_name = '';

    foreach ( $patterns as $icon => $regex_list ) {
        foreach ( $regex_list as $regex ) {
            if ( preg_match( $regex, $host ) ) {
                $icon_name = $icon;
                break 2;
            }
        }
    }

    if ( '' === $icon_name ) {
        return '';
    }

    $icon_path = get_stylesheet_directory() . '/assets/icons/' . $icon_name . '.svg';

    if ( ! file_exists( $icon_path ) ) {
        return '';
    }

    $svg = file_get_contents( $icon_path );
    $svg = mcd_sanitize_svg( $svg );

    if ( '' === $svg ) {
        return '';
    }

    if ( false === strpos( $svg, 'aria-hidden' ) ) {
        $svg = preg_replace( '/<svg\s+/i', '<svg aria-hidden="true" role="img" ', $svg, 1 );
    }

    return $svg;
}
