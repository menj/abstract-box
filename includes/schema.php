<?php
/**
 * Schema.org JSON-LD output for the abstract shortcode.
 *
 * @package AbstractBox
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output schema.org JSON-LD for the Abstract shortcode.
 *
 * - Only outputs on singular front-end views.
 * - Extracts the first [abstract] shortcode instance from post content.
 * - Outputs a minimal CreativeWork node to reduce conflicts with SEO plugins.
 */
function abstract_box_output_schema_jsonld() {
    // Bail if schema is disabled.
    if ( ! abstract_box_get_option( 'enable_schema' ) ) {
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

    // Allow themes/plugins to disable output.
    $enabled = apply_filters( 'abstract_box_output_schema', true, $post );
    if ( ! $enabled ) {
        return;
    }

    if ( ! has_shortcode( $post->post_content, 'abstract' ) ) {
        return;
    }

    $pattern = get_shortcode_regex( array( 'abstract' ) );
    if ( ! preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches, PREG_SET_ORDER ) ) {
        return;
    }

    $first = $matches[0];

    // Shortcode parts: [0]=full match, [3]=attrs, [5]=content.
    $attrs = array();
    if ( isset( $first[3] ) && '' !== $first[3] ) {
        $attrs = shortcode_parse_atts( $first[3] );
        if ( ! is_array( $attrs ) ) {
            $attrs = array();
        }
    }

    $shortcode_content = isset( $first[5] ) ? $first[5] : '';
    if ( '' === $shortcode_content ) {
        return;
    }

    // Convert to plain text for schema abstract.
    $abstract_text = wp_strip_all_tags( do_shortcode( $shortcode_content ), true );
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

    // Determine schema type from settings (filterable).
    $schema_type = apply_filters(
        'abstract_box_schema_type',
        abstract_box_get_option( 'schema_type' ),
        $post
    );

    // Build unique @id fragment for multiple abstracts.
    $fragment_index = 1;
    $fragment       = '#abstract';
    if ( count( $matches ) > 1 ) {
        $fragment = '#abstract-' . $fragment_index;
    }

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

    // Optional subtitle from shortcode attrs.
    if ( ! empty( $attrs['subtitle'] ) ) {
        $schema['alternativeName'] = wp_strip_all_tags( (string) $attrs['subtitle'], true );
    }

    $schema = apply_filters( 'abstract_box_schema_payload', $schema, $post, $attrs, $shortcode_content );

    echo "\n" . '<script type="application/ld+json">'
        . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
        . '</script>' . "\n";

    $done = true;
}

add_action( 'wp_head', 'abstract_box_output_schema_jsonld', 20 );
