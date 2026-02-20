<?php
/**
 * Urban CMS Platform — Theme Functions
 *
 * Sets up theme features, enqueues assets, registers post types,
 * taxonomies, menus, and widget areas for urban place management sites.
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

define( 'URBAN_CMS_VERSION', '1.0.0' );
define( 'URBAN_CMS_DIR', get_template_directory() );
define( 'URBAN_CMS_URI', get_template_directory_uri() );

/*
=========================================================
	Load modular includes
	========================================================= */
require_once URBAN_CMS_DIR . '/inc/post-types.php';
require_once URBAN_CMS_DIR . '/inc/taxonomies.php';
require_once URBAN_CMS_DIR . '/inc/customizer.php';
require_once URBAN_CMS_DIR . '/inc/helpers.php';
require_once URBAN_CMS_DIR . '/inc/social-icons.php';
require_once URBAN_CMS_DIR . '/inc/shortcodes.php';
require_once URBAN_CMS_DIR . '/inc/submission-forms.php';
require_once URBAN_CMS_DIR . '/inc/newsletter.php';
require_once URBAN_CMS_DIR . '/inc/archive-filters.php';
require_once URBAN_CMS_DIR . '/inc/blocks.php';

/*
=========================================================
	Theme Setup
	========================================================= */
add_action( 'after_setup_theme', 'urban_cms_setup' );

function urban_cms_setup() {
	// Translations
	load_theme_textdomain( 'urban-cms', URBAN_CMS_DIR . '/languages' );

	// Core WordPress features
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	// Custom image sizes
	add_image_size( 'urban-hero', 1920, 800, true );
	add_image_size( 'urban-card', 600, 400, true );
	add_image_size( 'urban-thumb', 300, 200, true );
	add_image_size( 'urban-business', 200, 200, true );
	add_image_size( 'urban-team', 400, 400, true );

	// Navigation menus
	register_nav_menus(
		array(
			'primary'  => __( 'Primary Navigation', 'urban-cms' ),
			'topbar'   => __( 'Topbar / Utility Navigation', 'urban-cms' ),
			'footer-1' => __( 'Footer Column 1', 'urban-cms' ),
			'footer-2' => __( 'Footer Column 2', 'urban-cms' ),
			'footer-3' => __( 'Footer Column 3', 'urban-cms' ),
		)
	);
}

/*
=========================================================
	Enqueue Scripts & Styles
	========================================================= */
add_action( 'wp_enqueue_scripts', 'urban_cms_assets' );

function urban_cms_assets() {
	// Google Fonts
	wp_enqueue_style(
		'urban-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);

	// Font Awesome (for social icons & UI icons)
	wp_enqueue_style(
		'font-awesome',
		'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
		array(),
		'6.5.0'
	);

	// Theme stylesheet
	wp_enqueue_style(
		'urban-cms',
		URBAN_CMS_URI . '/style.css',
		array( 'urban-fonts', 'font-awesome' ),
		URBAN_CMS_VERSION
	);

	// Forms, dashboard & newsletter styles (load everywhere — lightweight)
	wp_enqueue_style(
		'urban-cms-forms',
		URBAN_CMS_URI . '/assets/css/forms.css',
		array( 'urban-cms' ),
		URBAN_CMS_VERSION
	);

	// Leaflet.js for interactive maps
	if ( urban_cms_has_map() ) {
		wp_enqueue_style(
			'leaflet',
			'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
			array(),
			'1.9.4'
		);
		wp_enqueue_script(
			'leaflet',
			'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
			array(),
			'1.9.4',
			true
		);
		wp_enqueue_script(
			'urban-map',
			URBAN_CMS_URI . '/assets/js/district-map.js',
			array( 'leaflet' ),
			URBAN_CMS_VERSION,
			true
		);
		wp_localize_script( 'urban-map', 'urbanMapData', urban_cms_map_data() );
	}

	// Main theme JS
	wp_enqueue_script(
		'urban-cms',
		URBAN_CMS_URI . '/assets/js/main.js',
		array(),
		URBAN_CMS_VERSION,
		true
	);

	// Pass data to JS
	wp_localize_script(
		'urban-cms',
		'urbanCMS',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'urban_cms_nonce' ),
			'restUrl' => esc_url_raw( rest_url( 'urban-cms/v1/' ) ),
			'isRtl'   => is_rtl(),
		)
	);

	// Initiative archive filters
	if ( is_post_type_archive( 'urban_initiative' ) ) {
		wp_enqueue_script(
			'urban-initiative-filters',
			URBAN_CMS_URI . '/assets/js/initiative-filters.js',
			array( 'urban-cms' ),
			URBAN_CMS_VERSION,
			true
		);
	}

	// Comments script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/*
=========================================================
	Register Widget Areas (Sidebars)
	========================================================= */
add_action( 'widgets_init', 'urban_cms_widgets' );

function urban_cms_widgets() {
	$shared = array(
		'before_title'  => '<h3 class="widget__title">',
		'after_title'   => '</h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Main Sidebar', 'urban-cms' ),
				'id'          => 'sidebar-main',
				'description' => __( 'Appears alongside news and archive pages.', 'urban-cms' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Events Sidebar', 'urban-cms' ),
				'id'          => 'sidebar-events',
				'description' => __( 'Appears alongside the events calendar.', 'urban-cms' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Directory Sidebar', 'urban-cms' ),
				'id'          => 'sidebar-directory',
				'description' => __( 'Appears alongside the business directory.', 'urban-cms' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$shared,
			array(
				'name'        => __( 'Homepage — Announcements Bar', 'urban-cms' ),
				'id'          => 'homepage-announcements',
				'description' => __( 'Small widgets displayed in the homepage topbar.', 'urban-cms' ),
			)
		)
	);
}

/*
=========================================================
	Custom Body Classes
	========================================================= */
add_filter( 'body_class', 'urban_cms_body_classes' );

function urban_cms_body_classes( $classes ) {
	if ( is_singular() ) {
		$classes[] = 'is-singular';
	}
	if ( is_front_page() ) {
		$classes[] = 'is-front-page';
	}
	if ( is_post_type_archive( 'urban_event' ) || is_singular( 'urban_event' ) ) {
		$classes[] = 'is-events-section';
	}
	if ( is_post_type_archive( 'urban_business' ) || is_singular( 'urban_business' ) ) {
		$classes[] = 'is-directory-section';
	}
	return $classes;
}

/*
=========================================================
	Title Separator
	========================================================= */
add_filter( 'document_title_separator', fn() => '|' );

/*
=========================================================
	Excerpt Length & More
	========================================================= */
add_filter( 'excerpt_length', fn() => 25 );
add_filter( 'excerpt_more', fn() => '&hellip;' );

/*
=========================================================
	REST API: Business Directory endpoint
	========================================================= */
add_action( 'rest_api_init', 'urban_cms_register_rest_routes' );

function urban_cms_register_rest_routes() {
	register_rest_route(
		'urban-cms/v1',
		'/businesses',
		array(
			'methods'             => 'GET',
			'callback'            => 'urban_cms_rest_businesses',
			'permission_callback' => '__return_true',
			'args'                => array(
				'category' => array( 'sanitize_callback' => 'sanitize_text_field' ),
				'search'   => array( 'sanitize_callback' => 'sanitize_text_field' ),
				'per_page' => array(
					'sanitize_callback' => 'absint',
					'default'           => 20,
				),
			),
		)
	);

	register_rest_route(
		'urban-cms/v1',
		'/events',
		array(
			'methods'             => 'GET',
			'callback'            => 'urban_cms_rest_events',
			'permission_callback' => '__return_true',
			'args'                => array(
				'category'   => array( 'sanitize_callback' => 'sanitize_text_field' ),
				'start_date' => array( 'sanitize_callback' => 'sanitize_text_field' ),
				'per_page'   => array(
					'sanitize_callback' => 'absint',
					'default'           => 10,
				),
			),
		)
	);
}

function urban_cms_rest_businesses( WP_REST_Request $request ) {
	$args = array(
		'post_type'      => 'urban_business',
		'post_status'    => 'publish',
		'posts_per_page' => $request->get_param( 'per_page' ),
	);

	if ( $category = $request->get_param( 'category' ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'business_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	if ( $search = $request->get_param( 'search' ) ) {
		$args['s'] = $search;
	}

	$query      = new WP_Query( $args );
	$businesses = array();

	foreach ( $query->posts as $post ) {
		$businesses[] = array(
			'id'       => $post->ID,
			'name'     => $post->post_title,
			'excerpt'  => get_the_excerpt( $post ),
			'url'      => get_permalink( $post ),
			'address'  => get_post_meta( $post->ID, '_business_address', true ),
			'phone'    => get_post_meta( $post->ID, '_business_phone', true ),
			'website'  => get_post_meta( $post->ID, '_business_website', true ),
			'lat'      => (float) get_post_meta( $post->ID, '_business_lat', true ),
			'lng'      => (float) get_post_meta( $post->ID, '_business_lng', true ),
			'logo'     => get_the_post_thumbnail_url( $post->ID, 'urban-business' ),
			'category' => wp_get_post_terms( $post->ID, 'business_category', array( 'fields' => 'names' ) ),
		);
	}

	return rest_ensure_response( $businesses );
}

function urban_cms_rest_events( WP_REST_Request $request ) {
	$args = array(
		'post_type'      => 'urban_event',
		'post_status'    => 'publish',
		'posts_per_page' => $request->get_param( 'per_page' ),
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

	if ( $category = $request->get_param( 'category' ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'event_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$query  = new WP_Query( $args );
	$events = array();

	foreach ( $query->posts as $post ) {
		$events[] = array(
			'id'         => $post->ID,
			'title'      => $post->post_title,
			'url'        => get_permalink( $post ),
			'start_date' => get_post_meta( $post->ID, '_event_start_date', true ),
			'end_date'   => get_post_meta( $post->ID, '_event_end_date', true ),
			'start_time' => get_post_meta( $post->ID, '_event_start_time', true ),
			'location'   => get_post_meta( $post->ID, '_event_location', true ),
			'thumbnail'  => get_the_post_thumbnail_url( $post->ID, 'urban-card' ),
			'categories' => wp_get_post_terms( $post->ID, 'event_category', array( 'fields' => 'names' ) ),
		);
	}

	return rest_ensure_response( $events );
}

/*
=========================================================
	AJAX: Live business directory search
	========================================================= */
add_action( 'wp_ajax_urban_search_businesses', 'urban_cms_ajax_search_businesses' );
add_action( 'wp_ajax_nopriv_urban_search_businesses', 'urban_cms_ajax_search_businesses' );

function urban_cms_ajax_search_businesses() {
	check_ajax_referer( 'urban_cms_nonce', 'nonce' );

	$search   = sanitize_text_field( wp_unslash( $_POST['search'] ?? '' ) );
	$category = sanitize_text_field( wp_unslash( $_POST['category'] ?? '' ) );
	$paged    = absint( $_POST['paged'] ?? 1 );

	$args = array(
		'post_type'      => 'urban_business',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'paged'          => $paged,
	);

	if ( $search ) {
		$args['s'] = $search; }
	if ( $category ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'business_category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	$query = new WP_Query( $args );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/business', 'card' );
		}
	} else {
		echo '<p class="no-results">' . esc_html__( 'No businesses found.', 'urban-cms' ) . '</p>';
	}
	wp_reset_postdata();

	wp_send_json_success(
		array(
			'html'      => ob_get_clean(),
			'max_pages' => $query->max_num_pages,
			'found'     => $query->found_posts,
		)
	);
}
