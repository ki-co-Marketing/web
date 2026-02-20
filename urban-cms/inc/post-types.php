<?php
/**
 * Custom Post Types for Urban CMS Platform
 *
 * Registers: Events, Business Directory, Initiatives/Projects, Team Members
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'urban_cms_register_post_types' );

function urban_cms_register_post_types() {
	urban_cms_register_events();
	urban_cms_register_businesses();
	urban_cms_register_initiatives();
	urban_cms_register_team();
}

/*
=========================================================
	Events
	========================================================= */
function urban_cms_register_events() {
	$labels = array(
		'name'               => __( 'Events', 'urban-cms' ),
		'singular_name'      => __( 'Event', 'urban-cms' ),
		'add_new'            => __( 'Add Event', 'urban-cms' ),
		'add_new_item'       => __( 'Add New Event', 'urban-cms' ),
		'edit_item'          => __( 'Edit Event', 'urban-cms' ),
		'new_item'           => __( 'New Event', 'urban-cms' ),
		'view_item'          => __( 'View Event', 'urban-cms' ),
		'search_items'       => __( 'Search Events', 'urban-cms' ),
		'not_found'          => __( 'No events found.', 'urban-cms' ),
		'not_found_in_trash' => __( 'No events in trash.', 'urban-cms' ),
		'menu_name'          => __( 'Events', 'urban-cms' ),
	);

	register_post_type(
		'urban_event',
		array(
			'labels'        => $labels,
			'public'        => true,
			'has_archive'   => 'events',
			'rewrite'       => array(
				'slug'       => 'events',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-calendar-alt',
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'show_in_rest'  => true,
			'rest_base'     => 'urban-events',
			'taxonomies'    => array( 'event_category' ),
		)
	);

	// Custom meta fields for events
	add_action(
		'add_meta_boxes',
		function () {
			add_meta_box(
				'urban-event-details',
				__( 'Event Details', 'urban-cms' ),
				'urban_cms_event_meta_box',
				'urban_event',
				'normal',
				'high'
			);
		}
	);

	add_action( 'save_post_urban_event', 'urban_cms_save_event_meta', 10, 2 );
}

function urban_cms_event_meta_box( WP_Post $post ) {
	wp_nonce_field( 'urban_event_meta', 'urban_event_nonce' );

	$start_date = get_post_meta( $post->ID, '_event_start_date', true );
	$end_date   = get_post_meta( $post->ID, '_event_end_date', true );
	$start_time = get_post_meta( $post->ID, '_event_start_time', true );
	$end_time   = get_post_meta( $post->ID, '_event_end_time', true );
	$location   = get_post_meta( $post->ID, '_event_location', true );
	$address    = get_post_meta( $post->ID, '_event_address', true );
	$cost       = get_post_meta( $post->ID, '_event_cost', true );
	$rsvp_url   = get_post_meta( $post->ID, '_event_rsvp_url', true );
	$organizer  = get_post_meta( $post->ID, '_event_organizer', true );

	?>
	<div class="urban-meta-grid">
		<p>
			<label><strong><?php esc_html_e( 'Start Date', 'urban-cms' ); ?></strong></label><br>
			<input type="date" name="event_start_date" value="<?php echo esc_attr( $start_date ); ?>" style="width:100%">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'End Date', 'urban-cms' ); ?></strong></label><br>
			<input type="date" name="event_end_date" value="<?php echo esc_attr( $end_date ); ?>" style="width:100%">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'Start Time', 'urban-cms' ); ?></strong></label><br>
			<input type="time" name="event_start_time" value="<?php echo esc_attr( $start_time ); ?>" style="width:100%">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'End Time', 'urban-cms' ); ?></strong></label><br>
			<input type="time" name="event_end_time" value="<?php echo esc_attr( $end_time ); ?>" style="width:100%">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'Venue / Location Name', 'urban-cms' ); ?></strong></label><br>
			<input type="text" name="event_location" value="<?php echo esc_attr( $location ); ?>" style="width:100%">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'Street Address', 'urban-cms' ); ?></strong></label><br>
			<input type="text" name="event_address" value="<?php echo esc_attr( $address ); ?>" style="width:100%">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'Cost / Admission', 'urban-cms' ); ?></strong></label><br>
			<input type="text" name="event_cost" placeholder="<?php esc_attr_e( 'Free, $10, etc.', 'urban-cms' ); ?>" value="<?php echo esc_attr( $cost ); ?>" style="width:100%">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'RSVP / Ticket URL', 'urban-cms' ); ?></strong></label><br>
			<input type="url" name="event_rsvp_url" value="<?php echo esc_url( $rsvp_url ); ?>" style="width:100%">
		</p>
		<p>
			<label><strong><?php esc_html_e( 'Organizer / Host', 'urban-cms' ); ?></strong></label><br>
			<input type="text" name="event_organizer" value="<?php echo esc_attr( $organizer ); ?>" style="width:100%">
		</p>
	</div>
	<?php
}

function urban_cms_save_event_meta( int $post_id, WP_Post $post ) {
	if ( ! isset( $_POST['urban_event_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['urban_event_nonce'] ) ), 'urban_event_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$fields = array(
		'_event_start_date' => array( 'event_start_date', 'sanitize_text_field' ),
		'_event_end_date'   => array( 'event_end_date', 'sanitize_text_field' ),
		'_event_start_time' => array( 'event_start_time', 'sanitize_text_field' ),
		'_event_end_time'   => array( 'event_end_time', 'sanitize_text_field' ),
		'_event_location'   => array( 'event_location', 'sanitize_text_field' ),
		'_event_address'    => array( 'event_address', 'sanitize_text_field' ),
		'_event_cost'       => array( 'event_cost', 'sanitize_text_field' ),
		'_event_rsvp_url'   => array( 'event_rsvp_url', 'esc_url_raw' ),
		'_event_organizer'  => array( 'event_organizer', 'sanitize_text_field' ),
	);

	foreach ( $fields as $meta_key => [ $input_key, $sanitizer ] ) {
		if ( isset( $_POST[ $input_key ] ) ) {
			update_post_meta( $post_id, $meta_key, $sanitizer( wp_unslash( $_POST[ $input_key ] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}
	}
}

/*
=========================================================
	Business Directory
	========================================================= */
function urban_cms_register_businesses() {
	$labels = array(
		'name'               => __( 'Businesses', 'urban-cms' ),
		'singular_name'      => __( 'Business', 'urban-cms' ),
		'add_new'            => __( 'Add Business', 'urban-cms' ),
		'add_new_item'       => __( 'Add New Business', 'urban-cms' ),
		'edit_item'          => __( 'Edit Business', 'urban-cms' ),
		'view_item'          => __( 'View Business', 'urban-cms' ),
		'search_items'       => __( 'Search Businesses', 'urban-cms' ),
		'not_found'          => __( 'No businesses found.', 'urban-cms' ),
		'not_found_in_trash' => __( 'No businesses in trash.', 'urban-cms' ),
		'menu_name'          => __( 'Directory', 'urban-cms' ),
	);

	register_post_type(
		'urban_business',
		array(
			'labels'        => $labels,
			'public'        => true,
			'has_archive'   => 'directory',
			'rewrite'       => array(
				'slug'       => 'directory',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-store',
			'menu_position' => 6,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'show_in_rest'  => true,
			'rest_base'     => 'urban-businesses',
			'taxonomies'    => array( 'business_category', 'district_neighborhood' ),
		)
	);

	add_action(
		'add_meta_boxes',
		function () {
			add_meta_box(
				'urban-business-details',
				__( 'Business Details', 'urban-cms' ),
				'urban_cms_business_meta_box',
				'urban_business',
				'normal',
				'high'
			);
		}
	);

	add_action( 'save_post_urban_business', 'urban_cms_save_business_meta', 10, 2 );
}

function urban_cms_business_meta_box( WP_Post $post ) {
	wp_nonce_field( 'urban_business_meta', 'urban_business_nonce' );

	$fields = array(
		'_business_address'   => array( 'Address', 'text', '' ),
		'_business_suite'     => array( 'Suite / Floor', 'text', '' ),
		'_business_phone'     => array( 'Phone', 'tel', '' ),
		'_business_email'     => array( 'Email', 'email', '' ),
		'_business_website'   => array( 'Website URL', 'url', '' ),
		'_business_hours'     => array( 'Hours', 'text', 'e.g. Mon-Fri 9am–6pm' ),
		'_business_instagram' => array( 'Instagram URL', 'url', '' ),
		'_business_facebook'  => array( 'Facebook URL', 'url', '' ),
		'_business_lat'       => array( 'Latitude', 'text', 'e.g. 47.6062' ),
		'_business_lng'       => array( 'Longitude', 'text', 'e.g. -122.3321' ),
		'_business_featured'  => array( 'Featured Listing', 'checkbox', '' ),
	);

	foreach ( $fields as $key => [ $label, $type, $placeholder ] ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<p><label><strong>' . esc_html( $label ) . '</strong></label><br>';
		if ( $type === 'checkbox' ) {
			echo '<input type="checkbox" name="' . esc_attr( ltrim( $key, '_' ) ) . '" value="1" ' . checked( $val, '1', false ) . '> ';
			echo '<span>' . esc_html__( 'Show as featured business', 'urban-cms' ) . '</span>';
		} else {
			echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( ltrim( $key, '_' ) ) . '" '
				. 'value="' . esc_attr( $val ) . '" '
				. ( $placeholder ? 'placeholder="' . esc_attr( $placeholder ) . '" ' : '' )
				. 'style="width:100%">';
		}
		echo '</p>';
	}
}

function urban_cms_save_business_meta( int $post_id, WP_Post $post ) {
	if ( ! isset( $_POST['urban_business_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['urban_business_nonce'] ) ), 'urban_business_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$sanitizers = array(
		'business_address'   => 'sanitize_text_field',
		'business_suite'     => 'sanitize_text_field',
		'business_phone'     => 'sanitize_text_field',
		'business_email'     => 'sanitize_email',
		'business_website'   => 'esc_url_raw',
		'business_hours'     => 'sanitize_text_field',
		'business_instagram' => 'esc_url_raw',
		'business_facebook'  => 'esc_url_raw',
		'business_lat'       => 'sanitize_text_field',
		'business_lng'       => 'sanitize_text_field',
	);

	foreach ( $sanitizers as $input_key => $sanitizer ) {
		$meta_key = '_' . $input_key;
		if ( isset( $_POST[ $input_key ] ) ) {
			update_post_meta( $post_id, $meta_key, $sanitizer( wp_unslash( $_POST[ $input_key ] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}
	}

	// Featured checkbox (unchecked = not in POST)
	update_post_meta( $post_id, '_business_featured', isset( $_POST['business_featured'] ) ? '1' : '0' );
}

/*
=========================================================
	Initiatives / Projects
	========================================================= */
function urban_cms_register_initiatives() {
	$labels = array(
		'name'               => __( 'Initiatives', 'urban-cms' ),
		'singular_name'      => __( 'Initiative', 'urban-cms' ),
		'add_new'            => __( 'Add Initiative', 'urban-cms' ),
		'add_new_item'       => __( 'Add New Initiative', 'urban-cms' ),
		'edit_item'          => __( 'Edit Initiative', 'urban-cms' ),
		'view_item'          => __( 'View Initiative', 'urban-cms' ),
		'not_found'          => __( 'No initiatives found.', 'urban-cms' ),
		'not_found_in_trash' => __( 'No initiatives in trash.', 'urban-cms' ),
		'menu_name'          => __( 'Initiatives', 'urban-cms' ),
	);

	register_post_type(
		'urban_initiative',
		array(
			'labels'        => $labels,
			'public'        => true,
			'has_archive'   => 'initiatives',
			'rewrite'       => array(
				'slug'       => 'initiatives',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-building',
			'menu_position' => 7,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
			'show_in_rest'  => true,
			'rest_base'     => 'urban-initiatives',
			'taxonomies'    => array( 'initiative_type' ),
		)
	);

	add_action(
		'add_meta_boxes',
		function () {
			add_meta_box(
				'urban-initiative-details',
				__( 'Initiative Details', 'urban-cms' ),
				'urban_cms_initiative_meta_box',
				'urban_initiative',
				'normal',
				'high'
			);
		}
	);

	add_action( 'save_post_urban_initiative', 'urban_cms_save_initiative_meta', 10, 2 );
}

function urban_cms_initiative_meta_box( WP_Post $post ) {
	wp_nonce_field( 'urban_initiative_meta', 'urban_initiative_nonce' );

	$status   = get_post_meta( $post->ID, '_initiative_status', true );
	$status   = $status ? $status : 'active';
	$progress = get_post_meta( $post->ID, '_initiative_progress', true );
	$progress = $progress ? $progress : 0;
	$budget   = get_post_meta( $post->ID, '_initiative_budget', true );
	$start    = get_post_meta( $post->ID, '_initiative_start', true );
	$end      = get_post_meta( $post->ID, '_initiative_end', true );
	$lead     = get_post_meta( $post->ID, '_initiative_lead', true );
	$location = get_post_meta( $post->ID, '_initiative_location', true );

	$statuses = array(
		'planning'  => __( 'Planning', 'urban-cms' ),
		'active'    => __( 'Active', 'urban-cms' ),
		'on-hold'   => __( 'On Hold', 'urban-cms' ),
		'completed' => __( 'Completed', 'urban-cms' ),
	);
	?>
	<p>
		<label><strong><?php esc_html_e( 'Status', 'urban-cms' ); ?></strong></label><br>
		<select name="initiative_status" style="width:100%">
			<?php foreach ( $statuses as $val => $label ) : ?>
				<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $status, $val ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Progress (%)', 'urban-cms' ); ?></strong></label><br>
		<input type="number" name="initiative_progress" value="<?php echo esc_attr( $progress ); ?>" min="0" max="100" style="width:100%">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Budget', 'urban-cms' ); ?></strong></label><br>
		<input type="text" name="initiative_budget" value="<?php echo esc_attr( $budget ); ?>" placeholder="e.g. $250,000" style="width:100%">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Start Date', 'urban-cms' ); ?></strong></label><br>
		<input type="date" name="initiative_start" value="<?php echo esc_attr( $start ); ?>" style="width:100%">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Expected Completion', 'urban-cms' ); ?></strong></label><br>
		<input type="date" name="initiative_end" value="<?php echo esc_attr( $end ); ?>" style="width:100%">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Lead / Sponsor', 'urban-cms' ); ?></strong></label><br>
		<input type="text" name="initiative_lead" value="<?php echo esc_attr( $lead ); ?>" style="width:100%">
	</p>
	<p>
		<label><strong><?php esc_html_e( 'Location / Area', 'urban-cms' ); ?></strong></label><br>
		<input type="text" name="initiative_location" value="<?php echo esc_attr( $location ); ?>" style="width:100%">
	</p>
	<?php
}

function urban_cms_save_initiative_meta( int $post_id, WP_Post $post ) {
	if ( ! isset( $_POST['urban_initiative_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['urban_initiative_nonce'] ) ), 'urban_initiative_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$allowed_statuses = array( 'planning', 'active', 'on-hold', 'completed' );
	$status           = sanitize_text_field( wp_unslash( $_POST['initiative_status'] ?? 'active' ) );
	if ( ! in_array( $status, $allowed_statuses, true ) ) {
		$status = 'active';
	}
	update_post_meta( $post_id, '_initiative_status', $status );
	update_post_meta( $post_id, '_initiative_progress', min( 100, max( 0, absint( $_POST['initiative_progress'] ?? 0 ) ) ) );
	update_post_meta( $post_id, '_initiative_budget', sanitize_text_field( wp_unslash( $_POST['initiative_budget'] ?? '' ) ) );
	update_post_meta( $post_id, '_initiative_start', sanitize_text_field( wp_unslash( $_POST['initiative_start'] ?? '' ) ) );
	update_post_meta( $post_id, '_initiative_end', sanitize_text_field( wp_unslash( $_POST['initiative_end'] ?? '' ) ) );
	update_post_meta( $post_id, '_initiative_lead', sanitize_text_field( wp_unslash( $_POST['initiative_lead'] ?? '' ) ) );
	update_post_meta( $post_id, '_initiative_location', sanitize_text_field( wp_unslash( $_POST['initiative_location'] ?? '' ) ) );
}

/*
=========================================================
	Team Members
	========================================================= */
function urban_cms_register_team() {
	$labels = array(
		'name'          => __( 'Team', 'urban-cms' ),
		'singular_name' => __( 'Team Member', 'urban-cms' ),
		'add_new'       => __( 'Add Team Member', 'urban-cms' ),
		'add_new_item'  => __( 'Add New Team Member', 'urban-cms' ),
		'edit_item'     => __( 'Edit Team Member', 'urban-cms' ),
		'not_found'     => __( 'No team members found.', 'urban-cms' ),
		'menu_name'     => __( 'Team', 'urban-cms' ),
	);

	register_post_type(
		'urban_team',
		array(
			'labels'        => $labels,
			'public'        => true,
			'has_archive'   => false,
			'rewrite'       => array(
				'slug'       => 'team',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-groups',
			'menu_position' => 8,
			'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
			'show_in_rest'  => true,
		)
	);

	add_action(
		'add_meta_boxes',
		function () {
			add_meta_box(
				'urban-team-details',
				__( 'Team Member Details', 'urban-cms' ),
				'urban_cms_team_meta_box',
				'urban_team',
				'normal',
				'high'
			);
		}
	);

	add_action( 'save_post_urban_team', 'urban_cms_save_team_meta', 10, 2 );
}

function urban_cms_team_meta_box( WP_Post $post ) {
	wp_nonce_field( 'urban_team_meta', 'urban_team_nonce' );

	$fields = array(
		'_team_title'    => array( 'Job Title / Role', 'text', '' ),
		'_team_email'    => array( 'Email', 'email', '' ),
		'_team_phone'    => array( 'Phone', 'tel', '' ),
		'_team_linkedin' => array( 'LinkedIn URL', 'url', '' ),
	);

	foreach ( $fields as $key => [ $label, $type, $placeholder ] ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<p><label><strong>' . esc_html( $label ) . '</strong></label><br>';
		echo '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( ltrim( $key, '_' ) ) . '" '
			. 'value="' . esc_attr( $val ) . '" style="width:100%"></p>';
	}
}

function urban_cms_save_team_meta( int $post_id, WP_Post $post ) {
	if ( ! isset( $_POST['urban_team_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['urban_team_nonce'] ) ), 'urban_team_meta' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, '_team_title', sanitize_text_field( wp_unslash( $_POST['team_title'] ?? '' ) ) );
	update_post_meta( $post_id, '_team_email', sanitize_email( wp_unslash( $_POST['team_email'] ?? '' ) ) );
	update_post_meta( $post_id, '_team_phone', sanitize_text_field( wp_unslash( $_POST['team_phone'] ?? '' ) ) );
	update_post_meta( $post_id, '_team_linkedin', esc_url_raw( wp_unslash( $_POST['team_linkedin'] ?? '' ) ) );
}
