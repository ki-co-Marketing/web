<?php
/**
 * Main template fallback
 *
 * @package UrbanCMS
 */

get_header();
?>

<div class="archive-header">
	<div class="container">
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1><?php single_post_title(); ?></h1>
		<?php elseif ( is_search() ) : ?>
			<h1><?php printf( esc_html__( 'Search: %s', 'urban-cms' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
		<?php else : ?>
			<h1><?php esc_html_e( 'News &amp; Updates', 'urban-cms' ); ?></h1>
		<?php endif; ?>
	</div>
</div>

<div class="container">
	<div class="content-with-sidebar">
		<main>
			<?php if ( have_posts() ) : ?>
				<div class="card-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						?>
						<?php get_template_part( 'template-parts/news', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<?php urban_cms_pagination(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Nothing found.', 'urban-cms' ); ?></p>
			<?php endif; ?>
		</main>

		<aside class="sidebar">
			<?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
				<?php dynamic_sidebar( 'sidebar-main' ); ?>
			<?php endif; ?>
		</aside>
	</div>
</div>

<?php
get_footer();
