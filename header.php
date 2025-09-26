<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header" role="banner">
    <div class="site-branding">
        <?php
        if ( has_custom_logo() ) {
            // Get the custom logo URL
            $logo_id = get_theme_mod( 'custom_logo' );
            $logo = wp_get_attachment_image_src( $logo_id , 'full' );
            echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="custom-logo-link" rel="home"><img src="' . esc_url( $logo[0] ) . '" alt="' . get_bloginfo( 'name' ) . '" class="custom-logo"></a>';
        } else {
            echo '<h1 class="site-title"><a href="' . esc_url( home_url( '/' ) ) . '">' . get_bloginfo( 'name' ) . '</a></h1>';
        }
        ?>
    </div>

    <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>

    <nav id="site-navigation" class="main-navigation" aria-label="Primary Menu">
        <?php
          wp_nav_menu( array(
            'theme_location' => 'primary',
            'menu_id'        => 'primary-menu',
            'container'      => false,
            'fallback_cb'    => function () {
              echo '<ul id="primary-menu" class="menu">';
              echo '<li><a href="#services">Services</a></li>';
              echo '<li><a href="#about">About</a></li>';
              echo '<li><a href="#contact">Contact</a></li>';
              echo '</ul>';
            }
          ) );
        ?>
    </nav>
</header>

<main class="site-content">