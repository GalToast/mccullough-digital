<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Theme setup
 */
function mcd_setup() {
  add_theme_support( 'title-tag' );
  add_theme_support( 'post-thumbnails' );
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
}
add_action( 'after_setup_theme', 'mcd_setup' );

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
        $css_path = get_theme_file_path( 'assets/homepage-v3.css' );
        if ( file_exists( $css_path ) ) {
            wp_enqueue_style( 'mcd-homepage-v3',
                get_theme_file_uri( 'assets/homepage-v3.css' ),
                array( 'mcd-style' ),
                filemtime( $css_path )
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'mcd_enqueue_homepage_v3_styles' );

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
