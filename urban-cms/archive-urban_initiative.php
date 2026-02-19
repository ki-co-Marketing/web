<?php
/**
 * Initiatives / Projects Archive — with live AJAX filtering
 *
 * @package UrbanCMS
 */

get_header();

$types = get_terms( [ 'taxonomy' => 'initiative_type', 'hide_empty' => true ] );
?>

<div class="archive-header">
    <div class="container">
        <h1><?php esc_html_e( 'Initiatives &amp; Projects', 'urban-cms' ); ?></h1>
        <p><?php esc_html_e( 'Active programs, capital projects, and district improvements', 'urban-cms' ); ?></p>
    </div>
</div>

<div class="container" style="padding-bottom:var(--space-16)">

    <!-- =====================================================
         Filter Bar
         ===================================================== -->
    <div class="initiative-filter-bar" role="search" aria-label="<?php esc_attr_e( 'Filter initiatives', 'urban-cms' ); ?>">

        <!-- Keyword search -->
        <div class="initiative-filter-bar__search">
            <label for="initiative-search" class="sr-only"><?php esc_html_e( 'Search initiatives', 'urban-cms' ); ?></label>
            <span class="initiative-filter-bar__search-icon" aria-hidden="true">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text"
                   id="initiative-search"
                   class="form-input initiative-filter-bar__input"
                   placeholder="<?php esc_attr_e( 'Search initiatives…', 'urban-cms' ); ?>"
                   aria-controls="initiatives-results"
                   autocomplete="off">
        </div>

        <!-- Status tabs -->
        <div class="initiative-filter-bar__status" role="group" aria-label="<?php esc_attr_e( 'Filter by status', 'urban-cms' ); ?>">
            <?php
            $statuses = [
                ''          => __( 'All',       'urban-cms' ),
                'active'    => __( 'Active',    'urban-cms' ),
                'planning'  => __( 'Planning',  'urban-cms' ),
                'completed' => __( 'Completed', 'urban-cms' ),
                'on-hold'   => __( 'On Hold',   'urban-cms' ),
            ];
            foreach ( $statuses as $val => $label ) : ?>
                <button type="button"
                        class="filter-chip <?php echo $val === '' ? 'filter-chip--active' : ''; ?>"
                        data-filter="status"
                        data-value="<?php echo esc_attr( $val ); ?>"
                        aria-pressed="<?php echo $val === '' ? 'true' : 'false'; ?>">
                    <?php echo esc_html( $label ); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Type dropdown -->
        <?php if ( $types && ! is_wp_error( $types ) ) : ?>
        <div class="initiative-filter-bar__type">
            <label for="initiative-type-select" class="sr-only"><?php esc_html_e( 'Filter by type', 'urban-cms' ); ?></label>
            <select id="initiative-type-select"
                    class="form-input"
                    data-filter="type"
                    aria-controls="initiatives-results"
                    style="min-width:180px">
                <option value=""><?php esc_html_e( 'All Types', 'urban-cms' ); ?></option>
                <?php foreach ( $types as $type ) : ?>
                    <option value="<?php echo esc_attr( $type->slug ); ?>"><?php echo esc_html( $type->name ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <!-- Results count -->
        <span class="initiative-filter-bar__count" id="initiatives-count" aria-live="polite" aria-atomic="true">
            <?php
            global $wp_query;
            printf(
                esc_html( _n( '%d initiative', '%d initiatives', $wp_query->found_posts, 'urban-cms' ) ),
                (int) $wp_query->found_posts
            );
            ?>
        </span>

        <!-- Clear filters -->
        <button type="button" id="initiatives-clear" class="btn btn--outline btn--sm" hidden>
            <?php esc_html_e( 'Clear Filters', 'urban-cms' ); ?>
        </button>
    </div>

    <!-- =====================================================
         Results area
         ===================================================== -->
    <div id="initiatives-results"
         role="region"
         aria-label="<?php esc_attr_e( 'Initiative results', 'urban-cms' ); ?>"
         aria-live="polite"
         aria-busy="false">

        <div class="initiatives-loading" id="initiatives-loading" hidden aria-hidden="true">
            <span class="spinner spinner--dark" style="width:32px;height:32px;border-width:3px"></span>
        </div>

        <?php if ( have_posts() ) : ?>
        <div class="card-grid" id="initiatives-grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/initiative', 'card' ); ?>
            <?php endwhile; ?>
        </div>
        <?php urban_cms_pagination(); ?>
        <?php else : ?>
        <div class="no-results" style="text-align:center;padding:var(--space-16) 0">
            <h2><?php esc_html_e( 'No initiatives found.', 'urban-cms' ); ?></h2>
        </div>
        <?php endif; ?>

    </div><!-- #initiatives-results -->

</div>

<?php get_footer();
