<?php
namespace Menj\AbstractBox\Frontend;

defined( 'ABSPATH' ) || exit;

class Block {
    public function init() {
        add_action( 'init', [ $this, 'register_block' ] );
    }

    public function register_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        wp_register_script(
            'abstract-box-block-editor',
            ABSTRACT_BOX_URL . 'js/block-editor.js',
            array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' ),
            ABSTRACT_BOX_VERSION
        );

        register_block_type( 'abstract-box/abstract', array(
            'editor_script'   => 'abstract-box-block-editor',
            'render_callback' => [ $this, 'render_block' ]
        ) );
    }

    public function render_block( $attributes, $content ) {
        $atts = array(
            'title'        => isset( $attributes['title'] )       ? $attributes['title']       : 'Abstract',
            'subtitle'     => isset( $attributes['subtitle'] )    ? $attributes['subtitle']    : '',
            'title_tag'    => isset( $attributes['titleTag'] )    ? $attributes['titleTag']    : 'div',
            'bg_color'     => isset( $attributes['bgColor'] )     ? $attributes['bgColor']     : '',
            'bg_color_end' => isset( $attributes['bgColorEnd'] )  ? $attributes['bgColorEnd']  : '',
            'text_color'   => isset( $attributes['textColor'] )   ? $attributes['textColor']   : '',
            'title_color'  => isset( $attributes['titleColor'] )  ? $attributes['titleColor']  : '',
            'accent_color' => isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '',
        );

        $inner_content = isset( $attributes['content'] ) ? $attributes['content'] : '';

        return ( new Shortcode() )->render( $atts, $inner_content );
    }
}
