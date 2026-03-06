<?php
namespace Menj\AbstractBox\Admin;

use Menj\AbstractBox\Helpers;
use WP_Customize_Color_Control;

defined( 'ABSPATH' ) || exit;

class Customizer {
    public function init() {
        add_action( 'customize_register', [ $this, 'register' ] );
        add_action( 'customize_preview_init', [ $this, 'preview_js' ] );
    }

    public function register( $wp_customize ) {
        $wp_customize->add_section( 'abstract_box_section', array(
            'title'    => __( 'Abstract Box', 'abstract-box' ),
            'priority' => 120,
        ) );

        $wp_customize->add_setting( 'abstract_box_options[style]', array(
            'default'           => 'default',
            'type'              => 'option',
            'sanitize_callback' => [ $this, 'sanitize_style_choice' ],
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

        $wp_customize->add_setting( 'abstract_box_options[use_theme_css]', array(
            'default'           => false,
            'type'              => 'option',
            'sanitize_callback' => [ $this, 'sanitize_checkbox' ],
        ) );

        $wp_customize->add_control( 'abstract_box_use_theme_css_control', array(
            'label'    => __( 'Use theme CSS only', 'abstract-box' ),
            'section'  => 'abstract_box_section',
            'settings' => 'abstract_box_options[use_theme_css]',
            'type'     => 'checkbox',
        ) );

        $colors = array(
            'title_color'  => __( 'Title Colour', 'abstract-box' ),
            'text_color'   => __( 'Text Colour', 'abstract-box' ),
            'bg_color'     => __( 'Background Start', 'abstract-box' ),
            'bg_color_end' => __( 'Background End', 'abstract-box' ),
            'accent_color' => __( 'Accent Colour', 'abstract-box' ),
        );

        $defaults = Helpers::get_defaults();

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

        $wp_customize->add_setting( 'abstract_box_options[font_family]', array(
            'default'           => 'sans-serif',
            'type'              => 'option',
            'sanitize_callback' => [ $this, 'sanitize_font_choice' ],
        ) );

        $wp_customize->add_control( 'abstract_box_font_control', array(
            'label'    => __( 'Font Family', 'abstract-box' ),
            'section'  => 'abstract_box_section',
            'settings' => 'abstract_box_options[font_family]',
            'type'     => 'select',
            'choices'  => array(
                'sans-serif' => __( 'Sans-Serif (Modernist)', 'abstract-box' ),
                'serif'      => __( 'Serif (Traditional)', 'abstract-box' ),
                'humanist'   => __( 'Humanist Sans', 'abstract-box' ),
                'monospace'  => __( 'Monospace', 'abstract-box' ),
                'slab'       => __( 'Slab Serif (Academic)', 'abstract-box' ),
                'system'     => __( 'System Default', 'abstract-box' ),
            ),
        ) );
    }

    public function sanitize_style_choice( $value ) {
        return in_array( $value, array( 'default', 'custom' ), true ) ? $value : 'default';
    }

    public function sanitize_checkbox( $value ) {
        return (bool) $value;
    }

    public function sanitize_font_choice( $value ) {
        return in_array( $value, array( 'sans-serif', 'serif', 'humanist', 'monospace', 'slab', 'system' ), true ) ? $value : 'sans-serif';
    }

    public function preview_js() {
        wp_enqueue_script(
            'abstract-box-customizer-preview',
            ABSTRACT_BOX_URL . 'assets/js/customizer-preview.js',
            array( 'customize-preview' ),
            ABSTRACT_BOX_VERSION,
            true
        );
    }
}
