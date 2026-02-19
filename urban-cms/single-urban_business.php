<?php
/**
 * Single Business Profile Page
 *
 * @package UrbanCMS
 */

get_header();

while ( have_posts() ) : the_post();
    $post_id   = get_the_ID();
    $address   = get_post_meta( $post_id, '_business_address',   true );
    $suite     = get_post_meta( $post_id, '_business_suite',     true );
    $phone     = get_post_meta( $post_id, '_business_phone',     true );
    $email     = get_post_meta( $post_id, '_business_email',     true );
    $website   = get_post_meta( $post_id, '_business_website',   true );
    $hours     = get_post_meta( $post_id, '_business_hours',     true );
    $instagram = get_post_meta( $post_id, '_business_instagram', true );
    $facebook  = get_post_meta( $post_id, '_business_facebook',  true );
    $lat       = (float) get_post_meta( $post_id, '_business_lat', true );
    $lng       = (float) get_post_meta( $post_id, '_business_lng', true );
    $featured  = get_post_meta( $post_id, '_business_featured',  true );
    $cats      = get_the_terms( $post_id, 'business_category' );
    $hoods     = get_the_terms( $post_id, 'district_neighborhood' );
    $full_addr = trim( $address . ( $suite ? ', ' . $suite : '' ) );
?>

<!-- =====================================================
     Business Profile Header
     ===================================================== -->
<div class="biz-profile-header">
    <div class="container">
        <nav class="biz-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-cms' ); ?>">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'urban-cms' ); ?></a>
            <span aria-hidden="true">/</span>
            <a href="<?php echo esc_url( get_post_type_archive_link( 'urban_business' ) ); ?>"><?php esc_html_e( 'Directory', 'urban-cms' ); ?></a>
            <span aria-hidden="true">/</span>
            <span aria-current="page"><?php the_title(); ?></span>
        </nav>

        <div class="biz-profile-header__inner">
            <!-- Logo / Thumbnail -->
            <div class="biz-profile-logo" aria-hidden="true">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'urban-business', [ 'loading' => 'eager', 'alt' => '' ] ); ?>
                <?php else : ?>
                    <span class="biz-profile-logo__placeholder"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span>
                <?php endif; ?>
            </div>

            <div class="biz-profile-header__info">
                <?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
                    <div style="margin-bottom:var(--space-2)">
                        <?php foreach ( $cats as $cat ) : ?>
                            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="badge badge--primary"><?php echo esc_html( $cat->name ); ?></a>
                        <?php endforeach; ?>
                        <?php if ( $featured ) : ?>
                            <span class="badge badge--accent"><?php esc_html_e( 'Featured', 'urban-cms' ); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <h1><?php the_title(); ?></h1>

                <?php if ( $full_addr ) : ?>
                    <p class="biz-profile-header__address">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?php echo esc_html( $full_addr ); ?>
                        <?php if ( $hoods && ! is_wp_error( $hoods ) ) : ?>
                            &middot;
                            <?php foreach ( $hoods as $hood ) : ?>
                                <a href="<?php echo esc_url( get_term_link( $hood ) ); ?>"><?php echo esc_html( $hood->name ); ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </p>
                <?php endif; ?>

                <!-- Quick contact bar -->
                <div class="biz-profile-header__actions">
                    <?php if ( $phone ) : ?>
                        <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>" class="btn btn--outline">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <?php echo esc_html( $phone ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $website ) : ?>
                        <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            <?php esc_html_e( 'Visit Website', 'urban-cms' ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $email ) : ?>
                        <a href="mailto:<?php echo esc_attr( $email ); ?>" class="btn btn--outline">
                            <?php esc_html_e( 'Send Email', 'urban-cms' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div><!-- .biz-profile-header -->

<!-- =====================================================
     Profile Body
     ===================================================== -->
<div class="container biz-profile-body">
    <div class="content-with-sidebar">

        <!-- Main content -->
        <div>
            <?php if ( has_post_thumbnail() ) : ?>
                <div class="biz-profile-gallery">
                    <?php the_post_thumbnail( 'urban-hero', [ 'class' => 'biz-profile-gallery__main', 'loading' => 'eager' ] ); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <?php if ( $lat && $lng ) : ?>
            <!-- Business-specific mini map -->
            <div style="margin-top:var(--space-10)">
                <h2 style="margin-bottom:var(--space-4)"><?php esc_html_e( 'Find Us', 'urban-cms' ); ?></h2>
                <div id="business-single-map"
                     class="business-single-map"
                     data-lat="<?php echo esc_attr( $lat ); ?>"
                     data-lng="<?php echo esc_attr( $lng ); ?>"
                     data-name="<?php echo esc_attr( get_the_title() ); ?>"
                     role="application"
                     aria-label="<?php echo esc_attr( sprintf( __( 'Map showing location of %s', 'urban-cms' ), get_the_title() ) ); ?>">
                </div>
                <?php if ( $full_addr ) : ?>
                    <p style="margin-top:var(--space-3);font-size:var(--text-sm);color:var(--color-text-muted)">
                        <strong><?php echo esc_html( get_the_title() ); ?></strong> &mdash; <?php echo esc_html( $full_addr ); ?>
                    </p>
                    <a href="https://www.google.com/maps/search/<?php echo urlencode( get_the_title() . ' ' . $full_addr ); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="btn btn--outline btn--sm"
                       style="margin-top:var(--space-2)">
                        <?php esc_html_e( 'Get Directions', 'urban-cms' ); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="sidebar" aria-label="<?php esc_attr_e( 'Business info', 'urban-cms' ); ?>">

            <!-- Contact info card -->
            <div class="widget">
                <h3 class="widget__title"><?php esc_html_e( 'Contact &amp; Info', 'urban-cms' ); ?></h3>
                <dl class="biz-contact-list">
                    <?php if ( $full_addr ) : ?>
                    <div class="biz-contact-item">
                        <dt><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg> <?php esc_html_e( 'Address', 'urban-cms' ); ?></dt>
                        <dd><?php echo esc_html( $full_addr ); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $phone ) : ?>
                    <div class="biz-contact-item">
                        <dt><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg> <?php esc_html_e( 'Phone', 'urban-cms' ); ?></dt>
                        <dd><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $email ) : ?>
                    <div class="biz-contact-item">
                        <dt><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg> <?php esc_html_e( 'Email', 'urban-cms' ); ?></dt>
                        <dd><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $website ) : ?>
                    <div class="biz-contact-item">
                        <dt><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg> <?php esc_html_e( 'Website', 'urban-cms' ); ?></dt>
                        <dd><a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( preg_replace( '#^https?://(www\.)?#', '', $website ) ); ?></a></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $hours ) : ?>
                    <div class="biz-contact-item">
                        <dt><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> <?php esc_html_e( 'Hours', 'urban-cms' ); ?></dt>
                        <dd><?php echo esc_html( $hours ); ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>

                <?php if ( $instagram || $facebook ) : ?>
                <div style="margin-top:var(--space-4);display:flex;gap:var(--space-2)">
                    <?php if ( $instagram ) : ?>
                        <a href="<?php echo esc_url( $instagram ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="<?php echo esc_attr( get_the_title() . ' Instagram' ); ?>">
                            <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ( $facebook ) : ?>
                        <a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="<?php echo esc_attr( get_the_title() . ' Facebook' ); ?>">
                            <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Claim / update listing CTA -->
            <div class="widget">
                <h3 class="widget__title"><?php esc_html_e( 'Is this your business?', 'urban-cms' ); ?></h3>
                <p style="font-size:var(--text-sm)"><?php esc_html_e( 'Claim or update this listing to keep your information accurate.', 'urban-cms' ); ?></p>
                <a href="<?php echo esc_url( home_url( '/list-your-business/?claim=' . $post_id ) ); ?>"
                   class="btn btn--outline btn--sm"
                   style="margin-top:var(--space-3);width:100%;justify-content:center">
                    <?php esc_html_e( 'Claim This Listing', 'urban-cms' ); ?>
                </a>
            </div>

            <?php if ( is_active_sidebar( 'sidebar-directory' ) ) : ?>
                <?php dynamic_sidebar( 'sidebar-directory' ); ?>
            <?php endif; ?>
        </aside>
    </div>

    <!-- Related businesses (same category) -->
    <?php if ( $cats && ! is_wp_error( $cats ) ) :
        $related = new WP_Query( [
            'post_type'      => 'urban_business',
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'post__not_in'   => [ $post_id ],
            'tax_query'      => [ [
                'taxonomy' => 'business_category',
                'field'    => 'term_id',
                'terms'    => wp_list_pluck( $cats, 'term_id' ),
            ] ],
        ] );
        if ( $related->have_posts() ) : ?>
    <div style="margin-top:var(--space-16);padding-top:var(--space-10);border-top:1px solid var(--color-border)">
        <?php urban_cms_section_header(
            __( 'More in the Neighborhood', 'urban-cms' ),
            '',
            get_post_type_archive_link( 'urban_business' ),
            __( 'Full Directory', 'urban-cms' )
        ); ?>
        <div class="card-grid card-grid--tight">
            <?php while ( $related->have_posts() ) : $related->the_post(); ?>
                <?php get_template_part( 'template-parts/business', 'card' ); ?>
            <?php endwhile; ?>
        </div>
    </div>
    <?php
        endif;
        wp_reset_postdata();
    endif;
    ?>
</div><!-- .biz-profile-body -->

<?php endwhile; ?>

<!-- Initialize single-business mini map if coordinates exist -->
<?php if ( $lat && $lng ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var el  = document.getElementById('business-single-map');
  if (!el || typeof L === 'undefined') return;
  var lat  = parseFloat(el.dataset.lat);
  var lng  = parseFloat(el.dataset.lng);
  var name = el.dataset.name || '';
  var map  = L.map(el, { center:[lat,lng], zoom:16, scrollWheelZoom:false });
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
    attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom:19
  }).addTo(map);
  L.marker([lat,lng]).addTo(map).bindPopup('<strong>'+name+'</strong>').openPopup();
});
</script>
<?php endif; ?>

<?php get_footer();
