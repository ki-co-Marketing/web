<?php
/**
 * Newsletter Signup Integration
 *
 * Provides a lightweight subscriber store (wp_options-based for < 1000 subs,
 * custom DB table for larger lists). Fires action hooks so third-party
 * integrations (Mailchimp, Constant Contact, Klaviyo) can plug in.
 *
 * Usage in templates:
 *   get_template_part( 'template-parts/newsletter', 'signup' );
 *
 * To integrate Mailchimp (example):
 *   add_action( 'urban_cms_newsletter_subscribe', function( $email, $name, $lists ) {
 *       // Call Mailchimp API here
 *   }, 10, 3 );
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================
   Database table creation (on theme activation)
   ========================================================= */
add_action( 'after_switch_theme', 'urban_cms_create_subscribers_table' );
register_activation_hook( __FILE__,  'urban_cms_create_subscribers_table' );

function urban_cms_create_subscribers_table(): void {
    global $wpdb;
    $table   = $wpdb->prefix . 'urban_subscribers';
    $charset = $wpdb->get_charset_collate();

    if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) === $table ) {
        return; // Already exists
    }

    $sql = "CREATE TABLE $table (
        id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        email       VARCHAR(200)    NOT NULL,
        first_name  VARCHAR(100)    DEFAULT '',
        last_name   VARCHAR(100)    DEFAULT '',
        lists       VARCHAR(500)    DEFAULT 'general',
        status      VARCHAR(20)     DEFAULT 'pending',
        source      VARCHAR(100)    DEFAULT 'website',
        token       VARCHAR(64)     DEFAULT '',
        subscribed  DATETIME        DEFAULT NULL,
        created_at  DATETIME        NOT NULL,
        ip_address  VARCHAR(45)     DEFAULT '',
        PRIMARY KEY  (id),
        UNIQUE KEY email (email),
        KEY status (status)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

/* =========================================================
   AJAX Handlers
   ========================================================= */
add_action( 'wp_ajax_urban_newsletter_subscribe',        'urban_cms_handle_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_urban_newsletter_subscribe', 'urban_cms_handle_newsletter_subscribe' );

add_action( 'wp_ajax_urban_newsletter_confirm',        'urban_cms_handle_newsletter_confirm' );
add_action( 'wp_ajax_nopriv_urban_newsletter_confirm', 'urban_cms_handle_newsletter_confirm' );

function urban_cms_handle_newsletter_subscribe(): never {
    // Nonce
    if ( ! check_ajax_referer( 'urban_newsletter', 'nonce', false ) ) {
        wp_send_json_error( [ 'code' => 'bad_nonce' ], 403 );
    }

    // Honeypot
    if ( ! empty( $_POST['url_hp'] ) ) {
        wp_send_json_success( [ 'honeypot' => true ] );
    }

    // Rate limit: 5 signups per IP per hour
    $ip  = urban_cms_get_ip();
    $rl_key = 'urban_nl_rl_' . md5( $ip );
    if ( (int) get_transient( $rl_key ) >= 5 ) {
        wp_send_json_error( [ 'code' => 'rate_limit', 'message' => __( 'Too many requests. Please try again later.', 'urban-cms' ) ], 429 );
    }

    $email = sanitize_email( $_POST['email'] ?? '' );
    if ( ! $email || ! is_email( $email ) ) {
        wp_send_json_error( [ 'code' => 'invalid_email', 'message' => __( 'Please enter a valid email address.', 'urban-cms' ) ], 422 );
    }

    $first_name = sanitize_text_field( $_POST['first_name'] ?? '' );
    $last_name  = sanitize_text_field( $_POST['last_name']  ?? '' );
    $lists      = sanitize_text_field( $_POST['lists']      ?? 'general' );
    $source     = sanitize_text_field( $_POST['source']     ?? 'website' );

    global $wpdb;
    $table = $wpdb->prefix . 'urban_subscribers';

    // Check if already subscribed
    $existing = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE email = %s", $email ) );

    if ( $existing ) {
        if ( $existing->status === 'active' ) {
            wp_send_json_success( [
                'already_subscribed' => true,
                'message'            => __( 'You\'re already subscribed! Check your inbox for our latest updates.', 'urban-cms' ),
            ] );
        }
        // Re-send confirmation for pending
        $token = $existing->token;
    } else {
        $token = wp_generate_password( 32, false );
        $wpdb->insert( $table, [
            'email'      => $email,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'lists'      => $lists,
            'status'     => 'pending',
            'source'     => $source,
            'token'      => $token,
            'created_at' => current_time( 'mysql' ),
            'ip_address' => $ip,
        ], [ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ] );
    }

    // Send double-opt-in confirmation email
    urban_cms_send_confirmation_email( $email, $first_name, $token );

    set_transient( $rl_key, (int) get_transient( $rl_key ) + 1, HOUR_IN_SECONDS );

    wp_send_json_success( [
        'message' => __( 'Thanks! Please check your email to confirm your subscription.', 'urban-cms' ),
    ] );
}

/**
 * Confirm subscription via tokenized URL.
 * Called when a user clicks the confirmation link: ?action=urban_newsletter_confirm&token=...
 */
add_action( 'init', function () {
    if ( isset( $_GET['urban_confirm_sub'] ) ) {
        $token = sanitize_text_field( $_GET['urban_confirm_sub'] );
        if ( $token ) {
            urban_cms_confirm_subscription( $token );
        }
    }
} );

function urban_cms_confirm_subscription( string $token ): void {
    global $wpdb;
    $table = $wpdb->prefix . 'urban_subscribers';

    $sub = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE token = %s AND status = 'pending'", $token ) );

    if ( ! $sub ) {
        // Already confirmed or invalid token — redirect gracefully
        wp_safe_redirect( add_query_arg( 'sub_status', 'invalid', home_url( '/' ) ) );
        exit;
    }

    $wpdb->update(
        $table,
        [ 'status' => 'active', 'subscribed' => current_time( 'mysql' ), 'token' => '' ],
        [ 'id' => $sub->id ],
        [ '%s', '%s', '%s' ],
        [ '%d' ]
    );

    /**
     * Fires when a subscriber confirms their email address.
     *
     * @param string $email      The confirmed email.
     * @param string $first_name The subscriber's first name.
     * @param string $lists      Comma-separated list slugs.
     */
    do_action( 'urban_cms_newsletter_subscribe', $sub->email, $sub->first_name, $sub->lists );

    wp_safe_redirect( add_query_arg( 'sub_status', 'confirmed', home_url( '/' ) ) );
    exit;
}

function urban_cms_handle_newsletter_confirm(): never {
    // Handled via init hook above; this is a fallback AJAX endpoint
    wp_send_json_error( [ 'code' => 'use_link' ], 400 );
}

/* =========================================================
   Email sending
   ========================================================= */
function urban_cms_send_confirmation_email( string $email, string $name, string $token ): void {
    $confirm_url = add_query_arg( 'urban_confirm_sub', $token, home_url( '/' ) );
    $site_name   = get_bloginfo( 'name' );
    $greeting    = $name ? sprintf( __( 'Hi %s,', 'urban-cms' ), $name ) : __( 'Hi there,', 'urban-cms' );

    $subject = sprintf( __( 'Confirm your subscription to %s', 'urban-cms' ), $site_name );

    $message  = $greeting . "\n\n";
    $message .= sprintf( __( 'Thank you for subscribing to updates from %s.', 'urban-cms' ), $site_name ) . "\n\n";
    $message .= __( 'Please click the link below to confirm your email address:', 'urban-cms' ) . "\n\n";
    $message .= $confirm_url . "\n\n";
    $message .= __( 'If you didn\'t sign up for this, you can safely ignore this email.', 'urban-cms' ) . "\n\n";
    $message .= '— ' . $site_name;

    $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

    wp_mail( $email, $subject, $message, $headers );

    /**
     * Fires after the confirmation email is sent.
     *
     * @param string $email       Subscriber email.
     * @param string $token       Confirmation token.
     * @param string $confirm_url The confirmation URL.
     */
    do_action( 'urban_cms_newsletter_confirmation_sent', $email, $token, $confirm_url );
}

/* =========================================================
   Enqueue newsletter script where form is present
   ========================================================= */
add_action( 'wp_footer', function () {
    if ( ! is_admin() ) {
        wp_enqueue_script(
            'urban-cms-newsletter',
            URBAN_CMS_URI . '/assets/js/newsletter.js',
            [ 'urban-cms' ],
            URBAN_CMS_VERSION,
            true
        );
        wp_localize_script( 'urban-cms-newsletter', 'urbanNewsletter', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'urban_newsletter' ),
        ] );
    }
}, 5 );

/* =========================================================
   Admin: Subscribers list page
   ========================================================= */
add_action( 'admin_menu', function () {
    add_submenu_page(
        'options-general.php',
        __( 'Newsletter Subscribers', 'urban-cms' ),
        __( 'Subscribers', 'urban-cms' ),
        'manage_options',
        'urban-subscribers',
        'urban_cms_admin_subscribers_page'
    );
} );

function urban_cms_admin_subscribers_page(): void {
    global $wpdb;
    $table = $wpdb->prefix . 'urban_subscribers';
    $rows  = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC LIMIT 500" );

    echo '<div class="wrap"><h1>' . esc_html__( 'Newsletter Subscribers', 'urban-cms' ) . '</h1>';

    $active  = count( array_filter( (array) $rows, fn( $r ) => $r->status === 'active' ) );
    $pending = count( array_filter( (array) $rows, fn( $r ) => $r->status === 'pending' ) );

    echo '<p>' . sprintf( esc_html__( '%d active, %d pending confirmation.', 'urban-cms' ), $active, $pending ) . '</p>';

    // Export CSV link
    echo '<p><a href="' . esc_url( admin_url( 'options-general.php?page=urban-subscribers&export=1' ) ) . '" class="button">' . esc_html__( 'Export CSV', 'urban-cms' ) . '</a></p>';

    if ( ! empty( $_GET['export'] ) && current_user_can( 'manage_options' ) ) {
        urban_cms_export_subscribers_csv( $rows );
    }

    echo '<table class="wp-list-table widefat fixed striped"><thead><tr>';
    echo '<th>' . esc_html__( 'Email', 'urban-cms' )      . '</th>';
    echo '<th>' . esc_html__( 'Name', 'urban-cms' )       . '</th>';
    echo '<th>' . esc_html__( 'Status', 'urban-cms' )     . '</th>';
    echo '<th>' . esc_html__( 'Lists', 'urban-cms' )      . '</th>';
    echo '<th>' . esc_html__( 'Source', 'urban-cms' )     . '</th>';
    echo '<th>' . esc_html__( 'Subscribed', 'urban-cms' ) . '</th>';
    echo '</tr></thead><tbody>';

    foreach ( (array) $rows as $row ) {
        echo '<tr>';
        echo '<td>' . esc_html( $row->email )      . '</td>';
        echo '<td>' . esc_html( trim( $row->first_name . ' ' . $row->last_name ) ) . '</td>';
        echo '<td>' . esc_html( $row->status )     . '</td>';
        echo '<td>' . esc_html( $row->lists )      . '</td>';
        echo '<td>' . esc_html( $row->source )     . '</td>';
        echo '<td>' . esc_html( $row->subscribed ?: '—' ) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
}

function urban_cms_export_subscribers_csv( array $rows ): void {
    header( 'Content-Type: text/csv; charset=UTF-8' );
    header( 'Content-Disposition: attachment; filename="subscribers-' . date( 'Y-m-d' ) . '.csv"' );
    $out = fopen( 'php://output', 'w' );
    fputcsv( $out, [ 'Email', 'First Name', 'Last Name', 'Status', 'Lists', 'Source', 'Subscribed', 'Created' ] );
    foreach ( $rows as $row ) {
        fputcsv( $out, [ $row->email, $row->first_name, $row->last_name, $row->status, $row->lists, $row->source, $row->subscribed, $row->created_at ] );
    }
    fclose( $out );
    exit;
}
