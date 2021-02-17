<?php
/**
 * Scripts
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Scripts;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\admin_enqueue_scripts' );

/**
 * Admin Scripts
 *
 * @return void
 */
function admin_enqueue_scripts() {

	// phpcs:ignore
	if ( ! isset( $_GET['post_type'] ) ) {
		return;
	}
	// phpcs:ignore
	if ( ! isset( $_GET['page'] ) ) {
		return;
	}
	// phpcs:ignore
	if ( 'hey_notify' !== $_GET['post_type'] ) {
		return;
	}
	// phpcs:ignore
	if ( 'settings' !== $_GET['page'] ) {
		return;
	}

	wp_enqueue_script(
		'hey-notify-admin',
		HEY_NOTIFY_PLUGIN_URL . 'assets/js/admin.js',
		array( 'wp-api-request' ),
		HEY_NOTIFY_VERSION,
		true
	);

	wp_localize_script(
		'hey-notify-admin',
		'heynotify',
		array(
			'messages' => array(
				'done'    => __( 'Done Refreshing Custom Post Types', 'hey-notify' ),
				'running' => __( 'Refreshing Custom Post Types...', 'hey-notify' ),
				'error'   => __( 'Oops, there was an error', 'hey-notify' ),
			),
		)
	);
}
