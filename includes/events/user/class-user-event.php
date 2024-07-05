<?php
/**
 * User events
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * User Event class
 */
class User_Event extends Event {

	/**
	 * Sets the event names for different system events.
	 *
	 * @param string $type Event type.
	 */
	public function set_event_names( $type ) {
		$this->event_name_array = array(
			'user_new'                => __( 'New User Registration', 'hey-notify' ),
			'user_admin_login'        => __( 'Administrator Login', 'hey-notify' ),
			'user_admin_login_failed' => __( 'Administrator Failed Login', 'hey-notify' ),
		);
	}

	/**
	 * Add 'Users' to the $types array
	 *
	 * @param array $types Event types.
	 * @return array
	 */
	public function types( $types = array() ) {
		if ( ! isset( $types['user'] ) ) {
			$types['user'] = __( 'Users', 'hey-notify' );
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
				'field_name'        => 'user',
				'field_label'       => __( 'Action', 'hey-notify' ),
				'choices'           => $this->event_name_array,
				'width'             => '50%',
				'conditional_logic' => array(
					array(
						array(
							'field' => 'type',
							'value' => 'user',
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
			case 'user_new':
				add_action( 'user_register', array( $hook, 'user_new' ), 10, 1 );
				break;
			case 'user_admin_login':
				add_action( 'wp_login', array( $hook, 'user_admin_login' ), 10, 2 );
				break;
			case 'user_admin_login_failed':
				add_action( 'wp_login_failed', array( $hook, 'user_admin_login_failed' ), 10, 2 );
				break;
		}
	}
}

new User_Event( 'user', '\Hey_Notify\User_Hook' );
