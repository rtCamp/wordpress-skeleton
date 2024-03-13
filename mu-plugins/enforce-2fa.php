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

// muplugins_loaded fires before cookie constants are set.
if ( is_multisite() ) {
	ms_cookie_constants();
}

// Set the cookie constants if not set.
wp_cookie_constants();

// Define the cookies for Jetpack SSO.
define( 'E2FA_IS_JETPACK_SSO_COOKIE', AUTH_COOKIE . '_e2fa_jetpack_sso' );
define( 'E2FA_IS_JETPACK_SSO_2SA_COOKIE', AUTH_COOKIE . '_e2fa_jetpack_sso_2sa' );

// Set the cookies when the user logs in.
add_action(
	'jetpack_sso_handle_login',
	function ( $user, $user_data ) {

		add_action(
			'set_auth_cookie',
			function ( $auth_cookie, $expire, $expiration, $user_id, $scheme, $token ) use ( $user_data ) {

				// Check if the site is secure.
				$secure = is_ssl();

				// Set the cookies for Jetpack SSO.
				$sso_cookie = wp_generate_auth_cookie( $user_id, $expire, 'secure_auth', $token );
				setcookie( E2FA_IS_JETPACK_SSO_COOKIE, $sso_cookie, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true );

				// Set the cookies for Jetpack SSO 2SA.
				if ( $user_data->two_step_enabled ) {

					// Set the cookies for Jetpack SSO 2SA.
					$sso_2sa_cookie = wp_generate_auth_cookie( $user_id, $expire, 'secure_auth', $token );
					setcookie( E2FA_IS_JETPACK_SSO_2SA_COOKIE, $sso_2sa_cookie, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
				}
			},
			10,
			6
		);
	},
	10,
	2
);

// Clear the cookies when the user logs out.
add_action(
	'clear_auth_cookie',
	function () {

		if ( ! headers_sent() ) {

			// Clear the cookies for Jetpack SSO.
			setcookie( E2FA_IS_JETPACK_SSO_COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );

			// Clear the cookies for Jetpack SSO 2SA.
			setcookie( E2FA_IS_JETPACK_SSO_2SA_COOKIE, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		}
	}
);

/**
 * Check if the user is logged in with Jetpack SSO.
 *
 * @return bool
 */
function e2fa_is_jetpack_sso() {

	// If the user is not logged in, return early.
	if ( ! is_user_logged_in() ) {
		return false;
	}

	// If the user is not logged in with Jetpack SSO, return early.
	if ( ! isset( $_COOKIE[ E2FA_IS_JETPACK_SSO_COOKIE ] ) ) {
		return false;
	}

	// Check if the user is logged in with Jetpack SSO.
	$cookie = sanitize_text_field( wp_unslash( $_COOKIE[ E2FA_IS_JETPACK_SSO_COOKIE ] ) );
	return wp_validate_auth_cookie( $cookie, 'secure_auth' );
}

/**
 * Check if the user is logged in with Jetpack SSO and has 2SA enabled.
 *
 * @return bool
 */
function e2fa_is_jetpack_sso_two_step() {

	// If the user is not logged in, return early.
	if ( ! e2fa_is_jetpack_sso() ) {
		return false;
	}

	// If the user is not logged in with Jetpack SSO 2SA, return early.
	if ( ! isset( $_COOKIE[ E2FA_IS_JETPACK_SSO_2SA_COOKIE ] ) ) {
		return false;
	}

	// Check if the user is logged in with Jetpack SSO 2SA.
	$cookie = sanitize_text_field( wp_unslash( $_COOKIE[ E2FA_IS_JETPACK_SSO_2SA_COOKIE ] ) );
	return wp_validate_auth_cookie( $cookie, 'secure_auth' );
}

/**
 * Should Force 2FA.
 *
 * @return bool
 */
function e2fa_should_force_two_factor() {

	// Fallout, if Localhost.
	if ( 'local' === wp_get_environment_type() || ( isset( $_SERVER['HTTP_HOST'] ) && in_array( $_SERVER['HTTP_HOST'], array( 'localhost', '127.0.0.1', '[::1]', '192.168.0.1', '192.168.1.1' ), true ) ) ) {
		return false;
	}

	// If we're not forcing 2FA, return early.
	if ( ! class_exists( 'Two_Factor_Core' ) ) {
		return false;
	}

	// Check if user have 2FA enabled.
	if ( apply_filters( 'is_user_using_two_factor', false ) ) {
		return false;
	}

	// Don't force 2FA for Jetpack SSO users that have Two-step enabled.
	if ( e2fa_is_jetpack_sso_two_step() ) {
		return false;
	}

	// If it's a request attempting to connect a local user to a WordPress.com user via XML-RPC or REST, allow it through.
	if ( e2fa_is_jetpack_authorize_request() ) {
		return false;
	}

	// Allow custom SSO solutions.
	if ( e2fa_use_custom_sso() ) {
		return false;
	}

	return true;
}

/**
 * Use Custom SSO.
 *
 * @return bool
 */
function e2fa_use_custom_sso() {

	$custom_sso_enabled = apply_filters( 'e2fa_use_custom_sso', null );
	if ( null !== $custom_sso_enabled ) {
		return $custom_sso_enabled;
	}

	// Check for OneLogin SSO.
	if ( function_exists( 'is_saml_enabled' ) && is_saml_enabled() ) {
		return true;
	}

	// Check for SimpleSaml.
	if ( function_exists( '\HumanMade\SimpleSaml\instance' ) && \HumanMade\SimpleSaml\instance() ) {
		return true;
	}

	return false;
}

/**
 * Check if the current request is a Jetpack REST API request.
 *
 * @return bool
 */
function e2fa_is_jetpack_authorize_request() {

	return (
		// XML-RPC Jetpack authorize request
		// This works with the classic core XML-RPC endpoint, but not
		// Jetpack's alternate endpoint.
		defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST
		&& isset( $_GET['for'] ) && 'jetpack' === $_GET['for']  // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		&& isset( $GLOBALS['wp_xmlrpc_server'], $GLOBALS['wp_xmlrpc_server']->message, $GLOBALS['wp_xmlrpc_server']->message->methodName )
		&& 'jetpack.remoteAuthorize' === $GLOBALS['wp_xmlrpc_server']->message->methodName
	) || (
		// REST Jetpack authorize request.
		defined( 'REST_REQUEST' ) && REST_REQUEST
		&& isset( $GLOBALS['wp_rest_server'] )
		&& e2fa_is_jetpack_authorize_rest_request()
	);
}

/**
 * Setter/Getter to keep track of whether the current request is a REST
 * API request for /jetpack/v4/remote_authorize request that connects a
 * WordPress.com user to a local user.
 *
 * @param bool|null $set Set the value of the flag.
 */
function e2fa_is_jetpack_authorize_rest_request( $set = null ) {

	static $is_jetpack_authorize_rest_request = false;
	if ( ! is_null( $set ) ) {
		$is_jetpack_authorize_rest_request = $set;
	}

	return $is_jetpack_authorize_rest_request;
}

/**
 * Hooked to the `rest_request_before_callbacks` filter to keep track of
 * whether the current request is a REST API request for
 * /jetpack/v4/remote_authorize request that connects WordPress.com user
 * to a local user.
 *
 * @param WP_HTTP_Response $response The response object.
 * @param array            $handler  The request handler.
 *
 * @return WP_HTTP_Response - it's attached to a filter.
 */
function e2fa_is_jetpack_authorize_rest_request_hook( $response, $handler ) {

	// If the request is for /jetpack/v4/remote_authorize, set the flag.
	if ( isset( $handler['callback'] ) && 'Jetpack_Core_Json_Api_Endpoints::remote_authorize' === $handler['callback'] ) {
		e2fa_is_jetpack_authorize_rest_request( true );
	}
	return $response;
}

// Hook to keep track of whether the current request is a REST API request for /jetpack/v4/remote_authorize request that connects a WordPress.com user to a local user.
add_filter( 'rest_request_before_callbacks', 'e2fa_is_jetpack_authorize_rest_request_hook', 10, 2 );

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

		// If the user is not using 2FA, force them to use it.
		$is_user_using_two_factor = Two_Factor_Core::is_user_using_two_factor();

		add_filter(
			'is_user_using_two_factor',
			function () use ( $is_user_using_two_factor ) {
				return $is_user_using_two_factor;
			}
		);

		// Show admin notice, if user is not using 2FA.
		add_action( 'admin_notices', 'e2fa_two_factor_admin_notice' );
	}
}

// If we're not installing, enable the two-factor plugin on muplugins_loaded.
if ( ! defined( 'WP_INSTALLING' ) ) {

	// Enable the two-factor plugin after all mu-plugins have been loaded.
	add_action( 'muplugins_loaded', 'e2fa_enable_two_factor_plugin' );
}

/**
 * Enable 2FA Plugin.
 *
 * @return void
 */
function e2fa_enable_two_factor_plugin() {

	// If the two-factor plugin is not enabled, return early.
	$enable_two_factor = apply_filters( 'enable_two_factor', true );
	if ( true !== $enable_two_factor ) {
		return;
	}

	// If the two-factor plugin is not installed, return early.
	if ( file_exists( WP_PLUGIN_DIR . '/two-factor/two-factor.php' ) ) {

		// Load the two-factor plugin.
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! is_plugin_active( 'two-factor/two-factor.php' ) ) {
			activate_plugin( 'two-factor/two-factor.php' );
		}
		add_action( 'set_current_user', 'e2fa_enforce_two_factor_plugin' );
	}
}

/**
 * Ristrict block editor screen.
 *
 * @return bool
 */
function e2fa_should_show_notice_on_current_screen() {

	$screen = get_current_screen();

	// Don't show on the "Edit Post" screen as it interferes with the Block Editor.
	if ( $screen->is_block_editor() ) {
		return false;
	}

	return true;
}

/**
 * Admin Notice for 2FA.
 *
 * @return void
 */
function e2fa_two_factor_admin_notice() {

	// If 2FA is not forced, return early.
	if ( ! e2fa_is_two_factor_forced() ) {
		return;
	}

	if ( ! e2fa_should_show_notice_on_current_screen() ) {
		return;
	}

	?>
	<div id="e2fa-error" class="notice-error wrap clearfix" style="align-items: center;background: #ffffff;border-left-width:4px;border-left-style:solid;border-radius: 6px;display: flex;margin-top: 30px;padding: 30px;line-height: 2em;">
			<div class="dashicons dashicons-warning" style="display:flex;float:left;margin-right:2rem;font-size:38px;align-items:center;margin-left:-20px;color:#ffb900;"></div>
			<div>
				<p style="font-weight:bold; font-size:16px;">Two Factor Authentication is required to edit content on this site.</p>

				<p>For the safety and security of this site, your account access has been downgraded. Please enable two-factor authentication to restore your access.</p>

				<p>
					<a href="<?php echo esc_url( admin_url( 'profile.php#two-factor-options' ) ); ?>" class="button button-primary">
						Enable Two-factor Authentication
					</a>
				</p>
			</div>
	</div>
	<?php
}
