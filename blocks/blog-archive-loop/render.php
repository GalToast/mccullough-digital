<?php
/**
 * Server-side rendering for the Blog Archive Loop block.
 *
 * @package McCullough_Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mcd_get_blog_hero_context' ) ) {
	/**
	 * Determine hero copy based on the current request context.
	 *
	 * @return array{
	 *     kicker:string,
	 *     title:string,
	 *     subtitle:string,
	 *     search_placeholder:string
	 * }
	 */
	function mcd_get_blog_hero_context() {
		$context = [
			'kicker'             => __( 'Journal', 'mccullough-digital' ),
			'title'              => __( 'Ideas with a <span class="blog-hero__accent">neon edge</span>', 'mccullough-digital' ),
			'subtitle'           => __( 'Stories, tutorials, and process notes from the McCullough Digital studio. Crafted for speed, contrast, and that crisp midnight glow.', 'mccullough-digital' ),
			'search_placeholder' => __( 'Search articles...', 'mccullough-digital' ),
		];

		if ( is_404() ) {
			$context['kicker']             = __( '404', 'mccullough-digital' );
			$context['title']              = __( 'That page is lost in the glow', 'mccullough-digital' );
			$context['subtitle']           = __( 'We could not find the page you were after. Try a fresh search or jump into the latest stories below.', 'mccullough-digital' );
			$context['search_placeholder'] = __( 'Search the journal...', 'mccullough-digital' );
		} elseif ( is_search() ) {
			$query               = get_search_query();
			$context['kicker']   = __( 'Search', 'mccullough-digital' );
			$context['subtitle'] = __( 'You can refine your search below or explore the latest stories.', 'mccullough-digital' );

			if ( $query ) {
				$context['title'] = sprintf(
					/* translators: %s: search query. */
					__( 'Results for <span class="blog-hero__accent">"%s"</span>', 'mccullough-digital' ),
					esc_html( $query )
				);
			} else {
				$context['title'] = __( 'Search the <span class="blog-hero__accent">journal</span>', 'mccullough-digital' );
			}
		} elseif ( ! ( is_home() || is_post_type_archive( 'post' ) ) ) {
			$archive_title       = get_the_archive_title();
			$archive_description = get_the_archive_description();

			if ( $archive_title ) {
				$context['title'] = sprintf(
					'<span class="blog-hero__accent">%s</span>',
					esc_html( wp_strip_all_tags( $archive_title ) )
				);
			}

			if ( $archive_description ) {
				$context['subtitle'] = wp_strip_all_tags( $archive_description );
			} else {
				$context['subtitle'] = __( 'Browse curated insights from McCullough Digital.', 'mccullough-digital' );
			}
		}

		return $context;
	}
}

if ( ! function_exists( 'mcd_get_thumbnail_alt_fallback' ) ) {
	/**
	 * Generate an accessible alt attribute for a post thumbnail.
	 *
	 * @param int $post_id Post ID to derive the thumbnail alt text from.
	 * @return string
	 */
	function mcd_get_thumbnail_alt_fallback( $post_id ) {
		$thumbnail_id = get_post_thumbnail_id( $post_id );

		if ( ! $thumbnail_id ) {
			return __( 'Blog post featured image', 'mccullough-digital' );
		}

		$alt_text = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
		$alt_text = is_string( $alt_text ) ? trim( $alt_text ) : '';

		if ( '' !== $alt_text ) {
			return $alt_text;
		}

		$post_title = get_the_title( $post_id );

		if ( $post_title ) {
			/* translators: %s: Post title. */
			return sprintf( __( '%s featured image', 'mccullough-digital' ), $post_title );
		}

		return __( 'Blog post featured image', 'mccullough-digital' );
	}
}

if ( ! function_exists( 'mcd_render_category_pills' ) ) {
	/**
	 * Render the curated category pill navigation.
	 *
	 * @return string
	 */
	function mcd_render_category_pills() {
		$items = [
			[
				'label' => __( 'All Posts', 'mccullough-digital' ),
				'slug'  => '',
				'type'  => 'all',
			],
			[
				'label' => __( 'Web Design', 'mccullough-digital' ),
				'slug'  => 'web-design',
				'type'  => 'category',
			],
			[
				'label' => __( 'Development', 'mccullough-digital' ),
				'slug'  => 'development',
				'type'  => 'category',
			],
			[
				'label' => __( 'WordPress', 'mccullough-digital' ),
				'slug'  => 'wordpress',
				'type'  => 'category',
			],
			[
				'label' => __( 'UI/UX', 'mccullough-digital' ),
				'slug'  => 'ui-ux',
				'type'  => 'category',
			],
			[
				'label' => __( 'Tutorials', 'mccullough-digital' ),
				'slug'  => 'tutorials',
				'type'  => 'category',
			],
		];

		$posts_page_id = (int) get_option( 'page_for_posts' );
		$posts_page    = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/' );
		$current_topic = get_query_var( 'mcd_topic' );
		$current_topic = is_string( $current_topic ) ? sanitize_title( $current_topic ) : '';

		if ( '' === $current_topic && isset( $_GET['mcd_topic'] ) ) {
			$current_topic = sanitize_title( wp_unslash( (string) $_GET['mcd_topic'] ) );
		}

		$all_posts_url = remove_query_arg( 'mcd_topic', $posts_page );
		$output        = '<ul class="category-filters__list pills" role="list">';

		foreach ( $items as $item ) {
			$label      = $item['label'];
			$slug       = $item['slug'];
			$is_current = false;
			$url        = $all_posts_url;

			if ( 'category' === $item['type'] ) {
				$url        = add_query_arg( 'mcd_topic', $slug, $posts_page );
				$is_current = ( '' !== $current_topic && $current_topic === $slug ) || is_category( $slug );
			} else {
				$is_current = ( '' === $current_topic || 'all' === $current_topic ) && ( is_home() || is_post_type_archive( 'post' ) || is_search() || is_404() );
			}

			$classes = [ 'category-pill', 'pill' ];

			if ( $is_current ) {
				$classes[] = 'is-active';
			}

			$data_attr      = $slug ? ' data-category="' . esc_attr( $slug ) . '"' : ' data-category="all"';
			$aria_attribute = $is_current ? ' aria-current="page"' : '';

			$output .= sprintf(
				'<li><a class="%1$s" href="%2$s"%3$s%4$s>%5$s</a></li>',
				esc_attr( implode( ' ', $classes ) ),
				esc_url( $url ),
				$data_attr,
				$aria_attribute,
				esc_html( $label )
			);
		}

		$output .= '</ul>';

		return $output;
	}
}

if ( ! function_exists( 'mcd_get_primary_category' ) ) {
	/**
	 * Retrieve the first category assigned to a post.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return WP_Term|null
	 */
	function mcd_get_primary_category( $post_id ) {
		$categories = get_the_category( $post_id );

		if ( empty( $categories ) || is_wp_error( $categories ) ) {
			return null;
		}

		return $categories[0];
	}
}

if ( ! function_exists( 'mcd_render_blog_archive_loop' ) ) {
	/**
	 * Render callback for the blog archive loop block.
	 *
	 * @param array    $attributes Block attributes.
	 * @param string   $content    Block default content.
	 * @param WP_Block $block      Block instance.
	 *
	 * @return string
	 */
	function mcd_render_blog_archive_loop( $attributes, $content, $block ) {
		global $wp_query;

		if ( ! $wp_query instanceof WP_Query ) {
			return '';
		}

		$current_page = max( 1, (int) get_query_var( 'paged' ) );

		if ( 1 === $current_page ) {
			$page_number = (int) get_query_var( 'page' );
			if ( $page_number > $current_page ) {
				$current_page = $page_number;
			}
		}

		$query_args = $wp_query->query_vars;
		$topic_slug = '';

		if ( isset( $query_args['mcd_topic'] ) ) {
			$topic_slug = sanitize_title( (string) $query_args['mcd_topic'] );
			unset( $query_args['mcd_topic'] );
		}

		$query_args['paged']               = $current_page;
		$query_args['no_found_rows']       = false;
		$query_args['posts_per_page']      = $wp_query->get( 'posts_per_page' );
		$query_args['orderby']             = $wp_query->get( 'orderby' );
		$query_args['order']               = $wp_query->get( 'order' );
		$query_args['ignore_sticky_posts'] = $wp_query->get( 'ignore_sticky_posts' );

		unset( $query_args['fields'] );

		if ( empty( $query_args['post_type'] ) || 'any' === $query_args['post_type'] ) {
			$query_args['post_type'] = 'post';
		}

		if ( is_search() ) {
			$query_args['post_type'] = 'post';
		}

		if ( '' !== $topic_slug && 'all' !== $topic_slug && empty( $query_args['category_name'] ) ) {
			$query_args['category_name'] = $topic_slug;
		}

		if ( is_404() ) {
			$current_page           = 1;
			$query_args             = [
				'post_type'           => 'post',
				'posts_per_page'      => max( 4, (int) get_option( 'posts_per_page', 10 ) ),
				'orderby'             => 'date',
				'order'               => 'desc',
				'ignore_sticky_posts' => true,
				'paged'               => 1,
			];
		}

		$loop          = new WP_Query( $query_args );
		$hero_context  = mcd_get_blog_hero_context();
		$hero_title_id = wp_unique_id( 'blog-hero-title-' );
		$hero_desc_id  = wp_unique_id( 'blog-hero-subtitle-' );
		$search_id     = wp_unique_id( 'blog-search-field-' );

		ob_start();
		?>
		<section class="blog-hero" aria-labelledby="<?php echo esc_attr( $hero_title_id ); ?>">
			<div class="blog-hero__inner blog-container">
				<?php if ( ! empty( $hero_context['kicker'] ) ) : ?>
					<p class="blog-kicker"><?php echo esc_html( $hero_context['kicker'] ); ?></p>
				<?php endif; ?>

				<h1 class="blog-hero__title" id="<?php echo esc_attr( $hero_title_id ); ?>">
					<?php echo wp_kses_post( $hero_context['title'] ); ?>
				</h1>

				<?php if ( ! empty( $hero_context['subtitle'] ) ) : ?>
					<p class="blog-hero__subtitle" id="<?php echo esc_attr( $hero_desc_id ); ?>">
						<?php echo esc_html( $hero_context['subtitle'] ); ?>
					</p>
				<?php endif; ?>

				<form class="blog-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="<?php echo esc_attr( $search_id ); ?>">
						<?php esc_html_e( 'Search articles', 'mccullough-digital' ); ?>
					</label>
					<div class="blog-search__field">
						<span class="blog-search__icon" aria-hidden="true">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
								<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"></circle>
							</svg>
						</span>
						<input
							type="search"
							id="<?php echo esc_attr( $search_id ); ?>"
							name="s"
							value="<?php echo esc_attr( get_search_query() ); ?>"
							placeholder="<?php echo esc_attr( $hero_context['search_placeholder'] ); ?>"
							autocomplete="off"
							enterkeyhint="search"
							<?php if ( ! empty( $hero_context['subtitle'] ) ) : ?>
								aria-describedby="<?php echo esc_attr( $hero_desc_id ); ?>"
							<?php endif; ?>
						/>
					</div>
					<input type="hidden" name="post_type" value="post" />
					<?php if ( '' !== $topic_slug && 'all' !== $topic_slug ) : ?>
						<input type="hidden" name="mcd_topic" value="<?php echo esc_attr( $topic_slug ); ?>" />
					<?php endif; ?>
					<button type="submit"><?php esc_html_e( 'Search', 'mccullough-digital' ); ?></button>
				</form>
			</div>
		</section>

		<section class="category-filters pills-wrap" role="navigation" aria-label="<?php echo esc_attr__( 'Filter posts by category', 'mccullough-digital' ); ?>">
			<div class="category-filters__inner blog-container">
				<?php echo mcd_render_category_pills(); ?>
			</div>
		</section>
		<?php

		if ( ! $loop->have_posts() ) {
			?>
			<section class="blog-grid blog-grid--empty" aria-live="polite">
				<div class="blog-container">
					<p class="blog-empty"><?php esc_html_e( 'No posts yet. Try adjusting your search or check back soon.', 'mccullough-digital' ); ?></p>
				</div>
			</section>
			<?php
			wp_reset_postdata();
			return ob_get_clean();
		}

		$show_featured = ! is_paged();

		if ( $show_featured ) {
			$loop->the_post();

			$featured_id       = get_the_ID();
			$featured_link     = get_permalink();
			$featured_title    = get_the_title();
			$featured_date     = get_the_date( 'M j, Y' );
			$featured_excerpt  = wp_trim_words( get_the_excerpt(), 32, '...' );
			$primary_category  = mcd_get_primary_category( $featured_id );
			$category_link     = $primary_category ? get_term_link( $primary_category ) : '';
			$category_has_link = $primary_category && ! is_wp_error( $category_link );
			?>
			<section class="blog-featured">
				<div class="blog-container">
					<article class="featured-card feat reveal">
						<div class="featured-media">
							<a href="<?php echo esc_url( $featured_link ); ?>" class="featured-media__link">
								<?php
								$featured_image = get_the_post_thumbnail(
									$featured_id,
									'mcd-featured-landscape',
									[
										'class'         => 'featured-media__image',
										'alt'           => mcd_get_thumbnail_alt_fallback( $featured_id ),
										'loading'       => 'eager',
										'fetchpriority' => 'high',
										'decoding'      => 'async',
									]
								);

								if ( $featured_image ) {
									echo $featured_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									echo '<span class="featured-media__placeholder" aria-hidden="true">' . esc_html__( 'Featured image placeholder', 'mccullough-digital' ) . '</span>';
								}
								?>
							</a>
						</div>

						<div class="featured-content">
							<span class="badge"><?php esc_html_e( 'New', 'mccullough-digital' ); ?></span>

							<h2 class="featured-title">
								<a href="<?php echo esc_url( $featured_link ); ?>"><?php echo esc_html( $featured_title ); ?></a>
							</h2>

							<div class="meta">
								<span class="meta__date"><?php echo esc_html( $featured_date ); ?></span>
								<?php if ( $primary_category && $category_has_link ) : ?>
									<span class="meta__separator" aria-hidden="true">&bull;</span>
									<span class="meta__cat">
										<a href="<?php echo esc_url( $category_link ); ?>">
											<?php echo esc_html( $primary_category->name ); ?>
										</a>
									</span>
								<?php endif; ?>
							</div>

							<?php if ( $featured_excerpt ) : ?>
								<p class="excerpt"><?php echo esc_html( $featured_excerpt ); ?></p>
							<?php endif; ?>

							<a class="cta" href="<?php echo esc_url( $featured_link ); ?>" aria-label="<?php echo esc_attr__( 'Read more about the featured story', 'mccullough-digital' ); ?>">
								<?php esc_html_e( 'Read More', 'mccullough-digital' ); ?>
								<svg class="arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
									<path d="M5 12h14M13 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
								</svg>
							</a>
						</div>
					</article>
				</div>
			</section>
			<?php
		}

		?>
		<section class="blog-grid" aria-label="<?php echo esc_attr__( 'All posts', 'mccullough-digital' ); ?>">
			<div class="blog-container">
				<div class="cards">
					<?php
					$rendered_posts = 0;

					while ( $loop->have_posts() ) {
						$loop->the_post();

						$post_id          = get_the_ID();
						$post_link        = get_permalink();
						$post_title       = get_the_title();
						$post_date        = get_the_date( 'M j, Y' );
						$excerpt          = wp_trim_words( get_the_excerpt(), 24, '...' );
						$primary_category = mcd_get_primary_category( $post_id );
						$category_link    = $primary_category ? get_term_link( $primary_category ) : '';
						$has_cat_link     = $primary_category && ! is_wp_error( $category_link );

						$rendered_posts++;
						?>
						<article class="card post-card reveal">
							<a class="card__media post-card-image" href="<?php echo esc_url( $post_link ); ?>">
								<?php
								$card_image = get_the_post_thumbnail(
									$post_id,
									'mcd-post-card',
									[
										'class'    => 'card__image post-card-image__media',
										'alt'      => mcd_get_thumbnail_alt_fallback( $post_id ),
										'loading'  => 'lazy',
										'decoding' => 'async',
									]
								);

								if ( $card_image ) {
									echo $card_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									echo '<span class="card__placeholder post-card-image__placeholder" aria-hidden="true">' . esc_html__( 'Post image placeholder', 'mccullough-digital' ) . '</span>';
								}
								?>
							</a>

							<div class="card__body post-card-content">
								<div class="meta card__meta">
									<span class="meta__date"><?php echo esc_html( $post_date ); ?></span>
									<?php if ( $primary_category && $has_cat_link ) : ?>
										<span class="meta__separator" aria-hidden="true">&bull;</span>
										<span class="meta__cat">
											<a href="<?php echo esc_url( $category_link ); ?>">
												<?php echo esc_html( $primary_category->name ); ?>
											</a>
										</span>
									<?php endif; ?>
								</div>

								<h3 class="card__title post-card__title">
									<a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a>
								</h3>

								<?php if ( $excerpt ) : ?>
									<p class="card__excerpt post-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
								<?php endif; ?>

								<a class="cta text-link" href="<?php echo esc_url( $post_link ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Read more: %s', 'mccullough-digital' ), $post_title ) ); ?>">
									<?php esc_html_e( 'Read More', 'mccullough-digital' ); ?>
									<svg class="arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
										<path d="M5 12h14M13 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
									</svg>
								</a>
							</div>
						</article>
						<?php
					}
					?>
				</div>

				<?php if ( 0 === $rendered_posts ) : ?>
					<p class="blog-empty"><?php esc_html_e( 'No additional stories yet; check back soon.', 'mccullough-digital' ); ?></p>
				<?php endif; ?>

				<?php
				$pagination_args = [
					'total'     => max( 1, (int) $loop->max_num_pages ),
					'current'   => $current_page,
					'type'      => 'array',
					'prev_text' => __( 'Previous', 'mccullough-digital' ),
					'next_text' => __( 'Next', 'mccullough-digital' ),
				];

				if ( '' !== $topic_slug && 'all' !== $topic_slug ) {
					$pagination_args['add_args'] = [
						'mcd_topic' => $topic_slug,
					];
				}

				$pagination_links = paginate_links( $pagination_args );

				if ( ! empty( $pagination_links ) ) {
					echo '<nav class="blog-pagination" aria-label="' . esc_attr__( 'Posts pagination', 'mccullough-digital' ) . '">';
					foreach ( $pagination_links as $link ) {
						echo '<span class="blog-pagination__item">' . wp_kses_post( $link ) . '</span>';
					}
					echo '</nav>';
				}
				?>
			</div>
		</section>
		<?php

		wp_reset_postdata();

		return ob_get_clean();
	}
}

// Actually render the block when WordPress includes this file.
echo mcd_render_blog_archive_loop( $attributes, $content, $block );
