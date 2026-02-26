<?php
namespace Menj\AbstractBox;

defined( 'ABSPATH' ) || exit;

class Helpers {
    /** @var array|null Memoized defaults — built once per request, reused on every call. */
    private static $defaults = null;

    public static function get_defaults(): array {
        if ( null === self::$defaults ) {
            self::$defaults = array(
                'style'            => 'default',
                'use_theme_css'    => false,
                'title_color'      => '#1e293b',
                'text_color'       => '#334155',
                'bg_color'         => '#f8fafc',
                'bg_color_end'     => '#ffffff',
                'accent_color'     => '#3b82f6',
                'border_radius'    => 8,
                'font_family'      => 'sans-serif',
                'enable_schema'    => true,
                'schema_type'      => 'CreativeWork',
                'custom_css_class' => '',
                'hover_effect'     => true,
            );
        }
        return self::$defaults;
    }

    public static function get_option( $key ) {
        $defaults = self::get_defaults();
        $options  = get_option( 'abstract_box_options', array() );
        return isset( $options[ $key ] ) ? $options[ $key ] : ( isset( $defaults[ $key ] ) ? $defaults[ $key ] : null );
    }

    public static function get_options() {
        return wp_parse_args( get_option( 'abstract_box_options', array() ), self::get_defaults() );
    }

    public static function font_stack( $key = 'sans-serif' ) {
        $stacks = array(
            'sans-serif' => "'Helvetica Neue', Helvetica, Arial, sans-serif",
            'serif'      => "Georgia, 'Times New Roman', Times, serif",
            'system'     => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif",
        );
        return $stacks[ $key ] ?? $stacks['sans-serif'];
    }
}
