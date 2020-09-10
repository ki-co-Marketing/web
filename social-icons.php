<?php

function pistis_get_social_icons(){
    global $pistis_options;
    $return = array();
    
    $social = array(
        // [short_name] => '[name],[fa-icon]'
        'bh' => array( 'name'=>'Behance','fa'=>'behance'),
        'cp' => array( 'name'=>'CodePen','fa'=>'codepen'),
        'de' => array( 'name'=>'Devianart','fa'=>'deviantart'),
        'dg' => array( 'name'=>'Digg', 'fa'=>'digg'),
        'dr' => array( 'name'=>'Dribbble', 'fa'=>'dribbble'),
        'db' => array( 'name'=>'Dropbox', 'fa'=>'dropbox'),
        'fb' => array( 'name'=>'Facebook', 'fa'=>'facebook'),
        'fl' => array( 'name'=>'Flickr', 'fa'=>'flickr'),
        'fs' => array( 'name'=>'Foursquare', 'fa'=>'foursquare'),
        'git'=> array( 'name'=>'GitHub', 'fa'=>'github'),
        'gp' => array( 'name'=>'Google +', 'fa'=>'google-plus'),
        'in' => array( 'name'=>'Instagram', 'fa'=>'instagram', '_link'=>'https://www.instagram.com/kicomarketing/'),
        'lf' => array( 'name'=>'Last FM', 'fa'=>'lastfm'),
        'li' => array( 'name'=>'LinkedIN', 'fa '=>'linkedin'),
        'pt' => array( 'name'=>'Pinterest', 'fa'=>'pinterest'),
        'rss'=> array( 'name'=>'RSS', 'fa'=>'rss'),
        'tu' => array( 'name'=>'Tumblr', 'fa'=>'tumblr'),
        'tw' => array( 'name'=>'Twitter', 'fa'=>'twitter'),
        'vi' => array( 'name'=>'Vimeo', 'fa'=>'vimeo'),
        'wp' => array( 'name'=>'WordPress', 'fa'=>'wordpress'),
        'yt' => array( 'name'=>'YouTube', 'fa'=>'youtube'),
        'sk' => array( 'name'=>'Skype', 'fa'=>'skype'),
        'ss' => array( 'name'=>'Slideshare', 'fa'=>'slideshare'),
        'bd' => array( 'name'=>'Bandcamp', 'fa'=>'bandcamp'),
        'sd' => array( 'name'=>'Soundcloud', 'fa'=>'soundcloud'),
        'sc' => array( 'name'=>'Snapchat', 'fa'=>'snapchat'),
    );
    
    foreach ($social as $short=>$data){
        $icon = $data['fa'];
        $name = $data['name'];
        if ( isset($pistis_options[$short.'_link']) && $pistis_options[$short.'_link']!='' ) {
            $link = $pistis_options[$short.'_link'];
        }else{
            $link = false;
        }
        if($link){
            $return[] = array(
                'link' => $link,
                'name' => $name,
                'icon' => $icon,
            );
        }
    }
    
    return $return;
}
