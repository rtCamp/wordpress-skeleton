<?php
/**
 * Plugin Name: Enforce 2FA
 * Description: This is mu-plugin to enforce every user to enable 2FA
 * Author:      Shub
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Enforce_2FA
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Do not allow API requests from 2fa users.
add_filter( 'two_factor_user_api_login_enable', '__return_false', 1 ); // Hook in early to allow overrides.

/**
 * Should Force 2FA.
 *
 * @return bool
 */
function e2fa_should_force_two_factor() {

	return true;
}

/**
 * Check if 2FA is forced.
 *
 * @return bool
 */
function e2fa_is_two_factor_forced() {

	// If we're not forcing 2FA, return early.
	if ( ! e2fa_should_force_two_factor() ) {
		return false;
	}

	return apply_filters( 'e2fa_is_two_factor_forced', false );
}

/**
 * Force 2FA.
 */
function e2fa_enforce_two_factor_plugin() {

	// Check if user is logged in.
	if ( is_user_logged_in() ) {

		$cap     = apply_filters( 'two_factor_enforcement_cap', 'manage_options' );
		$limited = current_user_can( $cap );

		// Calculate current_user_can outside map_meta_cap to avoid callback loop.
		add_filter(
			'e2fa_is_two_factor_forced',
			function () use ( $limited ) {
				return $limited;
			},
			9
		);
	}
}

// If we're not installing, enable the two-factor plugin on muplugins_loaded.
if ( ! defined( 'WP_INSTALLING' ) ) {

	// Enable the two-factor plugin after all mu-plugins have been loaded.
	add_action( 'muplugins_loaded', 'e2fa_enable_two_factor_plugin' );
}
