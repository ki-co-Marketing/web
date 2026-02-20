<?php
/**
 * Template part: Public event calendar submission form
 *
 * @package UrbanCMS
 */

$categories = get_terms(
	array(
		'taxonomy'   => 'event_category',
		'hide_empty' => false,
	)
);
$min_date   = gmdate( 'Y-m-d' ); // Can't submit events in the past
?>

<div class="submission-form-wrap" id="event-submission-wrap">

	<!-- Success state -->
	<div class="submission-success" id="event-success" role="alert" aria-live="polite" hidden>
		<div class="submission-success__icon" aria-hidden="true">&#10003;</div>
		<h2><?php esc_html_e( 'Event Submitted!', 'urban-cms' ); ?></h2>
		<p><?php esc_html_e( 'Thank you for submitting your event. Our team will review it and add it to the calendar shortly. You\'ll receive a confirmation at the email address you provided.', 'urban-cms' ); ?></p>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'urban_event' ) ); ?>" class="btn btn--primary"><?php esc_html_e( 'View Events Calendar', 'urban-cms' ); ?></a>
	</div>

	<!-- The form -->
	<form id="event-submit-form"
			class="submission-form"
			enctype="multipart/form-data"
			novalidate
			aria-label="<?php esc_attr_e( 'Submit an event', 'urban-cms' ); ?>">

		<?php wp_nonce_field( 'urban_submit_event', 'urban_event_nonce_field' ); ?>

		<!-- Honeypot -->
		<div style="position:absolute;left:-9999px;top:-9999px" aria-hidden="true">
			<label for="website_url_hp_ev"><?php esc_html_e( 'Leave this blank', 'urban-cms' ); ?></label>
			<input type="text" id="website_url_hp_ev" name="website_url_hp" tabindex="-1" autocomplete="off">
		</div>

		<!-- Form error area -->
		<div class="form-message form-message--error" id="event-form-error" role="alert" aria-live="assertive" hidden></div>

		<!-- ============================================
			Section 1: Event Details
			============================================ -->
		<fieldset class="form-section">
			<legend class="form-section__legend"><?php esc_html_e( 'Event Details', 'urban-cms' ); ?></legend>

			<div class="form-field">
				<label class="form-label form-label--required" for="event-title">
					<?php esc_html_e( 'Event Title', 'urban-cms' ); ?>
				</label>
				<input type="text"
						id="event-title"
						name="event_title"
						class="form-input"
						required
						maxlength="150"
						placeholder="<?php esc_attr_e( 'e.g. Downtown Summer Street Fair 2026', 'urban-cms' ); ?>">
				<span class="form-error" id="event-title-error" role="alert"></span>
			</div>

			<div class="form-grid">
				<div class="form-field">
					<label class="form-label" for="event-category">
						<?php esc_html_e( 'Event Category', 'urban-cms' ); ?>
					</label>
					<select id="event-category" name="category" class="form-input">
						<option value=""><?php esc_html_e( '— Select a category —', 'urban-cms' ); ?></option>
						<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
							<?php foreach ( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</div>

				<div class="form-field">
					<label class="form-label" for="event-image">
						<?php esc_html_e( 'Event Image', 'urban-cms' ); ?>
						<span class="form-hint"><?php esc_html_e( 'JPEG, PNG, or WebP · max 5 MB', 'urban-cms' ); ?></span>
					</label>
					<input type="file"
							id="event-image"
							name="event_image"
							class="form-input form-input--file"
							accept="image/jpeg,image/png,image/webp">
					<div class="file-preview" id="event-image-preview" hidden>
						<img src="" alt="" id="event-image-preview-img">
						<button type="button" class="file-preview__remove" data-target="event-image" aria-label="<?php esc_attr_e( 'Remove image', 'urban-cms' ); ?>">&times;</button>
					</div>
					<span class="form-error" id="event-image-error" role="alert"></span>
				</div>
			</div>

			<div class="form-field">
				<label class="form-label" for="event-short-desc">
					<?php esc_html_e( 'Short Description', 'urban-cms' ); ?>
					<span class="form-hint"><?php esc_html_e( 'One to two sentences shown in calendar listings (max 200 chars)', 'urban-cms' ); ?></span>
				</label>
				<input type="text"
						id="event-short-desc"
						name="short_description"
						class="form-input"
						maxlength="200"
						placeholder="<?php esc_attr_e( 'A free outdoor street fair featuring local vendors, live music, and food.', 'urban-cms' ); ?>">
			</div>

			<div class="form-field">
				<label class="form-label" for="event-description">
					<?php esc_html_e( 'Full Description', 'urban-cms' ); ?>
				</label>
				<textarea id="event-description"
							name="description"
							class="form-input form-input--textarea"
							rows="6"
							placeholder="<?php esc_attr_e( 'Provide all the details visitors need to know about your event…', 'urban-cms' ); ?>"></textarea>
			</div>
		</fieldset>

		<!-- ============================================
			Section 2: Date & Time
			============================================ -->
		<fieldset class="form-section">
			<legend class="form-section__legend"><?php esc_html_e( 'Date &amp; Time', 'urban-cms' ); ?></legend>

			<div class="form-grid">
				<div class="form-field">
					<label class="form-label form-label--required" for="event-start-date">
						<?php esc_html_e( 'Start Date', 'urban-cms' ); ?>
					</label>
					<input type="date"
							id="event-start-date"
							name="start_date"
							class="form-input"
							required
							min="<?php echo esc_attr( $min_date ); ?>">
					<span class="form-error" id="event-start-date-error" role="alert"></span>
				</div>

				<div class="form-field">
					<label class="form-label" for="event-end-date">
						<?php esc_html_e( 'End Date', 'urban-cms' ); ?>
						<span class="form-hint"><?php esc_html_e( 'If multi-day event', 'urban-cms' ); ?></span>
					</label>
					<input type="date"
							id="event-end-date"
							name="end_date"
							class="form-input"
							min="<?php echo esc_attr( $min_date ); ?>">
				</div>

				<div class="form-field">
					<label class="form-label" for="event-start-time">
						<?php esc_html_e( 'Start Time', 'urban-cms' ); ?>
					</label>
					<input type="time" id="event-start-time" name="start_time" class="form-input">
				</div>

				<div class="form-field">
					<label class="form-label" for="event-end-time">
						<?php esc_html_e( 'End Time', 'urban-cms' ); ?>
					</label>
					<input type="time" id="event-end-time" name="end_time" class="form-input">
				</div>
			</div>
		</fieldset>

		<!-- ============================================
			Section 3: Location & Logistics
			============================================ -->
		<fieldset class="form-section">
			<legend class="form-section__legend"><?php esc_html_e( 'Location &amp; Logistics', 'urban-cms' ); ?></legend>

			<div class="form-grid">
				<div class="form-field">
					<label class="form-label" for="event-location">
						<?php esc_html_e( 'Venue / Location Name', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="event-location"
							name="location"
							class="form-input"
							placeholder="<?php esc_attr_e( 'e.g. Westlake Park', 'urban-cms' ); ?>">
				</div>

				<div class="form-field">
					<label class="form-label" for="event-address">
						<?php esc_html_e( 'Street Address', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="event-address"
							name="address"
							class="form-input"
							placeholder="<?php esc_attr_e( '400 Pine St, Seattle, WA 98101', 'urban-cms' ); ?>">
				</div>

				<div class="form-field">
					<label class="form-label" for="event-cost">
						<?php esc_html_e( 'Admission / Cost', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="event-cost"
							name="cost"
							class="form-input"
							placeholder="<?php esc_attr_e( 'Free, $10, $5–$20, etc.', 'urban-cms' ); ?>">
				</div>

				<div class="form-field">
					<label class="form-label" for="event-organizer">
						<?php esc_html_e( 'Organizer / Host', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="event-organizer"
							name="organizer"
							class="form-input"
							placeholder="<?php esc_attr_e( 'Organization or individual name', 'urban-cms' ); ?>">
				</div>
			</div>

			<div class="form-field">
				<label class="form-label" for="event-rsvp">
					<?php esc_html_e( 'RSVP / Ticket URL', 'urban-cms' ); ?>
					<span class="form-hint"><?php esc_html_e( 'Eventbrite, a ticketing page, or any external registration link.', 'urban-cms' ); ?></span>
				</label>
				<input type="url"
						id="event-rsvp"
						name="rsvp_url"
						class="form-input"
						placeholder="https://eventbrite.com/...">
			</div>
		</fieldset>

		<!-- ============================================
			Section 4: Your Contact Info
			============================================ -->
		<fieldset class="form-section">
			<legend class="form-section__legend">
				<?php esc_html_e( 'Your Contact Info', 'urban-cms' ); ?>
				<span class="form-section__legend-note"><?php esc_html_e( 'Not shown publicly — for review purposes only.', 'urban-cms' ); ?></span>
			</legend>

			<div class="form-grid">
				<div class="form-field">
					<label class="form-label form-label--required" for="event-submitter-name">
						<?php esc_html_e( 'Your Name', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="event-submitter-name"
							name="submitter_name"
							class="form-input"
							required
							placeholder="Jane Smith">
					<span class="form-error" id="event-submitter-name-error" role="alert"></span>
				</div>

				<div class="form-field">
					<label class="form-label form-label--required" for="event-submitter-email">
						<?php esc_html_e( 'Your Email', 'urban-cms' ); ?>
						<span class="form-hint"><?php esc_html_e( 'We\'ll send a confirmation here.', 'urban-cms' ); ?></span>
					</label>
					<input type="email"
							id="event-submitter-email"
							name="submitter_email"
							class="form-input"
							required
							placeholder="jane@example.com">
					<span class="form-error" id="event-submitter-email-error" role="alert"></span>
				</div>
			</div>
		</fieldset>

		<!-- Terms checkbox -->
		<div class="form-field" style="margin-top:var(--space-2)">
			<label class="form-checkbox-label">
				<input type="checkbox" id="event-terms" name="terms" required class="form-checkbox">
				<span>
					<?php
					printf(
						esc_html__( 'I confirm this event information is accurate and I have authority to submit it. I agree to the %s.', 'urban-cms' ),
						'<a href="' . esc_url( home_url( '/terms' ) ) . '" target="_blank">' . esc_html__( 'submission guidelines', 'urban-cms' ) . '</a>'
					);
					?>
				</span>
			</label>
			<span class="form-error" id="event-terms-error" role="alert"></span>
		</div>

		<div style="margin-top:var(--space-6)">
			<button type="submit" class="btn btn--primary btn--lg" id="event-submit-btn">
				<span class="btn-label"><?php esc_html_e( 'Submit Event for Review', 'urban-cms' ); ?></span>
				<span class="btn-loading" hidden aria-hidden="true">
					<span class="spinner" aria-hidden="true"></span>
					<?php esc_html_e( 'Submitting…', 'urban-cms' ); ?>
				</span>
			</button>
		</div>
	</form>
</div>
