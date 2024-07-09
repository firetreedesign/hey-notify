<?php
/**
 * Licenses
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Licenses;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Activate the license key when settings are saved.
add_action( 'admin_init', __NAMESPACE__ . '\\activate_license' );

/**
 * Activate the license key
 *
 * @return void
 */
function activate_license() {
	if ( ! isset( $_REQUEST['hey_notify_license_key-nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['hey_notify_license_key-nonce'] ), 'hey_notify_license_key-nonce' ) ) ) {
		wp_die( esc_html__( 'Nonce verification failed', 'hey-notify' ), esc_html__( 'Error', 'hey-notify' ), array( 'response' => 403 ) );
	}

	if ( ! isset( $_POST['hey_notify_settings_licenses'] ) ) {
		return;
	}

	foreach ( $_POST as $key => $value ) {
		if ( false !== strpos( $key, 'license_key_deactivate' ) ) {
			return;
		}
	}
}
