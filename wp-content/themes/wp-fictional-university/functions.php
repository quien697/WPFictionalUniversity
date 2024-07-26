<?php
/**
 * The file to create functions and definitions.
 *
 */

/**
 *
 */
function wp_fictional_university_files() {
    // Style
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('wp_fictional_university_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('wp_fictional_university_extra_styles', get_theme_file_uri('/build/index.css'));

    // Script
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=' . $_ENV['GOOGLE_MAP_KEY'], NULL, '1.0', true);
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'wp_fictional_university_files');

/**
 *
 */
function wp_fictional_university_features() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}
add_action('after_setup_theme', 'wp_fictional_university_features');

/**
 *
 */
function wp_fictional_university_adjust_queries($query) {
    // Event
    if (!is_admin() && is_post_type_archive('event') && $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
            )
        ));
    }
    // Program
    if (!is_admin() && is_post_type_archive('program') && $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }
    // Campus
    if (!is_admin() AND is_post_type_archive('campus') AND $query->is_main_query()) {
        $query->set('posts_per_page', -1);
    }
}
add_action('pre_get_posts', 'wp_fictional_university_adjust_queries');

/**
 *
 */
function pageBanner($args = NULL): void {
    if (!isset($args['title'])) {
        $args['title'] = get_the_title();
    }
    if (!isset($args['subtitle'])) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if (!isset($args['image'])) {
        if (get_field('page_banner_background_image') && !is_archive() && !is_home() ) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
    echo '<div class="page-banner">';
    echo '<div class="page-banner__bg-image" style="background-image: url('.$args['photo'].');"></div>';
    echo '<div class="page-banner__content container container--narrow">';
    echo '<h1 class="page-banner__title">'.$args['title'].'</h1>';
    echo '<div class="page-banner__intro">';
    echo '<p>'.$args['subtitle'].'</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

/**
 *
 */
function wp_fictional_university_map_key($api) {
    $api['key'] = $_ENV['GOOGLE_MAP_KEY'];
    return $api;
}

add_filter('acf/fields/google_map/api', 'wp_fictional_university_map_key');