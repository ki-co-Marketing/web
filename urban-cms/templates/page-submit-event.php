<?php
/**
 * Template Name: Submit an Event
 * Template Post Type: page
 *
 * @package UrbanCMS
 */

get_header();
?>

<div class="archive-header">
	<div class="container">
		<div class="district-hero__eyebrow"><?php esc_html_e( 'Events Calendar', 'urban-cms' ); ?></div>
		<h1><?php the_title(); ?></h1>
		<p><?php esc_html_e( 'Promote your event to the district community. All submissions are reviewed before publishing — typically within 1 business day.', 'urban-cms' ); ?></p>
	</div>
</div>

<div class="container" style="padding-top:var(--space-12);padding-bottom:var(--space-16)">
	<div style="display:grid;grid-template-columns:1fr 320px;gap:var(--space-12);align-items:start">

		<!-- Form -->
		<div>
			<?php get_template_part( 'template-parts/form', 'submit-event' ); ?>
		</div>

		<!-- Guidelines sidebar -->
		<aside style="position:sticky;top:calc(var(--nav-height) + var(--space-6))">
			<div class="widget">
				<h3 class="widget__title"><?php esc_html_e( 'Submission Guidelines', 'urban-cms' ); ?></h3>
				<ul style="font-size:var(--text-sm);display:flex;flex-direction:column;gap:var(--space-3);list-style:none">
					<li style="display:flex;gap:var(--space-2)">
						<span style="color:var(--color-success);font-weight:700;flex-shrink:0">&#10003;</span>
						<?php esc_html_e( 'Events must take place within or near the district.', 'urban-cms' ); ?>
					</li>
					<li style="display:flex;gap:var(--space-2)">
						<span style="color:var(--color-success);font-weight:700;flex-shrink:0">&#10003;</span>
						<?php esc_html_e( 'Submit at least 5 days before the event date.', 'urban-cms' ); ?>
					</li>
					<li style="display:flex;gap:var(--space-2)">
						<span style="color:var(--color-success);font-weight:700;flex-shrink:0">&#10003;</span>
						<?php esc_html_e( 'Images should be landscape orientation (16:9 preferred).', 'urban-cms' ); ?>
					</li>
					<li style="display:flex;gap:var(--space-2)">
						<span style="color:var(--color-success);font-weight:700;flex-shrink:0">&#10003;</span>
						<?php esc_html_e( 'Events open to the public are prioritized.', 'urban-cms' ); ?>
					</li>
					<li style="display:flex;gap:var(--space-2)">
						<span style="color:var(--color-danger);font-weight:700;flex-shrink:0">&times;</span>
						<?php esc_html_e( 'No purely commercial/sales events without community benefit.', 'urban-cms' ); ?>
					</li>
				</ul>
			</div>

			<div class="widget" style="background:var(--color-primary);color:#fff">
				<h3 class="widget__title" style="color:#fff;border-bottom-color:var(--color-accent)"><?php esc_html_e( 'Quick Tips', 'urban-cms' ); ?></h3>
				<ul style="font-size:var(--text-sm);color:rgba(255,255,255,0.85);display:flex;flex-direction:column;gap:var(--space-2);list-style:disc;padding-left:var(--space-4)">
					<li><?php esc_html_e( 'A clear, descriptive title gets more clicks.', 'urban-cms' ); ?></li>
					<li><?php esc_html_e( 'Include the full address so attendees can find you.', 'urban-cms' ); ?></li>
					<li><?php esc_html_e( 'Adding an RSVP link significantly boosts attendance.', 'urban-cms' ); ?></li>
					<li><?php esc_html_e( 'A high-quality image doubles engagement.', 'urban-cms' ); ?></li>
				</ul>
			</div>

			<div class="widget">
				<h3 class="widget__title"><?php esc_html_e( 'Questions?', 'urban-cms' ); ?></h3>
				<p style="font-size:var(--text-sm)"><?php esc_html_e( 'Contact us for expedited review of large community events.', 'urban-cms' ); ?></p>
				<?php $email = get_theme_mod( 'district_email' ); if ( $email ) : ?>
					<a href="mailto:<?php echo esc_attr( $email ); ?>" class="btn btn--outline btn--sm" style="margin-top:var(--space-3)">
						<?php esc_html_e( 'Contact Us', 'urban-cms' ); ?>
					</a>
				<?php endif; ?>
			</div>
		</aside>
	</div>
</div>

<?php
get_footer();
