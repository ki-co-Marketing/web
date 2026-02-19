<?php
/**
 * Theme Customizer Settings
 * Allows per-district branding and configuration without touching code.
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', 'urban_cms_customizer' );

function urban_cms_customizer( WP_Customize_Manager $wp_customize ): void {

    /* -------------------------------------------------------
       Panel: District Settings
       ------------------------------------------------------- */
    $wp_customize->add_panel( 'urban_district', [
        'title'       => __( 'District Settings', 'urban-cms' ),
        'description' => __( 'Configure your urban place management organization settings.', 'urban-cms' ),
        'priority'    => 30,
    ] );

    // ---- Section: District Identity ----
    $wp_customize->add_section( 'urban_identity', [
        'title'  => __( 'District Identity', 'urban-cms' ),
        'panel'  => 'urban_district',
    ] );

    urban_cms_add_setting( $wp_customize, 'district_name',     __( 'Organization Name', 'urban-cms' ),     'text',     'Downtown District Alliance' );
    urban_cms_add_setting( $wp_customize, 'district_tagline',  __( 'Tagline / Slogan', 'urban-cms' ),      'text',     'Building a vibrant downtown.' );
    urban_cms_add_setting( $wp_customize, 'district_phone',    __( 'Phone Number', 'urban-cms' ),          'text',     '' );
    urban_cms_add_setting( $wp_customize, 'district_email',    __( 'Contact Email', 'urban-cms' ),         'email',    '' );
    urban_cms_add_setting( $wp_customize, 'district_address',  __( 'Office Address', 'urban-cms' ),        'textarea', '' );
    urban_cms_add_setting( $wp_customize, 'district_ein',      __( 'EIN / Tax ID (optional)', 'urban-cms' ), 'text',   '' );

    // ---- Section: Brand Colors ----
    $wp_customize->add_section( 'urban_colors', [
        'title'  => __( 'Brand Colors', 'urban-cms' ),
        'panel'  => 'urban_district',
    ] );

    $colors = [
        'color_primary'       => [ __( 'Primary Color',       'urban-cms' ), '#1a3a5c' ],
        'color_primary_light' => [ __( 'Primary Light',       'urban-cms' ), '#2563a8' ],
        'color_accent'        => [ __( 'Accent / Gold Color', 'urban-cms' ), '#e8a020' ],
    ];

    foreach ( $colors as $key => [ $label, $default ] ) {
        $wp_customize->add_setting( $key, [
            'default'           => $default,
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ] );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, [
            'label'   => $label,
            'section' => 'urban_colors',
        ] ) );
    }

    // Output custom CSS variables from color settings
    add_action( 'wp_head', 'urban_cms_output_color_css', 99 );

    // ---- Section: Map Settings ----
    $wp_customize->add_section( 'urban_map', [
        'title'  => __( 'District Map', 'urban-cms' ),
        'panel'  => 'urban_district',
    ] );

    urban_cms_add_setting( $wp_customize, 'district_map_lat',  __( 'Map Center Latitude', 'urban-cms' ),  'text', '47.6062', 'urban_map' );
    urban_cms_add_setting( $wp_customize, 'district_map_lng',  __( 'Map Center Longitude', 'urban-cms' ), 'text', '-122.3321', 'urban_map' );
    urban_cms_add_setting( $wp_customize, 'district_map_zoom', __( 'Default Zoom Level', 'urban-cms' ),   'text', '15', 'urban_map' );

    // ---- Section: District Stats ----
    $wp_customize->add_section( 'urban_stats', [
        'title'  => __( 'Homepage Stats', 'urban-cms' ),
        'panel'  => 'urban_district',
    ] );

    for ( $i = 1; $i <= 4; $i++ ) {
        urban_cms_add_setting( $wp_customize, "stat_{$i}_number", sprintf( __( 'Stat %d: Number', 'urban-cms' ), $i ), 'text', '', 'urban_stats' );
        urban_cms_add_setting( $wp_customize, "stat_{$i}_label",  sprintf( __( 'Stat %d: Label', 'urban-cms' ),  $i ), 'text', '', 'urban_stats' );
    }

    // ---- Section: Hero / Banner ----
    $wp_customize->add_section( 'urban_hero', [
        'title'  => __( 'Homepage Hero', 'urban-cms' ),
        'panel'  => 'urban_district',
    ] );

    urban_cms_add_setting( $wp_customize, 'hero_heading',     __( 'Hero Heading', 'urban-cms' ),   'text',     '', 'urban_hero' );
    urban_cms_add_setting( $wp_customize, 'hero_subheading',  __( 'Hero Subheading', 'urban-cms' ), 'textarea', '', 'urban_hero' );
    urban_cms_add_setting( $wp_customize, 'hero_cta_text',    __( 'CTA Button Text', 'urban-cms' ), 'text',     'Explore the District', 'urban_hero' );
    urban_cms_add_setting( $wp_customize, 'hero_cta_url',     __( 'CTA Button URL', 'urban-cms' ),  'url',      '', 'urban_hero' );
    urban_cms_add_setting( $wp_customize, 'hero_cta2_text',   __( 'Secondary CTA Text', 'urban-cms' ), 'text',  'View Events', 'urban_hero' );
    urban_cms_add_setting( $wp_customize, 'hero_cta2_url',    __( 'Secondary CTA URL', 'urban-cms' ),  'url',   '', 'urban_hero' );

    $wp_customize->add_setting( 'hero_bg_image', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ] );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_bg_image', [
        'label'   => __( 'Hero Background Image', 'urban-cms' ),
        'section' => 'urban_hero',
    ] ) );

    // ---- Section: Social Media ----
    $wp_customize->add_section( 'urban_social', [
        'title'  => __( 'Social Media Links', 'urban-cms' ),
        'panel'  => 'urban_district',
    ] );

    $socials = [
        'social_fb'  => __( 'Facebook URL', 'urban-cms' ),
        'social_tw'  => __( 'Twitter / X URL', 'urban-cms' ),
        'social_in'  => __( 'Instagram URL', 'urban-cms' ),
        'social_li'  => __( 'LinkedIn URL', 'urban-cms' ),
        'social_yt'  => __( 'YouTube URL', 'urban-cms' ),
        'social_tk'  => __( 'TikTok URL', 'urban-cms' ),
        'social_rss' => __( 'RSS Feed URL', 'urban-cms' ),
    ];

    foreach ( $socials as $key => $label ) {
        urban_cms_add_setting( $wp_customize, $key, $label, 'url', '', 'urban_social' );
    }

    // ---- Section: Footer ----
    $wp_customize->add_section( 'urban_footer', [
        'title'  => __( 'Footer', 'urban-cms' ),
        'panel'  => 'urban_district',
    ] );

    urban_cms_add_setting( $wp_customize, 'footer_about',       __( 'Footer About Text', 'urban-cms' ),    'textarea', '', 'urban_footer' );
    urban_cms_add_setting( $wp_customize, 'footer_copyright',   __( 'Copyright Text', 'urban-cms' ),       'text',     '', 'urban_footer' );

    // ---- Section: Newsletter ----
    $wp_customize->add_section( 'urban_newsletter', [
        'title'  => __( 'Newsletter Signup', 'urban-cms' ),
        'panel'  => 'urban_district',
    ] );

    $wp_customize->add_setting( 'newsletter_enabled', [
        'default'           => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport'         => 'postMessage',
    ] );
    $wp_customize->add_control( 'newsletter_enabled', [
        'label'   => __( 'Show newsletter signup above footer', 'urban-cms' ),
        'section' => 'urban_newsletter',
        'type'    => 'checkbox',
    ] );

    urban_cms_add_setting( $wp_customize, 'newsletter_heading',    __( 'Heading', 'urban-cms' ),     'text',     __( 'Stay Connected', 'urban-cms' ),                                                              'urban_newsletter' );
    urban_cms_add_setting( $wp_customize, 'newsletter_subheading', __( 'Subheading', 'urban-cms' ),  'textarea', __( 'Get district news, events, and updates delivered to your inbox.', 'urban-cms' ), 'urban_newsletter' );
}

/**
 * Helper: add a text/url/textarea setting + control pair.
 */
function urban_cms_add_setting(
    WP_Customize_Manager $wpc,
    string $key,
    string $label,
    string $type     = 'text',
    string $default  = '',
    string $section  = 'urban_identity'
): void {
    $wpc->add_setting( $key, [
        'default'           => $default,
        'sanitize_callback' => $type === 'url'   ? 'esc_url_raw'
                             : ( $type === 'email' ? 'sanitize_email'
                                                   : 'sanitize_text_field' ),
        'transport'         => 'postMessage',
    ] );

    $control_args = [
        'label'   => $label,
        'section' => $section,
        'type'    => $type === 'url' ? 'url' : ( $type === 'email' ? 'email' : ( $type === 'textarea' ? 'textarea' : 'text' ) ),
    ];

    $wpc->add_control( $key, $control_args );
}

/**
 * Output brand color CSS variables in <head> based on Customizer values.
 */
function urban_cms_output_color_css(): void {
    $primary       = sanitize_hex_color( get_theme_mod( 'color_primary',       '#1a3a5c' ) );
    $primary_light = sanitize_hex_color( get_theme_mod( 'color_primary_light', '#2563a8' ) );
    $accent        = sanitize_hex_color( get_theme_mod( 'color_accent',        '#e8a020' ) );

    echo "<style>:root{"
       . "--color-primary:{$primary};"
       . "--color-primary-light:{$primary_light};"
       . "--color-accent:{$accent};"
       . "}</style>\n";
}
