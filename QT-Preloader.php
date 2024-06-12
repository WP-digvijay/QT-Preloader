<?php
/**
 * Plugin Name: QT Preloader
 * Description: This plugin name  based preloader.
 * Version: 1.0
 * Author: Rajput Digvijay
 */
if(!defined('ABSPATH')){
    exit();
}

// Enqueue preloader styles and scripts
function custom_preloader_enqueue_scripts() {
    $enabled = get_option('custom_preloader_enabled', 'yes');
    if ($enabled === 'yes') {
        wp_enqueue_style('custom-preloader-style', plugin_dir_url(__FILE__) . 'preloader.css');
        wp_enqueue_script('custom-preloader-script', plugin_dir_url(__FILE__) . 'preloader.js', array(), '1.1', true);

        // Pass timeout duration to the script
        $timeout = get_option('custom_preloader_timeout', 2500);
        wp_localize_script('custom-preloader-script', 'preloaderSettings', array('timeout' => $timeout));
    }
}
add_action('wp_enqueue_scripts', 'custom_preloader_enqueue_scripts');

// Add settings page to the admin menu
function custom_preloader_add_admin_menu() {
    add_options_page('Custom Preloader', 'Custom Preloader', 'manage_options', 'custom-preloader', 'custom_preloader_options_page');
}
add_action('admin_menu', 'custom_preloader_add_admin_menu');

// Register settings
function custom_preloader_settings_init() {
    register_setting('customPreloader', 'custom_preloader_text');
    register_setting('customPreloader', 'custom_preloader_timeout');
    register_setting('customPreloader', 'custom_preloader_enabled');
    register_setting('customPreloader', 'custom_preloader_custom_css');

    add_settings_section(
        'custom_preloader_section',
        __('Custom Preloader Settings', 'customPreloader'),
        null,
        'customPreloader'
    );

    add_settings_field(
        'custom_preloader_text',
        __('Preloader Text', 'customPreloader'),
        'custom_preloader_text_render',
        'customPreloader',
        'custom_preloader_section'
    );

    add_settings_field(
        'custom_preloader_timeout',
        __('Timeout Duration (ms)', 'customPreloader'),
        'custom_preloader_timeout_render',
        'customPreloader',
        'custom_preloader_section'
    );

    add_settings_field(
        'custom_preloader_enabled',
        __('Enable Preloader', 'customPreloader'),
        'custom_preloader_enabled_render',
        'customPreloader',
        'custom_preloader_section'
    );

    add_settings_field(
        'custom_preloader_custom_css',
        __('Custom CSS', 'customPreloader'),
        'custom_preloader_custom_css_render',
        'customPreloader',
        'custom_preloader_section'
    );
}
add_action('admin_init', 'custom_preloader_settings_init');

function custom_preloader_text_render() {
    $value = get_option('custom_preloader_text', 'DIGVIJAY');
    echo '<input type="text" name="custom_preloader_text" value="' . esc_attr($value) . '">';
}

function custom_preloader_timeout_render() {
    $value = get_option('custom_preloader_timeout', 2500);
    echo '<input type="number" name="custom_preloader_timeout" value="' . esc_attr($value) . '">';
}

function custom_preloader_enabled_render() {
    $value = get_option('custom_preloader_enabled', 'yes');
    echo '<input type="checkbox" name="custom_preloader_enabled" value="yes"' . checked($value, 'yes', false) . '> Enable Preloader';
}

function custom_preloader_custom_css_render() {
    $value = get_option('custom_preloader_custom_css', '');
    echo '<textarea name="custom_preloader_custom_css" rows="10" cols="50" class="large-text code">' . esc_textarea($value) . '</textarea>';
}

function custom_preloader_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Custom Preloader</h2>
        <?php
        settings_fields('customPreloader');
        do_settings_sections('customPreloader');
        submit_button();
        ?>
    </form>
    <?php
}

// Output preloader HTML
function custom_preloader_html() {
    $enabled = get_option('custom_preloader_enabled', 'yes');
    if ($enabled !== 'yes') {
        return;
    }

    $text = get_option('custom_preloader_text', 'DIGVIJAY');
    ?>
    <div class="wrapper">
        <div id="loader-wrapper">
            <div id="loader">
                <svg viewBox="0 0 1320 300" class="pre-load">
                    <text x="50%" y="50%" dy=".35em" text-anchor="middle"><?php echo esc_html($text); ?></text>
                </svg>
            </div>
            <div class="loader-section section-left"></div>
            <div class="loader-section section-right"></div>
        </div>
    </div>
    <?php

    // Output custom CSS
    $custom_css = get_option('custom_preloader_custom_css', '');
    if (!empty($custom_css)) {
        echo '<style>' . esc_html($custom_css) . '</style>';
    }
}
add_action('wp_footer', 'custom_preloader_html');
