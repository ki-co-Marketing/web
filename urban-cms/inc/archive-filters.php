<?php
/**
 * Archive Filters — AJAX handlers for initiatives and business directory
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================
   Initiatives filter AJAX
   ========================================================= */
add_action( 'wp_ajax_urban_filter_initiatives',        'urban_cms_ajax_filter_initiatives' );
add_action( 'wp_ajax_nopriv_urban_filter_initiatives', 'urban_cms_ajax_filter_initiatives' );

function urban_cms_ajax_filter_initiatives(): never {
    check_ajax_referer( 'urban_cms_nonce', 'nonce' );

    $status   = sanitize_text_field( $_POST['status']   ?? '' );
    $type     = sanitize_text_field( $_POST['type']     ?? '' );
    $search   = sanitize_text_field( $_POST['search']   ?? '' );
    $paged    = max( 1, absint( $_POST['paged']          ?? 1 ) );

    $args = [
        'post_type'      => 'urban_initiative',
        'post_status'    => 'publish',
        'posts_per_page' => 9,
        'paged'          => $paged,
        'orderby'        => 'menu_order date',
        'order'          => 'ASC',
    ];

    if ( $status ) {
        $args['meta_query'] = [ [
            'key'     => '_initiative_status',
            'value'   => $status,
            'compare' => '=',
        ] ];
    }

    if ( $type ) {
        $args['tax_query'] = [ [
            'taxonomy' => 'initiative_type',
            'field'    => 'slug',
            'terms'    => $type,
        ] ];
    }

    if ( $search ) {
        $args['s'] = $search;
    }

    $query = new WP_Query( $args );

    ob_start();

    if ( $query->have_posts() ) {
        echo '<div class="card-grid" id="initiatives-grid">';
        while ( $query->have_posts() ) {
            $query->the_post();
            get_template_part( 'template-parts/initiative', 'card' );
        }
        echo '</div>';
    } else {
        echo '<div class="no-results" style="text-align:center;padding:var(--space-16) 0">'
           . '<h2>' . esc_html__( 'No initiatives found.', 'urban-cms' ) . '</h2>'
           . '<p>' . esc_html__( 'Try adjusting your filters.', 'urban-cms' ) . '</p>'
           . '</div>';
    }

    wp_reset_postdata();

    wp_send_json_success( [
        'html'      => ob_get_clean(),
        'found'     => $query->found_posts,
        'max_pages' => $query->max_num_pages,
    ] );
}
