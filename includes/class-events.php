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

class Event {

	public $notification;
	public $event;
	public $hook;

	function __construct( $type, $hook ) {
		$this->hook = $hook;

		// Filters
		add_filter( 'hey_notify_event_types ', array( $this, 'types' ) );
		add_filter( 'hey_notify_event_actions', array( $this, 'actions' ) );

		// Actions
		add_action( "hey_notify_add_action_${type}", array( $this, 'watch' ), 10, 2 );
	}

	public function types( $types = array() ) {
		return $types;
	}

	public function actions( $actions = array() ) {
		return $actions;
	}

	public function watch( $notification, $event ) {
		$hook = new $this->hook( $notification, $event );
	}

}