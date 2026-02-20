<?php
/**
 * Single Event Template
 *
 * @package UrbanCMS
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id    = get_the_ID();
	$start_date = get_post_meta( $post_id, '_event_start_date', true );
	$start_time = get_post_meta( $post_id, '_event_start_time', true );
	$end_time   = get_post_meta( $post_id, '_event_end_time', true );
	$location   = get_post_meta( $post_id, '_event_location', true );
	$address    = get_post_meta( $post_id, '_event_address', true );
	$cost       = get_post_meta( $post_id, '_event_cost', true );
	$rsvp_url   = get_post_meta( $post_id, '_event_rsvp_url', true );
	$organizer  = get_post_meta( $post_id, '_event_organizer', true );
	$categories = get_the_terms( $post_id, 'event_category' );
	?>

<!-- Event Header -->
<div class="event-single__header">
	<div class="container">
		<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
			<div style="margin-bottom:var(--space-3)">
				<?php foreach ( $categories as $cat ) : ?>
					<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="badge badge--accent"><?php echo esc_html( $cat->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<h1><?php the_title(); ?></h1>

		<div class="event-single__date-block">
			<?php if ( $start_date ) : ?>
			<div class="event-detail">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
				<?php echo esc_html( urban_cms_event_date( $post_id ) ); ?>
			</div>
			<?php endif; ?>
			<?php if ( $start_time ) : ?>
			<div class="event-detail">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
				<?php echo esc_html( urban_cms_event_time( $post_id ) ); ?>
			</div>
			<?php endif; ?>
			<?php if ( $location ) : ?>
			<div class="event-detail">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
				<?php echo esc_html( $location ); ?>
				<?php
				if ( $address ) {
					echo ' &mdash; ' . esc_html( $address );}
				?>
			</div>
			<?php endif; ?>
			<?php if ( $cost ) : ?>
			<div class="event-detail">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
				<?php echo esc_html( $cost ); ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Event Content -->
<div class="container" style="padding-top:var(--space-12);padding-bottom:var(--space-16)">
	<div class="content-with-sidebar">

		<article class="event-content">
			<?php if ( has_post_thumbnail() ) : ?>
			<div style="border-radius:var(--radius-lg);overflow:hidden;margin-bottom:var(--space-8)">
				<?php the_post_thumbnail( 'urban-hero', array( 'loading' => 'eager' ) ); ?>
			</div>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>

		<!-- Event Detail Sidebar -->
		<aside class="sidebar" aria-label="<?php esc_attr_e( 'Event details', 'urban-cms' ); ?>">
			<?php if ( $rsvp_url ) : ?>
			<div class="widget">
				<a href="<?php echo esc_url( $rsvp_url ); ?>" target="_blank" rel="noopener noreferrer"
					class="btn btn--accent btn--lg" style="width:100%;justify-content:center">
					<?php esc_html_e( 'RSVP / Get Tickets', 'urban-cms' ); ?>
				</a>
			</div>
			<?php endif; ?>

			<div class="widget">
				<h3 class="widget__title"><?php esc_html_e( 'Event Details', 'urban-cms' ); ?></h3>
				<dl style="font-size:var(--text-sm);display:flex;flex-direction:column;gap:var(--space-3)">
					<?php if ( $start_date ) : ?>
					<div>
						<dt style="font-weight:700;color:var(--color-primary)"><?php esc_html_e( 'Date', 'urban-cms' ); ?></dt>
						<dd><?php echo esc_html( urban_cms_event_date( $post_id ) ); ?></dd>
					</div>
					<?php endif; ?>
					<?php if ( $start_time ) : ?>
					<div>
						<dt style="font-weight:700;color:var(--color-primary)"><?php esc_html_e( 'Time', 'urban-cms' ); ?></dt>
						<dd><?php echo esc_html( urban_cms_event_time( $post_id ) ); ?></dd>
					</div>
					<?php endif; ?>
					<?php if ( $location ) : ?>
					<div>
						<dt style="font-weight:700;color:var(--color-primary)"><?php esc_html_e( 'Location', 'urban-cms' ); ?></dt>
						<dd><?php echo esc_html( $location ); ?></dd>
						<?php if ( $address ) : ?>
						<dd style="color:var(--color-text-muted)"><?php echo esc_html( $address ); ?></dd>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<?php if ( $cost ) : ?>
					<div>
						<dt style="font-weight:700;color:var(--color-primary)"><?php esc_html_e( 'Admission', 'urban-cms' ); ?></dt>
						<dd><?php echo esc_html( $cost ); ?></dd>
					</div>
					<?php endif; ?>
					<?php if ( $organizer ) : ?>
					<div>
						<dt style="font-weight:700;color:var(--color-primary)"><?php esc_html_e( 'Organized by', 'urban-cms' ); ?></dt>
						<dd><?php echo esc_html( $organizer ); ?></dd>
					</div>
					<?php endif; ?>
				</dl>
			</div>

			<?php if ( is_active_sidebar( 'sidebar-events' ) ) : ?>
				<?php dynamic_sidebar( 'sidebar-events' ); ?>
			<?php endif; ?>
		</aside>
	</div>
</div>

<?php endwhile; ?>

<?php
get_footer();
