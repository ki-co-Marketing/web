<?php
/**
 * Theme Helper Functions
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================
   Event Helpers
   ========================================================= */

/**
 * Get formatted event date range string.
 */
function urban_cms_event_date( int $post_id ): string {
    $start = get_post_meta( $post_id, '_event_start_date', true );
    $end   = get_post_meta( $post_id, '_event_end_date',   true );

    if ( ! $start ) return '';

    $start_dt = new DateTime( $start );

    if ( $end && $end !== $start ) {
        $end_dt = new DateTime( $end );
        if ( $start_dt->format( 'Y-m' ) === $end_dt->format( 'Y-m' ) ) {
            return $start_dt->format( 'F j' ) . '–' . $end_dt->format( 'j, Y' );
        }
        return $start_dt->format( 'M j' ) . '–' . $end_dt->format( 'M j, Y' );
    }

    return $start_dt->format( 'F j, Y' );
}

/**
 * Get formatted event time range string.
 */
function urban_cms_event_time( int $post_id ): string {
    $start = get_post_meta( $post_id, '_event_start_time', true );
    $end   = get_post_meta( $post_id, '_event_end_time',   true );

    if ( ! $start ) return '';

    $fmt_start = date( 'g:i a', strtotime( $start ) );
    if ( $end ) {
        $fmt_end = date( 'g:i a', strtotime( $end ) );
        return "$fmt_start – $fmt_end";
    }

    return $fmt_start;
}

/**
 * Check whether the current page/template uses a district map.
 */
function urban_cms_has_map(): bool {
    return is_front_page()
        || is_page_template( 'templates/page-directory.php' )
        || is_post_type_archive( 'urban_business' );
}

/**
 * Build map marker data for all published businesses.
 */
function urban_cms_map_data(): array {
    $businesses = get_posts( [
        'post_type'      => 'urban_business',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [
            'relation' => 'AND',
            [ 'key' => '_business_lat', 'compare' => 'EXISTS' ],
            [ 'key' => '_business_lng', 'compare' => 'EXISTS' ],
            [ 'key' => '_business_lat', 'value' => '', 'compare' => '!=' ],
        ],
    ] );

    $markers = [];
    foreach ( $businesses as $b ) {
        $lat = (float) get_post_meta( $b->ID, '_business_lat', true );
        $lng = (float) get_post_meta( $b->ID, '_business_lng', true );
        if ( ! $lat || ! $lng ) continue;

        $markers[] = [
            'id'       => $b->ID,
            'name'     => $b->post_title,
            'url'      => get_permalink( $b->ID ),
            'address'  => get_post_meta( $b->ID, '_business_address', true ),
            'phone'    => get_post_meta( $b->ID, '_business_phone', true ),
            'lat'      => $lat,
            'lng'      => $lng,
            'logo'     => get_the_post_thumbnail_url( $b->ID, 'urban-thumb' ) ?: '',
            'category' => implode( ', ', wp_get_post_terms( $b->ID, 'business_category', [ 'fields' => 'names' ] ) ),
        ];
    }

    return [
        'markers'    => $markers,
        'tileUrl'    => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'tileAttrib' => '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        'center'     => [
            'lat' => (float) get_theme_mod( 'district_map_lat', 47.6062 ),
            'lng' => (float) get_theme_mod( 'district_map_lng', -122.3321 ),
        ],
        'zoom'       => (int) get_theme_mod( 'district_map_zoom', 15 ),
    ];
}

/* =========================================================
   Initiative Helpers
   ========================================================= */

function urban_cms_initiative_status_badge( int $post_id ): string {
    $status = get_post_meta( $post_id, '_initiative_status', true ) ?: 'active';
    $map    = [
        'planning'  => [ 'Planning',   'badge--primary' ],
        'active'    => [ 'Active',     'badge--success' ],
        'on-hold'   => [ 'On Hold',    'badge' ],
        'completed' => [ 'Completed',  'badge--accent' ],
    ];
    [ $label, $class ] = $map[ $status ] ?? [ ucfirst( $status ), 'badge' ];
    return '<span class="badge ' . esc_attr( $class ) . '">' . esc_html( $label ) . '</span>';
}

/* =========================================================
   Layout / Pagination Helpers
   ========================================================= */

/**
 * Render theme pagination using paginate_links().
 */
function urban_cms_pagination( WP_Query $query = null ): void {
    global $wp_query;
    $q = $query ?? $wp_query;

    if ( $q->max_num_pages < 2 ) return;

    $links = paginate_links( [
        'total'     => $q->max_num_pages,
        'current'   => max( 1, get_query_var( 'paged' ) ),
        'prev_text' => '&larr;',
        'next_text' => '&rarr;',
        'type'      => 'array',
    ] );

    if ( ! $links ) return;

    echo '<nav class="pagination" aria-label="' . esc_attr__( 'Page navigation', 'urban-cms' ) . '">';
    echo '<ul class="pagination__list">';
    foreach ( $links as $link ) {
        echo '<li class="pagination__item">' . $link . '</li>';
    }
    echo '</ul></nav>';
}

/**
 * Render a section header with optional "View All" link.
 */
function urban_cms_section_header( string $title, string $subtitle = '', string $view_all_url = '', string $view_all_text = '' ): void {
    echo '<div class="section__header-row">';
    echo '<div>';
    echo '<h2>' . esc_html( $title ) . '</h2>';
    if ( $subtitle ) echo '<p class="color-muted">' . esc_html( $subtitle ) . '</p>';
    echo '</div>';
    if ( $view_all_url ) {
        echo '<a href="' . esc_url( $view_all_url ) . '" class="btn btn--outline btn--sm">'
           . esc_html( $view_all_text ?: __( 'View All', 'urban-cms' ) )
           . '</a>';
    }
    echo '</div>';
}

/**
 * Safely render a post thumbnail with fallback placeholder.
 */
function urban_cms_thumbnail( int $post_id, string $size = 'urban-card', string $class = '' ): void {
    if ( has_post_thumbnail( $post_id ) ) {
        echo get_the_post_thumbnail( $post_id, $size, [
            'class'   => esc_attr( $class ),
            'loading' => 'lazy',
        ] );
    } else {
        echo '<div class="thumbnail-placeholder ' . esc_attr( $class ) . '" aria-hidden="true"></div>';
    }
}
