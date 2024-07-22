<?php
/**
 * The file to create functions and definitions.
 *
 */
?>

<?php

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
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'wp_fictional_university_files');


/**
 *
 */
function wp_fictional_university_features() {
//    register_nav_menus( 'headerMenuLocation', 'Header Menu Location');
    add_theme_support('title-tag');
}

add_action('after_setup_theme', 'wp_fictional_university_features');