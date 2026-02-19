<?php
/**
 * Template part: Initiative / project card
 *
 * @package UrbanCMS
 */

$post_id  = get_the_ID();
$status   = get_post_meta( $post_id, '_initiative_status',   true ) ?: 'active';
$progress = (int) get_post_meta( $post_id, '_initiative_progress', true );
$lead     = get_post_meta( $post_id, '_initiative_lead', true );
$location = get_post_meta( $post_id, '_initiative_location', true );
$start    = get_post_meta( $post_id, '_initiative_start', true );
$end      = get_post_meta( $post_id, '_initiative_end', true );
?>

<article class="card initiative-card" id="initiative-<?php echo (int) $post_id; ?>">

    <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" class="card__thumbnail" tabindex="-1" aria-hidden="true">
            <?php the_post_thumbnail( 'urban-card', [ 'loading' => 'lazy', 'alt' => '' ] ); ?>
        </a>
    <?php endif; ?>

    <div class="initiative-card__status">
        <?php echo urban_cms_initiative_status_badge( $post_id ); ?>
    </div>

    <div class="initiative-card__body">
        <?php if ( $location ) : ?>
            <p style="font-size:var(--text-xs);color:var(--color-text-muted);margin-bottom:var(--space-2)"><?php echo esc_html( $location ); ?></p>
        <?php endif; ?>

        <h3 class="initiative-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <p class="card__excerpt"><?php the_excerpt(); ?></p>

        <?php if ( $progress > 0 ) : ?>
        <div>
            <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);margin-bottom:var(--space-1)">
                <span style="color:var(--color-text-muted)"><?php esc_html_e( 'Progress', 'urban-cms' ); ?></span>
                <span style="font-weight:700"><?php echo (int) $progress; ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-bar__fill" style="width:<?php echo (int) $progress; ?>%" role="progressbar" aria-valuenow="<?php echo (int) $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $end ) : ?>
        <p style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:var(--space-3)">
            <?php
            printf(
                /* translators: %s: date */
                esc_html__( 'Target completion: %s', 'urban-cms' ),
                esc_html( date( 'F Y', strtotime( $end ) ) )
            );
            ?>
        </p>
        <?php endif; ?>
    </div>
</article>
