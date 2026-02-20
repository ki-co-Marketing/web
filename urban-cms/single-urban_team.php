<?php
/**
 * Single Team Member Profile
 *
 * @package UrbanCMS
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id  = get_the_ID();
	$title    = get_post_meta( $post_id, '_team_title', true );
	$email    = get_post_meta( $post_id, '_team_email', true );
	$phone    = get_post_meta( $post_id, '_team_phone', true );
	$linkedin = get_post_meta( $post_id, '_team_linkedin', true );
	?>

<!-- =====================================================
	Team Member Header
	===================================================== -->
<div style="background:var(--color-primary);padding:var(--space-12) 0">
	<div class="container">
		<!-- Breadcrumb -->
		<nav aria-label="<?php esc_attr_e( 'Breadcrumb', 'urban-cms' ); ?>"
			style="font-size:var(--text-sm);color:rgba(255,255,255,0.6);margin-bottom:var(--space-6)">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
				style="color:rgba(255,255,255,0.6)"><?php esc_html_e( 'Home', 'urban-cms' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a href="<?php echo esc_url( home_url( '/about/team' ) ); ?>"
				style="color:rgba(255,255,255,0.6)"><?php esc_html_e( 'Team', 'urban-cms' ); ?></a>
			<span aria-hidden="true"> / </span>
			<span style="color:#fff"><?php the_title(); ?></span>
		</nav>

		<div style="display:flex;gap:var(--space-8);align-items:flex-start;flex-wrap:wrap">
			<!-- Portrait -->
			<div style="flex-shrink:0">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php
					the_post_thumbnail(
						'urban-team',
						array(
							'loading' => 'eager',
							'style'   => 'width:160px;height:160px;border-radius:50%;object-fit:cover;border:4px solid rgba(255,255,255,0.2)',
							'alt'     => get_the_title(),
						)
					);
					?>
				<?php else : ?>
					<div style="width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;font-size:4rem;color:rgba(255,255,255,0.4)" aria-hidden="true">
						&#128100;
					</div>
				<?php endif; ?>
			</div>

			<!-- Identity -->
			<div>
				<h1 style="color:#fff;margin-bottom:var(--space-2)"><?php the_title(); ?></h1>
				<?php if ( $title ) : ?>
					<p style="font-size:var(--text-xl);color:var(--color-accent);font-weight:600;margin-bottom:var(--space-5)">
						<?php echo esc_html( $title ); ?>
					</p>
				<?php endif; ?>

				<div style="display:flex;gap:var(--space-3);flex-wrap:wrap;align-items:center">
					<?php if ( $email ) : ?>
						<a href="mailto:<?php echo esc_attr( $email ); ?>"
							class="btn btn--outline-white btn--sm">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
							<?php echo esc_html( $email ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"
							class="btn btn--outline-white btn--sm">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
							<?php echo esc_html( $phone ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $linkedin ) : ?>
						<a href="<?php echo esc_url( $linkedin ); ?>"
							target="_blank" rel="noopener noreferrer"
							class="btn btn--outline-white btn--sm">
							<i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
							LinkedIn
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- =====================================================
	Bio / Content
	===================================================== -->
<div class="container" style="padding-top:var(--space-12);padding-bottom:var(--space-16)">
	<div class="content-with-sidebar">

		<article>
			<?php if ( have_posts() && get_the_content() ) : ?>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			<?php else : ?>
				<p style="color:var(--color-text-muted);font-style:italic">
					<?php esc_html_e( 'No bio available.', 'urban-cms' ); ?>
				</p>
			<?php endif; ?>
		</article>

		<!-- Sidebar -->
		<aside class="sidebar" aria-label="<?php esc_attr_e( 'Team member details', 'urban-cms' ); ?>">
			<div class="widget">
				<h3 class="widget__title"><?php esc_html_e( 'Contact', 'urban-cms' ); ?></h3>
				<dl class="biz-contact-list">
					<?php if ( $title ) : ?>
					<div class="biz-contact-item">
						<dt><?php esc_html_e( 'Role', 'urban-cms' ); ?></dt>
						<dd><?php echo esc_html( $title ); ?></dd>
					</div>
					<?php endif; ?>
					<?php if ( $email ) : ?>
					<div class="biz-contact-item">
						<dt><?php esc_html_e( 'Email', 'urban-cms' ); ?></dt>
						<dd><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></dd>
					</div>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
					<div class="biz-contact-item">
						<dt><?php esc_html_e( 'Phone', 'urban-cms' ); ?></dt>
						<dd><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></dd>
					</div>
					<?php endif; ?>
					<?php if ( $linkedin ) : ?>
					<div class="biz-contact-item">
						<dt>LinkedIn</dt>
						<dd><a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View Profile', 'urban-cms' ); ?></a></dd>
					</div>
					<?php endif; ?>
				</dl>
			</div>

			<a href="<?php echo esc_url( home_url( '/about/team' ) ); ?>"
				class="btn btn--outline" style="width:100%;justify-content:center">
				&larr; <?php esc_html_e( 'Back to Team', 'urban-cms' ); ?>
			</a>
		</aside>

	</div><!-- .content-with-sidebar -->

	<!-- Other team members -->
	<?php
	$others = new WP_Query(
		array(
			'post_type'      => 'urban_team',
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'post__not_in'   => array( $post_id ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);
	if ( $others->have_posts() ) :
		?>
	<div style="margin-top:var(--space-16);padding-top:var(--space-10);border-top:1px solid var(--color-border)">
		<h2 style="margin-bottom:var(--space-8)"><?php esc_html_e( 'Also on the Team', 'urban-cms' ); ?></h2>
		<div class="team-grid">
			<?php
			while ( $others->have_posts() ) :
				$others->the_post();
				?>
				<?php get_template_part( 'template-parts/team', 'member' ); ?>
			<?php endwhile; ?>
		</div>
	</div>
		<?php
		wp_reset_postdata();
	endif;
	?>
</div>

<?php endwhile; ?>

<?php
get_footer();
