<?php
/**
 * PHP fallback for the 404 template so block markup stays consistent.
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

get_header();
?>

<main id="primary" class="site-content">
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            the_content();
        }
    } else {
        get_template_part( 'template-parts/content', 'none' );
    }
    ?>
</main>

<?php
get_footer();
