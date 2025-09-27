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
        <?php the_custom_logo(); ?>
    </div>

    <button type="button" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" aria-label="Toggle navigation">
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
            'walker'         => new Mcd_Nav_Menu_Walker(),
            'fallback_cb'    => function () {
              echo '<ul id="primary-menu" class="menu">';
              $pages = get_pages( array( 'sort_column' => 'menu_order', 'parent' => 0 ) );
              if ( ! empty( $pages ) ) {
                foreach ( $pages as $page ) {
                  echo '<li><a href="' . esc_url( get_permalink( $page->ID ) ) . '"><span class="menu-text-span">' . esc_html( $page->post_title ) . '</span></a></li>';
                }
              }
              echo '</ul>';
            }
          ) );
        ?>
    </nav>
</header>
