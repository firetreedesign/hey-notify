<?php
/**
 * System events
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * System Event class
 */
class System_Event extends Event {

	/**
	 * Sets the event names for different system events.
	 *
	 * @param string $type Event type.
	 */
	public function set_event_names( $type ) {
		$this->event_name_array = array(
			'system_core_update'        => __( 'WordPress Update Available', 'hey-notify' ),
			'system_plugin_update'      => __( 'Plugin Update Available', 'hey-notify' ),
			'system_plugin_activated'   => __( 'Plugin Activated', 'hey-notify' ),
			'system_plugin_deactivated' => __( 'Plugin Deactivated', 'hey-notify' ),
			'system_theme_update'       => __( 'Theme Update Available', 'hey-notify' ),
			'system_theme_changed'      => __( 'Theme Changed', 'hey-notify' ),
		);
	}

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
	 * Page events
	 *
	 * @param array $fields Action fields.
	 * @return array
	 */
	public function actions( $fields = array() ) {
		array_push(
			$fields,
			array(
				'field_type'        => 'select',
				'field_name'        => 'system',
				'field_label'       => __( 'Action', 'hey-notify' ),
				'choices'           => $this->event_name_array,
				'width'             => '50%',
				'conditional_logic' => array(
					array(
						array(
							'field' => 'type',
							'value' => 'system',
						),
					),
				),
			)
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
				\add_action( 'wp_version_check', array( $hook, 'system_core_update' ), 9 );
				\add_action( 'wp_version_check', array( $hook, 'system_core_update_done' ), 10 );
				break;
			case 'system_plugin_update':
				\add_action( 'wp_update_plugins', array( $hook, 'system_plugin_update' ), 10 );
				\add_action( 'wp_update_plugins', array( $hook, 'system_plugin_update_done' ), 11 );
				break;
			case 'system_plugin_activated':
				\add_action( 'activated_plugin', array( $hook, 'system_plugin_activated' ), 10, 2 );
				break;
			case 'system_plugin_deactivated':
				\add_action( 'deactivated_plugin', array( $hook, 'system_plugin_deactivated' ), 10, 2 );
				break;
			case 'system_theme_update':
				\add_action( 'wp_update_themes', array( $hook, 'system_theme_update' ), 10 );
				\add_action( 'wp_update_themes', array( $hook, 'system_theme_update_done' ), 11 );
				break;
			case 'system_theme_changed':
				\add_action( 'switch_theme', array( $hook, 'system_theme_changed' ), 10 );
				break;
		}
	}
}

new System_Event( 'system', '\Hey_Notify\System_Hook' );
