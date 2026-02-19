</main><!-- #main -->
</div><!-- #page -->

<?php
/* Newsletter section — full-width band above footer */
if ( get_theme_mod( 'newsletter_enabled', true ) ) :
    get_template_part( 'template-parts/newsletter', 'signup', [ 'variant' => 'full-width', 'source' => 'footer' ] );
endif;
?>

<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-grid">

            <!-- Brand Column -->
            <div class="footer-brand">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-brand__logo">
                    <?php bloginfo( 'name' ); ?>
                </a>
                <p class="footer-brand__description">
                    <?php echo esc_html( get_theme_mod( 'footer_about', get_bloginfo( 'description' ) ) ); ?>
                </p>
                <?php urban_cms_render_social_icons(); ?>
            </div>

            <!-- Footer Nav Columns -->
            <?php for ( $col = 1; $col <= 3; $col++ ) :
                $location = 'footer-' . $col;
                if ( ! has_nav_menu( $location ) ) continue;

                $menu_obj = get_nav_menu_locations()[ $location ] ?? null;
                $menu     = $menu_obj ? wp_get_nav_menu_object( $menu_obj ) : null;
            ?>
            <div class="footer-col">
                <?php if ( $menu ) : ?>
                    <p class="footer-heading"><?php echo esc_html( $menu->name ); ?></p>
                <?php endif; ?>
                <?php wp_nav_menu( [
                    'theme_location' => $location,
                    'container'      => false,
                    'menu_class'     => 'footer-links',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ] ); ?>
            </div>
            <?php endfor; ?>

        </div><!-- .footer-grid -->
    </div>

    <!-- Footer Bottom Bar -->
    <div class="container footer-bottom">
        <span>
            <?php
            $copyright = get_theme_mod( 'footer_copyright' );
            if ( $copyright ) {
                echo esc_html( $copyright );
            } else {
                printf(
                    /* translators: %1$s: year, %2$s: site name */
                    esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'urban-cms' ),
                    date( 'Y' ),
                    get_bloginfo( 'name' )
                );
            }
            ?>
        </span>
        <span>
            <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>"><?php esc_html_e( 'Privacy Policy', 'urban-cms' ); ?></a>
            &nbsp;&middot;&nbsp;
            <a href="<?php echo esc_url( home_url( '/accessibility' ) ); ?>"><?php esc_html_e( 'Accessibility', 'urban-cms' ); ?></a>
        </span>
    </div>

</footer><!-- .site-footer -->

<?php wp_footer(); ?>
</body>
</html>
