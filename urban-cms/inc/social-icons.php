<?php
/**
 * Social Icons — updated to use theme Customizer settings
 * Replaces the standalone social-icons.php helper used by the previous theme.
 *
 * @package UrbanCMS
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns an array of configured social icon links from the Customizer.
 * Drop-in compatible with the original pistis_get_social_icons() signature.
 *
 * @return array[] Each entry: [ 'link' => string, 'name' => string, 'icon' => string ]
 */
function urban_cms_get_social_icons(): array {
    $social = [
        'fb'  => [ 'name' => 'Facebook',   'fa' => 'facebook-f' ],
        'tw'  => [ 'name' => 'Twitter / X', 'fa' => 'x-twitter' ],
        'in'  => [ 'name' => 'Instagram',  'fa' => 'instagram' ],
        'li'  => [ 'name' => 'LinkedIn',   'fa' => 'linkedin-in' ],
        'yt'  => [ 'name' => 'YouTube',    'fa' => 'youtube' ],
        'tk'  => [ 'name' => 'TikTok',     'fa' => 'tiktok' ],
        'nx'  => [ 'name' => 'Next Door',  'fa' => 'n' ],   // no FA icon; use text
        'rss' => [ 'name' => 'RSS Feed',   'fa' => 'rss' ],
    ];

    $return = [];
    foreach ( $social as $key => $data ) {
        $link = get_theme_mod( 'social_' . $key );
        if ( $link ) {
            $return[] = [
                'link' => esc_url( $link ),
                'name' => $data['name'],
                'icon' => $data['fa'],
            ];
        }
    }

    return $return;
}

/**
 * Render social icon links as HTML.
 *
 * @param string $class  Wrapper element CSS class.
 */
function urban_cms_render_social_icons( string $class = 'social-icons' ): void {
    $icons = urban_cms_get_social_icons();
    if ( ! $icons ) return;

    echo '<div class="' . esc_attr( $class ) . '">';
    foreach ( $icons as $icon ) {
        printf(
            '<a class="social-icon" href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s">'
            . '<i class="fa-brands fa-%s" aria-hidden="true"></i>'
            . '</a>',
            esc_url( $icon['link'] ),
            esc_attr( $icon['name'] ),
            esc_attr( $icon['icon'] )
        );
    }
    echo '</div>';
}
