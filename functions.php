<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Theme setup
 */
function mcd_setup() {
  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'custom-logo', array(
    'height'      => 100,
    'width'       => 400,
    'flex-height' => true,
    'flex-width'  => true,
  ) );
  add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

  register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'mccullough-digital' ),
  ) );
}
add_action( 'after_setup_theme', 'mcd_setup' );

/**
 * Assets
 */
function mcd_assets() {
  $theme_version = wp_get_theme()->get('Version');

  // Enqueue Google Fonts
  wp_enqueue_style( 'mcd-google-fonts', 'https://fonts.googleapis.com/css2?family=Black+Ops+One&display=swap', array(), null );

  // Enqueue main stylesheet
  wp_enqueue_style( 'mcd-style', get_stylesheet_uri(), array(), $theme_version );

  // Cache-bust header-scripts.js by filemtime if possible
  $script_path = get_stylesheet_directory() . '/js/header-scripts.js';
  $ver = file_exists( $script_path ) ? filemtime( $script_path ) : $theme_version;
  wp_enqueue_script( 'mcd-header-scripts', get_stylesheet_directory_uri() . '/js/header-scripts.js', array(), $ver, true );
}
add_action( 'wp_enqueue_scripts', 'mcd_assets' );

/**
 * Back-compat for wp_body_open (if very old WP)
 */
if ( ! function_exists( 'wp_body_open' ) ) {
  function wp_body_open() { do_action( 'wp_body_open' ); }
}