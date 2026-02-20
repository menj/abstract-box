<?php
/**
 * Shortcode registration and rendering.
 *
 * @package AbstractBox
 */

defined( 'ABSPATH' ) || exit;

/**
 * Render the [abstract] shortcode.
 *
 * @param  array  $atts    Shortcode attributes.
 * @param  string $content Enclosed content.
 * @return string          HTML output.
 */
function abstract_box_shortcode( $atts = array(), $content = null ) {
    $atts = shortcode_atts(
        array(
            'title'    => __( 'Abstract', 'abstract-box' ),
            'subtitle' => '',
        ),
        $atts,
        'abstract'
    );

    $options = abstract_box_get_options();

    // Build CSS classes.
    $classes = array( 'abstract-box' );

    if ( 'custom' === $options['style'] ) {
        $classes[] = 'abstract-box--custom';
    }

    if ( $options['hover_effect'] ) {
        $classes[] = 'abstract-box--hover';
    }

    if ( ! empty( $options['custom_css_class'] ) ) {
        $classes[] = sanitize_html_class( $options['custom_css_class'] );
    }

    $class_attr = esc_attr( implode( ' ', $classes ) );

    // Build HTML — use <div> wrapper for content to allow block-level elements.
    $html  = '<div class="' . $class_attr . '">';
    $html .= '<h2 class="abstract-box__title">' . esc_html( $atts['title'] ) . '</h2>';

    if ( ! empty( $atts['subtitle'] ) ) {
        $html .= '<h3 class="abstract-box__subtitle">' . esc_html( $atts['subtitle'] ) . '</h3>';
    }

    $html .= '<div class="abstract-box__content">' . wp_kses_post( do_shortcode( $content ) ) . '</div>';
    $html .= '</div>';

    return $html;
}

add_shortcode( 'abstract', 'abstract_box_shortcode' );
