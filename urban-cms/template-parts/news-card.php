<?php
/**
 * Template part: News / post card
 *
 * Pass [ 'featured' => true ] via get_template_part() $args for large variant.
 *
 * @package UrbanCMS
 */

$featured = $args['featured'] ?? false;
$cats     = get_the_category();
?>

<article class="card news-card <?php echo $featured ? 'news-card--featured' : ''; ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="card__thumbnail" tabindex="-1" aria-hidden="true">
			<?php
			the_post_thumbnail(
				$featured ? 'urban-hero' : 'urban-card',
				array(
					'loading' => 'lazy',
					'alt'     => '',
				)
			);
			?>
		</a>
	<?php endif; ?>

	<div class="card__body">
		<div class="card__meta">
			<?php foreach ( array_slice( $cats, 0, 2 ) as $cat ) : ?>
				<a href="<?php echo esc_url( get_category_link( $cat ) ); ?>" class="badge badge--primary">
					<?php echo esc_html( $cat->name ); ?>
				</a>
			<?php endforeach; ?>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" style="margin-left:auto">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
		</div>

		<h3 class="card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<p class="card__excerpt"><?php the_excerpt(); ?></p>
	</div>

	<div class="card__footer">
		<span style="font-size:var(--text-sm);color:var(--color-text-muted)"><?php the_author(); ?></span>
		<a href="<?php the_permalink(); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'Read More', 'urban-cms' ); ?></a>
	</div>
</article>
