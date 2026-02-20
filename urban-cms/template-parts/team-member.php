<?php
/**
 * Template part: Team member card
 *
 * @package UrbanCMS
 */

$post_id  = get_the_ID();
$title    = get_post_meta( $post_id, '_team_title', true );
$email    = get_post_meta( $post_id, '_team_email', true );
$linkedin = get_post_meta( $post_id, '_team_linkedin', true );
?>

<div class="team-member" id="team-<?php echo (int) $post_id; ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<?php
		the_post_thumbnail(
			'urban-team',
			array(
				'class'   => 'team-member__photo',
				'loading' => 'lazy',
				'alt'     => get_the_title(),
			)
		);
		?>
	<?php else : ?>
		<div class="team-member__photo" style="background:var(--color-surface-alt);display:flex;align-items:center;justify-content:center;color:var(--color-text-muted);font-size:var(--text-3xl)">
			<span aria-hidden="true">&#128100;</span>
		</div>
	<?php endif; ?>

	<h3 class="team-member__name"><?php the_title(); ?></h3>
	<?php if ( $title ) : ?>
		<p class="team-member__title"><?php echo esc_html( $title ); ?></p>
	<?php endif; ?>

	<div style="margin-top:var(--space-2);display:flex;gap:var(--space-2);justify-content:center">
		<?php if ( $email ) : ?>
			<a href="mailto:<?php echo esc_attr( $email ); ?>" class="social-icon" aria-label="<?php echo esc_attr( get_the_title() . ' ' . __( 'email', 'urban-cms' ) ); ?>">
				<i class="fa-regular fa-envelope" aria-hidden="true"></i>
			</a>
		<?php endif; ?>
		<?php if ( $linkedin ) : ?>
			<a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" aria-label="<?php echo esc_attr( get_the_title() . ' LinkedIn' ); ?>">
				<i class="fa-brands fa-linkedin-in" aria-hidden="true"></i>
			</a>
		<?php endif; ?>
	</div>
</div>
