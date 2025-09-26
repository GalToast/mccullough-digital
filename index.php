<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main id="content" class="site-content">
  <?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <header class="entry-header">
          <?php if ( is_singular() ) : ?>
            <h1 class="entry-title"><?php the_title(); ?></h1>
          <?php else : ?>
            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php endif; ?>
        </header>
        <div class="entry-content">
          <?php the_content(); ?>
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