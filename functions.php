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

  register_nav_menus(
    array(
      'primary' => __( 'Primary Menu', 'mccullough-digital' ),
      'social'  => __( 'Social Links Menu', 'mccullough-digital' ),
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
    $block_folders = glob( get_stylesheet_directory() . '/blocks/*' );
    foreach ( $block_folders as $block_folder ) {
        if ( file_exists( $block_folder . '/block.json' ) ) {
            register_block_type( $block_folder );
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
        if ( $existing_home instanceof WP_Post ) {
            $front_page_id = (int) $existing_home->ID;
        } else {
            $inserted_page = wp_insert_post(
                [
                    'post_title'   => __( 'Home', 'mccullough-digital' ),
                    'post_name'    => 'home',
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_content' => $pattern['content'],
                ],
                true
            );

            if ( is_wp_error( $inserted_page ) ) {
                return;
            }

            $front_page_id = (int) $inserted_page;
        }

        if ( $front_page_id ) {
            update_option( 'page_on_front', $front_page_id );
            update_option( 'show_on_front', 'page' );
            delete_option( 'mcd_seed_home_pattern' );
            return;
        }
    }

    if ( ! $front_page_id ) {
        return;
    }

    $front_page = get_post( $front_page_id );
    if ( ! $front_page || 'page' !== $front_page->post_type ) {
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
    if ( ! class_exists( 'DOMDocument' ) ) {
        return '';
    }
    // A whitelist of allowed SVG elements and their attributes.
    $allowed_tags = [
        'svg'   => [ 'width', 'height', 'viewbox', 'xmlns', 'fill', 'class' ],
        'path'  => [ 'd', 'fill', 'stroke', 'stroke-width' ],
        'g'     => [ 'fill', 'transform' ],
        'rect'  => [ 'x', 'y', 'width', 'height', 'fill', 'transform' ],
        'circle' => [ 'cx', 'cy', 'r', 'fill' ],
        'line'  => [ 'x1', 'y1', 'x2', 'y2', 'stroke' ],
    ];

    // Suppress warnings for invalid HTML since we are parsing a fragment.
    libxml_use_internal_errors( true );

    $dom = new DOMDocument();
    $dom->loadXML( $svg, LIBXML_NOBLANKS );

    // Clear errors to handle them manually if needed.
    libxml_clear_errors();

    if ( ! $dom->documentElement ) {
        return ''; // Return empty if SVG is malformed.
    }

    $stack = [ $dom->documentElement ];
    while ( count( $stack ) > 0 ) {
        $node = array_pop( $stack );

        // Convert node name to lowercase for case-insensitive comparison
        $node_name = strtolower( $node->nodeName );

        if ( ! isset( $allowed_tags[ $node_name ] ) ) {
            $node->parentNode->removeChild( $node );
            continue;
        }

        // Sanitize attributes
        if ( $node->hasAttributes() ) {
            foreach ( iterator_to_array( $node->attributes ) as $attr ) {
                if ( ! in_array( strtolower( $attr->name ), $allowed_tags[ $node_name ], true ) ) {
                    $node->removeAttribute( $attr->name );
                }
            }
        }

        // Add child nodes to the stack to process them
        foreach ( $node->childNodes as $child ) {
            if ( $child instanceof DOMElement ) {
                $stack[] = $child;
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
  $social_icons = [
    'twitter.com'  => 'twitter',
    'linkedin.com' => 'linkedin',
    'github.com'   => 'github',
  ];

  // The host is cast to a string and converted to lowercase to ensure consistency.
  $host = strtolower( (string) parse_url( $url, PHP_URL_HOST ) );

  if ( ! $host ) {
    return '';
  }

  $icon_name = '';

  // Handle x.com as a special case because it's also Twitter.
  // Check for exact match or subdomain of x.com. This is PHP 7.4 compatible.
  if ( 'x.com' === $host || ( substr( $host, -strlen( '.x.com' ) ) === '.x.com' ) ) {
    $icon_name = 'twitter';
  } else {
    // For other domains, a simple substring check is sufficient and more flexible.
    // It correctly handles variations like 'blog.github.com' or 'twitter.co.uk'.
    foreach ( $social_icons as $domain => $icon ) {
      if ( false !== strpos( $host, $domain ) ) {
        $icon_name = $icon;
        break;
      }
    }
  }

  if ( ! empty( $icon_name ) ) {
    $icon_path = get_stylesheet_directory() . '/assets/icons/' . $icon_name . '.svg';
    if ( file_exists( $icon_path ) ) {
      $svg = file_get_contents( $icon_path );
      $svg = mcd_sanitize_svg( $svg );
      // Add accessibility attributes.
      $svg = preg_replace( '/<svg/', '<svg aria-hidden="true" role="img" ', $svg, 1 );
      return $svg;
    }
  }

  return '';
}

/**
 * Walker class for the social menu to output SVGs.
 */
class Mcd_Social_Nav_Menu_Walker extends Walker_Nav_Menu {
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $social_svg = mcd_get_social_link_svg( $item->url );

        $output .= '<li class="' . esc_attr( implode( ' ', $item->classes ) ) . '">';
        $output .= '<a href="' . esc_url( $item->url ) . '" class="social-link">';

        if ( empty( $social_svg ) ) {
            $output .= esc_html( $item->title );
        } else {
            $output .= '<span class="screen-reader-text">' . esc_html( $item->title ) . '</span>';
            $output .= $social_svg;
        }

        $output .= '</a>';
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>";
    }
}

/**
 * Adds a span to menu items to allow for more flexible styling.
 */
class Mcd_Nav_Menu_Walker extends Walker_Nav_Menu {
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $output .= "<li class='" .  esc_attr( implode( ' ', $item->classes ) ) . "'>";
        if ( ! empty( $item->url ) ) {
            $output .= '<a href="' . esc_url( $item->url ) . '">';
        } else {
            $output .= '<span>';
        }

        $output .= '<span class="menu-text-span">' . esc_html( $item->title ) . '</span>';

        if ( ! empty( $item->url ) ) {
            $output .= '</a>';
        } else {
            $output .= '</span>';
        }
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        $output .= "</li>";
    }
}