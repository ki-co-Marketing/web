<?php
/**
 * Template part: Business directory card
 *
 * @package UrbanCMS
 */

$post_id  = get_the_ID();
$phone    = get_post_meta( $post_id, '_business_phone', true );
$website  = get_post_meta( $post_id, '_business_website', true );
$address  = get_post_meta( $post_id, '_business_address', true );
$hours    = get_post_meta( $post_id, '_business_hours', true );
$featured = get_post_meta( $post_id, '_business_featured', true );
$cats     = get_the_terms( $post_id, 'business_category' );
?>

<article class="card" id="business-<?php echo (int) $post_id; ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="card__thumbnail" tabindex="-1" aria-hidden="true">
			<?php
			the_post_thumbnail(
				'urban-card',
				array(
					'loading' => 'lazy',
					'alt'     => '',
				)
			);
			?>
		</a>
	<?php endif; ?>

	<div class="card__body">
		<?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
			<div class="card__meta">
				<?php foreach ( $cats as $cat ) : ?>
					<span class="badge"><?php echo esc_html( $cat->name ); ?></span>
				<?php endforeach; ?>
				<?php if ( $featured ) : ?>
					<span class="badge badge--accent"><?php esc_html_e( 'Featured', 'urban-cms' ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<h3 class="card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php if ( $address ) : ?>
			<p class="card__excerpt"><?php echo esc_html( $address ); ?></p>
		<?php elseif ( has_excerpt() ) : ?>
			<p class="card__excerpt"><?php the_excerpt(); ?></p>
		<?php endif; ?>

		<?php if ( $hours ) : ?>
			<p style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:var(--space-2)"><?php echo esc_html( $hours ); ?></p>
		<?php endif; ?>
	</div>

	<div class="card__footer">
		<?php if ( $phone ) : ?>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"
				style="font-size:var(--text-sm)">
				<?php echo esc_html( $phone ); ?>
			</a>
		<?php elseif ( $website ) : ?>
			<a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener noreferrer"
				style="font-size:var(--text-sm)">
				<?php esc_html_e( 'Website', 'urban-cms' ); ?>
			</a>
		<?php else : ?>
			<span></span>
		<?php endif; ?>
		<a href="<?php the_permalink(); ?>" class="btn btn--outline btn--sm"><?php esc_html_e( 'View', 'urban-cms' ); ?></a>
	</div>
</article>
