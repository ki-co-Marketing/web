<?php
/**
 * Single post template
 *
 * @package UrbanCMS
 */

get_header();

while ( have_posts() ) : the_post();
    $cats = get_the_category();
?>

<div class="archive-header">
    <div class="container">
        <?php if ( $cats ) : ?>
            <div style="margin-bottom:var(--space-3)">
                <?php foreach ( array_slice( $cats, 0, 2 ) as $cat ) : ?>
                    <a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="badge badge--accent"><?php echo esc_html( $cat->name ); ?></a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <p style="color:rgba(255,255,255,0.75);font-size:var(--text-sm);margin-top:var(--space-3)">
            <?php echo esc_html( get_the_date() ); ?> &middot; <?php the_author(); ?>
        </p>
    </div>
</div>

<div class="container" style="padding-top:var(--space-12);padding-bottom:var(--space-16)">
    <div class="content-with-sidebar">
        <article>
            <?php if ( has_post_thumbnail() ) : ?>
                <div style="border-radius:var(--radius-lg);overflow:hidden;margin-bottom:var(--space-8)">
                    <?php the_post_thumbnail( 'urban-hero', [ 'loading' => 'eager' ] ); ?>
                </div>
            <?php endif; ?>

            <div class="entry-content"><?php the_content(); ?></div>

            <div style="margin-top:var(--space-8);padding-top:var(--space-6);border-top:1px solid var(--color-border);display:flex;justify-content:space-between;align-items:center">
                <?php previous_post_link( '<div>%link</div>', '&larr; %title' ); ?>
                <?php next_post_link( '<div>%link</div>', '%title &rarr;' ); ?>
            </div>

            <?php if ( comments_open() || get_comments_number() ) : ?>
                <div style="margin-top:var(--space-12)"><?php comments_template(); ?></div>
            <?php endif; ?>
        </article>

        <aside class="sidebar">
            <?php if ( is_active_sidebar( 'sidebar-main' ) ) : ?>
                <?php dynamic_sidebar( 'sidebar-main' ); ?>
            <?php endif; ?>
        </aside>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer();
