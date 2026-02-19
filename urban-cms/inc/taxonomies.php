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
    register_taxonomy( 'event_category', [ 'urban_event' ], [
        'labels'            => urban_cms_taxonomy_labels( 'Event Category', 'Event Categories' ),
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'rewrite'           => [ 'slug' => 'event-category' ],
        'show_admin_column' => true,
    ] );

    // Business Category
    register_taxonomy( 'business_category', [ 'urban_business' ], [
        'labels'            => urban_cms_taxonomy_labels( 'Business Category', 'Business Categories' ),
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'rewrite'           => [ 'slug' => 'business-type' ],
        'show_admin_column' => true,
    ] );

    // District / Neighborhood (shared across CPTs)
    register_taxonomy( 'district_neighborhood', [ 'urban_business', 'urban_initiative', 'urban_event' ], [
        'labels'            => urban_cms_taxonomy_labels( 'Neighborhood', 'Neighborhoods' ),
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'rewrite'           => [ 'slug' => 'neighborhood' ],
        'show_admin_column' => true,
    ] );

    // Initiative Type
    register_taxonomy( 'initiative_type', [ 'urban_initiative' ], [
        'labels'            => urban_cms_taxonomy_labels( 'Initiative Type', 'Initiative Types' ),
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'rewrite'           => [ 'slug' => 'initiative-type' ],
        'show_admin_column' => true,
    ] );
}

/**
 * Generate standard taxonomy labels.
 */
function urban_cms_taxonomy_labels( string $singular, string $plural ): array {
    return [
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
    ];
}
