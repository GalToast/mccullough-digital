<?php
/**
 * Minimal bootstrap for the McCullough Digital block theme.
 *
 * This file exists as a safeguard so WordPress can still render the
 * block templates defined in the theme when accessed directly.
 *
 * @package McCullough_Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'wp_is_block_theme' ) && function_exists( 'wp_template_loader' ) && wp_is_block_theme() ) {
    wp_template_loader();
    return;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
wp_body_open();
?>
<div class="wp-site-blocks">
    <?php
    if ( function_exists( 'block_header_area' ) ) {
        block_header_area();
    }

    if ( function_exists( 'block_template_part' ) ) {
        block_template_part( 'header' );
    }

    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            the_content();
        }
    }

    if ( function_exists( 'block_footer_area' ) ) {
        block_footer_area();
    }

    if ( function_exists( 'block_template_part' ) ) {
        block_template_part( 'footer' );
    }
    ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
