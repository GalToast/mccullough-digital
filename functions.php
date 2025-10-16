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
 * Enqueue contact page styles.
 */
function mcd_enqueue_contact_styles() {
    if ( is_page( 'contact' ) ) {
        $css_path = get_theme_file_path( 'assets/contact.css' );

        if ( $css_path && file_exists( $css_path ) ) {
            wp_enqueue_style(
                'mcd-contact',
                get_theme_file_uri( 'assets/contact.css' ),
                array( 'mcd-style' ),
                filemtime( $css_path )
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'mcd_enqueue_contact_styles' );

/**
 * Enqueue services page styles.
 */
function mcd_enqueue_services_styles() {
    if ( is_page( 'services' ) ) {
        $css_path = get_theme_file_path( 'assets/services.css' );

        if ( $css_path && file_exists( $css_path ) ) {
            wp_enqueue_style(
                'mcd-services',
                get_theme_file_uri( 'assets/services.css' ),
                array( 'mcd-style' ),
                filemtime( $css_path )
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'mcd_enqueue_services_styles' );

/**
 * Enqueue about page styles.
 */
function mcd_enqueue_about_styles() {
    if ( is_page( 'about-us' ) ) {
        $css_path = get_theme_file_path( 'assets/about.css' );

        if ( $css_path && file_exists( $css_path ) ) {
            wp_enqueue_style(
                'mcd-about',
                get_theme_file_uri( 'assets/about.css' ),
                array( 'mcd-style' ),
                filemtime( $css_path )
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'mcd_enqueue_about_styles' );

/**
 * Register custom query vars so we can drive curated filters from the blog pills.
 *
 * @param array $vars Public query vars.
 * @return array
 */
function mcd_register_public_query_vars( $vars ) {
    $vars[] = 'mcd_topic';
    $vars[] = 'case_segment';
    return $vars;
}
add_filter( 'query_vars', 'mcd_register_public_query_vars' );

if ( ! function_exists( 'mcd_host_requires_share_fallback' ) ) {
    /**
     * Detect whether the current site is served via Local's share URL.
     *
     * @return bool
     */
    function mcd_host_requires_share_fallback() {
        static $requires_fallback = null;

        if ( null !== $requires_fallback ) {
            return $requires_fallback;
        }

        $host = wp_parse_url( home_url(), PHP_URL_HOST );
        $requires_fallback = $host && preg_match( '/\.localsite\.io$/', $host );

        return $requires_fallback;
    }
}

if ( ! function_exists( 'mcd_get_case_study_link' ) ) {
    /**
     * Provide a permalink for case study posts that works on Local share URLs.
     *
     * @param int $post_id Case study post ID.
     * @return string
     */
    function mcd_get_case_study_link( $post_id ) {
        $permalink = get_permalink( $post_id );

        if ( mcd_host_requires_share_fallback() || ! $permalink ) {
            $permalink = add_query_arg(
                [
                    'post_type' => 'case_study',
                    'p'         => (int) $post_id,
                ],
                home_url( '/' )
            );
        }

        return $permalink;
    }
}

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

if ( ! function_exists( 'mcd_ensure_system_pages' ) ) {
    /**
     * Ensure support, privacy, and terms pages exist so footer links stay healthy.
     *
     * We create lightweight block-based pages if they have been deleted or never published.
     */
    function mcd_ensure_system_pages() {
        if ( wp_installing() ) {
            return;
        }

        $pages = array(
            array(
                'slug'    => 'support',
                'title'   => __( 'Support', 'mccullough-digital' ),
                'content' => <<<'HTML'
<!-- wp:group {"tagName":"main","className":"site-content mcd-legal mcd-support","layout":{"type":"constrained","contentSize":"760px"}} -->
<main class="wp-block-group site-content mcd-legal mcd-support">
  <!-- wp:heading {"level":1,"className":"mcd-legal__title"} -->
  <h1 class="wp-block-heading mcd-legal__title">Support</h1>
  <!-- /wp:heading -->

  <!-- wp:paragraph {"className":"mcd-legal__lede"} -->
  <p class="mcd-legal__lede">Need a quick hand with your McCullough Digital project? Start here for the fastest path to answers.</p>
  <!-- /wp:paragraph -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">How to reach us fast</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>Email <a href="mailto:hello@mccullough.digital">hello@mccullough.digital</a> for strategy, design, or handoff questions.</li>
      <li>Text <a href="sms:+18322260627">(832) 226-0627</a> when something is blocking a launch or you need a status update.</li>
      <li>Submit the <a href="/contact/#project-intake">project intake form</a> for scoped requests, estimates, or retainer add-ons.</li>
    </ul>
    <!-- /wp:list -->
    <!-- wp:paragraph -->
    <p>We reply within one business day (Monday–Friday, 9am–5pm CT). Launch-critical fixes are triaged within a few hours.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Self-serve resources</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>Check your shared client folder for launch checklists, credentials, and recorded walkthroughs.</li>
      <li>Browse the latest <a href="/case-studies/">case studies</a> for ideas on automation, SEO, and analytics rollouts.</li>
      <li>Use the <a href="/blog/">blog</a> for playbooks on copywriting, local search, and campaign optimization.</li>
    </ul>
    <!-- /wp:list -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">What happens after you message us</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>We log the request in our studio tracker so every task has an owner and timestamp.</li>
      <li>You receive a confirmation with next steps or clarifying questions if we need more detail.</li>
      <li>Launch blockers and uptime issues jump the queue ahead of optimization or content requests.</li>
    </ul>
    <!-- /wp:list -->
    <!-- wp:paragraph -->
    <p>Include screenshots, Loom links, or URLs whenever possible so we can replicate issues immediately.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:group -->
</main>
<!-- /wp:group -->
HTML
            ),
            array(
                'slug'    => 'privacy',
                'title'   => __( 'Privacy Policy', 'mccullough-digital' ),
                'content' => <<<'HTML'
<!-- wp:group {"tagName":"main","className":"site-content mcd-legal","layout":{"type":"constrained","contentSize":"760px"}} -->
<main class="wp-block-group site-content mcd-legal">
  <!-- wp:heading {"level":1,"className":"mcd-legal__title"} -->
  <h1 class="wp-block-heading mcd-legal__title">Privacy Policy</h1>
  <!-- /wp:heading -->

  <!-- wp:paragraph {"className":"mcd-legal__lede"} -->
  <p class="mcd-legal__lede">Last updated: October 16, 2025. We protect client data and visitor information with the same care we apply to our own systems.</p>
  <!-- /wp:paragraph -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Information we collect</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>Project details you share via forms, email, or support channels (business name, goals, analytics access).</li>
      <li>Basic analytics from our own site (page views, referral source, device data) captured through Google Analytics 4.</li>
      <li>Credentials you grant for platforms we manage together (Google, hosting, CRM, ad networks).</li>
      <li>Support history such as tickets, comments, or annotated Loom videos.</li>
    </ul>
    <!-- /wp:list -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">How we use the information</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>To scope, deliver, and refine the services you request.</li>
      <li>To send project updates, milestone reminders, and reporting you opt into.</li>
      <li>To secure your account access, maintain backups, and troubleshoot issues.</li>
      <li>To improve our own site content by understanding which resources our visitors find helpful.</li>
    </ul>
    <!-- /wp:list -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Analytics, cookies & third parties</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>We use Google Analytics 4 and Tag Manager to understand how visitors engage with our site. These tools set first-party cookies and may process anonymised IP addresses. We do not sell or rent your information. Hosting and infrastructure providers (like WP Engine, Google Workspace, and AWS) process data strictly to support the services we deliver.</p>
    <!-- /wp:paragraph -->
    <!-- wp:paragraph -->
    <p>You can disable analytics cookies using your browser preferences or opt out of GA4 tracking with the <a href="https://tools.google.com/dlpage/gaoptout">Google opt-out add-on</a>.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Data retention & security</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>Project files stay in encrypted cloud storage with least-access permissions.</li>
      <li>We retain analytics and form submissions only as long as needed to serve your account or legal obligations.</li>
      <li>Credentials are stored in a password manager with multi-factor authentication.</li>
      <li>If a breach ever impacts your data, we notify you promptly with remediation steps.</li>
    </ul>
    <!-- /wp:list -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Your rights & contact</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>You can request, update, or delete your stored information at any time. Email <a href="mailto:hello@mccullough.digital">hello@mccullough.digital</a> with “Privacy Request” in the subject line and we’ll respond within five business days.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:group -->
</main>
<!-- /wp:group -->
HTML
            ),
            array(
                'slug'    => 'terms',
                'title'   => __( 'Terms of Service', 'mccullough-digital' ),
                'content' => <<<'HTML'
<!-- wp:group {"tagName":"main","className":"site-content mcd-legal","layout":{"type":"constrained","contentSize":"760px"}} -->
<main class="wp-block-group site-content mcd-legal">
  <!-- wp:heading {"level":1,"className":"mcd-legal__title"} -->
  <h1 class="wp-block-heading mcd-legal__title">Terms of Service</h1>
  <!-- /wp:heading -->

  <!-- wp:paragraph {"className":"mcd-legal__lede"} -->
  <p class="mcd-legal__lede">These terms outline how we work together. By hiring McCullough Digital, you agree to the practices below.</p>
  <!-- /wp:paragraph -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Services & deliverables</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>Scope, deliverables, and pricing are confirmed in writing (proposal, email, or shared playbook).</li>
      <li>Minor adjustments are included; material scope changes may require a revised estimate.</li>
      <li>We may showcase non-sensitive results (before/after previews, anonymised metrics) unless you opt out in writing.</li>
    </ul>
    <!-- /wp:list -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Approvals & collaboration</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>You provide timely feedback, content, and access so the project can move on schedule.</li>
      <li>Approvals delivered via email or shared documents authorise us to move to the next phase.</li>
      <li>Pausing the project for more than 30 days may require rescheduling or an additional restart fee.</li>
    </ul>
    <!-- /wp:list -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Payment terms</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>Project work typically starts at 50% deposit and 50% at launch unless otherwise agreed.</li>
      <li>Retainers and recurring services are billed monthly in advance and may be cancelled with 30 days’ notice.</li>
      <li>Late invoices (past 15 days) may pause active work until payment clears.</li>
    </ul>
    <!-- /wp:list -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Intellectual property</h2>
    <!-- /wp:heading -->
    <!-- wp:list {"className":"mcd-legal__list"} -->
    <ul class="mcd-legal__list">
      <li>You own final deliverables upon full payment.</li>
      <li>We retain rights to internal tools, starter frameworks, and libraries used to deliver your project.</li>
      <li>Third-party licenses (fonts, plugins, stock assets) remain bound by their original terms.</li>
    </ul>
    <!-- /wp:list -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Cancellation</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>Either party may end the engagement with written notice. You are responsible for work completed to date, and we deliver all paid-for assets up to the cancellation point.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"mcd-legal__card","layout":{"type":"constrained"}} -->
  <div class="wp-block-group mcd-legal__card">
    <!-- wp:heading {"level":2,"className":"mcd-legal__subtitle"} -->
    <h2 class="wp-block-heading mcd-legal__subtitle">Questions</h2>
    <!-- /wp:heading -->
    <!-- wp:paragraph -->
    <p>Email <a href="mailto:hello@mccullough.digital">hello@mccullough.digital</a> with “Terms question” in the subject line and we’ll respond within one business day.</p>
    <!-- /wp:paragraph -->
  </div>
  <!-- /wp:group -->
</main>
<!-- /wp:group -->
HTML
            ),
        );

        $author_id = get_current_user_id();
        if ( ! $author_id ) {
            $author_id = 1;
        }

        foreach ( $pages as $page ) {
            $page_id  = 0;
            $existing = get_page_by_path( $page['slug'] );

            if ( ! $existing ) {
                $page_id = wp_insert_post(
                    array(
                        'post_type'    => 'page',
                        'post_status'  => 'publish',
                        'post_title'   => $page['title'],
                        'post_name'    => $page['slug'],
                        'post_author'  => $author_id,
                        'post_content' => wp_slash( $page['content'] ),
                    ),
                    true
                );
            } else {
                $page_id      = $existing->ID;
                $needs_update = false;
                $update       = array( 'ID' => $page_id );

                if ( 'publish' !== $existing->post_status ) {
                    $update['post_status'] = 'publish';
                    $needs_update          = true;
                }

                $existing_content = trim( wp_strip_all_tags( $existing->post_content ) );
                if ( '' === $existing_content ) {
                    $update['post_content'] = wp_slash( $page['content'] );
                    $needs_update           = true;
                }

                if ( $needs_update ) {
                    wp_update_post( $update );
                }
            }

            if ( ! is_wp_error( $page_id ) && ! empty( $page['template'] ) ) {
                update_post_meta( $page_id, '_wp_page_template', $page['template'] );
            }
        }
    }
}
add_action( 'after_setup_theme', 'mcd_ensure_system_pages', 20 );

function mcd_get_author_name_overrides() {
    return array(
        'fredjaur33guy' => 'Fred McCullough',
    );
}

function mcd_override_user_display_name( $display_name, $user_id ) {
    if ( ! $user_id ) {
        return $display_name;
    }

    $user = get_user_by( 'id', $user_id );
    if ( ! $user ) {
        return $display_name;
    }

    $overrides = mcd_get_author_name_overrides();
    if ( isset( $overrides[ $user->user_login ] ) ) {
        return $overrides[ $user->user_login ];
    }

    return $display_name;
}
add_filter( 'get_user_display_name', 'mcd_override_user_display_name', 10, 2 );

function mcd_override_post_author_display( $display_name ) {
    global $authordata;

    if ( $authordata instanceof WP_User ) {
        $overrides = mcd_get_author_name_overrides();
        if ( isset( $overrides[ $authordata->user_login ] ) ) {
            return $overrides[ $authordata->user_login ];
        }
    }

    return $display_name;
}
add_filter( 'the_author', 'mcd_override_post_author_display' );

function mcd_override_comment_author_display( $author, $comment_id ) {
    $comment = get_comment( $comment_id );
    if ( $comment && $comment->user_id ) {
        $user = get_user_by( 'id', $comment->user_id );
        if ( $user ) {
            $overrides = mcd_get_author_name_overrides();
            if ( isset( $overrides[ $user->user_login ] ) ) {
                return $overrides[ $user->user_login ];
            }
        }
    }

    return $author;
}
add_filter( 'get_comment_author', 'mcd_override_comment_author_display', 10, 2 );

function mcd_override_excerpt( $excerpt, $post ) {
    if ( ! $post instanceof WP_Post ) {
        return $excerpt;
    }

    $overrides = array(
        'mingling-dingling' => __( 'A quick look at how playful copy can still convert — and the guardrails we follow when we brighten a brand voice.', 'mccullough-digital' ),
    );

    $slug = $post->post_name;
    if ( isset( $overrides[ $slug ] ) ) {
        $stripped = trim( wp_strip_all_tags( $excerpt ) );
        if ( '' === $stripped || 'Laurumpuspuspuspuspus' === $stripped ) {
            return $overrides[ $slug ];
        }
    }

    return $excerpt;
}
add_filter( 'get_the_excerpt', 'mcd_override_excerpt', 10, 2 );

function mcd_sync_author_display_names() {
    if ( wp_installing() ) {
        return;
    }

    $overrides = mcd_get_author_name_overrides();
    foreach ( $overrides as $login => $display_name ) {
        $user = get_user_by( 'login', $login );
        if ( ! $user ) {
            continue;
        }

        $needs_update = false;
        $update       = array( 'ID' => $user->ID );

        if ( $user->display_name !== $display_name ) {
            $update['display_name'] = $display_name;
            $needs_update           = true;
        }

        $nickname = get_user_meta( $user->ID, 'nickname', true );
        if ( $nickname !== $display_name ) {
            update_user_meta( $user->ID, 'nickname', $display_name );
        }

        if ( $needs_update ) {
            wp_update_user( $update );
        }
    }
}
add_action( 'init', 'mcd_sync_author_display_names', 50 );


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

    $hero_image = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/placeholder-16x9.svg';

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
        'image'              => $hero_image,
        'startDate'          => '2025-01',
        'endDate'            => '2025-06',
        'locationCreated'    => 'Houston, TX',
        'creativeWorkStatus' => 'Published',
        'abstract'           => 'From no site or tracking to a conversion-first build with local SEO, paid, and automations.',
        'text'               => 'Launch from zero presence to a conversion-focused WordPress build with local SEO, paid acquisition, and automations that deliver 35-45 forms in 60 days, two calls per day, and Top-5 rankings for the primary Houston term.',
        'citation'           => '"We started from zero online and had a steady pipeline within two months -- Top-5 in Houston, about 4-5 form inquiries a week, and roughly two calls a day. Best of all, we can finally see exactly where every lead comes from." -- Owner, OnMark LLC',
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
 * Allow visitors to filter case studies by segment via the `case_segment` query var.
 *
 * @param WP_Query $query Query instance.
 */
function mcd_filter_case_study_queries( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $is_case_context = $query->is_post_type_archive( 'case_study' )
        || $query->is_tax( 'case_segment' )
        || ( $query->is_page() && 'case-studies' === $query->get( 'pagename' ) );

    if ( ! $is_case_context ) {
        return;
    }

    if ( $query->is_tax( 'case_segment' ) ) {
        return;
    }

    $segment = $query->get( 'case_segment' );
    $segment = is_string( $segment ) ? sanitize_title( $segment ) : '';

    if ( '' === $segment && isset( $_GET['case_segment'] ) ) {
        $segment = sanitize_title( wp_unslash( (string) $_GET['case_segment'] ) );
    }

    if ( '' === $segment ) {
        return;
    }

    $term = get_term_by( 'slug', $segment, 'case_segment' );

    if ( ! $term ) {
        $term = get_term_by( 'id', (int) $segment, 'case_segment' );
    }

    if ( ! $term instanceof WP_Term ) {
        return;
    }

    $query->set(
        'tax_query',
        array(
            array(
                'taxonomy' => 'case_segment',
                'field'    => 'term_id',
                'terms'    => array( (int) $term->term_id ),
            ),
        )
    );

    $query->set( 'case_segment', $term->slug );
}
add_action( 'pre_get_posts', 'mcd_filter_case_study_queries' );

/**
 * Build the case study segment filter pills.
 *
 * @return string
 */
function mcd_render_case_segment_pills() {
    $terms = get_terms(
        array(
            'taxonomy'   => 'case_segment',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        )
    );

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return '';
    }

    $current_segment = get_query_var( 'case_segment' );
    $current_segment = is_string( $current_segment ) ? sanitize_title( $current_segment ) : '';

    if ( '' === $current_segment && isset( $_GET['case_segment'] ) ) {
        $current_segment = sanitize_title( wp_unslash( (string) $_GET['case_segment'] ) );
    }

    if ( is_tax( 'case_segment' ) ) {
        $queried = get_queried_object();

        if ( $queried instanceof WP_Term ) {
            $current_segment = $queried->slug;
        }
    }

    if ( is_page( 'case-studies' ) ) {
        $base_url = get_permalink();
    } else {
        $base_url = get_post_type_archive_link( 'case_study' );
    }

    if ( ! $base_url ) {
        $base_url = home_url( '/' );
    }

    $all_classes = array( 'category-pill', 'pill' );

    if ( '' === $current_segment ) {
        $all_classes[] = 'is-active';
    }

    $all_url = remove_query_arg(
        array( 'case_segment', 'paged' ),
        add_query_arg( array(), $base_url )
    );

    $output  = '<nav class="category-filters case-segment-filters" aria-label="' . esc_attr__( 'Filter case studies by segment', 'mccullough-digital' ) . '">';
    $output .= '<div class="category-filters__inner">';
    $output .= '<ul class="category-filters__list pills" role="list">';
    $output .= sprintf(
        '<li><a class="%1$s" href="%2$s"%3$s>%4$s</a></li>',
        esc_attr( implode( ' ', $all_classes ) ),
        esc_url( $all_url ),
        '' === $current_segment ? ' aria-current="page"' : '',
        esc_html__( 'All Segments', 'mccullough-digital' )
    );

    foreach ( $terms as $term ) {
        if ( ! $term instanceof WP_Term ) {
            continue;
        }

        $slug       = $term->slug;
        $label      = $term->name;
        $url        = add_query_arg(
            array(
                'case_segment' => $slug,
            ),
            $base_url
        );
        $is_current = ( '' !== $current_segment && $current_segment === $slug );

        $classes = array( 'category-pill', 'pill' );

        if ( $is_current ) {
            $classes[] = 'is-active';
        }

        $output .= sprintf(
            '<li><a class="%1$s" href="%2$s"%3$s>%4$s</a></li>',
            esc_attr( implode( ' ', $classes ) ),
            esc_url( $url ),
            $is_current ? ' aria-current="page"' : '',
            esc_html( $label )
        );
    }

    $output .= '</ul>';
    $output .= '</div>';
    $output .= '</nav>';

    return $output;
}

/**
 * Ensure the post excerpt CTA links to the full case study.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block         Parsed block attributes.
 *
 * @return string
 */
function mcd_case_study_excerpt_more_link( $block_content, $block ) {
    $block_name = $block['blockName'] ?? '';

    if ( 'core/post-excerpt' !== $block_name || 'case_study' !== get_post_type() ) {
        return $block_content;
    }

    if ( false === strpos( $block_content, 'wp-block-post-excerpt__more-link' ) ) {
        return $block_content;
    }

    $post_id = get_the_ID();

    if ( ! $post_id ) {
        return $block_content;
    }

    $permalink = mcd_get_case_study_link( $post_id );

    if ( ! $permalink ) {
        return $block_content;
    }

    return preg_replace(
        '#(<a[^>]*wp-block-post-excerpt__more-link[^>]*href=")[^"]*(")#i',
        '$1' . esc_url( $permalink ) . '$2',
        $block_content,
        1
    );
}
add_filter( 'render_block_core/post-excerpt', 'mcd_case_study_excerpt_more_link', 10, 2 );

/**
 * Ensure case study "Read more" buttons link to the correct detail pages.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block         Parsed block attributes.
 *
 * @return string
 */
function mcd_case_study_read_more_link( $block_content, $block ) {
    $block_name = $block['blockName'] ?? '';

    if ( 'core/read-more' !== $block_name || 'case_study' !== get_post_type() ) {
        return $block_content;
    }

    $post_id = get_the_ID();
    if ( ! $post_id ) {
        return $block_content;
    }

    $permalink = mcd_get_case_study_link( $post_id );

    if ( preg_match( '#href\s*=\s*"#i', $block_content ) ) {
        return preg_replace(
            '#href\s*=\s*"([^"]*)"#i',
            'href="' . esc_url( $permalink ) . '"',
            $block_content,
            1
        );
    }

    $text = '';
    if ( ! empty( $block['attrs']['content'] ) ) {
        $text = wp_strip_all_tags( $block['attrs']['content'] );
    }

    if ( '' === $text && ! empty( $block['attrs']['text'] ) ) {
        $text = wp_strip_all_tags( $block['attrs']['text'] );
    }

    if ( '' === $text ) {
        $text = __( 'Read more', 'mccullough-digital' );
    }

    return sprintf(
        '<a class="wp-block-read-more" href="%1$s">%2$s</a>',
        esc_url( $permalink ),
        esc_html( $text )
    );
}
add_filter( 'render_block_core/read-more', 'mcd_case_study_read_more_link', 10, 2 );

/**
 * Ensure post author blocks output a friendly display name.
 *
 * Falls back to first + last name when the saved display name matches the
 * login, so the front end never surfaces raw usernames.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block         Parsed block metadata.
 * @return string
 */
function mcd_friendly_post_author_name( $block_content, $block ) {
    $block_name = $block['blockName'] ?? '';

    if ( 'core/post-author' !== $block_name ) {
        return $block_content;
    }

    if ( ! in_the_loop() || ! is_singular() ) {
        return $block_content;
    }

    $post_id = get_the_ID();

    if ( ! $post_id ) {
        return $block_content;
    }

    $author_id = (int) get_post_field( 'post_author', $post_id );

    if ( $author_id <= 0 ) {
        return $block_content;
    }

    $display_name = get_the_author_meta( 'display_name', $author_id );
    $user_login   = get_the_author_meta( 'user_login', $author_id );
    $first_name   = get_the_author_meta( 'first_name', $author_id );
    $last_name    = get_the_author_meta( 'last_name', $author_id );

    $preferred_name = '';

    if ( '' !== $display_name && $display_name !== $user_login ) {
        $preferred_name = $display_name;
    } elseif ( $first_name || $last_name ) {
        $preferred_name = trim( sprintf( '%s %s', $first_name, $last_name ) );
    } elseif ( '' !== $display_name ) {
        $preferred_name = $display_name;
    } else {
        $preferred_name = $user_login;
    }

    $preferred_name = trim( $preferred_name );

    if ( '' === $preferred_name ) {
        return $block_content;
    }

    $pattern = '/(<(?P<tag>[a-z0-9:-]+)[^>]*class="[^"]*wp-block-post-author__name[^"]*"[^>]*>)(.*?)(<\/(?P=tag)>)/is';

    $updated = preg_replace_callback(
        $pattern,
        static function ( $matches ) use ( $preferred_name ) {
            return $matches[1] . esc_html( $preferred_name ) . $matches[4];
        },
        $block_content,
        1
    );

    if ( null === $updated || false === $updated ) {
        return $block_content;
    }

    return $updated;
}
add_filter( 'render_block_core/post-author', 'mcd_friendly_post_author_name', 10, 2 );

/**
 * Demote the site logo fallback heading on non-home contexts.
 *
 * When no custom logo exists, WordPress falls back to the site title wrapped in
 * an H1 for the site-logo block. That creates duplicate H1s on inner pages, so
 * we convert it to a div while leaving the homepage untouched.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block         Parsed block details.
 * @return string
 */
function mcd_demote_site_logo_heading( $block_content, $block ) {
	$block_name = $block['blockName'] ?? '';

	if ( 'core/site-logo' !== $block_name ) {
		return $block_content;
	}

	if ( is_front_page() || is_home() ) {
		return $block_content;
	}

	if ( false === stripos( $block_content, '<h1' ) ) {
		return $block_content;
	}

	$updated = preg_replace(
		'/<h1([^>]*)>/i',
		'<div$1>',
		$block_content,
		1,
		$count
	);

	if ( 1 !== $count ) {
		return $block_content;
	}

	$updated = preg_replace( '/<\/h1>/i', '</div>', $updated, 1, $closing_count );

	if ( 1 !== $closing_count ) {
		return $block_content;
	}

	return $updated;
}
add_filter( 'render_block_core/site-logo', 'mcd_demote_site_logo_heading', 10, 2 );

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
    if ( ! function_exists( 'register_block_pattern_category' ) ) {
        return;
    }

    $categories = array(
        'mccullough-digital-sections'    => __( 'McCullough Digital Sections', 'mccullough-digital' ),
        'mccullough-digital-components' => __( 'McCullough Digital Components', 'mccullough-digital' ),
    );

    foreach ( $categories as $slug => $label ) {
        register_block_pattern_category(
            $slug,
            array(
                'label' => $label,
            )
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


function mcd_ensure_primary_nav_aria_label( $block_content, $block ) {
    if ( empty( $block['blockName'] ) || 'core/navigation' !== $block['blockName'] ) {
        return $block_content;
    }

    $classes = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';

    if ( false === strpos( $classes, 'main-navigation' ) ) {
        return $block_content;
    }

    $label = isset( $block['attrs']['ariaLabel'] ) && '' !== trim( $block['attrs']['ariaLabel'] )
        ? trim( $block['attrs']['ariaLabel'] )
        : 'Navigation';

    if ( preg_match( '/<nav\\b[^>]*\\saria-label=(\"|\')(.*?)\\1/i', $block_content ) ) {
        return preg_replace(
            '/(<nav\\b[^>]*\\saria-label=(\"|\'))(.*?)\\2/i',
            '$1' . esc_attr( $label ) . '$2',
            $block_content,
            1
        );
    }

    return preg_replace(
        '/<nav\\b/i',
        '<nav aria-label="' . esc_attr( $label ) . '"',
        $block_content,
        1
    );
}
add_filter( 'render_block_core/navigation', 'mcd_ensure_primary_nav_aria_label', 10, 2 );




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



/**
 * Register a private post type to store inbound project leads.
 */
function mcd_register_lead_post_type() {
    $labels = array(
        'name'          => __( 'Leads', 'mccullough-digital' ),
        'singular_name' => __( 'Lead', 'mccullough-digital' ),
        'menu_name'     => __( 'Leads', 'mccullough-digital' ),
        'edit_item'     => __( 'View Lead', 'mccullough-digital' ),
        'view_item'     => __( 'Preview Lead', 'mccullough-digital' ),
        'search_items'  => __( 'Search Leads', 'mccullough-digital' ),
        'not_found'     => __( 'No leads found.', 'mccullough-digital' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_admin_bar'  => false,
        'show_in_nav_menus'  => false,
        'supports'           => array( 'title', 'editor', 'custom-fields' ),
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'has_archive'        => false,
        'rewrite'            => false,
        'menu_icon'          => 'dashicons-clipboard',
        'menu_position'      => 23,
    );

    register_post_type( 'mcd_lead', $args );
}
add_action( 'init', 'mcd_register_lead_post_type' );

add_action( 'rest_api_init', 'mcd_register_contact_form_route' );

/**
 * Register the REST endpoint that processes contact form submissions.
 */
function mcd_register_contact_form_route() {
    register_rest_route(
        'mcd/v1',
        '/contact',
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'mcd_handle_contact_form_submission',
            'permission_callback' => '__return_true',
        )
    );
}

/**
 * Handle inbound contact form submissions.
 *
 * @param WP_REST_Request $request Incoming request payload.
 * @return WP_REST_Response|WP_Error
 */
function mcd_handle_contact_form_submission( WP_REST_Request $request ) {
    $nonce = $request->get_param( 'nonce' );

    if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'mcd_contact_form_submit' ) ) {
        return new WP_Error( 'mcd_contact_invalid_nonce', __( 'Refresh and try again so we know this came from a real person.', 'mccullough-digital' ), array( 'status' => 403 ) );
    }

    $ip_address = mcd_contact_form_get_client_ip();

    if ( $ip_address ) {
        $rate_key = 'mcd_contact_rate_' . md5( $ip_address );

        if ( get_transient( $rate_key ) ) {
            return new WP_Error( 'mcd_contact_rate_limited', __( 'We just heard from you. Give us a moment before sending another message.', 'mccullough-digital' ), array( 'status' => 429 ) );
        }

        set_transient( $rate_key, true, 45 );
    }

    $name     = sanitize_text_field( $request->get_param( 'name' ) );
    $email    = sanitize_email( $request->get_param( 'email' ) );
    $business = sanitize_text_field( $request->get_param( 'business' ) );
    $website  = esc_url_raw( $request->get_param( 'website' ) );
    $goals    = sanitize_textarea_field( $request->get_param( 'goals' ) );
    $budget   = sanitize_text_field( $request->get_param( 'budget' ) );
    $timeline = sanitize_text_field( $request->get_param( 'timeline' ) );
    $referral = sanitize_text_field( $request->get_param( 'referral' ) );
    $opt_in   = (bool) $request->get_param( 'opt_in' );

    if ( $website && ! filter_var( $website, FILTER_VALIDATE_URL ) ) {
        $website = '';
    }

    if ( '' === $name || '' === $email || '' === $goals ) {
        return new WP_Error( 'mcd_contact_missing_fields', __( 'Name, email, and goals are required.', 'mccullough-digital' ), array( 'status' => 422 ) );
    }

    if ( ! is_email( $email ) ) {
        return new WP_Error( 'mcd_contact_invalid_email', __( 'Please enter a valid email address.', 'mccullough-digital' ), array( 'status' => 422 ) );
    }

    $allowed_budgets = array( '', 'under-1k', '1k-3k', '3k-5k', '5k-plus' );
    $budget_labels   = array(
        ''         => __( 'Not specified', 'mccullough-digital' ),
        'under-1k' => __( 'Under $1k', 'mccullough-digital' ),
        '1k-3k'    => __( '$1k - $3k', 'mccullough-digital' ),
        '3k-5k'    => __( '$3k - $5k', 'mccullough-digital' ),
        '5k-plus'  => __( '$5k+', 'mccullough-digital' ),
    );

    if ( ! in_array( $budget, $allowed_budgets, true ) ) {
        $budget = '';
    }

    $allowed_timelines = array( '', 'asap', 'quarter', 'half-year' );
    $timeline_labels   = array(
        ''         => __( 'Not specified', 'mccullough-digital' ),
        'asap'     => __( 'ASAP (next 30 days)', 'mccullough-digital' ),
        'quarter'  => __( 'Next 2-3 months', 'mccullough-digital' ),
        'half-year'=> __( 'Within 6 months', 'mccullough-digital' ),
    );

    if ( ! in_array( $timeline, $allowed_timelines, true ) ) {
        $timeline = '';
    }

    $allowed_services = array(
        'website-brand'        => __( 'Website build or refresh', 'mccullough-digital' ),
        'content-seo'          => __( 'Content & SEO', 'mccullough-digital' ),
        'paid-ads'             => __( 'Paid ads & funnels', 'mccullough-digital' ),
        'automation-analytics' => __( 'Automation & analytics', 'mccullough-digital' ),
        'support-care'         => __( 'Care plan & support', 'mccullough-digital' ),
    );

    $services = (array) $request->get_param( 'services' );
    $services = array_values(
        array_filter(
            array_unique( array_map( 'sanitize_text_field', $services ) ),
            function ( $service ) use ( $allowed_services ) {
                return isset( $allowed_services[ $service ] );
            }
        )
    );

    $submitted_at = current_time( 'mysql' );

    $lead_data = array(
        'name'        => $name,
        'email'       => $email,
        'business'    => $business,
        'website'     => $website,
        'goals'       => $goals,
        'services'    => $services,
        'budget'      => $budget,
        'timeline'    => $timeline,
        'referral'    => $referral,
        'opt_in'      => $opt_in,
        'ip_address'  => $ip_address,
        'submitted_at'=> $submitted_at,
    );

    $service_labels = array_map(
        function ( $service ) use ( $allowed_services ) {
            return $allowed_services[ $service ];
        },
        $services
    );

    $summary_lines = array(
        sprintf( 'Email: %s', $email ),
        sprintf( 'Business: %s', $business ? $business : __( 'Not provided', 'mccullough-digital' ) ),
        sprintf( 'Website: %s', $website ? $website : __( 'Not provided', 'mccullough-digital' ) ),
        sprintf( 'Services: %s', $service_labels ? implode( ', ', $service_labels ) : __( 'Not specified', 'mccullough-digital' ) ),
        sprintf( 'Budget: %s', $budget_labels[ $budget ] ),
        sprintf( 'Timeline: %s', $timeline_labels[ $timeline ] ),
        sprintf( 'Referral: %s', $referral ? $referral : __( 'Not provided', 'mccullough-digital' ) ),
        sprintf( 'Opt-in: %s', $opt_in ? __( 'Yes', 'mccullough-digital' ) : __( 'No', 'mccullough-digital' ) ),
        sprintf( 'IP: %s', $ip_address ? $ip_address : __( 'Unknown', 'mccullough-digital' ) ),
    );

    $post_content = "Goals:\n" . $goals . "\n\nSummary:\n- " . implode( "\n- ", $summary_lines );

    $post_title = sprintf(
        '%1$s  %2$s',
        $name,
        date_i18n( 'Y-m-d H:i', current_time( 'timestamp' ) )
    );

    $post_id = wp_insert_post(
        array(
            'post_type'   => 'mcd_lead',
            'post_status' => 'private',
            'post_title'  => $post_title,
            'post_content'=> $post_content,
            'meta_input'  => array(
                '_mcd_contact_payload' => $lead_data,
                'mcd_lead_email'       => $email,
                'mcd_lead_services'    => $services,
                'mcd_lead_budget'      => $budget,
                'mcd_lead_timeline'    => $timeline,
                'mcd_lead_referral'    => $referral,
                'mcd_lead_opt_in'      => $opt_in ? 'yes' : 'no',
                'mcd_lead_ip'          => $ip_address,
            ),
        ),
        true
    );

    if ( is_wp_error( $post_id ) ) {
        return new WP_Error( 'mcd_contact_save_failed', __( 'We could not save your submission. Please email hello@mccullough.digital.', 'mccullough-digital' ), array( 'status' => 500 ) );
    }

    mcd_contact_form_send_notification( $lead_data, $post_id, $allowed_services, $budget_labels, $timeline_labels );

    do_action( 'mcd_contact_form_submission_saved', $post_id, $lead_data );

    return rest_ensure_response(
        array(
            'message' => __( 'Thanks! We received your details and will be in touch within one business day.', 'mccullough-digital' ),
        )
    );
}

/**
 * Format and email the contact form submission.
 *
 * @param array $lead_data        Sanitised lead data.
 * @param int   $post_id          Stored lead post ID.
 * @param array $allowed_services Mapping of service slugs to labels.
 * @param array $budget_labels    Mapping of budget slugs to labels.
 * @param array $timeline_labels  Mapping of timeline slugs to labels.
 * @return bool
 */
function mcd_contact_form_send_notification( array $lead_data, $post_id, array $allowed_services, array $budget_labels, array $timeline_labels ) {
    $recipient = apply_filters( 'mcd_contact_form_recipient', 'hello@mccullough.digital', $lead_data, $post_id );

    $service_labels = array_map(
        function ( $service ) use ( $allowed_services ) {
            return $allowed_services[ $service ];
        },
        $lead_data['services']
    );

    $lines = array(
        sprintf( 'Name: %s', $lead_data['name'] ),
        sprintf( 'Email: %s', $lead_data['email'] ),
        sprintf( 'Business: %s', $lead_data['business'] ? $lead_data['business'] : __( 'Not provided', 'mccullough-digital' ) ),
        sprintf( 'Website: %s', $lead_data['website'] ? $lead_data['website'] : __( 'Not provided', 'mccullough-digital' ) ),
        sprintf( 'Services: %s', $service_labels ? implode( ', ', $service_labels ) : __( 'Not specified', 'mccullough-digital' ) ),
        sprintf( 'Budget: %s', $budget_labels[ $lead_data['budget'] ] ?? $budget_labels[''] ),
        sprintf( 'Timeline: %s', $timeline_labels[ $lead_data['timeline'] ] ?? $timeline_labels[''] ),
        sprintf( 'Referral: %s', $lead_data['referral'] ? $lead_data['referral'] : __( 'Not provided', 'mccullough-digital' ) ),
        sprintf( 'Opt-in: %s', $lead_data['opt_in'] ? __( 'Yes', 'mccullough-digital' ) : __( 'No', 'mccullough-digital' ) ),
        sprintf( 'IP: %s', $lead_data['ip_address'] ? $lead_data['ip_address'] : __( 'Unknown', 'mccullough-digital' ) ),
        sprintf( 'Submitted: %s', $lead_data['submitted_at'] ),
        '',
        'Goals:',
        $lead_data['goals'],
        '',
        sprintf( 'View lead: %s', admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ),
    );

    $headers = array( 'Content-Type: text/plain; charset=UTF-8' );

    if ( ! empty( $lead_data['email'] ) && is_email( $lead_data['email'] ) ) {
        $headers[] = 'Reply-To: ' . $lead_data['name'] . ' <' . $lead_data['email'] . '>';
    }

    $subject = sprintf( '[McCullough Digital] New project inquiry from %s', $lead_data['name'] );

    $sent = wp_mail( $recipient, $subject, implode( "\n", $lines ), $headers );

    if ( $sent ) {
        update_post_meta( $post_id, 'mcd_lead_email_sent', current_time( 'mysql' ) );
    } else {
        update_post_meta( $post_id, 'mcd_lead_email_sent', '0' );
    }

    return $sent;
}

/**
 * Resolve the best-effort client IP address for rate limiting/logging.
 *
 * @return string
 */
function mcd_contact_form_get_client_ip() {
    $keys = array( 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );

    foreach ( $keys as $key ) {
        if ( empty( $_SERVER[ $key ] ) ) {
            continue;
        }

        $raw = wp_unslash( $_SERVER[ $key ] );

        if ( 'HTTP_X_FORWARDED_FOR' === $key ) {
            $parts = explode( ',', $raw );
            $raw   = trim( $parts[0] );
        }

        $ip = filter_var( $raw, FILTER_VALIDATE_IP );

        if ( $ip ) {
            return $ip;
        }
    }

    return '';
}























