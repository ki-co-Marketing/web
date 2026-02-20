<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="sr-only skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'urban-cms' ); ?></a>

<?php
/* Topbar: utility links + contact info */
$district_phone = get_theme_mod( 'district_phone' );
$district_email = get_theme_mod( 'district_email' );
if ( $district_phone || $district_email || has_nav_menu( 'topbar' ) ) :
	?>
<div class="topbar" role="banner" aria-label="<?php esc_attr_e( 'Utility navigation', 'urban-cms' ); ?>">
	<div class="container topbar__inner">
		<div class="topbar__contact">
			<?php if ( $district_phone ) : ?>
				<span><?php echo esc_html( $district_phone ); ?></span>
			<?php endif; ?>
			<?php if ( $district_email ) : ?>
				<a href="mailto:<?php echo esc_attr( $district_email ); ?>"><?php echo esc_html( $district_email ); ?></a>
			<?php endif; ?>
		</div>
		<?php if ( has_nav_menu( 'topbar' ) ) : ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'topbar',
					'menu_class'     => 'topbar__nav',
					'container'      => 'nav',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<header class="site-header" role="banner">
	<div class="container site-header__inner">

		<!-- Logo / Site Identity -->
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo" rel="home">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<span class="site-logo__icon" aria-hidden="true">&#9632;</span>
				<span class="site-logo__text"><?php bloginfo( 'name' ); ?></span>
			<?php endif; ?>
		</a>

		<!-- Primary Navigation -->
		<nav class="primary-nav" id="primary-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'urban-cms' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => '',
					'fallback_cb'    => false,
				)
			);
			?>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'urban_event' ) ); ?>" class="btn header-cta">
				<?php esc_html_e( 'Events', 'urban-cms' ); ?>
			</a>
		</nav>

		<!-- Mobile nav toggle -->
		<button class="nav-toggle" aria-controls="primary-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'urban-cms' ); ?>">
			<span></span>
			<span></span>
			<span></span>
		</button>

	</div>
</header><!-- .site-header -->

<div id="page" class="site">
<main id="main" class="site-main" role="main">
