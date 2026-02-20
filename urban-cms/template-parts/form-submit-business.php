<?php
/**
 * Template part: Public business listing submission form
 *
 * @package UrbanCMS
 */

$categories = get_terms(
	array(
		'taxonomy'   => 'business_category',
		'hide_empty' => false,
	)
);
?>

<div class="submission-form-wrap" id="business-submission-wrap">

	<!-- Success state (hidden until submission) -->
	<div class="submission-success" id="biz-success" role="alert" aria-live="polite" hidden>
		<div class="submission-success__icon" aria-hidden="true">&#10003;</div>
		<h2><?php esc_html_e( 'Listing Submitted!', 'urban-cms' ); ?></h2>
		<p><?php esc_html_e( 'Thank you for submitting your business. Our team will review your listing and publish it shortly. You\'ll receive a confirmation at the email address you provided.', 'urban-cms' ); ?></p>
		<a href="<?php echo esc_url( get_post_type_archive_link( 'urban_business' ) ); ?>" class="btn btn--primary"><?php esc_html_e( 'Browse the Directory', 'urban-cms' ); ?></a>
	</div>

	<!-- The form -->
	<form id="biz-submit-form"
			class="submission-form"
			enctype="multipart/form-data"
			novalidate
			aria-label="<?php esc_attr_e( 'Submit a business listing', 'urban-cms' ); ?>">

		<?php wp_nonce_field( 'urban_submit_business', 'urban_biz_nonce_field' ); ?>

		<!-- Honeypot (hidden from real users, visible to bots) -->
		<div style="position:absolute;left:-9999px;top:-9999px" aria-hidden="true">
			<label for="website_url_hp"><?php esc_html_e( 'Leave this blank', 'urban-cms' ); ?></label>
			<input type="text" id="website_url_hp" name="website_url_hp" tabindex="-1" autocomplete="off">
		</div>

		<!-- Form-level error message area -->
		<div class="form-message form-message--error" id="biz-form-error" role="alert" aria-live="assertive" hidden></div>

		<!-- ============================================
			Section 1: Business Identity
			============================================ -->
		<fieldset class="form-section">
			<legend class="form-section__legend"><?php esc_html_e( 'Business Identity', 'urban-cms' ); ?></legend>

			<div class="form-grid">
				<div class="form-field form-field--full">
					<label class="form-label form-label--required" for="biz-name">
						<?php esc_html_e( 'Business Name', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="biz-name"
							name="business_name"
							class="form-input"
							required
							maxlength="150"
							placeholder="<?php esc_attr_e( 'e.g. Pike Place Coffee Roasters', 'urban-cms' ); ?>">
					<span class="form-error" id="biz-name-error" role="alert"></span>
				</div>

				<div class="form-field">
					<label class="form-label form-label--required" for="biz-category">
						<?php esc_html_e( 'Business Category', 'urban-cms' ); ?>
					</label>
					<select id="biz-category" name="category" class="form-input" required>
						<option value=""><?php esc_html_e( '— Select a category —', 'urban-cms' ); ?></option>
						<?php if ( $categories && ! is_wp_error( $categories ) ) : ?>
							<?php foreach ( $categories as $cat ) : ?>
								<option value="<?php echo esc_attr( $cat->slug ); ?>"><?php echo esc_html( $cat->name ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
					<span class="form-error" id="biz-category-error" role="alert"></span>
				</div>

				<div class="form-field">
					<label class="form-label" for="biz-logo">
						<?php esc_html_e( 'Logo / Photo', 'urban-cms' ); ?>
						<span class="form-hint"><?php esc_html_e( 'JPEG, PNG, or WebP · max 5 MB', 'urban-cms' ); ?></span>
					</label>
					<input type="file"
							id="biz-logo"
							name="logo"
							class="form-input form-input--file"
							accept="image/jpeg,image/png,image/webp">
					<div class="file-preview" id="biz-logo-preview" hidden>
						<img src="" alt="" id="biz-logo-preview-img">
						<button type="button" class="file-preview__remove" data-target="biz-logo" aria-label="<?php esc_attr_e( 'Remove image', 'urban-cms' ); ?>">&times;</button>
					</div>
					<span class="form-error" id="biz-logo-error" role="alert"></span>
				</div>
			</div>

			<div class="form-field">
				<label class="form-label" for="biz-short-desc">
					<?php esc_html_e( 'Short Description', 'urban-cms' ); ?>
					<span class="form-hint"><?php esc_html_e( 'One sentence shown in directory listings (max 160 chars)', 'urban-cms' ); ?></span>
				</label>
				<input type="text"
						id="biz-short-desc"
						name="short_description"
						class="form-input"
						maxlength="160"
						placeholder="<?php esc_attr_e( 'A specialty coffee roaster and café in the heart of downtown.', 'urban-cms' ); ?>">
			</div>

			<div class="form-field">
				<label class="form-label" for="biz-description">
					<?php esc_html_e( 'Full Description', 'urban-cms' ); ?>
					<span class="form-hint"><?php esc_html_e( 'Shown on your full business profile page.', 'urban-cms' ); ?></span>
				</label>
				<textarea id="biz-description"
							name="description"
							class="form-input form-input--textarea"
							rows="5"
							placeholder="<?php esc_attr_e( 'Tell visitors about your business, your story, what makes you unique…', 'urban-cms' ); ?>"></textarea>
			</div>
		</fieldset>

		<!-- ============================================
			Section 2: Location
			============================================ -->
		<fieldset class="form-section">
			<legend class="form-section__legend"><?php esc_html_e( 'Location', 'urban-cms' ); ?></legend>

			<div class="form-grid">
				<div class="form-field">
					<label class="form-label form-label--required" for="biz-address">
						<?php esc_html_e( 'Street Address', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="biz-address"
							name="address"
							class="form-input"
							required
							placeholder="<?php esc_attr_e( '123 Main Street', 'urban-cms' ); ?>">
					<span class="form-error" id="biz-address-error" role="alert"></span>
				</div>

				<div class="form-field">
					<label class="form-label" for="biz-suite">
						<?php esc_html_e( 'Suite / Floor', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="biz-suite"
							name="suite"
							class="form-input"
							placeholder="<?php esc_attr_e( 'Suite 200', 'urban-cms' ); ?>">
				</div>
			</div>

			<div class="form-grid">
				<div class="form-field">
					<label class="form-label" for="biz-lat">
						<?php esc_html_e( 'Latitude', 'urban-cms' ); ?>
						<span class="form-hint"><?php esc_html_e( 'Optional — for map pin. Use Google Maps to find coordinates.', 'urban-cms' ); ?></span>
					</label>
					<input type="text"
							id="biz-lat"
							name="lat"
							class="form-input"
							pattern="-?\d{1,3}\.\d+"
							placeholder="47.6062">
				</div>
				<div class="form-field">
					<label class="form-label" for="biz-lng">
						<?php esc_html_e( 'Longitude', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="biz-lng"
							name="lng"
							class="form-input"
							pattern="-?\d{1,3}\.\d+"
							placeholder="-122.3321">
				</div>
			</div>
		</fieldset>

		<!-- ============================================
			Section 3: Contact & Hours
			============================================ -->
		<fieldset class="form-section">
			<legend class="form-section__legend"><?php esc_html_e( 'Contact &amp; Hours', 'urban-cms' ); ?></legend>

			<div class="form-grid">
				<div class="form-field">
					<label class="form-label" for="biz-phone">
						<?php esc_html_e( 'Phone Number', 'urban-cms' ); ?>
					</label>
					<input type="tel"
							id="biz-phone"
							name="phone"
							class="form-input"
							placeholder="(206) 555-0100">
				</div>

				<div class="form-field">
					<label class="form-label" for="biz-email-public">
						<?php esc_html_e( 'Public Email', 'urban-cms' ); ?>
						<span class="form-hint"><?php esc_html_e( 'Shown on your public listing.', 'urban-cms' ); ?></span>
					</label>
					<input type="email"
							id="biz-email-public"
							name="business_email"
							class="form-input"
							placeholder="hello@mybusiness.com">
				</div>

				<div class="form-field">
					<label class="form-label" for="biz-website">
						<?php esc_html_e( 'Website URL', 'urban-cms' ); ?>
					</label>
					<input type="url"
							id="biz-website"
							name="website"
							class="form-input"
							placeholder="https://www.mybusiness.com">
				</div>

				<div class="form-field">
					<label class="form-label" for="biz-hours">
						<?php esc_html_e( 'Hours', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="biz-hours"
							name="hours"
							class="form-input"
							placeholder="<?php esc_attr_e( 'Mon–Fri 9am–6pm, Sat 10am–4pm', 'urban-cms' ); ?>">
				</div>
			</div>
		</fieldset>

		<!-- ============================================
			Section 4: Social Media
			============================================ -->
		<fieldset class="form-section">
			<legend class="form-section__legend"><?php esc_html_e( 'Social Media', 'urban-cms' ); ?></legend>

			<div class="form-grid">
				<div class="form-field">
					<label class="form-label" for="biz-instagram">
						<?php esc_html_e( 'Instagram URL', 'urban-cms' ); ?>
					</label>
					<input type="url"
							id="biz-instagram"
							name="instagram"
							class="form-input"
							placeholder="https://www.instagram.com/mybusiness">
				</div>
				<div class="form-field">
					<label class="form-label" for="biz-facebook">
						<?php esc_html_e( 'Facebook URL', 'urban-cms' ); ?>
					</label>
					<input type="url"
							id="biz-facebook"
							name="facebook"
							class="form-input"
							placeholder="https://www.facebook.com/mybusiness">
				</div>
			</div>
		</fieldset>

		<!-- ============================================
			Section 5: Your Contact Info (not public)
			============================================ -->
		<fieldset class="form-section">
			<legend class="form-section__legend">
				<?php esc_html_e( 'Your Contact Info', 'urban-cms' ); ?>
				<span class="form-section__legend-note"><?php esc_html_e( 'Not shown publicly — for our records only.', 'urban-cms' ); ?></span>
			</legend>

			<div class="form-grid">
				<div class="form-field">
					<label class="form-label form-label--required" for="biz-submitter-name">
						<?php esc_html_e( 'Your Name', 'urban-cms' ); ?>
					</label>
					<input type="text"
							id="biz-submitter-name"
							name="submitter_name"
							class="form-input"
							required
							placeholder="Jane Smith">
					<span class="form-error" id="biz-submitter-name-error" role="alert"></span>
				</div>

				<div class="form-field">
					<label class="form-label form-label--required" for="biz-submitter-email">
						<?php esc_html_e( 'Your Email', 'urban-cms' ); ?>
						<span class="form-hint"><?php esc_html_e( 'We\'ll send a confirmation here.', 'urban-cms' ); ?></span>
					</label>
					<input type="email"
							id="biz-submitter-email"
							name="submitter_email"
							class="form-input"
							required
							placeholder="jane@example.com">
					<span class="form-error" id="biz-submitter-email-error" role="alert"></span>
				</div>
			</div>
		</fieldset>

		<!-- Terms checkbox -->
		<div class="form-field" style="margin-top:var(--space-2)">
			<label class="form-checkbox-label">
				<input type="checkbox" id="biz-terms" name="terms" required class="form-checkbox">
				<span>
					<?php
					printf(
						/* translators: %s: terms link */
						esc_html__( 'I confirm that this information is accurate and I have authority to list this business. I agree to the %s.', 'urban-cms' ),
						'<a href="' . esc_url( home_url( '/terms' ) ) . '" target="_blank">' . esc_html__( 'listing terms', 'urban-cms' ) . '</a>'
					);
					?>
				</span>
			</label>
			<span class="form-error" id="biz-terms-error" role="alert"></span>
		</div>

		<div style="margin-top:var(--space-6)">
			<button type="submit" class="btn btn--primary btn--lg" id="biz-submit-btn">
				<span class="btn-label"><?php esc_html_e( 'Submit Listing for Review', 'urban-cms' ); ?></span>
				<span class="btn-loading" hidden aria-hidden="true">
					<span class="spinner" aria-hidden="true"></span>
					<?php esc_html_e( 'Submitting…', 'urban-cms' ); ?>
				</span>
			</button>
		</div>
	</form>
</div>
