<?php
/**
 * Service
 * 
 * @package HeyNotify
 */

namespace HeyNotify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Service {

	function __construct() {
		add_filter( 'heynotify_service_fields', array( $this, 'fields' ), 10 );
		add_filter( 'heynotify_services_options', array( $this, 'services' ), 10 );
		add_action( 'heynotify_send_message', array( $this, 'message' ), 10, 1 );
	}

	/**
	 * Service options
	 *
	 * @param array $services
	 * @return array
	 */
	public function services( $services = array() ) {
		return $services;
	}

	/**
	 * Fields
	 *
	 * @param array $fields
	 * @return array
	 */
	public function fields( $fields = array() ) {
		return $fields;
	}

	/**
	 * Process the message
	 *
	 * @param array $message
	 * @return void
	 */
	public function message( $message ) {
		return;
	}

}