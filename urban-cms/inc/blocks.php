<?php
/**
 * Gutenberg Block Registration — Urban CMS Platform
 *
 * All blocks are server-side rendered (dynamic blocks) so content always
 * reflects the latest live data — no stale serialized HTML in post_content.
 *
 * Blocks registered:
 *   urban-cms/district-stats       — Customizable stats bar
 *   urban-cms/events-list          — Upcoming events list
 *   urban-cms/business-directory   — Business cards grid
 *   urban-cms/initiative-progress  — Single initiative highlight
 *   urban-cms/team-grid            — Team member photo grid
 *   urban-cms/newsletter           — Newsletter signup form
 *   urban-cms/hero-banner          — Full-width hero section
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================
   Register block category
   ========================================================= */
add_filter( 'block_categories_all', function ( array $cats ): array {
    array_unshift( $cats, [
        'slug'  => 'urban-cms',
        'title' => __( 'Urban CMS', 'urban-cms' ),
        'icon'  => 'building',
    ] );
    return $cats;
} );

/* =========================================================
   Enqueue editor script
   ========================================================= */
add_action( 'enqueue_block_editor_assets', function (): void {
    wp_register_script(
        'urban-cms-blocks-editor',
        URBAN_CMS_URI . '/assets/js/blocks-editor.js',
        [
            'wp-blocks', 'wp-element', 'wp-block-editor',
            'wp-components', 'wp-data', 'wp-api-fetch',
            'wp-i18n', 'wp-server-side-render',
        ],
        URBAN_CMS_VERSION,
        true
    );

    // Pass taxonomy/CPT data to the editor
    wp_localize_script( 'urban-cms-blocks-editor', 'urbanCMSBlocks', [
        'eventCategories'    => urban_cms_terms_for_select( 'event_category' ),
        'businessCategories' => urban_cms_terms_for_select( 'business_category' ),
        'initiativeTypes'    => urban_cms_terms_for_select( 'initiative_type' ),
        'initiatives'        => urban_cms_posts_for_select( 'urban_initiative' ),
        'restUrl'            => esc_url_raw( rest_url() ),
        'nonce'              => wp_create_nonce( 'wp_rest' ),
    ] );
} );

/* =========================================================
   Register all blocks
   ========================================================= */
add_action( 'init', 'urban_cms_register_blocks' );

function urban_cms_register_blocks(): void {
    $blocks = [
        'district-stats'      => 'urban_cms_render_district_stats',
        'events-list'         => 'urban_cms_render_events_list',
        'business-directory'  => 'urban_cms_render_business_directory',
        'initiative-progress' => 'urban_cms_render_initiative_progress',
        'team-grid'           => 'urban_cms_render_team_grid',
        'newsletter'          => 'urban_cms_render_newsletter',
        'hero-banner'         => 'urban_cms_render_hero_banner',
    ];

    foreach ( $blocks as $slug => $callback ) {
        register_block_type(
            URBAN_CMS_DIR . '/blocks/' . $slug . '/block.json',
            [ 'render_callback' => $callback ]
        );
    }
}

/* =========================================================
   Helpers
   ========================================================= */

/** Return [['value' => slug, 'label' => name], ...] for a taxonomy. */
function urban_cms_terms_for_select( string $taxonomy ): array {
    $terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
    if ( is_wp_error( $terms ) ) return [];
    return array_map( fn( $t ) => [ 'value' => $t->slug, 'label' => $t->name ], $terms );
}

/** Return [['value' => ID, 'label' => title], ...] for a CPT. */
function urban_cms_posts_for_select( string $post_type ): array {
    $posts = get_posts( [ 'post_type' => $post_type, 'post_status' => 'publish', 'numberposts' => 100, 'orderby' => 'title', 'order' => 'ASC' ] );
    return array_map( fn( $p ) => [ 'value' => $p->ID, 'label' => $p->post_title ], $posts );
}

/* =========================================================
   Render Callbacks
   ========================================================= */

/* --- District Stats --- */
function urban_cms_render_district_stats( array $attrs ): string {
    $use_custom = ! empty( $attrs['useCustom'] );

    $stats = [];
    for ( $i = 1; $i <= 4; $i++ ) {
        $number = $use_custom
            ? ( $attrs[ 'stat' . $i . 'Number' ] ?? '' )
            : get_theme_mod( "stat_{$i}_number", '' );
        $label = $use_custom
            ? ( $attrs[ 'stat' . $i . 'Label' ] ?? '' )
            : get_theme_mod( "stat_{$i}_label", '' );
        if ( $number ) {
            $stats[] = [ 'number' => $number, 'label' => $label ];
        }
    }

    if ( ! $stats ) return '';

    $html  = '<div class="district-stats"><div class="container"><div class="district-stats__grid">';
    foreach ( $stats as $stat ) {
        $html .= '<div class="stat-item">'
               . '<span class="stat-item__number">' . esc_html( $stat['number'] ) . '</span>'
               . '<span class="stat-item__label">'  . esc_html( $stat['label']  ) . '</span>'
               . '</div>';
    }
    $html .= '</div></div></div>';
    return $html;
}

/* --- Events List --- */
function urban_cms_render_events_list( array $attrs ): string {
    $count    = max( 1, (int) ( $attrs['count'] ?? 4 ) );
    $category = sanitize_text_field( $attrs['category'] ?? '' );
    $show_rsvp    = (bool) ( $attrs['showRsvpBtn'] ?? true );
    $show_view_all = (bool) ( $attrs['showViewAll'] ?? true );

    $args = [
        'post_type'      => 'urban_event',
        'post_status'    => 'publish',
        'posts_per_page' => $count,
        'meta_key'       => '_event_start_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => [ [
            'key'     => '_event_start_date',
            'value'   => date( 'Y-m-d' ),
            'compare' => '>=',
            'type'    => 'DATE',
        ] ],
    ];

    if ( $category ) {
        $args['tax_query'] = [ [ 'taxonomy' => 'event_category', 'field' => 'slug', 'terms' => $category ] ];
    }

    $query = new WP_Query( $args );
    if ( ! $query->have_posts() ) {
        return '<p class="urban-block-empty">' . esc_html__( 'No upcoming events found.', 'urban-cms' ) . '</p>';
    }

    ob_start();
    echo '<div class="events-list">';
    while ( $query->have_posts() ) {
        $query->the_post();
        get_template_part( 'template-parts/event', 'item' );
    }
    echo '</div>';
    wp_reset_postdata();

    if ( $show_view_all ) {
        echo '<p style="margin-top:var(--space-6)">'
           . '<a href="' . esc_url( get_post_type_archive_link( 'urban_event' ) ) . '" class="btn btn--outline">'
           . esc_html__( 'View All Events', 'urban-cms' )
           . '</a></p>';
    }

    return ob_get_clean();
}

/* --- Business Directory --- */
function urban_cms_render_business_directory( array $attrs ): string {
    $count    = max( 1, (int) ( $attrs['count'] ?? 6 ) );
    $category = sanitize_text_field( $attrs['category'] ?? '' );
    $featured = (bool) ( $attrs['featuredOnly'] ?? false );
    $show_all = (bool) ( $attrs['showViewAll'] ?? true );

    $args = [
        'post_type'      => 'urban_business',
        'post_status'    => 'publish',
        'posts_per_page' => $count,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ];

    if ( $featured ) {
        $args['meta_query'] = [ [ 'key' => '_business_featured', 'value' => '1', 'compare' => '=' ] ];
    }
    if ( $category ) {
        $args['tax_query'] = [ [ 'taxonomy' => 'business_category', 'field' => 'slug', 'terms' => $category ] ];
    }

    $query = new WP_Query( $args );
    if ( ! $query->have_posts() ) {
        return '<p class="urban-block-empty">' . esc_html__( 'No businesses found.', 'urban-cms' ) . '</p>';
    }

    ob_start();
    $layout = sanitize_text_field( $attrs['layout'] ?? 'grid' );
    echo '<div class="' . ( $layout === 'list' ? 'businesses-list' : 'card-grid card-grid--tight' ) . '">';
    while ( $query->have_posts() ) {
        $query->the_post();
        get_template_part( 'template-parts/business', 'card' );
    }
    echo '</div>';
    wp_reset_postdata();

    if ( $show_all ) {
        echo '<p style="margin-top:var(--space-6)">'
           . '<a href="' . esc_url( get_post_type_archive_link( 'urban_business' ) ) . '" class="btn btn--outline">'
           . esc_html__( 'View Full Directory', 'urban-cms' )
           . '</a></p>';
    }

    return ob_get_clean();
}

/* --- Initiative Progress --- */
function urban_cms_render_initiative_progress( array $attrs ): string {
    $post_id = absint( $attrs['postId'] ?? 0 );
    $show_bar = (bool) ( $attrs['showProgressBar'] ?? true );
    $show_det = (bool) ( $attrs['showDetails']     ?? true );

    if ( ! $post_id ) {
        return '<p class="urban-block-empty">' . esc_html__( 'Select an initiative in the block settings.', 'urban-cms' ) . '</p>';
    }

    $post = get_post( $post_id );
    if ( ! $post || $post->post_status !== 'publish' ) {
        return '<p class="urban-block-empty">' . esc_html__( 'Initiative not found or not published.', 'urban-cms' ) . '</p>';
    }

    $progress = (int) get_post_meta( $post_id, '_initiative_progress', true );
    $status   = get_post_meta( $post_id, '_initiative_status', true );
    $lead     = get_post_meta( $post_id, '_initiative_lead',   true );
    $end      = get_post_meta( $post_id, '_initiative_end',    true );
    $budget   = get_post_meta( $post_id, '_initiative_budget', true );

    ob_start();
    echo '<div class="card initiative-card">';

    if ( has_post_thumbnail( $post_id ) ) {
        echo '<a href="' . esc_url( get_permalink( $post_id ) ) . '" class="card__thumbnail" tabindex="-1" aria-hidden="true">'
           . get_the_post_thumbnail( $post_id, 'urban-card', [ 'loading' => 'lazy', 'alt' => '' ] )
           . '</a>';
    }

    echo '<div class="initiative-card__status">' . urban_cms_initiative_status_badge( $post_id ) . '</div>';
    echo '<div class="initiative-card__body">';
    echo '<h3 class="initiative-card__title"><a href="' . esc_url( get_permalink( $post_id ) ) . '">' . esc_html( $post->post_title ) . '</a></h3>';

    if ( $post->post_excerpt ) {
        echo '<p class="card__excerpt">' . esc_html( $post->post_excerpt ) . '</p>';
    }

    if ( $show_bar && $progress > 0 ) {
        echo '<div class="progress-bar" style="margin-top:var(--space-4)">'
           . '<div class="progress-bar__fill" style="width:' . (int) $progress . '%"'
           . ' role="progressbar" aria-valuenow="' . (int) $progress . '" aria-valuemin="0" aria-valuemax="100"></div>'
           . '</div>';
        echo '<p style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:var(--space-1)">'
           . (int) $progress . '% ' . esc_html__( 'complete', 'urban-cms' ) . '</p>';
    }

    if ( $show_det ) {
        echo '<dl style="font-size:var(--text-sm);margin-top:var(--space-4);display:flex;flex-direction:column;gap:var(--space-2)">';
        if ( $lead )   echo '<div><dt style="font-weight:700;color:var(--color-primary)">' . esc_html__( 'Lead', 'urban-cms' ) . '</dt><dd>' . esc_html( $lead ) . '</dd></div>';
        if ( $budget ) echo '<div><dt style="font-weight:700;color:var(--color-primary)">' . esc_html__( 'Budget', 'urban-cms' ) . '</dt><dd>' . esc_html( $budget ) . '</dd></div>';
        if ( $end )    echo '<div><dt style="font-weight:700;color:var(--color-primary)">' . esc_html__( 'Target', 'urban-cms' ) . '</dt><dd>' . esc_html( date( 'F Y', strtotime( $end ) ) ) . '</dd></div>';
        echo '</dl>';
    }

    echo '</div></div>';
    return ob_get_clean();
}

/* --- Team Grid --- */
function urban_cms_render_team_grid( array $attrs ): string {
    $count   = (int) ( $attrs['count']   ?? -1 );
    $columns = max( 2, min( 5, (int) ( $attrs['columns'] ?? 4 ) ) );

    $query = new WP_Query( [
        'post_type'      => 'urban_team',
        'post_status'    => 'publish',
        'posts_per_page' => $count < 1 ? -1 : $count,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ] );

    if ( ! $query->have_posts() ) {
        return '<p class="urban-block-empty">' . esc_html__( 'No team members found.', 'urban-cms' ) . '</p>';
    }

    ob_start();
    echo '<div class="team-grid" style="grid-template-columns:repeat(' . (int) $columns . ',1fr)">';
    while ( $query->have_posts() ) {
        $query->the_post();
        get_template_part( 'template-parts/team', 'member' );
    }
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}

/* --- Newsletter Signup --- */
function urban_cms_render_newsletter( array $attrs ): string {
    $variant    = sanitize_text_field( $attrs['variant']    ?? 'card' );
    $list       = sanitize_text_field( $attrs['list']       ?? 'general' );
    $source     = sanitize_text_field( $attrs['source']     ?? 'block' );
    $heading    = sanitize_text_field( $attrs['heading']    ?? '' );
    $subheading = sanitize_text_field( $attrs['subheading'] ?? '' );

    // Temporarily override Customizer values if block provides them
    if ( $heading )    add_filter( 'theme_mod_newsletter_heading',    fn() => $heading );
    if ( $subheading ) add_filter( 'theme_mod_newsletter_subheading', fn() => $subheading );

    ob_start();
    get_template_part( 'template-parts/newsletter', 'signup', compact( 'variant', 'list', 'source' ) );
    $html = ob_get_clean();

    if ( $heading )    remove_all_filters( 'theme_mod_newsletter_heading' );
    if ( $subheading ) remove_all_filters( 'theme_mod_newsletter_subheading' );

    return $html;
}

/* --- Hero Banner --- */
function urban_cms_render_hero_banner( array $attrs ): string {
    $eyebrow    = sanitize_text_field( $attrs['eyebrow']    ?? '' );
    $heading    = wp_kses_post( $attrs['heading']    ?? 'Welcome to the District' );
    $subheading = wp_kses_post( $attrs['subheading'] ?? '' );
    $cta1_text  = sanitize_text_field( $attrs['cta1Text']   ?? '' );
    $cta1_url   = esc_url_raw( $attrs['cta1Url']            ?? '' );
    $cta2_text  = sanitize_text_field( $attrs['cta2Text']   ?? '' );
    $cta2_url   = esc_url_raw( $attrs['cta2Url']            ?? '' );
    $bg_color   = sanitize_hex_color( $attrs['bgColor']     ?? '#1a3a5c' ) ?: '#1a3a5c';
    $accent     = sanitize_hex_color( $attrs['accentColor'] ?? '#e8a020' ) ?: '#e8a020';
    $bg_img     = esc_url_raw( $attrs['bgImageUrl']         ?? '' );
    $min_h      = max( 200, (int) ( $attrs['minHeight']     ?? 480 ) );

    $style = 'background-color:' . $bg_color . ';min-height:' . $min_h . 'px;';

    ob_start();
    ?>
    <section class="district-hero" style="<?php echo esc_attr( $style ); ?>">
        <?php if ( $bg_img ) : ?>
            <div class="district-hero__bg" style="background-image:url(<?php echo esc_url( $bg_img ); ?>)" role="img" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="container">
            <div class="district-hero__content">
                <?php if ( $eyebrow ) : ?>
                    <span class="district-hero__eyebrow" style="background:<?php echo esc_attr( $accent ); ?>"><?php echo esc_html( $eyebrow ); ?></span>
                <?php endif; ?>
                <?php if ( $heading ) : ?>
                    <h2 style="color:#fff"><?php echo wp_kses_post( $heading ); ?></h2>
                <?php endif; ?>
                <?php if ( $subheading ) : ?>
                    <p><?php echo wp_kses_post( $subheading ); ?></p>
                <?php endif; ?>
                <?php if ( $cta1_text || $cta2_text ) : ?>
                <div class="hero-actions">
                    <?php if ( $cta1_text && $cta1_url ) : ?>
                        <a href="<?php echo esc_url( $cta1_url ); ?>"
                           class="btn btn--lg"
                           style="background:<?php echo esc_attr( $accent ); ?>;color:#1a3a5c;font-weight:700">
                            <?php echo esc_html( $cta1_text ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $cta2_text && $cta2_url ) : ?>
                        <a href="<?php echo esc_url( $cta2_url ); ?>" class="btn btn--outline-white btn--lg">
                            <?php echo esc_html( $cta2_text ); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
