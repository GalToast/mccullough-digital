<?php
/**
 * Title: Case Study Card
 * Slug: mccullough-digital/case-study-card
 * Categories: query
 * Block Types: core/post-template
 * Inserter: no
 */
?>
<!-- wp:group {"tagName":"article","className":"case-study-card","style":{"spacing":{"padding":{"top":"60px","right":"clamp(40px,5vw,80px)","bottom":"60px","left":"clamp(40px,5vw,80px)"},"blockGap":"clamp(32px,6vw,72px)"},"border":{"radius":"24px","width":"2px"}},"borderColor":"neon-cyan","layout":{"type":"constrained","contentSize":"1000px"}} -->
<article class="wp-block-group case-study-card has-border-color has-neon-cyan-border-color" style="border-width:2px;border-radius:24px;padding-top:60px;padding-right:clamp(40px,5vw,80px);padding-bottom:60px;padding-left:clamp(40px,5vw,80px)">
    <!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"top":"clamp(24px,4vw,48px)","left":"clamp(40px,6vw,80px)"}}}} -->
    <div class="wp-block-columns are-vertically-aligned-center">
        <!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">
            <!-- wp:post-featured-image {"isLink":false,"sizeSlug":"mcd-featured-landscape","style":{"border":{"radius":"16px"}}} /-->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"verticalAlignment":"center","width":"60%","style":{"spacing":{"blockGap":"20px"}}} -->
        <div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">
            <!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"left"},"style":{"spacing":{"blockGap":"14px"}}} -->
            <div class="wp-block-group">
                <!-- wp:group {"layout":{"type":"flex","justifyContent":"left","flexWrap":"wrap"},"style":{"spacing":{"blockGap":"10px"}}} -->
                <div class="wp-block-group">
                    <!-- wp:post-terms {"term":"case_segment","className":"case-study-chip"} /-->
                </div>
                <!-- /wp:group -->

                <!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"fontSize":"2.2rem"}}} /-->

                <!-- wp:post-excerpt {"moreText":"View case study","moreAlign":"right","excerptLength":36} /-->

                <!-- wp:group {"layout":{"type":"flex","justifyContent":"left"}} -->
                <div class="wp-block-group">
                    <!-- wp:read-more {"className":"cta-button"} /-->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:column -->
    </div>
    <!-- /wp:columns -->
</article>
<!-- /wp:group -->
