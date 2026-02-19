<?php
/**
 * Single Initiative / Project Detail Page
 *
 * @package UrbanCMS
 */

get_header();

while ( have_posts() ) : the_post();
    $post_id  = get_the_ID();
    $status   = get_post_meta( $post_id, '_initiative_status',   true ) ?: 'active';
    $progress = (int) get_post_meta( $post_id, '_initiative_progress', true );
    $budget   = get_post_meta( $post_id, '_initiative_budget',   true );
    $start    = get_post_meta( $post_id, '_initiative_start',    true );
    $end      = get_post_meta( $post_id, '_initiative_end',      true );
    $lead     = get_post_meta( $post_id, '_initiative_lead',     true );
    $location = get_post_meta( $post_id, '_initiative_location', true );
    $types    = get_the_terms( $post_id, 'initiative_type' );
    $hoods    = get_the_terms( $post_id, 'district_neighborhood' );
?>

<!-- Initiative Header -->
<div class="initiative-single-header" style="background:var(--color-primary);color:#fff;padding:var(--space-12) 0">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-cms' ); ?>" style="margin-bottom:var(--space-4)">
            <span style="font-size:var(--text-sm);color:rgba(255,255,255,0.65)">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:rgba(255,255,255,0.65)"><?php esc_html_e( 'Home', 'urban-cms' ); ?></a>
                <span aria-hidden="true"> / </span>
                <a href="<?php echo esc_url( get_post_type_archive_link( 'urban_initiative' ) ); ?>" style="color:rgba(255,255,255,0.65)"><?php esc_html_e( 'Initiatives', 'urban-cms' ); ?></a>
                <span aria-hidden="true"> / </span>
                <span style="color:#fff"><?php the_title(); ?></span>
            </span>
        </nav>

        <div style="display:flex;gap:var(--space-3);flex-wrap:wrap;margin-bottom:var(--space-4)">
            <?php echo urban_cms_initiative_status_badge( $post_id ); ?>
            <?php if ( $types && ! is_wp_error( $types ) ) : ?>
                <?php foreach ( $types as $type ) : ?>
                    <span class="badge"><?php echo esc_html( $type->name ); ?></span>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h1 style="color:#fff;margin-bottom:var(--space-4)"><?php the_title(); ?></h1>

        <?php if ( has_excerpt() ) : ?>
            <p style="font-size:var(--text-lg);color:rgba(255,255,255,0.8);max-width:640px"><?php the_excerpt(); ?></p>
        <?php endif; ?>

        <!-- Key metrics bar -->
        <div class="initiative-metrics" style="display:flex;gap:var(--space-8);flex-wrap:wrap;margin-top:var(--space-8);padding-top:var(--space-6);border-top:1px solid rgba(255,255,255,0.15)">
            <?php if ( $start ) : ?>
            <div>
                <div style="font-size:var(--text-xs);text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.6);margin-bottom:var(--space-1)"><?php esc_html_e( 'Started', 'urban-cms' ); ?></div>
                <div style="font-weight:700"><?php echo esc_html( date( 'F Y', strtotime( $start ) ) ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $end ) : ?>
            <div>
                <div style="font-size:var(--text-xs);text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.6);margin-bottom:var(--space-1)"><?php esc_html_e( 'Target Completion', 'urban-cms' ); ?></div>
                <div style="font-weight:700"><?php echo esc_html( date( 'F Y', strtotime( $end ) ) ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $budget ) : ?>
            <div>
                <div style="font-size:var(--text-xs);text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.6);margin-bottom:var(--space-1)"><?php esc_html_e( 'Budget', 'urban-cms' ); ?></div>
                <div style="font-weight:700"><?php echo esc_html( $budget ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $lead ) : ?>
            <div>
                <div style="font-size:var(--text-xs);text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.6);margin-bottom:var(--space-1)"><?php esc_html_e( 'Lead / Sponsor', 'urban-cms' ); ?></div>
                <div style="font-weight:700"><?php echo esc_html( $lead ); ?></div>
            </div>
            <?php endif; ?>
            <?php if ( $location ) : ?>
            <div>
                <div style="font-size:var(--text-xs);text-transform:uppercase;letter-spacing:0.08em;color:rgba(255,255,255,0.6);margin-bottom:var(--space-1)"><?php esc_html_e( 'Location', 'urban-cms' ); ?></div>
                <div style="font-weight:700"><?php echo esc_html( $location ); ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Progress Bar Band -->
<?php if ( $progress > 0 ) : ?>
<div style="background:var(--color-surface-alt);padding:var(--space-5) 0;border-bottom:1px solid var(--color-border)">
    <div class="container">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-2)">
            <span style="font-size:var(--text-sm);font-weight:600;color:var(--color-primary)"><?php esc_html_e( 'Project Progress', 'urban-cms' ); ?></span>
            <span style="font-size:var(--text-xl);font-weight:700;color:var(--color-primary)"><?php echo (int) $progress; ?>%</span>
        </div>
        <div class="progress-bar" style="height:12px">
            <div class="progress-bar__fill" style="width:<?php echo (int) $progress; ?>%" role="progressbar" aria-valuenow="<?php echo (int) $progress; ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?php echo esc_attr( sprintf( __( '%d%% complete', 'urban-cms' ), $progress ) ); ?>"></div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Body -->
<div class="container" style="padding-top:var(--space-12);padding-bottom:var(--space-16)">
    <div class="content-with-sidebar">

        <!-- Main content -->
        <article>
            <?php if ( has_post_thumbnail() ) : ?>
            <div style="border-radius:var(--radius-lg);overflow:hidden;margin-bottom:var(--space-8)">
                <?php the_post_thumbnail( 'urban-hero', [ 'loading' => 'eager' ] ); ?>
            </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>

            <!-- Navigation between initiatives -->
            <div style="margin-top:var(--space-10);padding-top:var(--space-6);border-top:1px solid var(--color-border);display:flex;justify-content:space-between">
                <?php
                previous_post_link( '<div class="btn btn--outline btn--sm">&larr; %link</div>', '%title', false, '', 'urban_initiative' );
                next_post_link( '<div class="btn btn--outline btn--sm">%link &rarr;</div>', '%title', false, '', 'urban_initiative' );
                ?>
            </div>
        </article>

        <!-- Sidebar -->
        <aside class="sidebar" aria-label="<?php esc_attr_e( 'Initiative details', 'urban-cms' ); ?>">

            <!-- Status card -->
            <div class="widget">
                <h3 class="widget__title"><?php esc_html_e( 'Initiative Status', 'urban-cms' ); ?></h3>
                <dl style="font-size:var(--text-sm);display:flex;flex-direction:column;gap:var(--space-3)">
                    <div>
                        <dt style="font-weight:700;color:var(--color-primary);margin-bottom:var(--space-1)"><?php esc_html_e( 'Current Status', 'urban-cms' ); ?></dt>
                        <dd><?php echo urban_cms_initiative_status_badge( $post_id ); ?></dd>
                    </div>
                    <?php if ( $progress > 0 ) : ?>
                    <div>
                        <dt style="font-weight:700;color:var(--color-primary);margin-bottom:var(--space-1)"><?php esc_html_e( 'Completion', 'urban-cms' ); ?></dt>
                        <dd><?php echo (int) $progress; ?>%</dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $start ) : ?>
                    <div>
                        <dt style="font-weight:700;color:var(--color-primary);margin-bottom:var(--space-1)"><?php esc_html_e( 'Start Date', 'urban-cms' ); ?></dt>
                        <dd><?php echo esc_html( date( 'F j, Y', strtotime( $start ) ) ); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $end ) : ?>
                    <div>
                        <dt style="font-weight:700;color:var(--color-primary);margin-bottom:var(--space-1)"><?php esc_html_e( 'Target Completion', 'urban-cms' ); ?></dt>
                        <dd><?php echo esc_html( date( 'F j, Y', strtotime( $end ) ) ); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $budget ) : ?>
                    <div>
                        <dt style="font-weight:700;color:var(--color-primary);margin-bottom:var(--space-1)"><?php esc_html_e( 'Budget', 'urban-cms' ); ?></dt>
                        <dd><?php echo esc_html( $budget ); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $lead ) : ?>
                    <div>
                        <dt style="font-weight:700;color:var(--color-primary);margin-bottom:var(--space-1)"><?php esc_html_e( 'Lead / Sponsor', 'urban-cms' ); ?></dt>
                        <dd><?php echo esc_html( $lead ); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $location ) : ?>
                    <div>
                        <dt style="font-weight:700;color:var(--color-primary);margin-bottom:var(--space-1)"><?php esc_html_e( 'Location / Area', 'urban-cms' ); ?></dt>
                        <dd><?php echo esc_html( $location ); ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if ( $hoods && ! is_wp_error( $hoods ) ) : ?>
                    <div>
                        <dt style="font-weight:700;color:var(--color-primary);margin-bottom:var(--space-1)"><?php esc_html_e( 'Neighborhood', 'urban-cms' ); ?></dt>
                        <dd>
                            <?php foreach ( $hoods as $hood ) : ?>
                                <a href="<?php echo esc_url( get_term_link( $hood ) ); ?>"><?php echo esc_html( $hood->name ); ?></a>
                            <?php endforeach; ?>
                        </dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>

            <!-- Back link -->
            <a href="<?php echo esc_url( get_post_type_archive_link( 'urban_initiative' ) ); ?>" class="btn btn--outline" style="width:100%;justify-content:center">
                &larr; <?php esc_html_e( 'All Initiatives', 'urban-cms' ); ?>
            </a>
        </aside>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer();
