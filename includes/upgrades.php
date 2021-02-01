<?php
/**
 * Upgrades
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Upgrades;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions.
add_action( 'init', __NAMESPACE__ . '\\version_check', 20 );

/**
 * Version check
 *
 * @return void
 */
function version_check() {

	// Get the version from the options.
	$version = get_option( 'hey_notify_version' );

	// If it's empty, assign it a version number.
	if ( ! $version ) {
		$version = '1.2.0';
	}

	// Check if we've already run this.
	if ( version_compare( $version, HEY_NOTIFY_VERSION, '=' ) ) {
		return;
	}

	// Version is before 1.2.1.
	if ( version_compare( $version, '1.2.1', '<' ) ) {
		v1_2_1_upgrade();
	}

	update_option( 'hey_notify_version', HEY_NOTIFY_VERSION );

}
/**
 * Version 1.2.1 upgrade
 *
 * @return void
 */
function v1_2_1_upgrade() {
	$custom_post_types = \get_post_types(
		array(
			'_builtin' => false,
		),
		'objects',
		'and'
	);

	\update_option(
		'hey_notify_custom_post_types',
		\wp_json_encode( $custom_post_types )
	);
}
