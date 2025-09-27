<?php
/**
 * The template for displaying 404 pages (not found)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main class="site-content">
  <?php get_template_part( 'parts/page-header' ); ?>
  <div class="container">
    <section class="error-404 not-found">
      <div class="page-content">
        <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'mccullough-digital' ); ?></p>
        <?php get_search_form(); ?>
      </div>
    </section>
  </div><!-- .container -->
</main>
<?php get_footer(); ?>