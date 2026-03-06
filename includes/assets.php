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
     * Build the CSS custom-property block from validated option values.
     *
     * All values are validated (not merely escaped) — each is checked
     * against its expected format and replaced with a known-safe default
     * if invalid, so nothing untrusted ever enters the output.
     *
     * @return string Safe CSS string for wp_add_inline_style().
     */
    private function build_inline_css( array $options ): string {
        $defaults = Helpers::get_defaults();

        $title_color   = sanitize_hex_color( $options['title_color']   ?? '' ) ?: $defaults['title_color'];
        $text_color    = sanitize_hex_color( $options['text_color']    ?? '' ) ?: $defaults['text_color'];
        $bg_color      = sanitize_hex_color( $options['bg_color']      ?? '' ) ?: $defaults['bg_color'];
        $bg_color_end  = sanitize_hex_color( $options['bg_color_end']  ?? '' ) ?: $defaults['bg_color_end'];
        $accent_color  = sanitize_hex_color( $options['accent_color']  ?? '' ) ?: $defaults['accent_color'];
        $bullet_color  = sanitize_hex_color( $options['bullet_color']  ?? '' );
        $stripe_color  = sanitize_hex_color( $options['stripe_color']  ?? '' );
        $border_radius = min( 50, max( 0, absint( $options['border_radius'] ?? $defaults['border_radius'] ) ) );

        // Validate against the full six-family allowlist.
        $allowed_fonts = array( 'sans-serif', 'serif', 'humanist', 'monospace', 'slab', 'system' );
        $font_key      = in_array( $options['font_family'] ?? '', $allowed_fonts, true )
                            ? $options['font_family']
                            : $defaults['font_family'];
        $font_stack    = Helpers::font_stack( $font_key );

        $optional = '';
        if ( $bullet_color ) {
            $optional .= "    --ab-bullet-color:   {$bullet_color};\n";
        }
        if ( $stripe_color ) {
            $optional .= "    --ab-stripe-color:   {$stripe_color};\n";
        }

        return ":root {\n"
            . "    --ab-title-color:    {$title_color};\n"
            . "    --ab-text-color:     {$text_color};\n"
            . "    --ab-bg-color:       {$bg_color};\n"
            . "    --ab-bg-color-end:   {$bg_color_end};\n"
            . "    --ab-accent-color:   {$accent_color};\n"
            . "    --ab-border-radius:  {$border_radius}px;\n"
            . "    --ab-font-family:    {$font_stack};\n"
            . $optional
            . "}";
    }

    public function enqueue_frontend() {
        $options = Helpers::get_options();

        if ( ! empty( $options['use_theme_css'] ) ) {
            return;
        }

        wp_enqueue_style(
            'abstract-box',
            ABSTRACT_BOX_URL . 'assets/css/abstract-box.css',
            array(),
            ABSTRACT_BOX_VERSION
        );

        wp_add_inline_style( 'abstract-box', $this->build_inline_css( $options ) );
    }

    public function enqueue_admin( $hook_suffix ) {
        if ( 'settings_page_abstract-box' !== $hook_suffix ) {
            return;
        }

        /* Frontend stylesheet — needed so the live preview uses the real CSS classes. */
        wp_enqueue_style(
            'abstract-box',
            ABSTRACT_BOX_URL . 'assets/css/abstract-box.css',
            array(),
            ABSTRACT_BOX_VERSION
        );

        $options = Helpers::get_options();
        wp_add_inline_style( 'abstract-box', $this->build_inline_css( $options ) );

        wp_enqueue_style(
            'abstract-box-admin',
            ABSTRACT_BOX_URL . 'assets/css/admin-settings.css',
            array( 'wp-color-picker', 'abstract-box' ),
            ABSTRACT_BOX_VERSION
        );

        wp_enqueue_script(
            'abstract-box-admin',
            ABSTRACT_BOX_URL . 'assets/js/admin-settings.js',
            array( 'jquery', 'wp-color-picker' ),
            ABSTRACT_BOX_VERSION,
            true
        );

        /* Pass font stacks to JS so preview can update font-family live. */
        wp_localize_script( 'abstract-box-admin', 'abstractBoxAdmin', array(
            'fontStacks' => array(
                'sans-serif' => "'Helvetica Neue', Helvetica, Arial, sans-serif",
                'serif'      => "Georgia, 'Times New Roman', Times, serif",
                'humanist'   => "'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif",
                'monospace'  => "'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace",
                'slab'       => "'Rockwell', 'Courier Bold', Courier, Georgia, serif",
                'system'     => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, sans-serif",
            ),
        ) );
    }

    public function enqueue_block_editor() {
        if ( ! is_admin() ) {
            return;
        }

        $options = Helpers::get_options();

        wp_enqueue_style(
            'abstract-box',
            ABSTRACT_BOX_URL . 'assets/css/abstract-box.css',
            array(),
            ABSTRACT_BOX_VERSION
        );

        wp_add_inline_style( 'abstract-box', $this->build_inline_css( $options ) );
    }
}
