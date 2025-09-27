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
 * Displays social links with SVG icons.
 */
function mcd_the_social_links() {
    if ( ! has_nav_menu( 'social' ) ) {
        return;
    }

    wp_nav_menu(
        array(
            'theme_location'  => 'social',
            'container'       => 'nav',
            'container_class' => 'social-navigation',
            'menu_class'      => 'social-links-menu',
            'depth'           => 1,
            'walker'          => new Mcd_Social_Nav_Menu_Walker(),
        )
    );
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
        'x.com'        => 'twitter',
        'linkedin.com' => 'linkedin',
        'github.com'   => 'github',
    ];

    $domain = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

    if ( isset( $social_icons[ $domain ] ) ) {
        $icon_name = $social_icons[ $domain ];
        $icon_path = get_stylesheet_directory() . '/assets/icons/' . $icon_name . '.svg';
        if ( file_exists( $icon_path ) ) {
            return file_get_contents( $icon_path );
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

        if ( empty( $social_svg ) ) {
            return;
        }

        $output .= "<li class='" .  implode(" ", $item->classes) . "'>";
        $output .= '<a href="' . esc_url( $item->url ) . '" class="social-link">';
        $output .= '<span class="screen-reader-text">' . esc_html( $item->title ) . '</span>';
        $output .= $social_svg;
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
        $output .= "<li class='" .  implode(" ", $item->classes) . "'>";
        if ( ! empty( $item->url ) ) {
            $output .= '<a href="' . $item->url . '">';
        } else {
            $output .= '<span>';
        }

        $output .= '<span class="menu-text-span">' . $item->title . '</span>';

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