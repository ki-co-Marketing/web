<?php
/**
 * Page template
 *
 * @package UrbanCMS
 */

get_header();

while ( have_posts() ) : the_post();
?>

<div class="archive-header">
    <div class="container">
        <h1><?php the_title(); ?></h1>
    </div>
</div>

<div class="container" style="padding-top:var(--space-12);padding-bottom:var(--space-16)">
    <div class="entry-content">
        <?php the_content(); ?>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer();
