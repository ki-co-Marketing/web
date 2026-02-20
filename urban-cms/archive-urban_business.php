<?php
/**
 * Business Directory Archive
 *
 * @package UrbanCMS
 */

get_header();

$categories = get_terms(
	array(
		'taxonomy'   => 'business_category',
		'hide_empty' => true,
	)
);
?>

<div class="archive-header">
	<div class="container">
		<h1><?php esc_html_e( 'Business Directory', 'urban-cms' ); ?></h1>
		<p><?php esc_html_e( 'Discover local businesses, restaurants, retailers, and services in the district', 'urban-cms' ); ?></p>
	</div>
</div>

<div class="container">
	<!-- Search + Filter bar -->
	<div class="directory-search">
		<form class="directory-search__form" id="directory-search-form" role="search" aria-label="<?php esc_attr_e( 'Business search', 'urban-cms' ); ?>">
			<input type="text"
					id="biz-search-input"
					name="s"
					placeholder="<?php esc_attr_e( 'Search businesses...', 'urban-cms' ); ?>"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					aria-label="<?php esc_attr_e( 'Search businesses', 'urban-cms' ); ?>">
			<select id="biz-category-select" aria-label="<?php esc_attr_e( 'Filter by category', 'urban-cms' ); ?>">
				<option value=""><?php esc_html_e( 'All Categories', 'urban-cms' ); ?></option>
				<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
					<?php foreach ( $categories as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat->slug ); ?>"
							<?php echo is_tax( 'business_category', $cat ) ? 'selected' : ''; ?>>
							<?php echo esc_html( $cat->name ); ?> (<?php echo (int) $cat->count; ?>)
						</option>
					<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<button type="submit" class="btn btn--primary">
				<?php esc_html_e( 'Search', 'urban-cms' ); ?>
			</button>
		</form>
	</div>

	<div class="content-with-sidebar">
		<!-- Results area (AJAX-updated) -->
		<div>
			<div id="directory-results">
				<?php if ( have_posts() ) : ?>
					<p class="results-count" style="color:var(--color-text-muted);margin-bottom:var(--space-4)">
						<?php
						global $wp_query;
						printf(
							esc_html( _n( '%d business found', '%d businesses found', $wp_query->found_posts, 'urban-cms' ) ),
							(int) $wp_query->found_posts
						);
						?>
					</p>
					<div class="card-grid" id="biz-grid">
						<?php
						while ( have_posts() ) :
							the_post();
							?>
							<?php get_template_part( 'template-parts/business', 'card' ); ?>
						<?php endwhile; ?>
					</div>
					<?php urban_cms_pagination(); ?>
				<?php else : ?>
					<div class="no-results" id="biz-no-results">
						<h2><?php esc_html_e( 'No businesses found.', 'urban-cms' ); ?></h2>
						<p><?php esc_html_e( 'Try a different search or category.', 'urban-cms' ); ?></p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Map embed for directory page -->
			<div style="margin-top:var(--space-12)">
				<h2 style="margin-bottom:var(--space-4)"><?php esc_html_e( 'District Map', 'urban-cms' ); ?></h2>
				<div id="district-map" role="application" aria-label="<?php esc_attr_e( 'Business locations map', 'urban-cms' ); ?>" style="height:400px;border-radius:var(--radius-lg);overflow:hidden;border:1px solid var(--color-border)"></div>
			</div>
		</div>

		<!-- Sidebar -->
		<aside class="sidebar" aria-label="<?php esc_attr_e( 'Directory sidebar', 'urban-cms' ); ?>">
			<div class="widget">
				<h3 class="widget__title"><?php esc_html_e( 'List Your Business', 'urban-cms' ); ?></h3>
				<p><?php esc_html_e( 'Are you a district business? Get listed for free.', 'urban-cms' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/list-your-business' ) ); ?>"
					class="btn btn--primary" style="margin-top:var(--space-3);width:100%;justify-content:center">
					<?php esc_html_e( 'Get Listed', 'urban-cms' ); ?>
				</a>
			</div>
			<?php if ( is_active_sidebar( 'sidebar-directory' ) ) : ?>
				<?php dynamic_sidebar( 'sidebar-directory' ); ?>
			<?php endif; ?>
		</aside>
	</div>
</div>

<?php
get_footer();
