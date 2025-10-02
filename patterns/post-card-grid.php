<?php
/**
 * Title: Post Card
 * Slug: mccullough-digital/post-card
 * Categories: query
 * Block Types: core/post-template
 * Inserter: no
 */
?>
<!-- wp:group {"tagName":"article","className":"post-card"} -->
    <!-- wp:group {"className":"post-card-image","layout":{"type":"constrained"}} -->
        <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9"} /-->
    <!-- /wp:group -->

    <!-- wp:group {"className":"post-card-content","layout":{"type":"flex","orientation":"vertical","justifyContent":"space-between"}} -->
        <!-- wp:group {"className":"post-meta","layout":{"type":"flex","justifyContent":"left","flexWrap":"wrap"}} -->
            <!-- wp:post-terms {"term":"category","separator":" ","className":"post-category"} /-->
            <!-- wp:post-date {"format":"M j, Y","className":"post-date"} /-->
        <!-- /wp:group -->

        <!-- wp:post-title {"level":2,"isLink":true,"className":"post-card__title"} /-->

        <!-- wp:post-excerpt {"moreText":"","showMoreOnNewLine":false,"excerptLength":28,"className":"post-excerpt"} /-->

        <!-- wp:group {"className":"post-card-actions","layout":{"type":"flex","justifyContent":"left"}} -->
            <!-- wp:read-more {"className":"cta-button read-more"} /-->
        <!-- /wp:group -->
    <!-- /wp:group -->
<!-- /wp:group -->

