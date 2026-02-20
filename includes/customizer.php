<?php
/**
 * WordPress Customizer integration.
 *
 * Kept as a secondary convenience layer alongside the main Settings page.
 *
 * @package AbstractBox
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Customizer settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function abstract_box_customizer_register( $wp_customize ) {

    /* ── Section ───────────────────────────────────────────────────── */

    $wp_customize->add_section( 'abstract_box_section', array(
        'title'    => __( 'Abstract Box', 'abstract-box' ),
        'priority' => 120,
    ) );

    /* ── Style selector ────────────────────────────────────────────── */

    $wp_customize->add_setting( 'abstract_box_options[style]', array(
        'default'           => 'default',
        'type'              => 'option',
        'sanitize_callback' => 'abstract_box_sanitize_style_choice',
    ) );

    $wp_customize->add_control( 'abstract_box_style_control', array(
        'label'    => __( 'Box Style', 'abstract-box' ),
        'section'  => 'abstract_box_section',
        'settings' => 'abstract_box_options[style]',
        'type'     => 'select',
        'choices'  => array(
            'default' => __( 'Default', 'abstract-box' ),
            'custom'  => __( 'Custom', 'abstract-box' ),
        ),
    ) );

    /* ── Use theme CSS ─────────────────────────────────────────────── */

    $wp_customize->add_setting( 'abstract_box_options[use_theme_css]', array(
        'default'           => false,
        'type'              => 'option',
        'sanitize_callback' => 'abstract_box_sanitize_checkbox',
    ) );

    $wp_customize->add_control( 'abstract_box_use_theme_css_control', array(
        'label'    => __( 'Use theme CSS only', 'abstract-box' ),
        'section'  => 'abstract_box_section',
        'settings' => 'abstract_box_options[use_theme_css]',
        'type'     => 'checkbox',
    ) );

    /* ── Colour pickers ────────────────────────────────────────────── */

    $colors = array(
        'title_color'  => __( 'Title Colour', 'abstract-box' ),
        'text_color'   => __( 'Text Colour', 'abstract-box' ),
        'bg_color'     => __( 'Background Start', 'abstract-box' ),
        'bg_color_end' => __( 'Background End', 'abstract-box' ),
        'accent_color' => __( 'Accent Colour', 'abstract-box' ),
    );

    $defaults = abstract_box_get_defaults();

    foreach ( $colors as $key => $label ) {
        $wp_customize->add_setting( "abstract_box_options[{$key}]", array(
            'default'           => $defaults[ $key ],
            'type'              => 'option',
            'sanitize_callback' => 'sanitize_hex_color',
        ) );

        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, "abstract_box_{$key}_control", array(
            'label'    => $label,
            'section'  => 'abstract_box_section',
            'settings' => "abstract_box_options[{$key}]",
        ) ) );
    }

    /* ── Font family ───────────────────────────────────────────────── */

    $wp_customize->add_setting( 'abstract_box_options[font_family]', array(
        'default'           => 'sans-serif',
        'type'              => 'option',
        'sanitize_callback' => 'abstract_box_sanitize_font_choice',
    ) );

    $wp_customize->add_control( 'abstract_box_font_control', array(
        'label'    => __( 'Font Family', 'abstract-box' ),
        'section'  => 'abstract_box_section',
        'settings' => 'abstract_box_options[font_family]',
        'type'     => 'select',
        'choices'  => array(
            'sans-serif' => __( 'Sans-Serif (Modernist)', 'abstract-box' ),
            'serif'      => __( 'Serif (Traditional)', 'abstract-box' ),
            'system'     => __( 'System Default', 'abstract-box' ),
        ),
    ) );
}

add_action( 'customize_register', 'abstract_box_customizer_register' );

/* ── Customizer sanitisation helpers ───────────────────────────────── */

/**
 * Sanitise style choice (whitelist).
 *
 * @param  string $value Raw value.
 * @return string
 */
function abstract_box_sanitize_style_choice( $value ) {
    return in_array( $value, array( 'default', 'custom' ), true ) ? $value : 'default';
}

/**
 * Sanitise checkbox to boolean.
 *
 * @param  mixed $value Raw value.
 * @return bool
 */
function abstract_box_sanitize_checkbox( $value ) {
    return (bool) $value;
}

/**
 * Sanitise font family choice (whitelist).
 *
 * @param  string $value Raw value.
 * @return string
 */
function abstract_box_sanitize_font_choice( $value ) {
    return in_array( $value, array( 'sans-serif', 'serif', 'system' ), true ) ? $value : 'sans-serif';
}

/* ── Live preview ──────────────────────────────────────────────────── */

/**
 * Enqueue Customizer preview script.
 */
function abstract_box_customizer_preview_js() {
    wp_enqueue_script(
        'abstract-box-customizer-preview',
        ABSTRACT_BOX_URL . 'js/customizer-preview.js',
        array( 'customize-preview' ),
        ABSTRACT_BOX_VERSION,
        true
    );
}

add_action( 'customize_preview_init', 'abstract_box_customizer_preview_js' );
