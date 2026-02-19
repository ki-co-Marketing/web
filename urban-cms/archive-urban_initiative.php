<?php
/**
 * Initiatives / Projects Archive
 *
 * @package UrbanCMS
 */

get_header();

$types      = get_terms( [ 'taxonomy' => 'initiative_type', 'hide_empty' => true ] );
$statuses   = [
    ''          => __( 'All Initiatives', 'urban-cms' ),
    'active'    => __( 'Active',          'urban-cms' ),
    'planning'  => __( 'Planning',        'urban-cms' ),
    'completed' => __( 'Completed',       'urban-cms' ),
    'on-hold'   => __( 'On Hold',         'urban-cms' ),
];
$current_status = sanitize_text_field( $_GET['status'] ?? '' );
?>

<div class="archive-header">
    <div class="container">
        <h1><?php esc_html_e( 'Initiatives &amp; Projects', 'urban-cms' ); ?></h1>
        <p><?php esc_html_e( 'Active programs, capital projects, and district improvements', 'urban-cms' ); ?></p>
    </div>
</div>

<div class="container">

    <!-- Status filter row -->
    <div style="display:flex;gap:var(--space-2);flex-wrap:wrap;margin-bottom:var(--space-8)">
        <?php foreach ( $statuses as $val => $label ) : ?>
            <a href="<?php echo esc_url( add_query_arg( 'status', $val, get_post_type_archive_link( 'urban_initiative' ) ) ); ?>"
               class="badge <?php echo $current_status === $val ? 'badge--primary' : ''; ?>"
               style="padding:var(--space-2) var(--space-4);font-size:var(--text-sm)">
                <?php echo esc_html( $label ); ?>
            </a>
        <?php endforeach; ?>
        <?php if ( $types && ! is_wp_error( $types ) ) : ?>
            <?php foreach ( $types as $type ) : ?>
                <a href="<?php echo esc_url( get_term_link( $type ) ); ?>"
                   class="badge <?php echo is_tax( 'initiative_type', $type ) ? 'badge--primary' : ''; ?>"
                   style="padding:var(--space-2) var(--space-4);font-size:var(--text-sm)">
                    <?php echo esc_html( $type->name ); ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ( have_posts() ) : ?>
        <div class="card-grid">
            <?php while ( have_posts() ) : the_post(); ?>
                <?php get_template_part( 'template-parts/initiative', 'card' ); ?>
            <?php endwhile; ?>
        </div>
        <?php urban_cms_pagination(); ?>
    <?php else : ?>
        <div class="no-results" style="text-align:center;padding:var(--space-16) 0">
            <h2><?php esc_html_e( 'No initiatives found.', 'urban-cms' ); ?></h2>
            <p><?php esc_html_e( 'Check back soon for updates on district projects and programs.', 'urban-cms' ); ?></p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer();
