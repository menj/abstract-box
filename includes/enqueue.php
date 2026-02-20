<?php
/**
 * Asset enqueuing — unified, single callback.
 *
 * @package AbstractBox
 */

defined( 'ABSPATH' ) || exit;

/**
 * Enqueue front-end styles.
 *
 * Resolves the original plugin's double-enqueue bug by consolidating all
 * style logic into one callback with a clear priority chain:
 *
 *   1. "Use theme CSS" checked → enqueue nothing (theme provides styles).
 *   2. Style = "custom"        → enqueue abstract-box-custom.css.
 *   3. Default                 → enqueue abstract-box.css.
 *
 * In all non-theme cases, inline CSS variables are appended so the
 * colour-picker values take effect without extra HTTP requests.
 */
function abstract_box_enqueue_assets() {
    $options = abstract_box_get_options();

    // If the user has opted to style the box entirely from their theme, bail.
    if ( ! empty( $options['use_theme_css'] ) ) {
        return;
    }

    // Determine which stylesheet to load.
    $style_key  = ( 'custom' === $options['style'] ) ? 'custom' : 'default';
    $css_file   = ( 'custom' === $style_key ) ? 'abstract-box-custom.css' : 'abstract-box.css';
    $handle     = ( 'custom' === $style_key ) ? 'abstract-box-custom' : 'abstract-box';

    wp_enqueue_style(
        $handle,
        ABSTRACT_BOX_URL . 'css/' . $css_file,
        array(),
        ABSTRACT_BOX_VERSION
    );

    // Inject CSS custom properties so colour settings work without extra files.
    $font_stack = abstract_box_font_stack( $options['font_family'] );

    $inline_css = ":root {
    --ab-title-color:    {$options['title_color']};
    --ab-text-color:     {$options['text_color']};
    --ab-bg-color:       {$options['bg_color']};
    --ab-bg-color-end:   {$options['bg_color_end']};
    --ab-accent-color:   {$options['accent_color']};
    --ab-border-radius:  {$options['border_radius']}px;
    --ab-font-family:    {$font_stack};
}";

    wp_add_inline_style( $handle, $inline_css );
}

add_action( 'wp_enqueue_scripts', 'abstract_box_enqueue_assets' );

/**
 * Enqueue admin styles for the settings page.
 *
 * @param string $hook_suffix Current admin page hook.
 */
function abstract_box_enqueue_admin_assets( $hook_suffix ) {
    if ( 'settings_page_abstract-box' !== $hook_suffix ) {
        return;
    }

    // WordPress colour picker.
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );

    wp_enqueue_style(
        'abstract-box-admin',
        ABSTRACT_BOX_URL . 'css/admin-settings.css',
        array( 'wp-color-picker' ),
        ABSTRACT_BOX_VERSION
    );

    wp_enqueue_script(
        'abstract-box-admin',
        ABSTRACT_BOX_URL . 'js/admin-settings.js',
        array( 'jquery', 'wp-color-picker' ),
        ABSTRACT_BOX_VERSION,
        true
    );
}

add_action( 'admin_enqueue_scripts', 'abstract_box_enqueue_admin_assets' );
