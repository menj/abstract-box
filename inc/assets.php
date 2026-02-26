<?php
namespace Menj\AbstractBox;

defined( 'ABSPATH' ) || exit;

class Assets {
    public function init() {
        add_action( 'wp_enqueue_scripts',    [ $this, 'enqueue_frontend' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin' ] );
        add_action( 'enqueue_block_assets',  [ $this, 'enqueue_block_editor' ] );
    }

    /**
     * Build the :root CSS custom-property block from validated option values.
     * Validation (not escaping) is the correct defence for CSS output:
     * each value is checked against its expected format and replaced with a
     * known-safe default if invalid, so nothing untrusted ever enters the CSS.
     *
     * @return string Safe CSS string suitable for wp_add_inline_style().
     */
    private function build_inline_css( array $options ): string {
        $defaults = Helpers::get_defaults();

        $title_color   = sanitize_hex_color( $options['title_color']   ?? '' ) ?: $defaults['title_color'];
        $text_color    = sanitize_hex_color( $options['text_color']    ?? '' ) ?: $defaults['text_color'];
        $bg_color      = sanitize_hex_color( $options['bg_color']      ?? '' ) ?: $defaults['bg_color'];
        $bg_color_end  = sanitize_hex_color( $options['bg_color_end']  ?? '' ) ?: $defaults['bg_color_end'];
        $accent_color  = sanitize_hex_color( $options['accent_color']  ?? '' ) ?: $defaults['accent_color'];
        $border_radius = min( 50, max( 0, absint( $options['border_radius'] ?? $defaults['border_radius'] ) ) );

        // Font family: validated against a closed allowlist, never interpolated raw.
        $allowed_fonts = array( 'sans-serif', 'serif', 'system' );
        $font_key      = in_array( $options['font_family'] ?? '', $allowed_fonts, true )
                            ? $options['font_family']
                            : $defaults['font_family'];
        $font_stack    = Helpers::font_stack( $font_key );

        return ":root {
"
            . "    --ab-title-color:    {$title_color};
"
            . "    --ab-text-color:     {$text_color};
"
            . "    --ab-bg-color:       {$bg_color};
"
            . "    --ab-bg-color-end:   {$bg_color_end};
"
            . "    --ab-accent-color:   {$accent_color};
"
            . "    --ab-border-radius:  {$border_radius}px;
"
            . "    --ab-font-family:    {$font_stack};
"
            . "}";
    }

    /**
     * Resolve the CSS file and enqueue handle for a given context.
     * Keeps the style/handle mapping in one place so both the frontend
     * and block-editor enqueue methods stay in sync automatically.
     *
     * @param  string $context  'frontend' or 'editor'
     * @param  string $style    Option value — 'custom' or 'default'
     * @return array { handle: string, file: string }
     */
    private function resolve_style( string $context, string $style ): array {
        $is_custom = ( 'custom' === $style );
        $suffix    = ( 'editor' === $context ) ? '-editor' : '';
        return array(
            'handle' => $is_custom ? 'abstract-box-custom' . $suffix : 'abstract-box' . $suffix,
            'file'   => $is_custom ? 'abstract-box-custom.css' : 'abstract-box.css',
        );
    }

    public function enqueue_frontend() {
        $options = Helpers::get_options();

        if ( ! empty( $options['use_theme_css'] ) ) {
            return;
        }

        $resolved = $this->resolve_style( 'frontend', $options['style'] );

        wp_enqueue_style(
            $resolved['handle'],
            ABSTRACT_BOX_URL . 'css/' . $resolved['file'],
            array(),
            ABSTRACT_BOX_VERSION
        );

        wp_add_inline_style( $resolved['handle'], $this->build_inline_css( $options ) );
    }

    public function enqueue_admin( $hook_suffix ) {
        if ( 'settings_page_abstract-box' !== $hook_suffix ) {
            return;
        }

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

    public function enqueue_block_editor() {
        if ( ! is_admin() ) {
            return;
        }

        $options  = Helpers::get_options();
        $resolved = $this->resolve_style( 'editor', $options['style'] );

        wp_enqueue_style(
            $resolved['handle'],
            ABSTRACT_BOX_URL . 'css/' . $resolved['file'],
            array(),
            ABSTRACT_BOX_VERSION
        );

        wp_add_inline_style( $resolved['handle'], $this->build_inline_css( $options ) );
    }
}
