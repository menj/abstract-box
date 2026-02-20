<?php
/**
 * Uninstall handler — clean up plugin data.
 *
 * This file runs when the plugin is deleted via the WordPress admin.
 *
 * @package AbstractBox
 */

// Abort if not called by WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Remove plugin options.
delete_option( 'abstract_box_options' );

// Remove legacy theme_mod values from the original v1.x plugin.
remove_theme_mod( 'abstract_box_use_theme_css' );
remove_theme_mod( 'abstract_box_style' );
