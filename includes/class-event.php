<?php
/**
 * Events
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Event class
 */
class Event {

	/**
	 * Notification
	 *
	 * @var object
	 */
	public $notification;

	/**
	 * Event
	 *
	 * @var object
	 */
	public $event;

	/**
	 * Hook
	 *
	 * @var class
	 */
	public $hook;

	/**
	 * Class constructor
	 *
	 * @param string $type Type.
	 * @param string $hook Hook.
	 */
	public function __construct( $type, $hook ) {
		$this->hook = $hook;

		// Filters.
		add_filter( 'hey_notify_event_types', array( $this, 'types' ) );
		add_filter( 'hey_notify_event_actions', array( $this, 'actions' ) );

		// Actions.
		add_action( "hey_notify_add_action_${type}", array( $this, 'watch' ), 10, 2 );
	}

	/**
	 * Types
	 *
	 * @param array $types Types.
	 * @return array
	 */
	public function types( $types = array() ) {
		return $types;
	}

	/**
	 * Actions
	 *
	 * @param array $actions Actions.
	 * @return array
	 */
	public function actions( $actions = array() ) {
		return $actions;
	}

	/**
	 * Watch
	 *
	 * @param object $notification Notification.
	 * @param object $event Event.
	 * @return void
	 */
	public function watch( $notification, $event ) {
		$hook = new $this->hook( $notification, $event );
	}

}
