<?php
ob_start(); // Start output buffering

/**
 * Plugin Name: Abstract Box
 * Plugin URI: https://menj.net/abstract-box
 * Description: Adds a chic and modernist "Abstract" section to posts via a shortcode [abstract].
 * Version: 1.1
 * Author: MENJ
 * Author URI: https://menj.org
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

/**
 * Output schema.org JSON-LD for the Abstract shortcode (property: abstract).
 * - Only outputs on singular frontend views.
 * - Extracts the first [abstract] shortcode instance from the post content.
 * - Outputs a minimal CreativeWork node to reduce conflicts with SEO plugins' Article graphs.
 */
function abstract_box_output_abstract_schema_jsonld() {
    if (is_admin() || !is_singular()) {
        return;
    }

    static $done = false;
    if ($done) {
        return;
    }

    global $post;
    if (empty($post) || empty($post->post_content)) {
        return;
    }

    // Allow themes/plugins to disable output.
    $enabled = apply_filters('abstract_box_output_schema', true, $post);
    if (!$enabled) {
        return;
    }

    if (!has_shortcode($post->post_content, 'abstract')) {
        return;
    }

    $pattern = get_shortcode_regex(array('abstract'));
    if (!preg_match_all('/' . $pattern . '/s', $post->post_content, $matches, PREG_SET_ORDER)) {
        return;
    }

    $first = $matches[0];

    // Shortcode parts: [0]=full match, [3]=attrs, [5]=content (enclosed shortcode).
    $attrs = array();
    if (isset($first[3]) && $first[3] !== '') {
        $attrs = shortcode_parse_atts($first[3]);
        if (!is_array($attrs)) {
            $attrs = array();
        }
    }

    $shortcode_content = isset($first[5]) ? $first[5] : '';
    if ($shortcode_content === '') {
        return;
    }

    // Convert to plain text for schema abstract.
    $abstract_text = wp_strip_all_tags(do_shortcode($shortcode_content), true);
    $abstract_text = preg_replace('/\s+/u', ' ', $abstract_text);
    $abstract_text = trim($abstract_text);

    if ($abstract_text === '') {
        return;
    }

    $permalink = get_permalink($post);
    if (!$permalink) {
        return;
    }

    $author_name = '';
    $author_id = (int) get_post_field('post_author', $post->ID);
    if ($author_id > 0) {
        $author_name = get_the_author_meta('display_name', $author_id);
    }

    $schema_type = apply_filters('abstract_box_schema_type', 'CreativeWork', $post);

    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => $schema_type,
        '@id'      => trailingslashit($permalink) . '#abstract',
        'url'      => $permalink,
        'name'     => get_the_title($post),
        'abstract' => $abstract_text,
        'datePublished' => get_the_date('c', $post),
        'dateModified'  => get_the_modified_date('c', $post),
    );

    if (!empty($author_name)) {
        $schema['author'] = array(
            '@type' => 'Person',
            'name'  => $author_name,
        );
    }

    // Optional subtitle from shortcode attrs (stored as alternativeName).
    if (!empty($attrs['subtitle'])) {
        $schema['alternativeName'] = wp_strip_all_tags((string) $attrs['subtitle'], true);
    }

    $schema = apply_filters('abstract_box_schema_payload', $schema, $post, $attrs, $shortcode_content);

    echo "\n" . '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";

    $done = true;
}
add_action('wp_head', 'abstract_box_output_abstract_schema_jsonld', 20);


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