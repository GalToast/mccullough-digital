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
        the_custom_logo();
      } else { ?>
        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
    <?php } ?>
  </div>

  <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'mccullough-digital' ); ?>">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
  </button>

  <nav id="site-navigation" class="main-navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'mccullough-digital' ); ?>">
    <?php
      wp_nav_menu( array(
        'theme_location' => 'primary',
        'menu_id'        => 'primary-menu',
        'container'      => false,
        'fallback_cb'    => function () {
          echo '<ul id="primary-menu" class="menu"><li><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">Add a menu</a></li></ul>';
        }
      ) );
    ?>
  </nav>
</header>