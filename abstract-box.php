<?php
ob_start(); // Start output buffering

/**
 * Plugin Name: Abstract Box
 * Plugin URI: https://menj.net/abstract-box
 * Description: Adds a chic and modernist "Abstract" section to posts via a shortcode [abstract].
 * Version: 1.1
 * Author: MENJ
 * Author URI: https://menj.net
 */

// Function to register the Customizer settings
function abstract_box_customizer($wp_customize) {
    // Add a section for the Abstract Box settings
    $wp_customize->add_section('abstract_box_settings', array(
        'title' => __('Abstract Box Settings', 'abstract-box'),
        'priority' => 30,
    ));

    // Add a setting for using theme CSS
    $wp_customize->add_setting('abstract_box_use_theme_css', array(
        'default' => false,
        'sanitize_callback' => 'absint',
    ));

    // Add a control for the setting
    $wp_customize->add_control('abstract_box_use_theme_css_control', array(
        'label' => __('Use theme CSS', 'abstract-box'),
        'section' => 'abstract_box_settings',
        'settings' => 'abstract_box_use_theme_css',
        'type' => 'checkbox',
    ));
}

add_action('customize_register', 'abstract_box_customizer');

// Function to conditionally enqueue styles based on Customizer setting
function abstract_box_styles() {
    if (!get_theme_mod('abstract_box_use_theme_css', false)) {
        wp_enqueue_style('abstract-box-css', plugins_url('abstract-box.css', __FILE__));
    }
}

add_action('wp_enqueue_scripts', 'abstract_box_styles');

function abstract_shortcode($atts = [], $content = null) {
    // Default values for the attributes
    $atts = shortcode_atts(
        array(
            'title' => 'Abstract',
            'subtitle' => ''
        ),
        $atts,
        'abstract'
    );

    // Construct the HTML for the Abstract box
    $abstract_html = '<div class="abstract">';
    $abstract_html .= '<h2 class="abstract_title">' . esc_html($atts['title']) . '</h2>';
    if (!empty($atts['subtitle'])) {
        $abstract_html .= '<h3 class="abstract_subtitle">' . esc_html($atts['subtitle']) . '</h3>';
    }
    $abstract_html .= '<p class="abstract_text">' . wp_kses_post($content) . '</p>';
    $abstract_html .= '</div>';

    return $abstract_html;
}

add_shortcode('abstract', 'abstract_shortcode');

ob_end_clean(); // Clean output buffer

// Customizer setting for choosing the abstract box style
function abstract_box_custom_style($wp_customize) {
    $wp_customize->add_setting('abstract_box_style', array(
        'default' => 'default',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('abstract_box_style_control', array(
        'label' => __('Abstract Box Style', 'abstract-box'),
        'section' => 'abstract_box_settings',
        'settings' => 'abstract_box_style',
        'type' => 'select',
        'choices' => array(
            'default' => 'Default',
            'custom' => 'Custom'
        ),
    ));
}
add_action('customize_register', 'abstract_box_custom_style');

// Enqueue the correct style based on Customizer choice
function abstract_box_enqueue_correct_style() {
    $style_choice = get_theme_mod('abstract_box_style', 'default');
    if ($style_choice == 'custom') {
        wp_enqueue_style('abstract-box-custom-css', plugins_url('abstract-box-custom.css', __FILE__));
    } else {
        wp_enqueue_style('abstract-box-css', plugins_url('abstract-box.css', __FILE__));
    }
}
add_action('wp_enqueue_scripts', 'abstract_box_enqueue_correct_style');