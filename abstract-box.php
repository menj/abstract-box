<?php
/**
 * Plugin Name: Abstract Box
 * Plugin URI:  https://menj.net/abstract-box
 * Description: Adds a chic and modernist "Abstract" section to posts via a shortcode [abstract], with schema.org JSON-LD structured data output.
 * Version:     2.0.4
 * Author:      MENJ
 * Author URI:  https://menj.org
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: abstract-box
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

/* ── Constants ─────────────────────────────────────────────────────── */

define( 'ABSTRACT_BOX_VERSION', '2.0.4' );
define( 'ABSTRACT_BOX_FILE',    __FILE__ );
define( 'ABSTRACT_BOX_DIR',     plugin_dir_path( __FILE__ ) );
define( 'ABSTRACT_BOX_URL',     plugin_dir_url( __FILE__ ) );

/*
 * Internationalisation: WordPress automatically loads translations for
 * plugins that follow the standard text-domain slug convention (WP 4.6+).
 * The Text Domain and Domain Path headers above are sufficient.
 */

/* ── Module Loader ─────────────────────────────────────────────────── */

require_once ABSTRACT_BOX_DIR . 'includes/helpers.php';
require_once ABSTRACT_BOX_DIR . 'includes/shortcode.php';
require_once ABSTRACT_BOX_DIR . 'includes/schema.php';
require_once ABSTRACT_BOX_DIR . 'includes/enqueue.php';
require_once ABSTRACT_BOX_DIR . 'includes/customizer.php';

if ( is_admin() ) {
    require_once ABSTRACT_BOX_DIR . 'includes/admin-settings.php';
}
