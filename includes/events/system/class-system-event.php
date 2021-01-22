<?php
/**
 * System events
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * System Event class
 */
class System_Event extends Event {

	/**
	 * Add 'System' to the $types array
	 *
	 * @param array $types Event types.
	 * @return array
	 */
	public function types( $types = array() ) {
		if ( ! isset( $types['system'] ) ) {
			$types['system'] = __( 'System', 'hey-notify' );
		}
		return $types;
	}

	/**
	 * System events
	 *
	 * @param array $fields Action fields.
	 * @return array
	 */
	public function actions( $fields = array() ) {
		$fields[] = (
			Field::make( 'select', 'system', __( 'Action', 'hey-notify' ) )
				->set_options(
					array(
						'system_core_update'   => __( 'WordPress Update Available', 'hey-notify' ),
						'system_theme_update'  => __( 'Theme Update Available', 'hey-notify' ),
						'system_plugin_update' => __( 'Plugin Update Available', 'hey-notify' ),
					)
				)
				->set_conditional_logic(
					array(
						array(
							'field' => 'type',
							'value' => 'system',
						),
					)
				)
				->set_width( 50 )
		);
		return $fields;
	}

	/**
	 * Add the event actions
	 *
	 * @param object $notification Notification post object.
	 * @param object $event Event object.
	 * @return void
	 */
	public function watch( $notification, $event ) {
		$hook = new $this->hook( $notification, $event );

		switch ( $event->{$event->type} ) {
			case 'system_core_update':
				add_action( 'wp_version_check', array( $hook, 'system_core_update' ), 9 );
				add_action( 'wp_version_check', array( $hook, 'system_core_update_done' ), 10 );
				break;
			case 'system_theme_update':
				add_action( 'wp_update_themes', array( $hook, 'system_theme_update' ), 10 );
				add_action( 'wp_update_themes', array( $hook, 'system_theme_update_done' ), 11 );
				break;
			case 'system_plugin_update':
				add_action( 'wp_update_plugins', array( $hook, 'system_plugin_update' ), 10 );
				add_action( 'wp_update_plugins', array( $hook, 'system_plugin_update_done' ), 11 );
				break;
		}
	}

}

new System_Event( 'system', '\Hey_Notify\System_Hook' );
