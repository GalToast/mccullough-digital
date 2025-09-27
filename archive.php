<?php
/**
 * The template for displaying archive pages
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main class="site-content">
  <?php get_template_part( 'parts/page-header' ); ?>

  <div class="container">
    <?php if ( have_posts() ) : ?>

      <div class="post-listing-grid">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
              <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <div class="entry-summary">
                  <?php the_excerpt(); ?>
              </div>
              <a href="<?php the_permalink(); ?>" class="cta-button">
                  <span class="btn-text"><?php esc_html_e( 'Read More', 'mccullough-digital' ); ?></span>
              </a>
            </article>
            <?php
        endwhile;
        ?>
      </div>

        <?php the_posts_navigation(); ?>

    <?php else : ?>
      <div class="no-results not-found">
        <p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'mccullough-digital' ); ?></p>
        <?php get_search_form(); ?>
      </div>
    <?php endif; ?>
  </div><!-- .container -->
</main>
<?php get_footer(); ?>