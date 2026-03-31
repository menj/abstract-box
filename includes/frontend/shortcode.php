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
                'style'        => '',
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

        // Map style option value to modifier class.
        // 'default' and 'custom' are legacy values from pre-2.2.0 — migrate transparently.
        $style_class_map = array(
            'modern'    => 'abstract-box--modern',
            'academic'  => 'abstract-box--academic',
            'minimal'   => 'abstract-box--minimal',
            'card'      => 'abstract-box--card',
            'ruled'     => 'abstract-box--ruled',
            'editorial' => 'abstract-box--editorial',
            'summary'   => 'abstract-box--summary',
            'bulletin'  => 'abstract-box--bulletin',
            // Legacy aliases — kept for existing installs.
            'default'   => '',
            'custom'    => 'abstract-box--academic',
        );
        $style_key   = ! empty( $atts['style'] ) && isset( $style_class_map[ $atts['style'] ] )
                        ? $atts['style']
                        : $options['style'];
        $style_class = $style_class_map[ $style_key ] ?? '';
        if ( $style_class ) {
            $classes[] = $style_class;
        }

        if ( ! empty( $options['hover_effect'] ) ) {
            $classes[] = 'abstract-box--hover';
        }

        if ( 'compact' === ( $options['box_width'] ?? 'full' ) ) {
            $classes[] = 'abstract-box--compact';
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
        include ABSTRACT_BOX_DIR . 'includes/views/shortcode.php';
        return ob_get_clean();
    }
}
