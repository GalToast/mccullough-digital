<?php
/**
 * The template for displaying search results pages
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main id="content" class="site-content">
  <header class="page-header">
    <h1 class="page-title">
      <?php
        /* translators: %s: search query. */
        printf( esc_html__( 'Search Results for: %s', 'mccullough-digital' ), '<span>' . get_search_query() . '</span>' );
      ?>
    </h1>
  </header>

  <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
          <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        </header>
        <div class="entry-summary">
          <?php the_excerpt(); ?>
        </div>
      </article>
    <?php endwhile; ?>
    <?php the_posts_navigation(); ?>
  <?php else : ?>
    <article class="no-results not-found">
      <h1>No content found</h1>
      <p>Sorry, but nothing matched your search terms. Please try again with some different keywords.</p>
      <?php get_search_form(); ?>
    </article>
  <?php endif; ?>
</main>
<?php get_footer(); ?>