<?php

/*
    Plugin Name: WP Fictional University - Word Count Plugin
    Description: Counting words for WP Fictional University
    Version: 1.0
    Author: Quien
    Author URI: https://tsunghsun.me/
    Text Domain: wcpDomain
    Domain Path: /languages
*/

class WordCountPlugin {
    function __construct() {
        add_action('admin_menu', array($this, 'adminPage'));
        add_action('admin_init', array($this, 'settings'));
        add_filter('the_content', array($this, 'frontendPage'));
        add_action('init', array($this, 'languages'));
    }

    /*
     * Generate a menu item under `Settings` to navigate to `Word Count Settings` page
     */
    function adminPage(): void {
        add_options_page(
            'Word Count Settings',
            __('Word Count', 'wcpDomain'),
            'manage_options',
            'word-count-settings-page',
            array($this, 'wordCountSettingsHTML')
        );
    }

    /*
     * generate a content in `Word Count Settings` page
     */
    function settings(): void {
        add_settings_section('wcp_section', null, null, 'word-count-settings-page');

        // Display Location
        add_settings_field(
            'wcp_location',
            'Display Location',
            array($this, 'locationHTML'),
            'word-count-settings-page',
            'wcp_section'
        );
        register_setting(
            'wordCountPlugin',
            'wcp_location',
            array('sanitize_callback' => array($this, 'sanitize_location'), 'default' => '0')
        );

        // Headline Text
        add_settings_field(
            'wcp_headline',
            'Headline Text',
            array($this, 'headlineHTML'),
            'word-count-settings-page',
            'wcp_section'
        );
        register_setting(
            'wordCountPlugin',
            'wcp_headline',
            array('sanitize_callback' => 'sanitize_text_field', 'default' => 'Post Statistics')
        );

        // Word Count
        add_settings_field(
            'wcp_word_count',
            'Word Count',
            array($this, 'checkboxHTML'),
            'word-count-settings-page',
            'wcp_section',
            array('theName' => 'wcp_word_count')
        );
        register_setting(
            'wordCountPlugin',
            'wcp_word_count',
            array('sanitize_callback' => 'sanitize_text_field', 'default' => '1')
        );

        // Character Count
        add_settings_field(
            'wcp_character_count',
            'Character Count',
            array($this, 'checkboxHTML'),
            'word-count-settings-page',
            'wcp_section',
            array('theName' => 'wcp_character_count')
        );
        register_setting(
            'wordCountPlugin',
            'wcp_character_count',
            array('sanitize_callback' => 'sanitize_text_field', 'default' => '1')
        );

        // Read Time
        add_settings_field(
            'wcp_read_time',
            'Read Time',
            array($this, 'checkboxHTML'),
            'word-count-settings-page',
            'wcp_section',
            array('theName' => 'wcp_read_time')
        );
        register_setting(
            'wordCountPlugin',
            'wcp_read_time',
            array('sanitize_callback' => 'sanitize_text_field', 'default' => '1')
        );
    }

    /*
    * Handle the value of location, must be 0 or 1
    */
    function sanitize_location($input) {
        if ($input != '0' AND $input != '1') {
            add_settings_error('wcp_location', 'wcp_location_error', 'Display location must be either beginning or end.');
            return get_option('wcp_location');
        }
        return $input;
    }

    /*
    * Generate location html
    */
    function locationHTML(): void
    { ?>
        <label>
            <select name="wcp_location">
                <option value="0" <?php selected(get_option('wcp_location'), '0') ?>>Beginning of post</option>
                <option value="1" <?php selected(get_option('wcp_location'), '1') ?>>End of post</option>
            </select>
        </label>
    <?php }

    /*
     * Generate headline html
     */
    function headlineHTML(): void
    { ?>
        <label>
            <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option('wcp_headline')) ?>">
        </label>
    <?php }

    /*
     * Generate checkbox html
     */
    function checkboxHTML($args): void
    { ?>
        <label>
            <input type="checkbox" name="<?php echo $args['theName'] ?>" value="1" <?php checked(get_option($args['theName']), '1') ?>>
        </label>
    <?php }

    /*
     * Generate word count settings html
     */
    function wordCountSettingsHTML(): void
    { ?>
        <div class="wrap">
            <h1>Word Count Settings</h1>
            <form action="options.php" method="POST">
                <?php
                settings_fields('wordCountPlugin');
                do_settings_sections('word-count-settings-page');
                submit_button();
                ?>
            </form>
        </div>
    <?php }

    /*
     * Register `word count` in frontend
     */
    function frontendPage($content) {
        if (is_main_query() && is_single() &&
            (
                get_option('wcp_word_count', '1') ||
                get_option('wcp_character_count', '1') ||
                get_option('wcp_read_time', '1')
            )) {
            return $this->generateFrontendHTML($content);
        }
        return $content;
    }

    /*
     * Generate content of `word count` in the frontend
     */
    function generateFrontendHTML($content): string {
        $html = '<h3>'.esc_html(get_option('wcp_headline', 'Post Statistics')).'</h3>';
        $html .= '<p>';

        // get word count once because both word count and read time will need it.
        if (get_option('wcp_word_count', '1') || get_option('wcp_read_time', '1')) {
            $wordCount = str_word_count(strip_tags($content));
        }

        if (get_option('wcp_word_count', '1')) {
            $html .= esc_html__('This post has', 'wcpDomain').' '.$wordCount.' '.esc_html__('words', 'wcpDomain').'.<br />';
        }

        if (get_option('wcp_character_count', '1')) {
            $html .= esc_html__('This post has', 'wcpDomain').' '.strlen(strip_tags($content)).' '.esc_html__('characters', 'wcpDomain').'.<br />';
        }

        if (get_option('wcp_read_time', '1')) {
            $html .= esc_html__('This post will take about', 'wcpDomain').' '.round($wordCount/225).' '.esc_html__('minute(s) to read', 'wcpDomain').'.<br />';
        }

        $html .= '</p>';

        if (get_option('wcp_location', '0') == '0') {
            return $html.$content;
        }

        return $content.$html;
    }

    /*
     * Load domain
     */
    function languages(): void {
        load_plugin_textdomain('wcpDomain', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
}

$wordCountPluginPlugin = new WordCountPlugin();