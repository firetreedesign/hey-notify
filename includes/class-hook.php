<?php
/**
 * Event hook
 * 
 * @package HeyNotify
 */

namespace HeyNotify;

class Hook {
	public $notification;
	public $event;

	function __construct( $notification, $event ) {
		$this->notification = $notification;
		$this->event = $event;
	}
}