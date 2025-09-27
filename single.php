<?php
/**
 * The template for displaying all single posts
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main class="site-content">
  <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
      <?php get_template_part( 'parts/page-header' ); ?>
      <div class="container">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <div class="entry-content">
            <?php
              the_content();
              wp_link_pages(
                  array(
                      'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'mccullough-digital' ),
                      'after'  => '</div>',
                  )
              );
            ?>
          </div><!-- .entry-content -->

          <footer class="entry-footer">
            <div class="entry-meta">
              <span><?php echo get_the_date(); ?></span>
            </div>
            <?php the_tags( '<span class="tag-links">', ', ', '</span>' ); ?>
          </footer><!-- .entry-footer -->
        </article><!-- #post-<?php the_ID(); ?> -->

        <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if ( comments_open() || get_comments_number() ) :
          comments_template();
        endif;
        ?>
      </div><!-- .container -->

    <?php endwhile; ?>
  <?php endif; ?>
</main>
<?php get_footer(); ?>