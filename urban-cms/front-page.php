<?php
/**
 * Homepage Template — Urban District Dashboard
 *
 * @package UrbanCMS
 */

get_header();

$hero_heading   = get_theme_mod( 'hero_heading', get_bloginfo( 'name' ) );
$hero_sub       = get_theme_mod( 'hero_subheading', get_bloginfo( 'description' ) );
$hero_cta_text  = get_theme_mod( 'hero_cta_text', __( 'Explore the District', 'urban-cms' ) );
$hero_cta_url   = get_theme_mod( 'hero_cta_url', get_post_type_archive_link( 'urban_business' ) );
$hero_cta2_text = get_theme_mod( 'hero_cta2_text', __( 'View Events', 'urban-cms' ) );
$hero_cta2_url  = get_theme_mod( 'hero_cta2_url', get_post_type_archive_link( 'urban_event' ) );
$hero_bg        = get_theme_mod( 'hero_bg_image' );
?>

<!-- =====================================================
	HERO
	===================================================== -->
<section class="district-hero" aria-labelledby="hero-heading">
	<?php if ( $hero_bg ) : ?>
		<div class="district-hero__bg"
			style="background-image:url(<?php echo esc_url( $hero_bg ); ?>)"
			role="img"
			aria-hidden="true"></div>
	<?php endif; ?>
	<div class="container">
		<div class="district-hero__content">
			<span class="district-hero__eyebrow"><?php echo esc_html( get_theme_mod( 'district_name', get_bloginfo( 'name' ) ) ); ?></span>
			<h1 id="hero-heading"><?php echo esc_html( $hero_heading ); ?></h1>
			<p><?php echo esc_html( $hero_sub ); ?></p>
			<div class="hero-actions">
				<?php if ( $hero_cta_url ) : ?>
					<a href="<?php echo esc_url( $hero_cta_url ); ?>" class="btn btn--accent btn--lg"><?php echo esc_html( $hero_cta_text ); ?></a>
				<?php endif; ?>
				<?php if ( $hero_cta2_url ) : ?>
					<a href="<?php echo esc_url( $hero_cta2_url ); ?>" class="btn btn--outline-white btn--lg"><?php echo esc_html( $hero_cta2_text ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<!-- =====================================================
	DISTRICT STATS BAR
	===================================================== -->
<?php
$has_stats = false;
for ( $i = 1; $i <= 4; $i++ ) {
	if ( get_theme_mod( "stat_{$i}_number" ) ) {
		$has_stats = true;
		break; }
}
if ( $has_stats ) :
	?>
<div class="district-stats">
	<div class="container">
		<div class="district-stats__grid">
			<?php
			for ( $i = 1; $i <= 4; $i++ ) :
				$number = get_theme_mod( "stat_{$i}_number" );
				$label  = get_theme_mod( "stat_{$i}_label" );
				if ( ! $number ) {
					continue;
				}
				?>
			<div class="stat-item">
				<span class="stat-item__number"><?php echo esc_html( $number ); ?></span>
				<span class="stat-item__label"><?php echo esc_html( $label ); ?></span>
			</div>
			<?php endfor; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- =====================================================
	UPCOMING EVENTS
	===================================================== -->
<?php
$events_query = new WP_Query(
	array(
		'post_type'      => 'urban_event',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'meta_key'       => '_event_start_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array(
				'key'     => '_event_start_date',
				'value'   => gmdate( 'Y-m-d' ),
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	)
);

if ( $events_query->have_posts() ) :
	?>
<section class="section" aria-labelledby="events-heading">
	<div class="container">
		<?php
		urban_cms_section_header(
			__( 'Upcoming Events', 'urban-cms' ),
			__( 'What\'s happening in the district', 'urban-cms' ),
			get_post_type_archive_link( 'urban_event' ),
			__( 'Full Calendar', 'urban-cms' )
		);
		?>
		<div class="events-list">
			<?php
			while ( $events_query->have_posts() ) :
				$events_query->the_post();
				?>
				<?php get_template_part( 'template-parts/event', 'item' ); ?>
			<?php endwhile; ?>
		</div>
	</div>
</section>
	<?php
	wp_reset_postdata();
endif;
?>

<!-- =====================================================
	LATEST NEWS
	===================================================== -->
<?php
$news_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
	)
);

if ( $news_query->have_posts() ) :
	$news_posts = $news_query->posts;
	$featured   = array_shift( $news_posts );
	?>
<section class="section section--alt" aria-labelledby="news-heading">
	<div class="container">
		<?php
		urban_cms_section_header(
			__( 'District News', 'urban-cms' ),
			__( 'Updates, announcements, and stories', 'urban-cms' ),
			get_permalink( get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/news' ),
			__( 'All News', 'urban-cms' )
		);
		?>

		<div class="news-featured">
			<!-- Featured / largest card -->
			<div class="news-featured__main">
				<?php
				setup_postdata( $featured );
				get_template_part( 'template-parts/news', 'card', array( 'featured' => true ) );
				wp_reset_postdata();
				?>
			</div>
			<!-- Secondary news cards -->
			<div style="display:flex;flex-direction:column;gap:var(--space-4)">
				<?php
				foreach ( $news_posts as $post ) :
					setup_postdata( $post );
					get_template_part( 'template-parts/news', 'card' );
				endforeach;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</div>
</section>
	<?php
	wp_reset_postdata();
endif;
?>

<!-- =====================================================
	FEATURED BUSINESSES
	===================================================== -->
<?php
$biz_query = new WP_Query(
	array(
		'post_type'      => 'urban_business',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'meta_query'     => array(
			array(
				'key'     => '_business_featured',
				'value'   => '1',
				'compare' => '=',
			),
		),
		'orderby'        => 'rand',
	)
);

if ( ! $biz_query->have_posts() ) {
	// Fall back to any businesses if none are marked featured
	$biz_query = new WP_Query(
		array(
			'post_type'      => 'urban_business',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
}

if ( $biz_query->have_posts() ) :
	?>
<section class="section" aria-labelledby="directory-heading">
	<div class="container">
		<?php
		urban_cms_section_header(
			__( 'District Businesses', 'urban-cms' ),
			__( 'Shop, dine, and discover local businesses', 'urban-cms' ),
			get_post_type_archive_link( 'urban_business' ),
			__( 'Full Directory', 'urban-cms' )
		);
		?>
		<div class="card-grid card-grid--tight">
			<?php
			while ( $biz_query->have_posts() ) :
				$biz_query->the_post();
				?>
				<?php get_template_part( 'template-parts/business', 'card' ); ?>
			<?php endwhile; ?>
		</div>
	</div>
</section>
	<?php
	wp_reset_postdata();
endif;
?>

<!-- =====================================================
	ACTIVE INITIATIVES / PROJECTS
	===================================================== -->
<?php
$init_query = new WP_Query(
	array(
		'post_type'      => 'urban_initiative',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'meta_key'       => '_initiative_status',
		'meta_value'     => 'active',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	)
);

if ( $init_query->have_posts() ) :
	?>
<section class="section section--alt" aria-labelledby="initiatives-heading">
	<div class="container">
		<?php
		urban_cms_section_header(
			__( 'Active Initiatives', 'urban-cms' ),
			__( 'Projects shaping the future of our district', 'urban-cms' ),
			get_post_type_archive_link( 'urban_initiative' ),
			__( 'All Initiatives', 'urban-cms' )
		);
		?>
		<div class="card-grid">
			<?php
			while ( $init_query->have_posts() ) :
				$init_query->the_post();
				?>
				<?php get_template_part( 'template-parts/initiative', 'card' ); ?>
			<?php endwhile; ?>
		</div>
	</div>
</section>
	<?php
	wp_reset_postdata();
endif;
?>

<!-- =====================================================
	DISTRICT MAP
	===================================================== -->
<section class="district-map-section" aria-labelledby="map-heading">
	<div class="container">
		<div class="section__header-row" style="margin-bottom:var(--space-6)">
			<div>
				<h2 id="map-heading"><?php esc_html_e( 'Explore the District', 'urban-cms' ); ?></h2>
				<p style="color:var(--color-text-muted)"><?php esc_html_e( 'Businesses, landmarks, and points of interest', 'urban-cms' ); ?></p>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'urban_business' ) ); ?>" class="btn btn--outline btn--sm">
				<?php esc_html_e( 'Full Directory', 'urban-cms' ); ?>
			</a>
		</div>
		<div id="district-map" role="application" aria-label="<?php esc_attr_e( 'Interactive district map', 'urban-cms' ); ?>"></div>
		<div class="map-legend">
			<div class="map-legend__item">
				<span class="map-legend__dot" style="background:var(--color-accent)"></span>
				<?php esc_html_e( 'Business', 'urban-cms' ); ?>
			</div>
			<div class="map-legend__item">
				<span class="map-legend__dot" style="background:var(--color-primary)"></span>
				<?php esc_html_e( 'Landmark', 'urban-cms' ); ?>
			</div>
			<div class="map-legend__item">
				<span class="map-legend__dot" style="background:var(--color-success)"></span>
				<?php esc_html_e( 'Initiative', 'urban-cms' ); ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
