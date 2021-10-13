<?php
/**
 * Plugin Name: Plugin and Theme Update Notification
 * Author: rtCamp, Chandra Patel, Rutvik Savsani
 * Description: Extension to show notification's for themes and plugins when `DISALLOW_FILE_MODS` is true.
 *
 * Note : The Notification for themes will work on only multisite-setups.
 *
 * @package plugin-and-theme-update-notification
 */

/**
 * Display plugin update notification when DISALLOW_FILE_MODS constant set to true.
 */
function rt_wp_plugin_update_rows() {

	if ( ! defined( 'DISALLOW_FILE_MODS' ) || false === DISALLOW_FILE_MODS ) {
		return;
	}

	$plugins = get_site_transient( 'update_plugins' );

	if ( isset( $plugins->response ) && is_array( $plugins->response ) ) {

		$plugins = array_keys( $plugins->response );

		foreach ( $plugins as $plugin_file ) {
			add_action( "after_plugin_row_$plugin_file", 'wp_plugin_update_row', 10, 2 );
		}
	}

}
add_action( 'load-plugins.php', 'rt_wp_plugin_update_rows', 30 );

/**
 * Display theme update notification when DISALLOW_FILE_MODS constant set to true.
 *
 * Note: This will work only for Multisite setup.
 */
function rt_wp_theme_update_rows() {

	if ( ! defined( 'DISALLOW_FILE_MODS' ) || false === DISALLOW_FILE_MODS ) {
		return;
	}

	$themes = get_site_transient( 'update_themes' );

	if ( isset( $themes->response ) && is_array( $themes->response ) ) {
		$themes = array_keys( $themes->response );
		foreach ( $themes as $theme ) {
			add_action( "after_theme_row_$theme", 'wp_theme_update_row', 10, 2 );
		}
	}
}

add_action( 'load-themes.php', 'rt_wp_theme_update_rows', 30 );
