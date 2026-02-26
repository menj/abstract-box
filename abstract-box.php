<?php
/**
 * Plugin Name: Abstract Box
 * Plugin URI:  https://github.com/menj/abstract-box
 * Description: Adds a chic and modernist "Abstract" section to posts via a shortcode [abstract], with schema.org JSON-LD structured data output.
 * Version:     2.1.3
 * Author:      MENJ
 * Author URI:  https://github.com/menj
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: abstract-box
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

/* ── Constants ─────────────────────────────────────────────────────── */

define( 'ABSTRACT_BOX_VERSION', '2.1.3' );
define( 'ABSTRACT_BOX_FILE',    __FILE__ );
define( 'ABSTRACT_BOX_DIR',     plugin_dir_path( __FILE__ ) );
define( 'ABSTRACT_BOX_URL',     plugin_dir_url( __FILE__ ) );

/* ── Autoloader ────────────────────────────────────────────────────── */

spl_autoload_register( function ( $class ) {
    $prefix = 'Menj\\AbstractBox\\';
    $base_dir = ABSTRACT_BOX_DIR . 'inc/';

    $len = strlen( $prefix );
    if ( strncmp( $prefix, $class, $len ) !== 0 ) {
        return;
    }

    $relative_class = substr( $class, $len );
    $file = $base_dir . str_replace( '\\', '/', strtolower( $relative_class ) ) . '.php';

    if ( file_exists( $file ) ) {
        require $file;
    }
} );

/* ── Plugin Initialisation ─────────────────────────────────────────── */

function abstract_box_init() {
    ( new \Menj\AbstractBox\Admin\Settings() )->init();
    ( new \Menj\AbstractBox\Admin\Customizer() )->init();
    ( new \Menj\AbstractBox\Frontend\Block() )->init(); 
    ( new \Menj\AbstractBox\Frontend\Shortcode() )->init();
    ( new \Menj\AbstractBox\Frontend\Schema() )->init();
    ( new \Menj\AbstractBox\Assets() )->init();
}

add_action( 'plugins_loaded', 'abstract_box_init' );

/* ── Plugin Action Links ───────────────────────────────────────────── */

function abstract_box_plugin_action_links( $links ) {
    $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=abstract-box' ) ) . '">' . __( 'Settings', 'abstract-box' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'abstract_box_plugin_action_links' );
