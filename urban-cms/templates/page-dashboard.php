<?php
/**
 * Template Name: Member Dashboard
 * Template Post Type: page
 *
 * Shows a logged-in member's submitted businesses and events, with
 * status indicators. Logged-out visitors see a login/register prompt.
 *
 * @package UrbanCMS
 */

get_header();

$is_logged_in = is_user_logged_in();
$user         = $is_logged_in ? wp_get_current_user() : null;
?>

<div style="background:var(--color-primary);padding:var(--space-10) 0">
	<div class="container">
		<?php if ( $is_logged_in ) : ?>
			<p style="color:rgba(255,255,255,0.7);font-size:var(--text-sm);margin-bottom:var(--space-2)"><?php esc_html_e( 'Member Dashboard', 'urban-cms' ); ?></p>
			<h1 style="color:#fff">
				<?php printf( esc_html__( 'Welcome, %s', 'urban-cms' ), esc_html( $user->display_name ) ); ?>
			</h1>
		<?php else : ?>
			<h1 style="color:#fff"><?php esc_html_e( 'Member Dashboard', 'urban-cms' ); ?></h1>
			<p style="color:rgba(255,255,255,0.8)"><?php esc_html_e( 'Sign in to manage your business listings and event submissions.', 'urban-cms' ); ?></p>
		<?php endif; ?>
	</div>
</div>

<div class="container" style="padding-top:var(--space-10);padding-bottom:var(--space-16)">

	<?php if ( ! $is_logged_in ) : ?>
	<!-- =====================================================
		Logged-out state: login form + CTAs
		===================================================== -->
	<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-10);align-items:start;max-width:960px;margin:0 auto">
		<div>
			<h2 style="margin-bottom:var(--space-6)"><?php esc_html_e( 'Sign In', 'urban-cms' ); ?></h2>
			<?php
			wp_login_form(
				array(
					'label_username' => __( 'Email or Username', 'urban-cms' ),
					'label_password' => __( 'Password', 'urban-cms' ),
					'label_log_in'   => __( 'Sign In', 'urban-cms' ),
					'redirect'       => get_permalink(),
				)
			);
			?>
			<p style="margin-top:var(--space-4);font-size:var(--text-sm)">
				<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot your password?', 'urban-cms' ); ?></a>
			</p>
		</div>

		<div style="border-left:1px solid var(--color-border);padding-left:var(--space-10)">
			<h2 style="margin-bottom:var(--space-4)"><?php esc_html_e( 'Not a Member?', 'urban-cms' ); ?></h2>
			<p><?php esc_html_e( 'Create a free account to submit and manage your business listings and events in the district directory.', 'urban-cms' ); ?></p>
			<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="btn btn--primary" style="margin-top:var(--space-4)">
				<?php esc_html_e( 'Create a Free Account', 'urban-cms' ); ?>
			</a>

			<div style="margin-top:var(--space-8)">
				<h3 style="margin-bottom:var(--space-4)"><?php esc_html_e( 'Or submit without an account:', 'urban-cms' ); ?></h3>
				<div style="display:flex;flex-direction:column;gap:var(--space-3)">
					<a href="<?php echo esc_url( home_url( '/list-your-business' ) ); ?>" class="btn btn--outline">
						<?php esc_html_e( 'Submit a Business Listing', 'urban-cms' ); ?>
					</a>
					<a href="<?php echo esc_url( home_url( '/submit-event' ) ); ?>" class="btn btn--outline">
						<?php esc_html_e( 'Submit an Event', 'urban-cms' ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>

	<?php else : ?>
	<!-- =====================================================
		Logged-in Dashboard
		===================================================== -->
	<div style="display:grid;grid-template-columns:220px 1fr;gap:var(--space-10);align-items:start">

		<!-- Dashboard nav -->
		<nav class="dashboard-nav" aria-label="<?php esc_attr_e( 'Dashboard navigation', 'urban-cms' ); ?>">
			<?php
			$current_section = sanitize_text_field( wp_unslash( $_GET['section'] ?? 'overview' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only UI nav, no state mutation
			$sections        = array(
				'overview'   => __( 'Overview', 'urban-cms' ),
				'businesses' => __( 'My Businesses', 'urban-cms' ),
				'events'     => __( 'My Events', 'urban-cms' ),
				'profile'    => __( 'Account Profile', 'urban-cms' ),
			);
			?>
			<ul style="display:flex;flex-direction:column;gap:var(--space-1)">
				<?php foreach ( $sections as $key => $label ) : ?>
					<li>
						<a href="<?php echo esc_url( add_query_arg( 'section', $key, get_permalink() ) ); ?>"
							class="dashboard-nav__link <?php echo $current_section === $key ? 'active' : ''; ?>">
							<?php echo esc_html( $label ); ?>
						</a>
					</li>
				<?php endforeach; ?>
				<li style="margin-top:var(--space-4)">
					<a href="<?php echo esc_url( wp_logout_url( home_url( '/' ) ) ); ?>" class="dashboard-nav__link dashboard-nav__link--danger">
						<?php esc_html_e( 'Sign Out', 'urban-cms' ); ?>
					</a>
				</li>
			</ul>
		</nav>

		<!-- Dashboard content -->
		<div class="dashboard-content">

			<?php
			switch ( $current_section ) {

				/* ---- Overview ---- */
				case 'overview':
					urban_cms_dashboard_overview( $user );
					break;

				/* ---- My Businesses ---- */
				case 'businesses':
					urban_cms_dashboard_businesses( $user );
					break;

				/* ---- My Events ---- */
				case 'events':
					urban_cms_dashboard_events( $user );
					break;

				/* ---- Profile ---- */
				case 'profile':
					urban_cms_dashboard_profile( $user );
					break;

				default:
					urban_cms_dashboard_overview( $user );
			}
			?>

		</div><!-- .dashboard-content -->
	</div>
	<?php endif; ?>
</div>

<?php
get_footer();

/*
=========================================================
	Dashboard Section Renderers
	========================================================= */

function urban_cms_dashboard_overview( WP_User $user ): void {
	$biz_args = urban_cms_user_submission_args( $user->ID, 'urban_business' );
	$ev_args  = urban_cms_user_submission_args( $user->ID, 'urban_event' );

	$biz_count   = ( new WP_Query(
		array_merge(
			$biz_args,
			array(
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			)
		)
	) )->found_posts;
	$ev_count    = ( new WP_Query(
		array_merge(
			$ev_args,
			array(
				'post_status'    => 'publish',
				'posts_per_page' => -1,
			)
		)
	) )->found_posts;
	$draft_count = ( new WP_Query(
		array(
			'post_type'      => array( 'urban_business', 'urban_event' ),
			'post_status'    => 'draft',
			'author'         => $user->ID,
			'posts_per_page' => -1,
		)
	) )->found_posts;
	?>
	<h2><?php esc_html_e( 'Overview', 'urban-cms' ); ?></h2>

	<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-4);margin:var(--space-6) 0">
		<?php
		urban_cms_stat_card( $biz_count, __( 'Published Businesses', 'urban-cms' ), '?section=businesses' );
		urban_cms_stat_card( $ev_count, __( 'Published Events', 'urban-cms' ), '?section=events' );
		urban_cms_stat_card( $draft_count, __( 'Pending Review', 'urban-cms' ), '?section=businesses' );
		?>
	</div>

	<div style="display:flex;gap:var(--space-4);margin-top:var(--space-8)">
		<a href="<?php echo esc_url( home_url( '/list-your-business' ) ); ?>" class="btn btn--primary">
			<?php esc_html_e( '+ Add Business', 'urban-cms' ); ?>
		</a>
		<a href="<?php echo esc_url( home_url( '/submit-event' ) ); ?>" class="btn btn--outline">
			<?php esc_html_e( '+ Submit Event', 'urban-cms' ); ?>
		</a>
	</div>
	<?php
}

function urban_cms_dashboard_businesses( WP_User $user ): void {
	?>
	<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-6)">
		<h2><?php esc_html_e( 'My Business Listings', 'urban-cms' ); ?></h2>
		<a href="<?php echo esc_url( home_url( '/list-your-business' ) ); ?>" class="btn btn--primary btn--sm">
			<?php esc_html_e( '+ Add New', 'urban-cms' ); ?>
		</a>
	</div>
	<?php
	urban_cms_render_submission_table( $user->ID, 'urban_business' );
}

function urban_cms_dashboard_events( WP_User $user ): void {
	?>
	<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-6)">
		<h2><?php esc_html_e( 'My Event Submissions', 'urban-cms' ); ?></h2>
		<a href="<?php echo esc_url( home_url( '/submit-event' ) ); ?>" class="btn btn--primary btn--sm">
			<?php esc_html_e( '+ Submit New', 'urban-cms' ); ?>
		</a>
	</div>
	<?php
	urban_cms_render_submission_table( $user->ID, 'urban_event' );
}

function urban_cms_dashboard_profile( WP_User $user ): void {
	?>
	<h2><?php esc_html_e( 'Account Profile', 'urban-cms' ); ?></h2>
	<div class="widget" style="max-width:480px;margin-top:var(--space-6)">
		<dl style="display:flex;flex-direction:column;gap:var(--space-3);font-size:var(--text-sm)">
			<div><dt style="font-weight:700;color:var(--color-primary)"><?php esc_html_e( 'Display Name', 'urban-cms' ); ?></dt><dd><?php echo esc_html( $user->display_name ); ?></dd></div>
			<div><dt style="font-weight:700;color:var(--color-primary)"><?php esc_html_e( 'Username', 'urban-cms' ); ?></dt><dd><?php echo esc_html( $user->user_login ); ?></dd></div>
			<div><dt style="font-weight:700;color:var(--color-primary)"><?php esc_html_e( 'Email', 'urban-cms' ); ?></dt><dd><?php echo esc_html( $user->user_email ); ?></dd></div>
			<div><dt style="font-weight:700;color:var(--color-primary)"><?php esc_html_e( 'Member Since', 'urban-cms' ); ?></dt><dd><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $user->user_registered ) ) ); ?></dd></div>
		</dl>
		<a href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>" class="btn btn--outline btn--sm" style="margin-top:var(--space-4)">
			<?php esc_html_e( 'Edit Profile', 'urban-cms' ); ?>
		</a>
	</div>
	<?php
}

function urban_cms_render_submission_table( int $user_id, string $post_type ): void {
	$args = array(
		'post_type'      => $post_type,
		'post_status'    => array( 'publish', 'draft', 'pending' ),
		'author'         => $user_id,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'posts_per_page' => 50,
	);

	// Also query by submission email meta for non-logged-in-at-time-of-submission posts
	$meta_email_raw = get_user_meta( $user_id, '_submission_email', true );
	$meta_email     = $meta_email_raw ? $meta_email_raw : get_userdata( $user_id )->user_email;
	$args_meta  = array_merge(
		$args,
		array(
			'author'     => 0,
			'meta_query' => array(
				array(
					'key'     => '_submission_email',
					'value'   => $meta_email,
					'compare' => '=',
				),
			),
		)
	);

	$q1 = new WP_Query( $args );
	$q2 = new WP_Query( $args_meta );

	// Deduplicate
	$ids   = array();
	$posts = array();
	foreach ( array_merge( $q1->posts, $q2->posts ) as $p ) {
		if ( ! in_array( $p->ID, $ids, true ) ) {
			$ids[]   = $p->ID;
			$posts[] = $p;
		}
	}

	if ( ! $posts ) {
		echo '<div class="widget" style="text-align:center;padding:var(--space-12)">';
		echo '<p style="color:var(--color-text-muted)">' . esc_html__( 'No submissions yet.', 'urban-cms' ) . '</p>';
		echo '</div>';
		return;
	}

	echo '<div class="dashboard-table-wrap"><table class="dashboard-table">';
	echo '<thead><tr>';
	echo '<th>' . esc_html__( 'Title', 'urban-cms' ) . '</th>';
	echo '<th>' . esc_html__( 'Status', 'urban-cms' ) . '</th>';
	echo '<th>' . esc_html__( 'Date', 'urban-cms' ) . '</th>';
	echo '<th>' . esc_html__( 'Actions', 'urban-cms' ) . '</th>';
	echo '</tr></thead><tbody>';

	foreach ( $posts as $post ) {
		$status                         = $post->post_status;
		$status_map                     = array(
			'publish' => array( __( 'Published', 'urban-cms' ), 'badge--success' ),
			'draft'   => array( __( 'Pending Review', 'urban-cms' ), 'badge' ),
			'pending' => array( __( 'Pending Review', 'urban-cms' ), 'badge' ),
		);
		[ $status_label, $badge_class ] = $status_map[ $status ] ?? array( ucfirst( $status ), 'badge' );

		echo '<tr>';
		echo '<td><strong>' . esc_html( $post->post_title ) . '</strong></td>';
		echo '<td><span class="badge ' . esc_attr( $badge_class ) . '">' . esc_html( $status_label ) . '</span></td>';
		echo '<td>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ) . '</td>';
		echo '<td>';
		if ( $status === 'publish' ) {
			echo '<a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="btn btn--outline btn--sm">' . esc_html__( 'View', 'urban-cms' ) . '</a>';
		} else {
			echo '<span style="font-size:var(--text-sm);color:var(--color-text-muted)">' . esc_html__( 'Under review', 'urban-cms' ) . '</span>';
		}
		echo '</td></tr>';
	}

	echo '</tbody></table></div>';
}

function urban_cms_user_submission_args( int $user_id, string $post_type ): array {
	return array(
		'post_type'      => $post_type,
		'author'         => $user_id,
		'posts_per_page' => -1,
	);
}

function urban_cms_stat_card( int $count, string $label, string $href ): void {
	echo '<a href="' . esc_url( get_permalink() . $href ) . '" class="dashboard-stat-card">';
	echo '<span class="dashboard-stat-card__number">' . (int) $count . '</span>';
	echo '<span class="dashboard-stat-card__label">' . esc_html( $label ) . '</span>';
	echo '</a>';
}
