<?php
/**
 * Public Submission Forms
 *
 * Handles public-facing forms for submitting business listings and events.
 * All submissions are created as draft posts pending admin review.
 *
 * Security layers:
 *   1. WordPress nonce verification
 *   2. Honeypot field (bot trap)
 *   3. Rate limiting via transients (per IP, 3 submissions per hour)
 *   4. Full input sanitization
 *   5. File upload type + size validation
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

/*
=========================================================
	Register AJAX hooks
	========================================================= */
add_action( 'wp_ajax_urban_submit_business', 'urban_cms_handle_business_submission' );
add_action( 'wp_ajax_nopriv_urban_submit_business', 'urban_cms_handle_business_submission' );

add_action( 'wp_ajax_urban_submit_event', 'urban_cms_handle_event_submission' );
add_action( 'wp_ajax_nopriv_urban_submit_event', 'urban_cms_handle_event_submission' );

/*
=========================================================
	Enqueue form scripts on relevant page templates
	========================================================= */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! is_page_template( array( 'templates/page-submit-business.php', 'templates/page-submit-event.php' ) ) ) {
			return;
		}
		wp_enqueue_script(
			'urban-cms-forms',
			URBAN_CMS_URI . '/assets/js/submission-forms.js',
			array( 'urban-cms' ),
			URBAN_CMS_VERSION,
			true
		);
		wp_localize_script(
			'urban-cms-forms',
			'urbanForms',
			array(
				'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
				'bizNonce'         => wp_create_nonce( 'urban_submit_business' ),
				'eventNonce'       => wp_create_nonce( 'urban_submit_event' ),
				'maxFileSize'      => urban_cms_max_upload_bytes(),
				'maxFileSizeLabel' => size_format( urban_cms_max_upload_bytes() ),
				'strings'          => array(
					'submitting'    => __( 'Submitting…', 'urban-cms' ),
					'uploading'     => __( 'Uploading image…', 'urban-cms' ),
					'success_biz'   => __( 'Thank you! Your business listing has been submitted and is pending review.', 'urban-cms' ),
					'success_event' => __( 'Thank you! Your event has been submitted and is pending review.', 'urban-cms' ),
					'error_generic' => __( 'Something went wrong. Please try again or contact us directly.', 'urban-cms' ),
					'error_rate'    => __( 'You have submitted too many times recently. Please wait an hour and try again.', 'urban-cms' ),
					'error_file'    => __( 'File too large or not an accepted image type (JPEG, PNG, WebP).', 'urban-cms' ),
				),
			)
		);
	}
);

/*
=========================================================
	Business Submission Handler
	========================================================= */
function urban_cms_handle_business_submission(): never {
	// 1. Nonce
	if ( ! check_ajax_referer( 'urban_submit_business', 'nonce', false ) ) {
		wp_send_json_error( array( 'code' => 'bad_nonce' ), 403 );
	}

	// 2. Honeypot
	if ( ! empty( $_POST['website_url_hp'] ) ) {
		wp_send_json_success( array( 'honeypot' => true ) ); // Silently succeed to fool bots
	}

	// 3. Rate limit
	if ( ! urban_cms_check_rate_limit( 'biz' ) ) {
		wp_send_json_error(
			array(
				'code'    => 'rate_limit',
				'message' => __( 'Rate limit exceeded. Please wait before submitting again.', 'urban-cms' ),
			),
			429
		);
	}

	// 4. Validate required fields
	$name = sanitize_text_field( wp_unslash( $_POST['business_name'] ?? '' ) );
	if ( ! $name ) {
		wp_send_json_error(
			array(
				'code'    => 'missing_name',
				'message' => __( 'Business name is required.', 'urban-cms' ),
			),
			422
		);
	}

	$submitter_email = sanitize_email( wp_unslash( $_POST['submitter_email'] ?? '' ) );
	if ( ! $submitter_email || ! is_email( $submitter_email ) ) {
		wp_send_json_error(
			array(
				'code'    => 'invalid_email',
				'message' => __( 'A valid contact email is required.', 'urban-cms' ),
			),
			422
		);
	}

	// 5. Build the draft post
	$post_id = wp_insert_post(
		array(
			'post_title'   => $name,
			'post_content' => wp_kses_post( wp_unslash( $_POST['description'] ?? '' ) ),
			'post_excerpt' => sanitize_text_field( wp_unslash( $_POST['short_description'] ?? '' ) ),
			'post_status'  => 'draft',
			'post_type'    => 'urban_business',
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error(
			array(
				'code'    => 'insert_failed',
				'message' => $post_id->get_error_message(),
			),
			500
		);
	}

	// 6. Save meta
	$meta_map = array(
		'_business_address'   => sanitize_text_field( wp_unslash( $_POST['address'] ?? '' ) ),
		'_business_suite'     => sanitize_text_field( wp_unslash( $_POST['suite'] ?? '' ) ),
		'_business_phone'     => sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) ),
		'_business_email'     => sanitize_email( wp_unslash( $_POST['business_email'] ?? '' ) ),
		'_business_website'   => esc_url_raw( wp_unslash( $_POST['website'] ?? '' ) ),
		'_business_hours'     => sanitize_text_field( wp_unslash( $_POST['hours'] ?? '' ) ),
		'_business_instagram' => esc_url_raw( wp_unslash( $_POST['instagram'] ?? '' ) ),
		'_business_facebook'  => esc_url_raw( wp_unslash( $_POST['facebook'] ?? '' ) ),
		'_business_lat'       => sanitize_text_field( wp_unslash( $_POST['lat'] ?? '' ) ),
		'_business_lng'       => sanitize_text_field( wp_unslash( $_POST['lng'] ?? '' ) ),
		'_submission_ip'      => urban_cms_get_ip(),
		'_submission_email'   => $submitter_email,
		'_submission_date'    => current_time( 'mysql' ),
		'_business_featured'  => '0',
	);

	foreach ( $meta_map as $key => $value ) {
		if ( $value !== '' ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	// 7. Assign taxonomy
	$category_slug = sanitize_text_field( wp_unslash( $_POST['category'] ?? '' ) );
	if ( $category_slug ) {
		$term = get_term_by( 'slug', $category_slug, 'business_category' );
		if ( $term ) {
			wp_set_post_terms( $post_id, array( $term->term_id ), 'business_category' );
		}
	}

	// 8. Handle logo upload
	$attachment_id = urban_cms_handle_image_upload( 'logo', $post_id );
	if ( is_wp_error( $attachment_id ) ) {
		wp_delete_post( $post_id, true );
		wp_send_json_error(
			array(
				'code'    => 'upload_failed',
				'message' => $attachment_id->get_error_message(),
			),
			422
		);
	}
	if ( $attachment_id ) {
		set_post_thumbnail( $post_id, $attachment_id );
	}

	// 9. Admin notification
	urban_cms_notify_admin_new_submission( $post_id, 'business', $name, $submitter_email );

	urban_cms_consume_rate_limit( 'biz' );

	wp_send_json_success(
		array(
			'message' => __( 'Thank you! Your business listing is pending review.', 'urban-cms' ),
			'post_id' => $post_id,
		)
	);
}

/*
=========================================================
	Event Submission Handler
	========================================================= */
function urban_cms_handle_event_submission(): never {
	// 1. Nonce
	if ( ! check_ajax_referer( 'urban_submit_event', 'nonce', false ) ) {
		wp_send_json_error( array( 'code' => 'bad_nonce' ), 403 );
	}

	// 2. Honeypot
	if ( ! empty( $_POST['website_url_hp'] ) ) {
		wp_send_json_success( array( 'honeypot' => true ) );
	}

	// 3. Rate limit
	if ( ! urban_cms_check_rate_limit( 'event' ) ) {
		wp_send_json_error(
			array(
				'code'    => 'rate_limit',
				'message' => __( 'Rate limit exceeded. Please wait before submitting again.', 'urban-cms' ),
			),
			429
		);
	}

	// 4. Validate required fields
	$title = sanitize_text_field( wp_unslash( $_POST['event_title'] ?? '' ) );
	if ( ! $title ) {
		wp_send_json_error(
			array(
				'code'    => 'missing_title',
				'message' => __( 'Event title is required.', 'urban-cms' ),
			),
			422
		);
	}

	$start_date = sanitize_text_field( wp_unslash( $_POST['start_date'] ?? '' ) );
	if ( ! $start_date || ! strtotime( $start_date ) ) {
		wp_send_json_error(
			array(
				'code'    => 'missing_date',
				'message' => __( 'A valid start date is required.', 'urban-cms' ),
			),
			422
		);
	}

	$submitter_email = sanitize_email( wp_unslash( $_POST['submitter_email'] ?? '' ) );
	if ( ! $submitter_email || ! is_email( $submitter_email ) ) {
		wp_send_json_error(
			array(
				'code'    => 'invalid_email',
				'message' => __( 'A valid contact email is required.', 'urban-cms' ),
			),
			422
		);
	}

	// 5. Build draft post
	$post_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_content' => wp_kses_post( wp_unslash( $_POST['description'] ?? '' ) ),
			'post_excerpt' => sanitize_text_field( wp_unslash( $_POST['short_description'] ?? '' ) ),
			'post_status'  => 'draft',
			'post_type'    => 'urban_event',
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		wp_send_json_error(
			array(
				'code'    => 'insert_failed',
				'message' => $post_id->get_error_message(),
			),
			500
		);
	}

	// 6. Save event meta
	$end_date   = sanitize_text_field( wp_unslash( $_POST['end_date'] ?? $start_date ) );
	$start_time = sanitize_text_field( wp_unslash( $_POST['start_time'] ?? '' ) );
	$end_time   = sanitize_text_field( wp_unslash( $_POST['end_time'] ?? '' ) );

	// Validate end date is not before start date
	if ( $end_date && strtotime( $end_date ) < strtotime( $start_date ) ) {
		$end_date = $start_date;
	}

	$meta_map = array(
		'_event_start_date' => $start_date,
		'_event_end_date'   => $end_date,
		'_event_start_time' => $start_time,
		'_event_end_time'   => $end_time,
		'_event_location'   => sanitize_text_field( wp_unslash( $_POST['location'] ?? '' ) ),
		'_event_address'    => sanitize_text_field( wp_unslash( $_POST['address'] ?? '' ) ),
		'_event_cost'       => sanitize_text_field( wp_unslash( $_POST['cost'] ?? '' ) ),
		'_event_rsvp_url'   => esc_url_raw( wp_unslash( $_POST['rsvp_url'] ?? '' ) ),
		'_event_organizer'  => sanitize_text_field( wp_unslash( $_POST['organizer'] ?? '' ) ),
		'_submission_ip'    => urban_cms_get_ip(),
		'_submission_email' => $submitter_email,
		'_submission_date'  => current_time( 'mysql' ),
	);

	foreach ( $meta_map as $key => $value ) {
		if ( $value !== '' ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	// 7. Taxonomy
	$category_slug = sanitize_text_field( wp_unslash( $_POST['category'] ?? '' ) );
	if ( $category_slug ) {
		$term = get_term_by( 'slug', $category_slug, 'event_category' );
		if ( $term ) {
			wp_set_post_terms( $post_id, array( $term->term_id ), 'event_category' );
		}
	}

	// 8. Featured image
	$attachment_id = urban_cms_handle_image_upload( 'event_image', $post_id );
	if ( is_wp_error( $attachment_id ) ) {
		wp_delete_post( $post_id, true );
		wp_send_json_error(
			array(
				'code'    => 'upload_failed',
				'message' => $attachment_id->get_error_message(),
			),
			422
		);
	}
	if ( $attachment_id ) {
		set_post_thumbnail( $post_id, $attachment_id );
	}

	// 9. Admin notification
	urban_cms_notify_admin_new_submission( $post_id, 'event', $title, $submitter_email );

	urban_cms_consume_rate_limit( 'event' );

	wp_send_json_success(
		array(
			'message' => __( 'Thank you! Your event is pending review.', 'urban-cms' ),
			'post_id' => $post_id,
		)
	);
}

/*
=========================================================
	Shared Utilities
	========================================================= */

/**
 * Rate limiting: 3 submissions per type per IP per hour.
 */
function urban_cms_check_rate_limit( string $type ): bool {
	$ip    = urban_cms_get_ip();
	$key   = 'urban_rl_' . $type . '_' . md5( $ip );
	$count = (int) get_transient( $key );
	return $count < 3;
}

function urban_cms_consume_rate_limit( string $type ): void {
	$ip    = urban_cms_get_ip();
	$key   = 'urban_rl_' . $type . '_' . md5( $ip );
	$count = (int) get_transient( $key );
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );
}

/**
 * Get the best available client IP address.
 */
function urban_cms_get_ip(): string {
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- validated via filter_var below
	$ip = wp_unslash( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
	// Trust X-Forwarded-For only if you are behind a known proxy
	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
}

/**
 * Max upload size: lower of WP max and 5 MB.
 */
function urban_cms_max_upload_bytes(): int {
	return min( (int) wp_max_upload_size(), 5 * MB_IN_BYTES );
}

/**
 * Handle an image file upload from $_FILES and attach it to a post.
 *
 * @param  string $field_name  Key in $_FILES.
 * @param  int    $parent_id   Post to attach media to.
 * @return int|WP_Error        Attachment ID or WP_Error. Returns 0 if no file sent.
 */
function urban_cms_handle_image_upload( string $field_name, int $parent_id ) {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce verified in calling AJAX handler
	if ( empty( $_FILES[ $field_name ] ) || $_FILES[ $field_name ]['error'] === UPLOAD_ERR_NO_FILE ) { // phpcs:ignore
		return 0;
	}

	$file = $_FILES[ $field_name ]; // phpcs:ignore
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	if ( $file['error'] !== UPLOAD_ERR_OK ) {
		return new WP_Error( 'upload_error', __( 'File upload error. Please try again.', 'urban-cms' ) );
	}

	// Size check
	$max = urban_cms_max_upload_bytes();
	if ( $file['size'] > $max ) {
		return new WP_Error(
			'file_too_large',
			sprintf(
			/* translators: %s max file size */
				__( 'File exceeds maximum size of %s.', 'urban-cms' ),
				size_format( $max )
			)
		);
	}

	// Type check (MIME, not just extension)
	$allowed_mime = array( 'image/jpeg', 'image/png', 'image/webp', 'image/gif' );
	$finfo        = new finfo( FILEINFO_MIME_TYPE );
	$mime         = $finfo->file( $file['tmp_name'] );
	if ( ! in_array( $mime, $allowed_mime, true ) ) {
		return new WP_Error( 'invalid_type', __( 'Only JPEG, PNG, WebP, or GIF images are accepted.', 'urban-cms' ) );
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$attachment_id = media_handle_upload( $field_name, $parent_id );

	return $attachment_id; // WP_Error or int
}

/**
 * Send admin notification email when a new public submission arrives.
 */
function urban_cms_notify_admin_new_submission( int $post_id, string $type, string $title, string $submitter_email ): void {
	$admin_email = get_option( 'admin_email' );
	$site_name   = get_bloginfo( 'name' );

	$type_label = $type === 'event' ? __( 'Event', 'urban-cms' ) : __( 'Business Listing', 'urban-cms' );
	$edit_url   = admin_url( 'post.php?post=' . $post_id . '&action=edit' );

	$subject = sprintf(
		/* translators: 1: type, 2: title, 3: site name */
		__( '[%3$s] New %1$s Submission: %2$s', 'urban-cms' ),
		$type_label,
		$title,
		$site_name
	);

	$message  = sprintf( __( 'A new %s has been submitted for review.', 'urban-cms' ), strtolower( $type_label ) ) . "\n\n";
	$message .= __( 'Title:', 'urban-cms' ) . ' ' . $title . "\n";
	$message .= __( 'Submitted by:', 'urban-cms' ) . ' ' . $submitter_email . "\n";
	$message .= __( 'Review link:', 'urban-cms' ) . ' ' . $edit_url . "\n";

	wp_mail( $admin_email, $subject, $message );

	/**
	 * Fires after a new public submission notification is sent.
	 *
	 * @param int    $post_id          The draft post ID.
	 * @param string $type             'business' or 'event'.
	 * @param string $submitter_email  The submitter's email address.
	 */
	do_action( 'urban_cms_submission_notify', $post_id, $type, $submitter_email );
}
