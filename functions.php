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