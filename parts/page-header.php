<?php
/**
 * Template part for displaying a page header.
 *
 * This template part is designed to be used in various templates like page.php,
 * single.php, archive.php, etc., to provide a consistent header section
 * that matches the design of the front page's section titles.
 *
 * @package McCullough_Digital
 */

$title_text = '';

if ( is_home() && get_option( 'page_for_posts' ) ) {
    $title_text = get_the_title( get_option( 'page_for_posts' ) );
} elseif ( is_archive() ) {
    $title_text = get_the_archive_title();
} elseif ( is_search() ) {
    /* translators: %s: search query. */
    $title_text = sprintf( esc_html__( 'Search results for: %s', 'mccullough-digital' ), '<span>' . get_search_query() . '</span>' );
} elseif ( is_404() ) {
    $title_text = esc_html__( 'Page Not Found', 'mccullough-digital' );
} elseif ( is_singular() ) {
    $title_text = get_the_title();
} else {
    // Fallback for any other case.
    $title_text = get_the_title();
}

?>
<div class="page-header-container">
    <div class="container">
        <h1 class="section-title"><?php echo wp_kses_post( $title_text ); ?></h1>
    </div>
</div>