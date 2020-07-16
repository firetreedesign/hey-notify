<?php
/**
 * Event hook
 * 
 * @package Hey_Notify
 */

namespace Hey_Notify;

class Hook {
	public $notification;
	public $event;

	function __construct( $notification, $event ) {
		$this->notification = $notification;
		$this->event = $event;
	}
}