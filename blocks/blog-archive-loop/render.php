<?php
/**
 * Server-side rendering for the Blog Archive Loop block.
 *
 * @package McCullough_Digital
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
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

        $output = '<ul class="category-filters__list">';

        foreach ( $items as $item ) {
            $label      = $item['label'];
            $slug       = $item['slug'];
            $is_current = false;
            $url        = $posts_page;

            if ( 'category' === $item['type'] ) {
                $term = get_term_by( 'slug', $slug, 'category' );

                if ( $term && ! is_wp_error( $term ) ) {
                    $term_link = get_term_link( $term );
                    if ( ! is_wp_error( $term_link ) ) {
                        $url = $term_link;
                    }
                } else {
                    $url = home_url( '/category/' . $slug . '/' );
                }

                $is_current = is_category( $slug );
            } else {
                $is_current = is_home() || is_post_type_archive( 'post' );
            }

            $classes = [ 'category-pill' ];

            if ( $is_current ) {
                $classes[] = 'is-active';
            }

            $data_attr = $slug ? ' data-category="' . esc_attr( $slug ) . '"' : ' data-category="all"';

            $output .= sprintf(
                '<li><a class="%1$s" href="%2$s"%3$s>%4$s</a></li>',
                esc_attr( implode( ' ', $classes ) ),
                esc_url( $url ),
                $data_attr,
                esc_html( $label )
            );
        }

        $output .= '</ul>';

        return $output;
    }
}

if ( ! function_exists( 'mcd_render_post_categories' ) ) {
    /**
     * Render the category badges for a post.
     *
     * @param int $post_id Post ID.
     * @return string
     */
    function mcd_render_post_categories( $post_id ) {
        $categories = get_the_category( $post_id );

        if ( empty( $categories ) || is_wp_error( $categories ) ) {
            return '';
        }

        $badges = '';

        foreach ( $categories as $category ) {
            $link = get_term_link( $category );

            if ( is_wp_error( $link ) ) {
                continue;
            }

            $badges .= sprintf(
                '<span class="post-category"><a class="post-category__link" href="%1$s">%2$s</a></span>',
                esc_url( $link ),
                esc_html( $category->name )
            );
        }

        return $badges;
    }
}

if ( ! function_exists( 'mcd_render_blog_archive_loop' ) ) {
    /**
     * Render callback for the blog archive loop block.
     *
     * @param array    $attributes Block attributes.
     * @param string   $content    Block default content.
     * @param WP_Block $block      Block instance.
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
        $query_args   = $wp_query->query_vars;

        $query_args['paged']           = $current_page;
        $query_args['no_found_rows']   = false;
        $query_args['posts_per_page']  = $wp_query->get( 'posts_per_page' );
        $query_args['orderby']         = $wp_query->get( 'orderby' );
        $query_args['order']           = $wp_query->get( 'order' );
        $query_args['ignore_sticky_posts'] = $wp_query->get( 'ignore_sticky_posts' );

        unset( $query_args['fields'] );

        $loop = new WP_Query( $query_args );

        if ( ! $loop->have_posts() ) {
            ob_start();
            ?>
            <section class="category-filters" aria-label="<?php esc_attr_e( 'Category filters', 'mccullough-digital' ); ?>">
                <div class="category-filters__inner">
                    <?php echo mcd_render_category_pills(); ?>
                </div>
            </section>
            <section class="blog-archive__loop container">
                <div class="no-results not-found">
                    <p><?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'mccullough-digital' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </section>
            <?php
            $markup = ob_get_clean();
            wp_reset_postdata();
            return $markup;
        }

        $show_featured = ! is_paged();

        ob_start();
        ?>
        <section class="category-filters" aria-label="<?php esc_attr_e( 'Category filters', 'mccullough-digital' ); ?>">
            <div class="category-filters__inner">
                <?php echo mcd_render_category_pills(); ?>
            </div>
        </section>
        <?php

        if ( $show_featured && $loop->have_posts() ) {
            $loop->the_post();
            $featured_id    = get_the_ID();
            $featured_link  = get_permalink();
            $featured_title = get_the_title();
            $featured_date  = get_the_date( 'M j, Y' );
            $featured_excerpt = wp_trim_words( get_the_excerpt(), 40, '...' );
            $category_badges  = mcd_render_post_categories( $featured_id );
            ?>
            <section class="latest-post" aria-label="<?php esc_attr_e( 'Latest post', 'mccullough-digital' ); ?>">
                <article class="latest-card">
                    <?php if ( is_home() ) : ?>
                        <span class="latest-badge"><?php esc_html_e( 'Most Recent', 'mccullough-digital' ); ?></span>
                    <?php endif; ?>
                    <div class="latest-inner">
                        <a class="latest-media" href="<?php echo esc_url( $featured_link ); ?>">
                            <?php
                            if ( has_post_thumbnail( $featured_id ) ) {
                                the_post_thumbnail( 'large', [ 'class' => 'latest-media__image' ] );
                            } else {
                                ?>
                                <span class="latest-media__placeholder" aria-hidden="true">Image</span>
                                <?php
                            }
                            ?>
                        </a>
                        <div class="latest-content">
                            <div class="post-meta">
                                <?php echo $category_badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <span class="post-date"><?php echo esc_html( $featured_date ); ?></span>
                            </div>
                            <h2 class="latest-title"><a href="<?php echo esc_url( $featured_link ); ?>"><?php echo esc_html( $featured_title ); ?></a></h2>
                            <?php if ( $featured_excerpt ) : ?>
                                <p class="latest-excerpt"><?php echo esc_html( $featured_excerpt ); ?></p>
                            <?php endif; ?>
                            <div class="latest-actions">
                                <a class="cta-button read-more" href="<?php echo esc_url( $featured_link ); ?>"><?php esc_html_e( 'Read Article', 'mccullough-digital' ); ?></a>
                            </div>
                        </div>
                    </div>
                </article>
            </section>
            <?php
        }

        ?>
        <section class="blog-archive__loop container" aria-label="<?php esc_attr_e( 'Blog posts', 'mccullough-digital' ); ?>">
            <div class="post-grid">
                <?php
                $rendered_posts = 0;

                while ( $loop->have_posts() ) {
                    $loop->the_post();
                    $post_id    = get_the_ID();
                    $post_link  = get_permalink();
                    $post_title = get_the_title();
                    $post_date  = get_the_date( 'M j, Y' );
                    $excerpt    = wp_trim_words( get_the_excerpt(), 28, '...' );
                    $rendered_posts++;
                    ?>
                    <article class="post-card">
                        <a class="post-card-image" href="<?php echo esc_url( $post_link ); ?>">
                            <?php
                            if ( has_post_thumbnail() ) {
                                the_post_thumbnail( 'large', [ 'class' => 'post-card-image__media' ] );
                            } else {
                                ?>
                                <span class="post-card-image__placeholder" aria-hidden="true">Image</span>
                                <?php
                            }
                            ?>
                        </a>
                        <div class="post-card-content">
                            <div class="post-meta">
                                <?php echo mcd_render_post_categories( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <span class="post-date"><?php echo esc_html( $post_date ); ?></span>
                            </div>
                            <h2 class="post-card__title"><a href="<?php echo esc_url( $post_link ); ?>"><?php echo esc_html( $post_title ); ?></a></h2>
                            <?php if ( $excerpt ) : ?>
                                <p class="post-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                            <?php endif; ?>
                            <div class="post-card-actions">
                                <a class="cta-button read-more" href="<?php echo esc_url( $post_link ); ?>"><?php esc_html_e( 'Read More', 'mccullough-digital' ); ?></a>
                            </div>
                        </div>
                    </article>
                    <?php
                }
                ?>
            </div>
            <?php if ( 0 === $rendered_posts ) : ?>
                <p class="post-grid__empty"><?php esc_html_e( 'No additional posts yet—check back soon for more stories.', 'mccullough-digital' ); ?></p>
            <?php endif; ?>
            <?php
            $pagination_links = paginate_links(
                [
                    'total'     => max( 1, (int) $loop->max_num_pages ),
                    'current'   => $current_page,
                    'type'      => 'array',
                    'prev_text' => __( '&larr; Previous', 'mccullough-digital' ),
                    'next_text' => __( 'Next &rarr;', 'mccullough-digital' ),
                ]
            );

            if ( ! empty( $pagination_links ) ) {
                echo '<nav class="pagination" aria-label="' . esc_attr__( 'Posts pagination', 'mccullough-digital' ) . '">';
                foreach ( $pagination_links as $link ) {
                    echo wp_kses_post( $link );
                }
                echo '</nav>';
            }
            ?>
        </section>
        <?php

        wp_reset_postdata();

        return ob_get_clean();
    }
}

// Actually render the block when WordPress includes this file
echo mcd_render_blog_archive_loop( $attributes, $content, $block );
