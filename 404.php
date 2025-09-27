<?php
/**
 * The template for displaying 404 pages (not found)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main class="site-content">
  <section class="error-404 not-found">
    <header class="page-header">
      <h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'mccullough-digital' ); ?></h1>
    </header>
    <div class="page-content">
      <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'mccullough-digital' ); ?></p>
      <?php get_search_form(); ?>
    </div>
  </section>
</main>
<?php get_footer(); ?>