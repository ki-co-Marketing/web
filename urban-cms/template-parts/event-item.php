<?php
/**
 * Template part: Event list item
 *
 * @package UrbanCMS
 */

$post_id    = get_the_ID();
$start_date = get_post_meta( $post_id, '_event_start_date', true );
$location   = get_post_meta( $post_id, '_event_location', true );
$rsvp_url   = get_post_meta( $post_id, '_event_rsvp_url', true );
$cost       = get_post_meta( $post_id, '_event_cost', true );
$categories = get_the_terms( $post_id, 'event_category' );
$date_obj   = $start_date ? new DateTime( $start_date ) : null;
?>

<article class="event-item" id="event-<?php echo (int) $post_id; ?>">

    <!-- Date block -->
    <div class="event-date" aria-label="<?php echo $date_obj ? esc_attr( $date_obj->format( 'F j, Y' ) ) : ''; ?>">
        <?php if ( $date_obj ) : ?>
            <div class="event-date__month"><?php echo esc_html( $date_obj->format( 'M' ) ); ?></div>
            <div class="event-date__day"><?php echo esc_html( $date_obj->format( 'j' ) ); ?></div>
        <?php else : ?>
            <div class="event-date__month"><?php esc_html_e( 'TBD', 'urban-cms' ); ?></div>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="event-info">
        <?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
            <div style="margin-bottom:var(--space-1)">
                <?php foreach ( $categories as $cat ) : ?>
                    <span class="badge badge--primary"><?php echo esc_html( $cat->name ); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h3 class="event-info__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <div class="event-info__meta">
            <?php if ( $start_date ) echo esc_html( urban_cms_event_date( $post_id ) ); ?>
            <?php $time = urban_cms_event_time( $post_id ); if ( $time ) echo ' &middot; ' . esc_html( $time ); ?>
        </div>

        <?php if ( $location ) : ?>
            <div class="event-info__location" style="font-size:var(--text-sm);color:var(--color-text-muted);margin-top:var(--space-1)">
                <?php echo esc_html( $location ); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- CTA -->
    <div>
        <?php if ( $rsvp_url ) : ?>
            <a href="<?php echo esc_url( $rsvp_url ); ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="btn btn--accent btn--sm">
                <?php echo esc_html( $cost === 'Free' || $cost === 'free' ? __( 'RSVP Free', 'urban-cms' ) : __( 'Get Tickets', 'urban-cms' ) ); ?>
            </a>
        <?php else : ?>
            <a href="<?php the_permalink(); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'Details', 'urban-cms' ); ?></a>
        <?php endif; ?>
    </div>

</article>
