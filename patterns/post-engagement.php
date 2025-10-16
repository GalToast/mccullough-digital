<?php
/**
 * Title: Post Engagement
 * Slug: mccullough-digital/post-engagement
 * Categories: mccullough-digital-sections
 * Inserter: no
 */

$related_categories    = wp_get_post_categories( get_the_ID() );
$primary_category_name = '';
if ( ! empty( $related_categories ) ) {
	$primary_term = get_term( $related_categories[0], 'category' );
	if ( $primary_term && ! is_wp_error( $primary_term ) ) {
		$primary_category_name = $primary_term->name;
	}
}

$placeholder_image = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/placeholder-16x9.svg';

$related_args = array(
	'post_type'           => 'post',
	'posts_per_page'      => 3,
	'post__not_in'        => array( get_the_ID() ),
	'ignore_sticky_posts' => true,
);

if ( ! empty( $related_categories ) ) {
	$related_args['category__in'] = array_map( 'intval', array_slice( $related_categories, 0, 3 ) );
}

$related_query = new WP_Query( $related_args );
?>
<!-- wp:group {"className":"single-related","layout":{"type":"constrained"}} -->
<div class="wp-block-group single-related">
	<!-- wp:group {"className":"single-related__header","layout":{"type":"constrained"}} -->
	<div class="wp-block-group single-related__header">
		<!-- wp:heading {"level":2,"className":"single-related__title"} -->
		<h2 class="wp-block-heading single-related__title"><?php echo esc_html( $primary_category_name ? sprintf( 'More from %s', $primary_category_name ) : 'Latest from the blog' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"single-related__intro"} -->
		<p class="single-related__intro"><?php echo esc_html( $primary_category_name ? 'Explore additional posts covering similar topics.' : 'Explore more insights, guides, and launch notes.' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<div class="single-related__list">
		<?php if ( $related_query->have_posts() ) : ?>
			<?php
			while ( $related_query->have_posts() ) :
				$related_query->the_post();
				$post_id    = get_the_ID();
				$post_title = get_the_title();
				$post_link  = get_permalink();
				$post_date  = get_the_date( 'M j, Y' );
				$excerpt    = wp_trim_words( get_the_excerpt(), 24, '...' );
				$has_thumb  = has_post_thumbnail( $post_id );
				?>
				<article class="single-related__item">
					<a class="single-related__thumb" href="<?php echo esc_url( $post_link ); ?>">
						<?php
						if ( $has_thumb ) {
							echo wp_get_attachment_image( get_post_thumbnail_id( $post_id ), 'mcd-featured-landscape' );
						} else {
							printf(
								'<img src="%1$s" alt="%2$s" />',
								esc_url( $placeholder_image ),
								esc_attr( $post_title )
							);
						}
						?>
					</a>
					<div class="single-related__meta">
						<span class="single-related__date"><?php echo esc_html( $post_date ); ?></span>
						<a class="single-related__item-title" href="<?php echo esc_url( $post_link ); ?>">
							<?php echo esc_html( $post_title ); ?>
						</a>
						<p class="single-related__excerpt"><?php echo esc_html( $excerpt ); ?></p>
						<a class="single-related__more" href="<?php echo esc_url( $post_link ); ?>">Keep reading</a>
					</div>
				</article>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p class="single-related__empty">Fresh stories are on the way. Check back soon for more launches and playbooks.</p>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>

	<!-- wp:group {"className":"single-related__cta","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","orientation":"horizontal","verticalAlignment":"center"}} -->
	<div class="wp-block-group single-related__cta">
		<!-- wp:group {"className":"single-related__cta-copy","layout":{"type":"constrained"}} -->
		<div class="wp-block-group single-related__cta-copy">
			<!-- wp:heading {"level":3,"className":"single-related__cta-title"} -->
			<h3 class="wp-block-heading single-related__cta-title">Plan your next launch</h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"single-related__cta-text"} -->
			<p class="single-related__cta-text">Need the same conversion-first build, analytics, and lead flow for your team? Share your goals and get a scoped roadmap within one business day.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:buttons {"className":"single-related__cta-actions"} -->
		<div class="wp-block-buttons single-related__cta-actions">
			<!-- wp:pattern {"slug":"mccullough-digital/cta-button-pair"} /-->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
