<?php
/**
 * Service
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service class
 */
class Service {

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'hey_notify_service_fields', array( $this, 'fields' ), 10 );
		add_filter( 'hey_notify_services_options', array( $this, 'services' ), 10 );
	}

	/**
	 * Service options
	 *
	 * @param array $services Services.
	 * @return array
	 */
	public function services( $services = array() ) {
		return $services;
	}

	/**
	 * Fields
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function fields( $fields = array() ) {
		return $fields;
	}

}
