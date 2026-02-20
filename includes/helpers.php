<?php
/**
 * Helper utilities and option defaults.
 *
 * @package AbstractBox
 */

defined( 'ABSPATH' ) || exit;

/* ── Option Defaults ───────────────────────────────────────────────── */

/**
 * Return the full set of default option values.
 *
 * @return array
 */
function abstract_box_get_defaults() {
    return array(
        // Appearance tab.
        'style'            => 'default',
        'use_theme_css'    => false,
        'title_color'      => '#1e293b',
        'text_color'       => '#334155',
        'bg_color'         => '#f8fafc',
        'bg_color_end'     => '#ffffff',
        'accent_color'     => '#3b82f6',
        'border_radius'    => 8,
        'font_family'      => 'sans-serif',

        // Schema tab.
        'enable_schema'    => true,
        'schema_type'      => 'CreativeWork',

        // Advanced tab.
        'custom_css_class' => '',
        'hover_effect'     => true,
    );
}

/**
 * Retrieve a single plugin option with its default fallback.
 *
 * @param  string $key Option key.
 * @return mixed
 */
function abstract_box_get_option( $key ) {
    $defaults = abstract_box_get_defaults();
    $options  = get_option( 'abstract_box_options', array() );

    return isset( $options[ $key ] ) ? $options[ $key ] : ( isset( $defaults[ $key ] ) ? $defaults[ $key ] : null );
}

/**
 * Retrieve the entire options array merged with defaults.
 *
 * @return array
 */
function abstract_box_get_options() {
    return wp_parse_args( get_option( 'abstract_box_options', array() ), abstract_box_get_defaults() );
}

/* ── Sanitisation Callbacks ────────────────────────────────────────── */

/**
 * Sanitise the full options array before saving.
 *
 * @param  array $input Raw input from settings form.
 * @return array        Sanitised output.
 */
function abstract_box_sanitize_options( $input ) {
    $defaults  = abstract_box_get_defaults();
    $sanitized = array();

    // Style selector - whitelist.
    $valid_styles               = array( 'default', 'custom' );
    $sanitized['style']         = in_array( $input['style'] ?? '', $valid_styles, true ) ? $input['style'] : $defaults['style'];

    // Boolean: use theme CSS.
    $sanitized['use_theme_css'] = ! empty( $input['use_theme_css'] );

    // Colour fields.
    $color_keys = array( 'title_color', 'text_color', 'bg_color', 'bg_color_end', 'accent_color' );
    foreach ( $color_keys as $ck ) {
        $sanitized[ $ck ] = sanitize_hex_color( $input[ $ck ] ?? '' ) ?: $defaults[ $ck ];
    }

    // Border radius (0-50).
    $sanitized['border_radius'] = absint( $input['border_radius'] ?? $defaults['border_radius'] );
    $sanitized['border_radius'] = min( 50, max( 0, $sanitized['border_radius'] ) );

    // Font family - whitelist.
    $valid_fonts                = array( 'sans-serif', 'serif', 'system' );
    $sanitized['font_family']   = in_array( $input['font_family'] ?? '', $valid_fonts, true ) ? $input['font_family'] : $defaults['font_family'];

    // Schema settings.
    $sanitized['enable_schema'] = ! empty( $input['enable_schema'] );

    $valid_schema_types           = array( 'CreativeWork', 'ScholarlyArticle', 'Article' );
    $sanitized['schema_type']     = in_array( $input['schema_type'] ?? '', $valid_schema_types, true ) ? $input['schema_type'] : $defaults['schema_type'];

    // Advanced.
    $sanitized['custom_css_class'] = sanitize_html_class( $input['custom_css_class'] ?? '' );
    $sanitized['hover_effect']     = ! empty( $input['hover_effect'] );

    return $sanitized;
}

/* ── Font Stack Helper ─────────────────────────────────────────────── */

/**
 * Return a CSS font-family value for the chosen key.
 *
 * @param  string $key Font family key.
 * @return string      CSS font-family value.
 */
function abstract_box_font_stack( $key = 'sans-serif' ) {
    $stacks = array(
        'sans-serif' => "'Helvetica Neue', Helvetica, Arial, sans-serif",
        'serif'      => "Georgia, 'Times New Roman', Times, serif",
        'system'     => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif",
    );

    return $stacks[ $key ] ?? $stacks['sans-serif'];
}
