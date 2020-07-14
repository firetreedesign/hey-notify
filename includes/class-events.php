<?php
/**
 * Events
 * 
 * @package HeyNotify
 */

namespace HeyNotify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Event {

	public $notification;
	public $event;
	public $hook;

	function __construct( $type, $hook ) {
		// error_log( json_encode( $hook ) );
		$this->hook = $hook;

		// Filters
		add_filter( 'heynotify_event_types ', array( $this, 'types' ) );
		add_filter( 'heynotify_event_actions', array( $this, 'actions' ) );

		// Actions
		add_action( "heynotify_add_action_${type}", array( $this, 'watch' ), 10, 2 );
		// error_log( "heynotify_add_action_${type}" );
	}

	public function types( $types = array() ) {
		return $types;
	}

	public function actions( $actions = array() ) {
		return $actions;
	}

	public function watch( $notification, $event ) {
		$hook = new $this->hook( $notification, $event );
		// error_log( 'running here?' );
		// switch( $event[ $event['type'] ] ) {
		// 	case 'post_published':
		// 		add_action( 'transition_post_status', array( $hook, 'post_published' ), 10, 3 );
		// 		break;
		// 	case 'post_scheduled':
		// 		add_action( 'transition_post_status', array( $hook, 'post_scheduled' ), 10, 3 );
		// 		break;
		// }
	}

}