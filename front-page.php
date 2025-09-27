<?php
/**
 * The template for displaying the front page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package McCullough_Digital
 */

get_header();

?>
<main class="site-content">
<?php
// The Loop
if ( have_posts() ) {
        while ( have_posts() ) {
                the_post();
                the_content();
        }
}
?>
</main>
<?php

get_footer();
