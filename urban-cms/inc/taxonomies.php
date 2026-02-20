<?php
/**
 * Custom Taxonomies for Urban CMS Platform
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'urban_cms_register_taxonomies' );

function urban_cms_register_taxonomies() {

	// Event Category
	register_taxonomy(
		'event_category',
		array( 'urban_event' ),
		array(
			'labels'            => urban_cms_taxonomy_labels( 'Event Category', 'Event Categories' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'event-category' ),
			'show_admin_column' => true,
		)
	);

	// Business Category
	register_taxonomy(
		'business_category',
		array( 'urban_business' ),
		array(
			'labels'            => urban_cms_taxonomy_labels( 'Business Category', 'Business Categories' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'business-type' ),
			'show_admin_column' => true,
		)
	);

	// District / Neighborhood (shared across CPTs)
	register_taxonomy(
		'district_neighborhood',
		array( 'urban_business', 'urban_initiative', 'urban_event' ),
		array(
			'labels'            => urban_cms_taxonomy_labels( 'Neighborhood', 'Neighborhoods' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'neighborhood' ),
			'show_admin_column' => true,
		)
	);

	// Initiative Type
	register_taxonomy(
		'initiative_type',
		array( 'urban_initiative' ),
		array(
			'labels'            => urban_cms_taxonomy_labels( 'Initiative Type', 'Initiative Types' ),
			'hierarchical'      => true,
			'public'            => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'initiative-type' ),
			'show_admin_column' => true,
		)
	);
}

/**
 * Generate standard taxonomy labels.
 */
function urban_cms_taxonomy_labels( string $singular, string $plural ): array {
	return array(
		'name'              => $plural,
		'singular_name'     => $singular,
		'search_items'      => "Search {$plural}",
		'all_items'         => "All {$plural}",
		'parent_item'       => "Parent {$singular}",
		'parent_item_colon' => "Parent {$singular}:",
		'edit_item'         => "Edit {$singular}",
		'update_item'       => "Update {$singular}",
		'add_new_item'      => "Add New {$singular}",
		'new_item_name'     => "New {$singular} Name",
		'menu_name'         => $plural,
	);
}
