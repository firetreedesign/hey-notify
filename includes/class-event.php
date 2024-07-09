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
	 * Type
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Event name array
	 *
	 * @var array
	 */
	public $event_name_array = array();

	/**
	 * Class constructor
	 *
	 * @param string $type Type.
	 * @param string $hook Hook.
	 */
	public function __construct( $type, $hook ) {
		$this->type = $type;
		$this->hook = $hook;

		$this->set_event_names( $type );

		// Filters.
		add_filter( 'hey_notify_event_types', array( $this, 'types' ) );
		add_filter( 'hey_notify_event_actions', array( $this, 'actions' ) );
		add_filter( 'hey_notify_event_names', array( $this, 'event_names' ) );

		// Actions.
		add_action( "hey_notify_add_action_{$type}", array( $this, 'watch' ), 10, 2 );
	}

	/**
	 * Sets the event names array.
	 *
	 * This function initializes the event_name_array property of the class
	 * with an empty array.
	 *
	 * @param string $type Event type.
	 * @return void
	 */
	// phpcs:ignore
	public function set_event_names( $type ) {
		$this->event_name_array = array();
	}

	/**
	 * Merge the event names.
	 *
	 * @hook hey_notify_event_names
	 * @param array $event_names Event names array.
	 * @return array
	 */
	public function event_names( $event_names = array() ) {
		return array_merge( $event_names, $this->event_name_array );
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
