<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<footer id="colophon" class="site-footer">
    <div class="stars"></div>
    <div class="stars2"></div>
    <div class="stars3"></div>
    <div class="footer-container">
        <div class="footer-branding">
            <?php
            if ( has_custom_logo() ) {
                the_custom_logo();
            } else {
                echo '<h2 class="site-title"><a href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . get_bloginfo( 'name' ) . '</a></h2>';
            }
            ?>
        </div>

        <div class="footer-social">
            <?php mcd_the_social_links(); ?>
        </div>

        <div class="site-info">
            <p>&copy; <?php echo date('Y'); ?> <?php echo get_bloginfo( 'name' ); ?>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>