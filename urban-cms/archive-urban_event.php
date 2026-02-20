<?php
/**
 * Events Calendar Archive
 *
 * @package UrbanCMS
 */

get_header();

$categories  = get_terms(
	array(
		'taxonomy'   => 'event_category',
		'hide_empty' => true,
	)
);
$current_cat = get_query_var( 'event_category' );
?>

<div class="archive-header">
	<div class="container">
		<h1><?php esc_html_e( 'Events Calendar', 'urban-cms' ); ?></h1>
		<p><?php esc_html_e( 'Discover what\'s happening in the district', 'urban-cms' ); ?></p>
	</div>
</div>

<div class="container">
	<!-- Category filter tabs -->
	<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
	<div class="filter-tabs" role="tablist" style="display:flex;gap:var(--space-2);margin-bottom:var(--space-8);flex-wrap:wrap;">
		<a href="<?php echo esc_url( get_post_type_archive_link( 'urban_event' ) ); ?>"
			class="badge badge--primary <?php echo ! $current_cat ? 'active' : ''; ?>"
			style="padding:var(--space-2) var(--space-4);font-size:var(--text-sm);">
			<?php esc_html_e( 'All Events', 'urban-cms' ); ?>
		</a>
		<?php foreach ( $categories as $cat ) : ?>
		<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>"
			class="badge <?php echo is_tax( 'event_category', $cat ) ? 'badge--primary' : ''; ?>"
			style="padding:var(--space-2) var(--space-4);font-size:var(--text-sm);">
			<?php echo esc_html( $cat->name ); ?>
		</a>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>

	<div class="content-with-sidebar">
		<!-- Events list -->
		<div>
			<?php if ( have_posts() ) : ?>
			<div class="events-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<?php get_template_part( 'template-parts/event', 'item' ); ?>
				<?php endwhile; ?>
			</div>
				<?php urban_cms_pagination(); ?>
			<?php else : ?>
			<div class="no-results">
				<h2><?php esc_html_e( 'No upcoming events found.', 'urban-cms' ); ?></h2>
				<p><?php esc_html_e( 'Check back soon for upcoming events in the district.', 'urban-cms' ); ?></p>
			</div>
			<?php endif; ?>
		</div>

		<!-- Sidebar -->
		<aside class="sidebar" aria-label="<?php esc_attr_e( 'Events sidebar', 'urban-cms' ); ?>">
			<div class="widget">
				<h3 class="widget__title"><?php esc_html_e( 'Submit an Event', 'urban-cms' ); ?></h3>
				<p><?php esc_html_e( 'Have an event to promote in the district? Get listed here.', 'urban-cms' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/submit-event' ) ); ?>" class="btn btn--primary" style="margin-top:var(--space-3);width:100%;justify-content:center">
					<?php esc_html_e( 'Submit Your Event', 'urban-cms' ); ?>
				</a>
			</div>
			<?php if ( is_active_sidebar( 'sidebar-events' ) ) : ?>
				<?php dynamic_sidebar( 'sidebar-events' ); ?>
			<?php endif; ?>
		</aside>
	</div>
</div>

<?php
get_footer();
