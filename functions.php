<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Theme setup
 */
function mcd_setup() {
  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );
  add_image_size( 'mcd-featured-landscape', 1280, 720, true );
  add_image_size( 'mcd-post-card', 640, 360, true );
  add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
  add_theme_support(
    'custom-logo',
    array(
      'height'      => 120,
      'width'       => 120,
      'flex-width'  => true,
      'flex-height' => true,
    )
  );

  add_theme_support( 'wp-block-styles' );
  add_theme_support( 'responsive-embeds' );
  add_theme_support( 'editor-styles' );
  add_editor_style( 'editor-style.css' );

  if ( function_exists( 'register_block_pattern_category' ) ) {
    register_block_pattern_category( 'case-studies', array(
      'label' => __( 'Case Studies', 'mccullough-digital' ),
    ) );
  }
}
add_action( 'after_setup_theme', 'mcd_setup' );

function mcd_custom_image_sizes( $sizes ) {
  $sizes['mcd-featured-landscape'] = __( 'Featured Landscape (McCullough Digital)', 'mccullough-digital' );
  $sizes['mcd-post-card']          = __( 'Post Card (McCullough Digital)', 'mccullough-digital' );

  return $sizes;
}
add_filter( 'image_size_names_choose', 'mcd_custom_image_sizes' );

/**
 * Assets
 */
function mcd_assets() {
  $theme_version = wp_get_theme()->get( 'Version' );

  wp_enqueue_style(
    'mcd-fonts',
    'https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Manrope:wght@300;400;500;600;700&display=swap',
    array(),
    null
  );

  // Enqueue main stylesheet
  wp_enqueue_style( 'mcd-style', get_stylesheet_uri(), array( 'mcd-fonts' ), $theme_version );

  // Enqueue blog header fix CSS (if it exists)
  $blog_fix_path = get_theme_file_path( 'blog-fix.css' );
  if ( $blog_fix_path && file_exists( $blog_fix_path ) ) {
    wp_enqueue_style( 'mcd-blog-fix', get_theme_file_uri( 'blog-fix.css' ), array( 'mcd-style' ), $theme_version );
  }

  // Manually enqueue button block styles to ensure they load
  $button_style_relative = 'blocks/button/style.css';
  $button_style_path     = get_theme_file_path( $button_style_relative );
  if ( $button_style_path && file_exists( $button_style_path ) ) {
    $button_style_ver = filemtime( $button_style_path );
    wp_enqueue_style(
      'mcd-button-block',
      get_theme_file_uri( $button_style_relative ),
      array( 'mcd-style' ),
      $button_style_ver
    );
  }

  // Cache-bust the theme interaction script by filemtime if possible
  $script_relative = 'js/header-scripts.js';
  $script_path     = get_theme_file_path( $script_relative );
  $ver             = ( $script_path && file_exists( $script_path ) ) ? filemtime( $script_path ) : $theme_version;
  wp_enqueue_script( 'mcd-header-scripts', get_theme_file_uri( $script_relative ), array(), $ver, true );

  // Enqueue footer debug script (TEMPORARY - for debugging footer gap)
  $debug_relative = 'js/footer-debug.js';
  $debug_path     = get_theme_file_path( $debug_relative );
  if ( $debug_path && file_exists( $debug_path ) ) {
    $debug_ver = filemtime( $debug_path );
    wp_enqueue_script( 'mcd-footer-debug', get_theme_file_uri( $debug_relative ), array(), $debug_ver, true );
  }
}
add_action( 'wp_enqueue_scripts', 'mcd_assets' );

/**
 * Enqueue homepage v3 styles
 */
function mcd_enqueue_homepage_v3_styles() {
    if ( is_front_page() ) {
        $candidates = array(
            array(
                'handle' => 'mcd-homepage-v4',
                'path'   => 'assets/homepage-v4.css',
            ),
            array(
                'handle' => 'mcd-homepage-v3',
                'path'   => 'assets/homepage-v3.css',
            ),
        );

        foreach ( $candidates as $candidate ) {
            if ( empty( $candidate['handle'] ) || empty( $candidate['path'] ) ) {
                continue;
            }

            $css_path = get_theme_file_path( $candidate['path'] );

            if ( ! $css_path || ! file_exists( $css_path ) ) {
                continue;
            }

            wp_enqueue_style(
                $candidate['handle'],
                get_theme_file_uri( $candidate['path'] ),
                array( 'mcd-style' ),
                filemtime( $css_path )
            );

            break;
        }
    }
}
add_action( 'wp_enqueue_scripts', 'mcd_enqueue_homepage_v3_styles' );

/**
 * Enqueue case study styles on the dedicated template/page.
 */
function mcd_enqueue_case_study_styles() {
    if ( is_page( 'case-studies' ) || is_singular( 'case_study' ) || is_post_type_archive( 'case_study' ) || is_tax( 'case_segment' ) ) {
        $css_path = get_theme_file_path( 'assets/case-study.css' );
        if ( file_exists( $css_path ) ) {
            wp_enqueue_style(
                'mcd-case-study',
                get_theme_file_uri( 'assets/case-study.css' ),
                array( 'mcd-style' ),
                filemtime( $css_path )
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'mcd_enqueue_case_study_styles' );

/**
 * Register custom query vars so we can drive curated filters from the blog pills.
 *
 * @param array $vars Public query vars.
 * @return array
 */
function mcd_register_public_query_vars( $vars ) {
    $vars[] = 'mcd_topic';
    return $vars;
}
add_filter( 'query_vars', 'mcd_register_public_query_vars' );

/**
 * Register the Case Study post type.
 */
function mcd_register_case_study_post_type() {
    $labels = array(
        'name'                  => __( 'Case Studies', 'mccullough-digital' ),
        'singular_name'         => __( 'Case Study', 'mccullough-digital' ),
        'add_new'               => __( 'Add Case Study', 'mccullough-digital' ),
        'add_new_item'          => __( 'Add New Case Study', 'mccullough-digital' ),
        'edit_item'             => __( 'Edit Case Study', 'mccullough-digital' ),
        'new_item'              => __( 'New Case Study', 'mccullough-digital' ),
        'view_item'             => __( 'View Case Study', 'mccullough-digital' ),
        'view_items'            => __( 'View Case Studies', 'mccullough-digital' ),
        'search_items'          => __( 'Search Case Studies', 'mccullough-digital' ),
        'all_items'             => __( 'All Case Studies', 'mccullough-digital' ),
        'archives'              => __( 'Case Study Archives', 'mccullough-digital' ),
        'attributes'            => __( 'Case Study Attributes', 'mccullough-digital' ),
        'insert_into_item'      => __( 'Insert into case study', 'mccullough-digital' ),
        'uploaded_to_this_item' => __( 'Uploaded to this case study', 'mccullough-digital' ),
        'featured_image'        => __( 'Hero Image', 'mccullough-digital' ),
        'set_featured_image'    => __( 'Set hero image', 'mccullough-digital' ),
        'remove_featured_image' => __( 'Remove hero image', 'mccullough-digital' ),
        'use_featured_image'    => __( 'Use as hero image', 'mccullough-digital' ),
        'menu_name'             => __( 'Case Studies', 'mccullough-digital' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-analytics',
        'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
        'has_archive'        => true,
        'rewrite'            => array(
            'slug'       => 'case-study',
            'with_front' => false,
        ),
        'menu_position'      => 21,
        'show_in_nav_menus'  => true,
        'publicly_queryable' => true,
        'capability_type'    => 'post',
    );

    register_post_type( 'case_study', $args );
}
add_action( 'init', 'mcd_register_case_study_post_type' );

/**
 * Optional segmentation for case studies.
 */
function mcd_register_case_study_taxonomy() {
    $labels = array(
        'name'          => __( 'Case Study Segments', 'mccullough-digital' ),
        'singular_name' => __( 'Case Study Segment', 'mccullough-digital' ),
        'search_items'  => __( 'Search Segments', 'mccullough-digital' ),
        'all_items'     => __( 'All Segments', 'mccullough-digital' ),
        'edit_item'     => __( 'Edit Segment', 'mccullough-digital' ),
        'update_item'   => __( 'Update Segment', 'mccullough-digital' ),
        'add_new_item'  => __( 'Add New Segment', 'mccullough-digital' ),
        'new_item_name' => __( 'New Segment Name', 'mccullough-digital' ),
        'menu_name'     => __( 'Segments', 'mccullough-digital' ),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array(
            'slug'       => 'case-segment',
            'with_front' => false,
        ),
    );

    register_taxonomy( 'case_segment', array( 'case_study' ), $args );
}
add_action( 'init', 'mcd_register_case_study_taxonomy' );

/**
 * Ensure rewrite rules include the case study endpoints (one-time flush).
 */
function mcd_maybe_flush_case_study_rewrites() {
    if ( get_option( 'mcd_case_study_rewrite_flushed' ) ) {
        return;
    }

    if ( ! post_type_exists( 'case_study' ) ) {
        return;
    }

    flush_rewrite_rules( false );
    update_option( 'mcd_case_study_rewrite_flushed', 'yes' );
}
add_action( 'init', 'mcd_maybe_flush_case_study_rewrites', 30 );

/**
 * Sanitize stored JSON-LD for case studies.
 *
 * @param mixed $value Incoming value.
 * @return string
 */
function mcd_sanitize_case_schema_json( $value ) {
    if ( is_array( $value ) ) {
        return wp_json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    }

    if ( is_string( $value ) ) {
        $value = trim( $value );

        if ( '' === $value ) {
            return '';
        }

        $decoded = json_decode( $value, true );

        if ( null === $decoded && JSON_ERROR_NONE !== json_last_error() ) {
            return '';
        }

        return wp_json_encode( $decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    }

    return '';
}

/**
 * Register meta storage for case study schema.
 */
function mcd_register_case_study_meta() {
    register_post_meta(
        'case_study',
        'mcd_case_schema_json',
        array(
            'type'              => 'string',
            'single'            => true,
            'show_in_rest'      => true,
            'sanitize_callback' => 'mcd_sanitize_case_schema_json',
            'auth_callback'     => function() {
                return current_user_can( 'edit_posts' );
            },
        )
    );
}
add_action( 'init', 'mcd_register_case_study_meta' );

/**
 * Output JSON-LD on case study pages when available.
 */
function mcd_output_case_study_schema() {
    if ( ! is_singular( 'case_study' ) ) {
        return;
    }

    $post_id = get_queried_object_id();

    if ( ! $post_id ) {
        return;
    }

    $schema_raw = get_post_meta( $post_id, 'mcd_case_schema_json', true );

    if ( empty( $schema_raw ) ) {
        return;
    }

    $decoded = json_decode( $schema_raw, true );

    if ( ! is_array( $decoded ) ) {
        return;
    }

    echo '<script type="application/ld+json">' . wp_json_encode( $decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
}
add_action( 'wp_head', 'mcd_output_case_study_schema' );

/**
 * Seed an initial draft case study from the legacy page content.
 */
function mcd_seed_case_study_from_legacy_page() {
    $existing_posts = get_posts(
        array(
            'post_type'      => 'case_study',
            'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
            'numberposts'    => 1,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'suppress_filters' => true,
        )
    );

    $pattern_path = get_theme_file_path( 'patterns/case-study-onmark.php' );

    if ( ! $pattern_path || ! file_exists( $pattern_path ) ) {
        return;
    }

    ob_start();
    include $pattern_path;
    $content = trim( ob_get_clean() );

    if ( '' === $content ) {
        return;
    }

    $title       = __( 'OnMark (Houston) Digital Transformation', 'mccullough-digital' );
    $excerpt_raw = __( 'Houston fabricator with zero digital footprint to commercial market leader that paused paid ads in six months.', 'mccullough-digital' );

    $case_study_id = 0;

    if ( ! empty( $existing_posts ) ) {
        $case_study_id = (int) $existing_posts[0]->ID;
    }

    if ( 0 === $case_study_id ) {
        $case_study_id = wp_insert_post(
            array(
                'post_type'    => 'case_study',
                'post_status'  => 'publish',
                'post_title'   => $title,
                'post_name'    => sanitize_title( $title ),
                'post_content' => $content,
                'post_excerpt' => $excerpt_raw,
            ),
            true
        );

        if ( is_wp_error( $case_study_id ) || ! $case_study_id ) {
            return;
        }
    } else {
        wp_update_post(
            array(
                'ID'           => $case_study_id,
                'post_title'   => $title,
                'post_name'    => sanitize_title( $title ),
                'post_content' => $content,
                'post_excerpt' => $excerpt_raw,
                'post_status'  => 'publish',
            )
        );
    }

    $segment_term = term_exists( 'Growth Sprint', 'case_segment' );

    if ( ! $segment_term ) {
        $segment_term = wp_insert_term( 'Growth Sprint', 'case_segment' );
    }

    if ( ! is_wp_error( $segment_term ) ) {
        $term_id = is_array( $segment_term ) ? (int) ( $segment_term['term_id'] ?? 0 ) : (int) $segment_term;
        if ( $term_id > 0 ) {
            wp_set_object_terms( $case_study_id, array( $term_id ), 'case_segment', false );
        }
    }

    $schema = array(
        '@context'           => 'https://schema.org',
        '@type'              => 'CaseStudy',
        'name'               => 'OnMark (Houston): From zero to Top-5 for solid surface countertops',
        'about'              => 'Solid-surface fabrication; website + local SEO + ads + automations',
        'provider'           => array(
            '@type' => 'Organization',
            'name'  => 'McCullough Digital Launchpad',
            'url'   => home_url( '/' ),
        ),
        'image'              => 'YOUR-HERO-IMAGE-URL',
        'startDate'          => '2025-01',
        'endDate'            => '2025-06',
        'locationCreated'    => 'Houston, TX',
        'creativeWorkStatus' => 'Published',
        'abstract'           => 'From no site or tracking to a conversion-first build with local SEO, paid, and automations.',
        'text'               => 'Problem → Fix → Result narrative as on page.',
        'citation'           => 'Owner, OnMark LLC — “We started from zero online and had a steady pipeline within two months—Top-5 in Houston, about 4–5 form inquiries a week, and roughly two calls a day. Best of all, we can finally see exactly where every lead comes from.”',
    );

    update_post_meta(
        $case_study_id,
        'mcd_case_schema_json',
        wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
    );
}
add_action( 'init', 'mcd_seed_case_study_from_legacy_page', 20 );

/**
 * Remove legacy static content from the Case Studies page (runs once).
 */
function mcd_cleanup_case_studies_page() {
    if ( get_option( 'mcd_case_studies_page_cleaned' ) ) {
        return;
    }

    $page = get_page_by_path( 'case-studies' );

    if ( ! $page instanceof WP_Post ) {
        update_option( 'mcd_case_studies_page_cleaned', 'missing' );
        return;
    }

    if ( '' === trim( wp_strip_all_tags( $page->post_content ) ) ) {
        update_option( 'mcd_case_studies_page_cleaned', 'already-empty' );
        return;
    }

    wp_update_post(
        array(
            'ID'           => $page->ID,
            'post_content' => '',
        )
    );

    delete_post_meta( $page->ID, 'mcd_case_schema_json' );
    update_option( 'mcd_case_studies_page_cleaned', 'done' );
}
add_action( 'init', 'mcd_cleanup_case_studies_page', 25 );

/**
 * Ensure the case studies query loop always targets the case_study post type.
 *
 * @param array $block Parsed block data.
 * @return array
 */
function mcd_force_case_study_query_attributes( $block ) {
    $block_name = $block['blockName'] ?? 'unknown';

    if ( 'core/query' !== $block_name ) {
        return $block;
    }

    if ( ! is_page( 'case-studies' ) && ! is_post_type_archive( 'case_study' ) ) {
        return $block;
    }

    if ( ! isset( $block['attrs'] ) || ! is_array( $block['attrs'] ) ) {
        $block['attrs'] = array();
    }

    if ( ! isset( $block['attrs']['query'] ) || ! is_array( $block['attrs']['query'] ) ) {
        $block['attrs']['query'] = array();
    }

    $block['attrs']['query']['postType'] = array( 'case_study' );
    $block['attrs']['query']['inherit']  = false;

    return $block;
}
add_filter( 'render_block_data', 'mcd_force_case_study_query_attributes' );

/**
 * Shape the main query for blog related contexts.
 *
 * Ensures the blog filter pills map to category archives without 404s and that
 * search traffic from the hero form only surfaces posts.
 *
 * @param WP_Query $query Query instance to mutate.
 */
function mcd_tune_blog_queries( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_search() ) {
        $query->set( 'post_type', 'post' );
    }

    if ( $query->is_home() || $query->is_post_type_archive( 'post' ) || $query->is_search() ) {
        $topic = $query->get( 'mcd_topic' );
        $topic = is_string( $topic ) ? sanitize_title( $topic ) : '';

        if ( '' !== $topic && 'all' !== $topic ) {
            $term = get_term_by( 'slug', $topic, 'category' );

            if ( $term && ! is_wp_error( $term ) ) {
                $query->set( 'category_name', $term->slug );
            } else {
                $query->set( 'category_name', $topic );
            }
        }

        $query->set( 'post_status', 'publish' );
    }
}
add_action( 'pre_get_posts', 'mcd_tune_blog_queries' );

/**
 * Keep curated blog topic filters on the custom archive URL.
 *
 * Prevents WordPress canonical redirects from bouncing filtered blog requests
 * to the default /category/ slug when the `mcd_topic` query var is present.
 *
 * @param string|false $redirect_url Potential canonical redirect destination.
 * @param string       $requested_url The original requested URL.
 * @return string|false
 */
function mcd_preserve_blog_topic_canonical( $redirect_url, $requested_url ) {
    if ( false === $redirect_url ) {
        return false;
    }

    if ( is_admin() ) {
        return $redirect_url;
    }

    if ( ! ( is_home() || is_post_type_archive( 'post' ) || is_search() ) ) {
        return $redirect_url;
    }

    $topic = get_query_var( 'mcd_topic' );
    $topic = is_string( $topic ) ? sanitize_title( $topic ) : '';

    if ( '' === $topic || 'all' === $topic ) {
        return $redirect_url;
    }

    // Keep pagination working for the blog filters without forcing category permalinks.
    return false;
}
add_filter( 'redirect_canonical', 'mcd_preserve_blog_topic_canonical', 10, 2 );

/**
 * Back-compat for wp_body_open (if very old WP)
 */
if ( ! function_exists( 'wp_body_open' ) ) {
  function wp_body_open() { do_action( 'wp_body_open' ); }
}

/**
 * Register Blocks
 */
function mcd_register_blocks() {
    $block_roots = array_unique(
        array_filter(
            array(
                trailingslashit( get_stylesheet_directory() ) . 'blocks/',
                trailingslashit( get_template_directory() ) . 'blocks/',
            )
        )
    );

    if ( empty( $block_roots ) ) {
        return;
    }

    $registry     = WP_Block_Type_Registry::get_instance();
    $seen_blocks  = array();

    foreach ( $block_roots as $blocks_dir ) {
        if ( ! $blocks_dir || ! file_exists( $blocks_dir ) || ! is_dir( $blocks_dir ) ) {
            error_log( 'MCD Blocks: Directory does not exist: ' . $blocks_dir );
            continue;
        }

        $block_folders = scandir( $blocks_dir );

        foreach ( $block_folders as $block_folder ) {
            if ( '.' === $block_folder || '..' === $block_folder ) {
                continue;
            }

            $block_path      = $blocks_dir . $block_folder;
            $metadata_path   = trailingslashit( $block_path ) . 'block.json';

            if ( ! is_dir( $block_path ) || ! file_exists( $metadata_path ) ) {
                continue;
            }

            $metadata = json_decode( file_get_contents( $metadata_path ), true );
            $name     = is_array( $metadata ) && ! empty( $metadata['name'] ) ? $metadata['name'] : '';

            if ( $name && ( isset( $seen_blocks[ $name ] ) || $registry->is_registered( $name ) ) ) {
                error_log( 'MCD Blocks: Skipping already registered block ' . $name . ' at: ' . $block_path );
                continue;
            }

            $result = register_block_type( $block_path );

            if ( $name ) {
                $seen_blocks[ $name ] = true;
            }

            if ( is_wp_error( $result ) ) {
                error_log( 'MCD Blocks: Failed to register block at: ' . $block_path . ' - ' . $result->get_error_message() );
            } elseif ( ! $result ) {
                error_log( 'MCD Blocks: Failed to register block at: ' . $block_path );
            } else {
                error_log( 'MCD Blocks: Successfully registered block at: ' . $block_path );
            }
        }
    }
}
add_action( 'init', 'mcd_register_blocks' );

/**
 * Custom Block Category
 */
function mcd_block_categories( $categories ) {
    return array_merge(
        $categories,
        [
            [
                'slug'  => 'mcd-blocks',
                'title' => __( 'McCullough Digital Blocks', 'mccullough-digital' ),
                'icon'  => 'star-filled',
            ],
        ]
    );
}
add_action( 'block_categories_all', 'mcd_block_categories' );

/**
 * Resolve the site About page permalink using common slug/title variants.
 *
 * Falls back to `/about/` so existing installs keep a working link even when
 * no About-style page is published yet. The resolved URL is cached in memory
 * for repeated calls during a single request.
 *
 * @since 1.2.39
 *
 * @return string About page URL.
 */
if ( ! function_exists( 'mcd_get_about_page_url' ) ) {
  function mcd_get_about_page_url() {
    static $cached_url = null;

    if ( null !== $cached_url ) {
      return $cached_url;
    }

    $resolved_permalink = '';

    $slug_candidates = apply_filters(
      'mcd_about_page_slug_candidates',
      [ 'about-us', 'about', 'our-story' ]
    );

    if ( is_array( $slug_candidates ) ) {
      foreach ( $slug_candidates as $slug ) {
        if ( ! $slug ) {
          continue;
        }

        $page = get_page_by_path( $slug );

        if ( $page instanceof WP_Post && 'trash' !== $page->post_status ) {
          $permalink = get_permalink( $page );

          if ( $permalink ) {
            $resolved_permalink = $permalink;
            break;
          }
        }
      }
    }

    if ( '' === $resolved_permalink ) {
      $title_candidates = apply_filters(
        'mcd_about_page_title_candidates',
        [
          __( 'About Us', 'mccullough-digital' ),
          __( 'About', 'mccullough-digital' ),
        ]
      );

      if ( is_array( $title_candidates ) ) {
        foreach ( $title_candidates as $title ) {
          if ( ! $title ) {
            continue;
          }

          $page = get_page_by_title( $title, OBJECT, 'page' );

          if ( $page instanceof WP_Post && 'trash' !== $page->post_status ) {
            $permalink = get_permalink( $page );

            if ( $permalink ) {
              $resolved_permalink = $permalink;
              break;
            }
          }
        }
      }
    }

    if ( '' === $resolved_permalink ) {
      $resolved_permalink = home_url( '/about/' );
    }

    /**
     * Filter the resolved About page permalink before caching.
     *
     * @since 1.2.39
     *
     * @param string $resolved_permalink Permalink that will be cached.
     */
    $cached_url = apply_filters( 'mcd_about_page_url', $resolved_permalink );

    return $cached_url;
  }
}

/**
 * Register custom pattern category for theme patterns.
 */
function mcd_register_pattern_category() {
    if ( function_exists( 'register_block_pattern_category' ) ) {
        register_block_pattern_category(
            'mccullough-digital-sections',
            [
                'label' => __( 'McCullough Digital Sections', 'mccullough-digital' ),
            ]
        );
    }
}
add_action( 'init', 'mcd_register_pattern_category' );

/**
 * Flag the home page seeding routine so it runs after the theme is activated.
 */
function mcd_schedule_home_page_seed() {
  if ( ! get_option( 'mcd_seed_home_page' ) ) {
    update_option( 'mcd_seed_home_page', 1 );
  }
}
add_action( 'after_switch_theme', 'mcd_schedule_home_page_seed' );

/**
 * Populate an empty home page with the default landing pattern so it is editable via the page editor.
 */
function mcd_maybe_seed_home_page() {
  if ( ! get_option( 'mcd_seed_home_page' ) ) {
    return;
  }

  if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
    return;
  }

  $registry = WP_Block_Patterns_Registry::get_instance();

  if ( ! $registry->is_registered( 'mccullough-digital/home-landing' ) ) {
    return;
  }

  $pattern = $registry->get_registered( 'mccullough-digital/home-landing' );

  if ( empty( $pattern['content'] ) ) {
    return;
  }

  $page          = null;
  $front_page_id = (int) get_option( 'page_on_front' );

  if ( $front_page_id ) {
    $front_page = get_post( $front_page_id );

    if ( $front_page && 'page' === $front_page->post_type ) {
      $page = $front_page;
    }
  }

  if ( ! $page ) {
    $page = get_page_by_path( 'home' );
  }

  if ( ! $page ) {
    $page = get_page_by_title( __( 'Home', 'mccullough-digital' ), OBJECT, 'page' );
  }

  $seeded = false;

  if ( $page ) {
    if ( 'trash' === $page->post_status ) {
      return;
    }

    $existing_content = trim( (string) $page->post_content );

    if ( '' === wp_strip_all_tags( $existing_content ) ) {
      $updated = wp_update_post(
        [
          'ID'           => $page->ID,
          'post_content' => $pattern['content'],
        ],
        true
      );

      if ( ! is_wp_error( $updated ) && $updated ) {
        $seeded = true;
      }
    }
  } else {
    $inserted = wp_insert_post(
      [
        'post_title'   => __( 'Home', 'mccullough-digital' ),
        'post_name'    => 'home',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => $pattern['content'],
      ],
      true
    );

    if ( ! is_wp_error( $inserted ) && $inserted ) {
      $seeded = true;
    }
  }

  if ( $seeded ) {
    delete_option( 'mcd_seed_home_page' );
  }
}
add_action( 'init', 'mcd_maybe_seed_home_page', 20 );


/**
 * Sanitizes SVG code using a whitelist of allowed tags and attributes.
 *
 * @param string $svg The SVG code to sanitize.
 * @return string Sanitized SVG code.
 */
function mcd_sanitize_svg( $svg ) {
    if ( ! class_exists( 'DOMDocument' ) || ! is_string( $svg ) || '' === trim( $svg ) ) {
        return '';
    }

    // Disable network access when parsing XML to guard against XXE attacks.
    $previous_entity_loader = null;
    if ( function_exists( 'libxml_disable_entity_loader' ) && PHP_VERSION_ID < 80000 ) {
        $previous_entity_loader = libxml_disable_entity_loader( true );
    }

    libxml_use_internal_errors( true );

    $dom = new DOMDocument();

    $loaded = $dom->loadXML( $svg, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );

    libxml_clear_errors();

    if ( null !== $previous_entity_loader && function_exists( 'libxml_disable_entity_loader' ) ) {
        libxml_disable_entity_loader( $previous_entity_loader );
    }

    if ( ! $loaded || ! $dom->documentElement ) {
        return '';
    }

    $allowed_tags = [
        'svg'            => [ 'xmlns', 'viewbox', 'viewBox', 'fill', 'width', 'height', 'class', 'aria-hidden', 'focusable', 'role' ],
        'path'           => [ 'd', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'fill-rule', 'clip-rule', 'stroke-dasharray', 'stroke-miterlimit' ],
        'g'              => [ 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'transform', 'opacity', 'class', 'fill-rule', 'clip-path' ],
        'rect'           => [ 'x', 'y', 'width', 'height', 'rx', 'ry', 'fill', 'stroke', 'stroke-width', 'transform', 'opacity' ],
        'circle'         => [ 'cx', 'cy', 'r', 'fill', 'stroke', 'stroke-width', 'opacity' ],
        'ellipse'        => [ 'cx', 'cy', 'rx', 'ry', 'fill', 'stroke', 'stroke-width', 'opacity' ],
        'line'           => [ 'x1', 'y1', 'x2', 'y2', 'stroke', 'stroke-width', 'stroke-linecap', 'opacity' ],
        'polyline'       => [ 'points', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'opacity' ],
        'polygon'        => [ 'points', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'opacity' ],
        'defs'           => [],
        'symbol'         => [ 'id', 'viewbox', 'viewBox', 'preserveaspectratio' ],
        'use'            => [ 'href', 'xlink:href', 'x', 'y', 'width', 'height', 'transform' ],
        'lineargradient' => [ 'id', 'x1', 'y1', 'x2', 'y2', 'gradientunits', 'gradienttransform', 'href', 'xlink:href', 'spreadmethod' ],
        'radialgradient' => [ 'id', 'cx', 'cy', 'r', 'fx', 'fy', 'gradientunits', 'gradienttransform', 'href', 'xlink:href', 'spreadmethod' ],
        'stop'           => [ 'offset', 'stop-color', 'stop-opacity' ],
        'clippath'       => [ 'id', 'clippathunits' ],
        'mask'           => [ 'id', 'x', 'y', 'width', 'height', 'maskunits', 'maskcontentunits' ],
        'pattern'        => [ 'id', 'x', 'y', 'width', 'height', 'patternunits', 'patterntransform', 'viewbox', 'viewBox', 'href', 'xlink:href' ],
        'title'          => [],
        'desc'           => [],
    ];

    $allowed_global_attributes = [
        'id',
        'class',
        'aria-hidden',
        'focusable',
        'role',
        'aria-label',
        'aria-labelledby',
        'aria-describedby',
        'xmlns:xlink',
        'xml:space',
        'xlink:href',
        'style',
    ];

    $nodes = [ $dom->documentElement ];

    while ( $nodes ) {
        /** @var DOMElement $node */
        $node = array_pop( $nodes );

        $tag_name = strtolower( $node->tagName );

        if ( ! isset( $allowed_tags[ $tag_name ] ) ) {
            $node->parentNode->removeChild( $node );
            continue;
        }

        if ( $node->hasAttributes() ) {
            foreach ( iterator_to_array( $node->attributes ) as $attr ) {
                $attr_name = strtolower( $attr->name );

                if ( in_array( $attr_name, $allowed_global_attributes, true ) ) {
                    continue;
                }

                $allowed_for_tag = $allowed_tags[ $tag_name ];
                if ( ! in_array( $attr_name, $allowed_for_tag, true ) ) {
                    $node->removeAttribute( $attr->name );
                    continue;
                }

                $attribute_value = trim( (string) $attr->value );

                $fragment_only_attributes = [ 'href', 'xlink:href' ];
                if ( in_array( $attr_name, $fragment_only_attributes, true ) ) {
                    if ( '' === $attribute_value || '#' !== $attribute_value[0] ) {
                        $node->removeAttribute( $attr->name );
                        continue;
                    }
                }

                $url_fragment_attributes = [ 'clip-path', 'filter', 'mask', 'fill', 'stroke' ];
                if (
                    in_array( $attr_name, $url_fragment_attributes, true )
                    && preg_match( '/^url\((?!\s*#)/i', $attribute_value )
                ) {
                    $node->removeAttribute( $attr->name );
                    continue;
                }
            }
        }

        foreach ( $node->childNodes as $child ) {
            if ( $child instanceof DOMElement ) {
                $nodes[] = $child;
            }
        }
    }

    return $dom->saveXML( $dom->documentElement );
}

/**
 * Get the SVG for a social link.
 *
 * @param string $url The URL to get the SVG for.
 * @return string The SVG markup or empty string.
 */
function mcd_get_social_link_svg( $url ) {
    $host = wp_parse_url( $url, PHP_URL_HOST );

    if ( ! $host ) {
        return '';
    }

    $host = strtolower( (string) $host );
    $host = preg_replace( '#^www\.#', '', $host );

    // Corrected regex patterns with escaped dots for accuracy.
    $patterns = [
        'twitter'  => [
            '/(^|\.)twitter\.[a-z0-9.-]+$/i',
            '/(^|\.)x\.com$/i',
        ],
        'linkedin' => [ '/(^|\.)linkedin\.[a-z0-9.-]+$/i' ],
        'github'   => [ '/(^|\.)github\.[a-z0-9.-]+$/i' ],
    ];

    /**
     * Filter the social link icon patterns. This allows child themes or plugins
     * to add support for more social networks without modifying theme code.
     *
     * @since 1.2.0
     *
     * @param array  $patterns An associative array of icon names to regex patterns.
     * @param string $host     The hostname being checked.
     */
    $patterns = apply_filters( 'mcd_social_link_svg_patterns', $patterns, $host );

    $icon_name = '';

    foreach ( $patterns as $icon => $regex_list ) {
        foreach ( $regex_list as $regex ) {
            if ( preg_match( $regex, $host ) ) {
                $icon_name = $icon;
                break 2;
            }
        }
    }

    if ( '' === $icon_name ) {
        return '';
    }

    $icon_path = get_stylesheet_directory() . '/assets/icons/' . $icon_name . '.svg';

    if ( ! file_exists( $icon_path ) ) {
        return '';
    }

    $svg = file_get_contents( $icon_path );
    $svg = mcd_sanitize_svg( $svg );

    if ( '' === $svg ) {
        return '';
    }

    if ( false === strpos( $svg, 'aria-hidden' ) ) {
        $svg = preg_replace( '/<svg\s+/i', '<svg aria-hidden="true" role="img" ', $svg, 1 );
    }

    return $svg;
}

/**
 * Limit the "Most Recent" badge to the primary blog query on the first page.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block         Block context array.
 * @return string Filtered block markup.
 */
function mcd_filter_latest_badge_markup( $block_content, $block ) {
    if ( empty( $block_content ) || ! is_array( $block ) ) {
        return $block_content;
    }

    if ( is_admin() ) {
        return $block_content;
    }

    $block_name = isset( $block['blockName'] ) ? $block['blockName'] : null;

    if ( 'core/paragraph' !== $block_name ) {
        return $block_content;
    }

    $class_attribute = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';

    if ( false === strpos( $class_attribute, 'latest-badge' ) && false === strpos( $block_content, 'latest-badge' ) ) {
        return $block_content;
    }

    $show_badge = is_main_query() && is_home() && ! is_paged();

    if ( $show_badge ) {
        return $block_content;
    }

    return '';
}

add_filter( 'render_block', 'mcd_filter_latest_badge_markup', 10, 2 );

/**
 * Enhance the neon footer template part with tagline, service area, and legal links.
 *
 * Ensures the integrated footer experience loads even when the template part
 * has been customised in the Site Editor.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block Block context array.
 * @return string
 */
function mcd_enhance_footer_neon( $block_content, $block ) {
    if ( empty( $block_content ) || ! is_array( $block ) ) {
        return $block_content;
    }

    if ( 'core/template-part' !== ( $block['blockName'] ?? '' ) ) {
        return $block_content;
    }

    if ( 'footer-neon' !== ( $block['attrs']['slug'] ?? '' ) ) {
        return $block_content;
    }

    $block_content = preg_replace(
        '/&copy;[^<]+/',
        '&copy; 2025 McCullough Digital &middot; Crafted with care and spark.',
        $block_content,
        1
    );

    if ( false === strpos( $block_content, 'footer-tagline' ) ) {
        $tagline_markup = <<<HTML
\n          <!-- wp:paragraph {"className":"footer-tagline"} -->\n          <p class="footer-tagline">Built to purr. Wired to roar.</p>\n          <!-- /wp:paragraph -->\n\n          <!-- wp:paragraph {"className":"footer-service-area"} -->\n          <p class="footer-service-area">Serving small businesses across Conroe, The Woodlands, Spring, Magnolia, North Houston, and Montgomery.</p>\n          <!-- /wp:paragraph -->\n
HTML;

        $block_content = preg_replace(
            '/<!-- \\/wp:html -->/',
            '<!-- /wp:html -->' . $tagline_markup,
            $block_content,
            1
        );
    }

    if ( false === strpos( $block_content, 'footer-base__links' ) ) {
        $base_markup = <<<HTML
\n    <!-- wp:group {"className":"footer-base","layout":{"type":"constrained","contentSize":"100%"}} -->\n    <div class="wp-block-group footer-base">\n      <!-- wp:paragraph {"className":"footer-base__links"} -->\n      <p class="footer-base__links">\n        <a href="/privacy">Privacy</a>\n        <span aria-hidden="true">&middot;</span>\n        <a href="/terms">Terms</a>\n        <span aria-hidden="true">&middot;</span>\n        <a href="https://www.facebook.com/mcculloughdigital" rel="noopener noreferrer">Facebook</a>\n        <span aria-hidden="true">&middot;</span>\n        <a href="https://www.instagram.com/mcculloughdigital" rel="noopener noreferrer">Instagram</a>\n        <span aria-hidden="true">&middot;</span>\n        <a href="https://www.linkedin.com/company/mccullough-digital" rel="noopener noreferrer">LinkedIn</a>\n      </p>\n      <!-- /wp:paragraph -->\n    </div>\n    <!-- /wp:group -->\n
HTML;

        $block_content = str_replace(
            '<!-- /wp:columns -->',
            '<!-- /wp:columns -->' . $base_markup,
            $block_content
        );
    }

    return $block_content;
}
add_filter( 'render_block', 'mcd_enhance_footer_neon', 20, 2 );

/**
 * Debug: Show which template is being used
 */
function mcd_debug_template() {
    if ( is_admin() ) {
        return;
    }
    global $template;
    if ( current_user_can( 'manage_options' ) ) {
        echo '<!-- Template being used: ' . esc_html( basename( $template ) ) . ' -->';
    }
}
add_action( 'wp_footer', 'mcd_debug_template' );

if ( ! function_exists( 'mcd_get_neon_button_default_label' ) ) {
    /**
     * Retrieve the default neon button label from the block definition.
     *
     * @return string
     */
    function mcd_get_neon_button_default_label() {
        static $cached_default = null;

        if ( null !== $cached_default ) {
            return $cached_default;
        }

        $default_label = '';

        if ( class_exists( 'WP_Block_Type_Registry' ) ) {
            $block_type = WP_Block_Type_Registry::get_instance()->get_registered( 'mccullough-digital/button' );

            if ( $block_type && isset( $block_type->attributes['buttonText']['default'] ) ) {
                $default_label = trim( wp_strip_all_tags( (string) $block_type->attributes['buttonText']['default'] ) );
            }
        }

        if ( '' === $default_label ) {
            $block_metadata_path = trailingslashit( get_stylesheet_directory() ) . 'blocks/button/block.json';

            if ( file_exists( $block_metadata_path ) ) {
                if ( function_exists( 'wp_json_file_decode' ) ) {
                    $metadata = wp_json_file_decode( $block_metadata_path, array( 'associative' => true ) );
                    if ( is_wp_error( $metadata ) ) {
                        $metadata = null;
                    }
                } else {
                    $metadata = json_decode( file_get_contents( $block_metadata_path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions
                }

                if ( is_array( $metadata ) && isset( $metadata['attributes']['buttonText']['default'] ) ) {
                    $default_label = trim( wp_strip_all_tags( (string) $metadata['attributes']['buttonText']['default'] ) );
                }
            }
        }

        if ( '' === $default_label ) {
            $default_label = __( 'Start a Project', 'mccullough-digital' );
        }

        $cached_default = $default_label;

        return $cached_default;
    }
}
