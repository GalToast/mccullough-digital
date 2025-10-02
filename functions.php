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
 * Flag the home page seeding routine so it runs after the theme is activated.
 */
function mcd_schedule_home_page_seed() {
  if ( ! get_option( 'mcd_seed_home_page' ) ) {
    update_option( 'mcd_seed_home_page', 1 );
  }
}
add_action( 'after_switch_theme', 'mcd_schedule_home_page_seed' );

/**
 * Populate an empty home page with the default landing pattern so it is editable via the page editor.
 */
function mcd_maybe_seed_home_page() {
  if ( ! get_option( 'mcd_seed_home_page' ) ) {
    return;
  }

  if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
    return;
  }

  $registry = WP_Block_Patterns_Registry::get_instance();

  if ( ! $registry->is_registered( 'mccullough-digital/home-landing' ) ) {
    return;
  }

  delete_option( 'mcd_seed_home_page' );

  $pattern = $registry->get_registered( 'mccullough-digital/home-landing' );

  if ( empty( $pattern['content'] ) ) {
    return;
  }

  $page          = null;
  $front_page_id = (int) get_option( 'page_on_front' );

  if ( $front_page_id ) {
    $front_page = get_post( $front_page_id );

    if ( $front_page && 'page' === $front_page->post_type && 'trash' !== $front_page->post_status ) {
      $page = $front_page;
    }
  }

  if ( ! $page ) {
    $page = get_page_by_path( 'home' );

    if ( $page && 'trash' === $page->post_status ) {
      $page = null;
    }
  }

  if ( ! $page ) {
    $pages = get_posts(
      [
        'post_type'              => 'page',
        'title'                  => __( 'Home', 'mccullough-digital' ),
        'post_status'            => [ 'publish', 'pending', 'draft', 'future', 'private' ],
        'numberposts'            => 1,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
        'orderby'                => [
          'post_date' => 'ASC',
          'ID'        => 'ASC',
        ],
      ]
    );
    if ( ! empty( $pages ) ) {
      $page = $pages[0];
    }
  }

  if ( $page ) {
    $existing_content = trim( (string) $page->post_content );

    if ( '' === wp_strip_all_tags( $existing_content ) ) {
      wp_update_post(
        [
          'ID'           => $page->ID,
          'post_content' => $pattern['content'],
        ]
      );
    }

    return;
  }

  wp_insert_post(
    [
      'post_title'   => __( 'Home', 'mccullough-digital' ),
      'post_name'    => 'home',
      'post_status'  => 'publish',
      'post_type'    => 'page',
      'post_content' => $pattern['content'],
    ]
  );
}
add_action( 'init', 'mcd_maybe_seed_home_page', 20 );


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
        'style',
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

/**
 * Limit the "Most Recent" badge to the primary blog query on the first page.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block         Block context array.
 * @return string Filtered block markup.
 */
function mcd_filter_latest_badge_markup( $block_content, $block ) {
    if ( empty( $block_content ) || ! is_array( $block ) ) {
        return $block_content;
    }

    if ( is_admin() ) {
        return $block_content;
    }

    $block_name = isset( $block['blockName'] ) ? $block['blockName'] : null;

    if ( 'core/paragraph' !== $block_name ) {
        return $block_content;
    }

    $class_attribute = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';

    if ( false === strpos( $class_attribute, 'latest-badge' ) && false === strpos( $block_content, 'latest-badge' ) ) {
        return $block_content;
    }

    $show_badge = is_main_query() && is_home() && ! is_paged();

    if ( $show_badge ) {
        return $block_content;
    }

    return '';
}

add_filter( 'render_block', 'mcd_filter_latest_badge_markup', 10, 2 );
