<?php
/**
 * Fields
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Fields;

use Carbon_Fields\Container;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\boot' );
add_action( 'carbon_fields_register_fields', __NAMESPACE__ . '\\service_container' );
add_action( 'carbon_fields_register_fields', __NAMESPACE__ . '\\notification_container' );
add_action( 'carbon_fields_register_fields', __NAMESPACE__ . '\\settings' );
add_action( 'carbon_fields_post_meta_container_saved', __NAMESPACE__ . '\\save_events_meta' );
add_action( 'admin_head', __NAMESPACE__ . '\\admin_head' );

/**
 * Boot up Carbon Fields
 *
 * @return void
 */
function boot() {
	\Carbon_Fields\Carbon_Fields::boot();
}

/**
 * Service post meta container
 *
 * @return void
 */
function service_container() {
	Container::make( 'post_meta', __( 'Service', 'hey-notify' ) )
		->where( 'post_type', '=', 'hey_notify' )
		->set_context( 'normal' )
		->set_priority( 'default' )
		->add_fields( apply_filters( 'hey_notify_service_fields', array() ) );
}

/**
 * Notification post meta container
 *
 * @return void
 */
function notification_container() {
	Container::make( 'post_meta', __( 'Events', 'hey-notify' ) )
		->where( 'post_type', '=', 'hey_notify' )
		->set_context( 'normal' )
		->set_priority( 'default' )
		->add_fields( apply_filters( 'hey_notify_event_fields', array() ) );
}

/**
 * Settings
 *
 * @return void
 */
function settings() {
	Container::make( 'theme_options', __( 'Hey Notify Settings', 'hey-notify' ) )
		->set_page_parent( 'edit.php?post_type=hey_notify' )
		->set_page_menu_title( __( 'Settings', 'hey-notify' ) )
		->set_page_file( 'settings' )
		->add_tab(
			__( 'General', 'hey-notify' ),
			apply_filters( 'hey_notify_settings_general', array() )
		)
		->add_tab(
			__( 'Uninstall', 'hey-notify' ),
			apply_filters( 'hey_notify_settings_uninstall', array() )
		);
}

/**
 * Save events meta
 *
 * @param int $post_id Post ID.
 * @return varies
 */
function save_events_meta( $post_id ) {
	if ( \get_post_type( $post_id ) !== 'hey_notify' ) {
		return;
	}

	$events = \carbon_get_post_meta( $post_id, 'hey_notify_events' );
	\update_post_meta( $post_id, '_hey_notify_events_json', \wp_json_encode( $events ) );
}

/**
 * Content to output in the Admin head
 *
 * @return void
 */
function admin_head() {
	global $pagenow;

	if (
		'edit.php' !== $pagenow
		&& 'post.php' !== $pagenow
		&& 'post-new.php' !== $pagenow
	) {
		return;
	}

	if (
		( ! isset( $_GET['post_type'] ) || 'hey_notify' !== $_GET['post_type'] ) // phpcs:ignore
		&& ( ! isset( $_GET['action'] ) || 'edit' !== $_GET['action'] ) // phpcs:ignore
	) {
		return;
	}

	?>
	<style>
		body .cf-container-theme-options .cf-radio__list-item, body .cf-radio-image .cf-radio__list-item { flex: 0 0 120px; }
	</style>
	<?php
}
