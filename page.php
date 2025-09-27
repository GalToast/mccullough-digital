<?php
/**
 * The template for displaying all pages
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main class="site-content">
  <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
      <?php get_template_part( 'parts/page-header' ); ?>
      <div class="container">
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-content' ); ?>>
            <?php
              the_content();
              wp_link_pages(
                  array(
                      'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'mccullough-digital' ),
                      'after'  => '</div>',
                  )
              );
            ?>
        </article>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</main>
<?php get_footer(); ?>