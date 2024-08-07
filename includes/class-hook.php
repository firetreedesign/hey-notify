<?php
/**
 * Event hook
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

/**
 * Hook class
 */
class Hook {

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
	 * Class constructor
	 *
	 * @param object $notification Notification.
	 * @param object $event Event.
	 */
	public function __construct( $notification, $event ) {
		$this->notification = $notification;
		$this->event        = $event;
	}

	/**
	 * Send the message
	 *
	 * @param array $data Message data.
	 * @return void
	 */
	protected function send( $data ) {

		$defaults = array(
			'subject' => null,
			'title'   => null,
			'url'     => null,
			'image'   => null,
			'content' => null,
			'fields'  => array(),
			'footer'  => null,
		);

		$message = \wp_parse_args( $data, $defaults );

		$service = \get_post_meta( $this->notification->ID, '_hey_notify_service', true );

		\do_action(
			"hey_notify_send_message_{$service}",
			$message, // Message.
			'core', // Trigger.
			$this->notification // Data.
		);
	}
}
