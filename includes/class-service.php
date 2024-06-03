<?php
/**
 * Service
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

use Hey_Notify\Admin\Metabox\Builder;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service class
 */
class Service {

	/**
	 * Service
	 *
	 * @var string
	 */
	public $service = '';

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		\add_filter( 'hey_notify_service_fields_carbon', array( $this, 'fields_carbon' ), 10 );
		\add_filter( 'hey_notify_services_options', array( $this, 'services' ), 10 );
		\add_action( "hey_notify_render_service_fields_{$this->service}", array( $this, 'render_metabox_fields' ), 10, 2 );
	}

	/**
	 * Render the metabox fields
	 *
	 * @param int    $post_id Post ID.
	 * @param string $prefix Prefix.
	 * @return void
	 */
	public function render_metabox_fields( $post_id, $prefix ) {
		$post   = get_post( $post_id );
		$fields = $this->get_metabox_fields( array(), $post, $prefix );
		$output = '';
		foreach ( $fields as $field ) {
			$builder = new Builder( null );
			$output  = $builder->build_field( $post, $field );
		}
		echo wp_kses( $output, \Hey_Notify\Helpers\get_allowed_tags() );
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
	 * Fields - Carbon
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function fields_carbon( $fields = array() ) {
		return $fields;
	}
}
