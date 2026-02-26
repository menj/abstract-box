<?php
namespace Menj\AbstractBox\Frontend;

use Menj\AbstractBox\Helpers;

defined( 'ABSPATH' ) || exit;

class Shortcode {
    public function init() {
        add_shortcode( 'abstract', [ $this, 'render' ] );
    }

    public function render( $atts = array(), $content = '' ) {
        $content = (string) $content;
        $atts = shortcode_atts(
            array(
                'title'        => __( 'Abstract', 'abstract-box' ),
                'subtitle'     => '',
                'title_tag'    => 'div',
                'bg_color'     => '',
                'bg_color_end' => '',
                'text_color'   => '',
                'title_color'  => '',
                'accent_color' => '',
            ),
            $atts,
            'abstract'
        );

        $allowed_tags = array( 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'p', 'span' );
        $title_tag    = in_array( strtolower( $atts['title_tag'] ), $allowed_tags, true ) ? strtolower( $atts['title_tag'] ) : 'div';

        $options = Helpers::get_options();

        $classes = array( 'abstract-box' );

        if ( 'custom' === $options['style'] ) {
            $classes[] = 'abstract-box--custom';
        }

        if ( ! empty( $options['hover_effect'] ) ) {
            $classes[] = 'abstract-box--hover';
        }

        if ( ! empty( $options['custom_css_class'] ) ) {
            $classes[] = sanitize_html_class( $options['custom_css_class'] );
        }

        $class_attr = implode( ' ', $classes );

        // Inline CSS Overrides
        $inline_style = '';
        if ( ! empty( $atts['bg_color'] ) ) {
            $inline_style .= '--ab-bg-color: ' . sanitize_hex_color( $atts['bg_color'] ) . '; ';
        }
        if ( ! empty( $atts['bg_color_end'] ) ) {
            $inline_style .= '--ab-bg-color-end: ' . sanitize_hex_color( $atts['bg_color_end'] ) . '; ';
        }
        if ( ! empty( $atts['text_color'] ) ) {
            $inline_style .= '--ab-text-color: ' . sanitize_hex_color( $atts['text_color'] ) . '; ';
        }
        if ( ! empty( $atts['title_color'] ) ) {
            $inline_style .= '--ab-title-color: ' . sanitize_hex_color( $atts['title_color'] ) . '; ';
        }
        if ( ! empty( $atts['accent_color'] ) ) {
            $inline_style .= '--ab-accent-color: ' . sanitize_hex_color( $atts['accent_color'] ) . '; ';
        }

        ob_start();
        include ABSTRACT_BOX_DIR . 'views/shortcode.php';
        return ob_get_clean();
    }
}
