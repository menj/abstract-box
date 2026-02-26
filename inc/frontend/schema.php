<?php
namespace Menj\AbstractBox\Frontend;

use Menj\AbstractBox\Helpers;

defined( 'ABSPATH' ) || exit;

class Schema {
    public function init() {
        add_action( 'wp_head', [ $this, 'output_jsonld' ], 20 );
    }

    public function output_jsonld() {
        if ( ! Helpers::get_option( 'enable_schema' ) ) {
            return;
        }

        if ( is_admin() || ! is_singular() ) {
            return;
        }

        static $done = false;
        if ( $done ) {
            return;
        }

        global $post;
        if ( empty( $post ) || empty( $post->post_content ) ) {
            return;
        }

        $enabled = apply_filters( 'abstract_box_output_schema', true, $post );
        if ( ! $enabled ) {
            return;
        }

        $has_shortcode = has_shortcode( $post->post_content, 'abstract' );
        $has_block     = has_block( 'abstract-box/abstract', $post->post_content );

        if ( ! $has_shortcode && ! $has_block ) {
            return;
        }

        $abstract_text = '';
        $attrs         = array();
        $fragment_name = 'abstract';

        if ( $has_block ) {
            $blocks = parse_blocks( $post->post_content );
            foreach ( $blocks as $block ) {
                if ( 'abstract-box/abstract' === $block['blockName'] ) {
                    $attrs = isset( $block['attrs'] ) ? $block['attrs'] : array();
                    $content_html = isset( $attrs['content'] ) ? $attrs['content'] : '';
                    if ( empty( $content_html ) && ! empty( $block['innerHTML'] ) ) {
                        $content_html = $block['innerHTML'];
                    }
                    $abstract_text = wp_strip_all_tags( do_shortcode( $content_html ), true );
                    break;
                }
            }
        } 
        
        if ( empty( $abstract_text ) && $has_shortcode ) {
            $pattern = get_shortcode_regex( array( 'abstract' ) );
            if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches, PREG_SET_ORDER ) ) {
                $first = $matches[0];
                if ( isset( $first[3] ) && '' !== $first[3] ) {
                    $parsed = shortcode_parse_atts( $first[3] );
                    if ( is_array( $parsed ) ) {
                        $attrs = $parsed;
                    }
                }
                $shortcode_content = isset( $first[5] ) ? $first[5] : '';
                $abstract_text = wp_strip_all_tags( do_shortcode( $shortcode_content ), true );
            }
        }

        $abstract_text = preg_replace( '/\s+/u', ' ', $abstract_text );
        $abstract_text = trim( $abstract_text );

        if ( '' === $abstract_text ) {
            return;
        }

        $permalink = get_permalink( $post );
        if ( ! $permalink ) {
            return;
        }

        $author_name = '';
        $author_id   = (int) get_post_field( 'post_author', $post->ID );
        if ( $author_id > 0 ) {
            $author_name = get_the_author_meta( 'display_name', $author_id );
        }

        $schema_type = apply_filters(
            'abstract_box_schema_type',
            Helpers::get_option( 'schema_type' ),
            $post
        );

        $fragment = '#abstract';

        $schema = array(
            '@context'      => 'https://schema.org',
            '@type'         => $schema_type,
            '@id'           => trailingslashit( $permalink ) . $fragment,
            'url'           => $permalink,
            'name'          => get_the_title( $post ),
            'abstract'      => $abstract_text,
            'datePublished' => get_the_date( 'c', $post ),
            'dateModified'  => get_the_modified_date( 'c', $post ),
        );

        if ( ! empty( $author_name ) ) {
            $schema['author'] = array(
                '@type' => 'Person',
                'name'  => $author_name,
            );
        }

        if ( ! empty( $attrs['subtitle'] ) ) {
            $schema['alternativeName'] = wp_strip_all_tags( (string) $attrs['subtitle'], true );
        }

        $shortcode_content = isset( $shortcode_content ) ? $shortcode_content : ( isset( $content_html ) ? $content_html : '' );
        
        $schema = apply_filters( 'abstract_box_schema_payload', $schema, $post, $attrs, $shortcode_content );

        // wp_json_encode with no extra flags: forward slashes and unicode are safely
        // escaped by default, preventing any </script> breakout sequences in the output.
        $json = wp_json_encode( $schema );
        if ( false === $json ) {
            return;
        }

        echo "\n<script type='application/ld+json'>" . $json . '</script>' . "\n";

        $done = true;
    }
}
