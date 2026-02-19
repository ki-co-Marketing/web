<?php
/**
 * Template Name: Submit a Business Listing
 * Template Post Type: page
 *
 * @package UrbanCMS
 */

get_header();
?>

<div class="archive-header">
    <div class="container">
        <div class="district-hero__eyebrow"><?php esc_html_e( 'Business Directory', 'urban-cms' ); ?></div>
        <h1><?php the_title(); ?></h1>
        <p><?php esc_html_e( 'Add your business to the district directory. All submissions are reviewed before publishing — usually within 1–2 business days.', 'urban-cms' ); ?></p>
    </div>
</div>

<div class="container" style="padding-top:var(--space-12);padding-bottom:var(--space-16)">
    <div style="display:grid;grid-template-columns:1fr 320px;gap:var(--space-12);align-items:start">

        <!-- Form -->
        <div>
            <?php get_template_part( 'template-parts/form', 'submit-business' ); ?>
        </div>

        <!-- Guidelines sidebar -->
        <aside style="position:sticky;top:calc(var(--nav-height) + var(--space-6))">
            <div class="widget">
                <h3 class="widget__title"><?php esc_html_e( 'Listing Guidelines', 'urban-cms' ); ?></h3>
                <ul style="font-size:var(--text-sm);display:flex;flex-direction:column;gap:var(--space-3);list-style:none">
                    <li style="display:flex;gap:var(--space-2)">
                        <span style="color:var(--color-success);font-weight:700;flex-shrink:0">&#10003;</span>
                        <?php esc_html_e( 'Business must be physically located within or adjacent to the district.', 'urban-cms' ); ?>
                    </li>
                    <li style="display:flex;gap:var(--space-2)">
                        <span style="color:var(--color-success);font-weight:700;flex-shrink:0">&#10003;</span>
                        <?php esc_html_e( 'All information must be accurate and up to date.', 'urban-cms' ); ?>
                    </li>
                    <li style="display:flex;gap:var(--space-2)">
                        <span style="color:var(--color-success);font-weight:700;flex-shrink:0">&#10003;</span>
                        <?php esc_html_e( 'Logos and photos must be owned by you or licensed for use.', 'urban-cms' ); ?>
                    </li>
                    <li style="display:flex;gap:var(--space-2)">
                        <span style="color:var(--color-success);font-weight:700;flex-shrink:0">&#10003;</span>
                        <?php esc_html_e( 'Listings are free and listings are live within 1–2 business days.', 'urban-cms' ); ?>
                    </li>
                    <li style="display:flex;gap:var(--space-2)">
                        <span style="color:var(--color-danger);font-weight:700;flex-shrink:0">&times;</span>
                        <?php esc_html_e( 'No promotional or spam content.', 'urban-cms' ); ?>
                    </li>
                </ul>
            </div>

            <div class="widget">
                <h3 class="widget__title"><?php esc_html_e( 'Need Help?', 'urban-cms' ); ?></h3>
                <p style="font-size:var(--text-sm)"><?php esc_html_e( 'Questions about listing your business? Contact our team.', 'urban-cms' ); ?></p>
                <?php $email = get_theme_mod( 'district_email' ); if ( $email ) : ?>
                    <a href="mailto:<?php echo esc_attr( $email ); ?>" class="btn btn--outline btn--sm" style="margin-top:var(--space-3)">
                        <?php esc_html_e( 'Contact Us', 'urban-cms' ); ?>
                    </a>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</div>

<?php get_footer();
