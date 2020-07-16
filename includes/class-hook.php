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

	protected function send( $data ) {
		
		$defaults = array(
			'subject' => null,
			'title' => null,
			'url' => null,
			'image' => null,
			'content' => null,
			'fields' => array(),
			'footer' => null,
		);

		$new_data = wp_parse_args( $data, $defaults );

		do_action(
			'hey_notify_send_message',
			array(
				'notification' => $this->notification,
				'subject'      => $new_data['subject'],
				'title'        => $new_data['title'],
				'url'          => $new_data['url'],
				'image'        => $new_data['image'],
				'content'      => $new_data['content'],
				'fields'       => $new_data['fields'],
				'footer'       => $new_data['footer'],
			)
		);
	}
}