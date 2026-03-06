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
            ABSTRACT_BOX_URL . 'assets/js/block-editor.js',
            array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components' ),
            ABSTRACT_BOX_VERSION,
            true
        );

        wp_register_style(
            'abstract-box-block-editor',
            ABSTRACT_BOX_URL . 'assets/css/block-editor.css',
            array(),
            ABSTRACT_BOX_VERSION
        );

        register_block_type( 'abstract-box/abstract', array(
            'editor_script'   => 'abstract-box-block-editor',
            'editor_style'    => 'abstract-box-block-editor',
            'render_callback' => [ $this, 'render_block' ],
            'attributes'      => array(
                'title'       => array( 'type' => 'string',  'default' => 'Abstract' ),
                'subtitle'    => array( 'type' => 'string',  'default' => '' ),
                'titleTag'    => array( 'type' => 'string',  'default' => 'div' ),
                'style'       => array( 'type' => 'string',  'default' => '' ),
                'bgColor'     => array( 'type' => 'string',  'default' => '' ),
                'bgColorEnd'  => array( 'type' => 'string',  'default' => '' ),
                'textColor'   => array( 'type' => 'string',  'default' => '' ),
                'titleColor'  => array( 'type' => 'string',  'default' => '' ),
                'accentColor' => array( 'type' => 'string',  'default' => '' ),
            ),
        ) );
    }

    public function render_block( $attributes, $content ) {
        // $content is the rendered inner HTML from InnerBlocks — the correct
        // channel for dynamic block content. We pass it directly to the
        // shortcode renderer instead of reading from attributes.
        $atts = array(
            'title'        => isset( $attributes['title'] )       ? $attributes['title']       : 'Abstract',
            'subtitle'     => isset( $attributes['subtitle'] )    ? $attributes['subtitle']    : '',
            'title_tag'    => isset( $attributes['titleTag'] )    ? $attributes['titleTag']    : 'div',
            'style'        => isset( $attributes['style'] )       ? $attributes['style']       : '',
            'bg_color'     => isset( $attributes['bgColor'] )     ? $attributes['bgColor']     : '',
            'bg_color_end' => isset( $attributes['bgColorEnd'] )  ? $attributes['bgColorEnd']  : '',
            'text_color'   => isset( $attributes['textColor'] )   ? $attributes['textColor']   : '',
            'title_color'  => isset( $attributes['titleColor'] )  ? $attributes['titleColor']  : '',
            'accent_color' => isset( $attributes['accentColor'] ) ? $attributes['accentColor'] : '',
        );

        return ( new Shortcode() )->render( $atts, $content );
    }
}
