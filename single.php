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
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
          <h1 class="entry-title"><?php the_title(); ?></h1>
          <div class="entry-meta">
            <span><?php echo get_the_date(); ?></span>
          </div>
        </header>
        <div class="entry-content">
          <?php
            the_content();
            wp_link_pages( array(
              'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'mccullough-digital' ),
              'after'  => '</div>',
            ) );
          ?>
        </div>
        <footer class="entry-footer">
          <?php the_tags( '<span class="tag-links">', ', ', '</span>' ); ?>
        </footer>
      </article>

      <?php
      // If comments are open or we have at least one comment, load up the comment template.
      if ( comments_open() || get_comments_number() ) :
        comments_template();
      endif;
      ?>

    <?php endwhile; ?>
  <?php endif; ?>
</main>
<?php get_footer(); ?>