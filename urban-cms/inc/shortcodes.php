<?php
/**
 * Shortcodes for Urban CMS Platform
 * Lets editors embed dynamic content anywhere via the block editor or classic editor.
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

add_shortcode( 'urban_events', 'urban_cms_sc_events' );
add_shortcode( 'urban_directory', 'urban_cms_sc_directory' );
add_shortcode( 'urban_stats', 'urban_cms_sc_stats' );
add_shortcode( 'urban_team', 'urban_cms_sc_team' );

/**
 * [urban_events count="3" category="arts"]
 */
function urban_cms_sc_events( array $atts ): string {
	$atts = shortcode_atts(
		array(
			'count'    => 3,
			'category' => '',
		),
		$atts,
		'urban_events'
	);

	$args = array(
		'post_type'      => 'urban_event',
		'post_status'    => 'publish',
		'posts_per_page' => absint( $atts['count'] ),
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
	);

	if ( $atts['category'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'event_category',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $atts['category'] ),
			),
		);
	}

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No upcoming events.', 'urban-cms' ) . '</p>';
	}

	ob_start();
	echo '<div class="events-list">';
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/event', 'item' );
	}
	echo '</div>';
	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * [urban_directory count="6" category="restaurants"]
 */
function urban_cms_sc_directory( array $atts ): string {
	$atts = shortcode_atts(
		array(
			'count'    => 6,
			'category' => '',
			'featured' => '',
		),
		$atts,
		'urban_directory'
	);

	$args = array(
		'post_type'      => 'urban_business',
		'post_status'    => 'publish',
		'posts_per_page' => absint( $atts['count'] ),
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	if ( $atts['featured'] ) {
		$args['meta_query'] = array(
			array(
				'key'     => '_business_featured',
				'value'   => '1',
				'compare' => '=',
			),
		);
	}

	if ( $atts['category'] ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'business_category',
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $atts['category'] ),
			),
		);
	}

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No businesses found.', 'urban-cms' ) . '</p>';
	}

	ob_start();
	echo '<div class="card-grid">';
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/business', 'card' );
	}
	echo '</div>';
	wp_reset_postdata();

	return ob_get_clean();
}

/**
 * [urban_stats] — renders the district stats bar from Customizer values
 */
function urban_cms_sc_stats( array $atts ): string {
	ob_start();
	echo '<div class="district-stats"><div class="district-stats__grid">';
	for ( $i = 1; $i <= 4; $i++ ) {
		$number = get_theme_mod( "stat_{$i}_number" );
		$label  = get_theme_mod( "stat_{$i}_label" );
		if ( ! $number ) {
			continue;
		}
		echo '<div class="stat-item">'
			. '<span class="stat-item__number">' . esc_html( $number ) . '</span>'
			. '<span class="stat-item__label">' . esc_html( $label ) . '</span>'
			. '</div>';
	}
	echo '</div></div>';
	return ob_get_clean();
}

/**
 * [urban_team count="8"]
 */
function urban_cms_sc_team( array $atts ): string {
	$atts = shortcode_atts( array( 'count' => -1 ), $atts, 'urban_team' );

	$query = new WP_Query(
		array(
			'post_type'      => 'urban_team',
			'post_status'    => 'publish',
			'posts_per_page' => absint( $atts['count'] ) ? absint( $atts['count'] ) : -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		)
	);

	if ( ! $query->have_posts() ) {
		return '<p>' . esc_html__( 'No team members found.', 'urban-cms' ) . '</p>';
	}

	ob_start();
	echo '<div class="team-grid">';
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/team', 'member' );
	}
	echo '</div>';
	wp_reset_postdata();

	return ob_get_clean();
}
