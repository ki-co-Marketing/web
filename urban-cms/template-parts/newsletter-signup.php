<?php
/**
 * Template part: Newsletter signup form
 * Include anywhere: get_template_part( 'template-parts/newsletter', 'signup' );
 *
 * Optional $args:
 *   'variant'   => 'inline' (default) | 'card' | 'full-width'
 *   'list'      => comma-separated list slugs (default: 'general')
 *   'source'    => tracking source label (default: current page slug)
 *
 * @package UrbanCMS
 */

$variant = $args['variant'] ?? 'inline';
$list    = sanitize_text_field( $args['list'] ?? 'general' );
$source  = sanitize_text_field( $args['source'] ?? ( is_page() ? get_post_field( 'post_name', get_the_ID() ) : 'widget' ) );
?>

<div class="newsletter-signup newsletter-signup--<?php echo esc_attr( $variant ); ?>"
	id="newsletter-signup-<?php echo esc_attr( $list ); ?>">

	<?php if ( $variant === 'full-width' ) : ?>
	<div class="newsletter-signup__inner">
		<div class="newsletter-signup__copy">
			<h2 class="newsletter-signup__title">
				<?php echo esc_html( get_theme_mod( 'newsletter_heading', __( 'Stay Connected', 'urban-cms' ) ) ); ?>
			</h2>
			<p class="newsletter-signup__description">
				<?php echo esc_html( get_theme_mod( 'newsletter_subheading', __( 'Get district news, events, and updates delivered to your inbox.', 'urban-cms' ) ) ); ?>
			</p>
		</div>
		<div class="newsletter-signup__form-wrap">
	<?php endif; ?>

	<form class="newsletter-form" data-list="<?php echo esc_attr( $list ); ?>" data-source="<?php echo esc_attr( $source ); ?>" novalidate>
		<!-- Honeypot -->
		<div style="position:absolute;left:-9999px" aria-hidden="true">
			<input type="text" name="url_hp" tabindex="-1" autocomplete="off">
		</div>

		<div class="newsletter-form__success" role="alert" aria-live="polite" hidden>
			<span class="newsletter-form__check" aria-hidden="true">&#10003;</span>
			<?php esc_html_e( 'Check your inbox to confirm!', 'urban-cms' ); ?>
		</div>

		<div class="newsletter-form__fields">
			<?php if ( $variant === 'full-width' || $variant === 'card' ) : ?>
			<div class="newsletter-form__name-row">
				<input type="text"
						name="first_name"
						class="newsletter-input"
						placeholder="<?php esc_attr_e( 'First name', 'urban-cms' ); ?>"
						autocomplete="given-name">
				<input type="text"
						name="last_name"
						class="newsletter-input"
						placeholder="<?php esc_attr_e( 'Last name', 'urban-cms' ); ?>"
						autocomplete="family-name">
			</div>
			<?php endif; ?>

			<div class="newsletter-form__email-row">
				<input type="email"
						name="email"
						class="newsletter-input newsletter-input--email"
						placeholder="<?php esc_attr_e( 'Your email address', 'urban-cms' ); ?>"
						required
						autocomplete="email"
						aria-label="<?php esc_attr_e( 'Email address', 'urban-cms' ); ?>">
				<button type="submit" class="btn btn--accent newsletter-form__submit">
					<span class="btn-label"><?php esc_html_e( 'Subscribe', 'urban-cms' ); ?></span>
					<span class="btn-loading" hidden aria-hidden="true">
						<span class="spinner spinner--dark"></span>
					</span>
				</button>
			</div>

			<div class="newsletter-form__error" role="alert" aria-live="assertive" hidden></div>

			<p class="newsletter-form__privacy">
				<?php
				printf(
					esc_html__( 'No spam, ever. Unsubscribe anytime. See our %s.', 'urban-cms' ),
					'<a href="' . esc_url( get_privacy_policy_url() ) . '">' . esc_html__( 'privacy policy', 'urban-cms' ) . '</a>'
				);
				?>
			</p>
		</div>
	</form>

	<?php if ( $variant === 'full-width' ) : ?>
		</div><!-- .form-wrap -->
	</div><!-- .inner -->
	<?php endif; ?>

</div>
