<?php
/**
 * The template for displaying archive pages
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main class="site-content">
  <header class="archive-header">
    <?php
      the_archive_title( '<h1 class="archive-title">', '</h1>' );
      the_archive_description( '<div class="archive-description">', '</div>' );
    ?>
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
      <p>Try adding a post or a page.</p>
    </article>
  <?php endif; ?>
</main>
<?php get_footer(); ?>