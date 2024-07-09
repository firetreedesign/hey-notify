<?php
/**
 * Hey Notify Metabox
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Admin;

use Hey_Notify\Admin\Metabox\Builder;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once HEY_NOTIFY_PLUGIN_DIR . 'includes/admin/metabox/class-builder.php';

/**
 * Metabox class
 */
class MetaBox {

	/**
	 * Prefix
	 *
	 * @var string
	 */
	private $prefix = '_hey_notify_';

	/**
	 * Class constructor
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'setup' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue the styles
	 *
	 * @since 1.0
	 * @param string $hook The hook suffix.
	 * @return void
	 */
	public function enqueue( $hook ) {
		global $post_type;

		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		if ( 'hey_notify' !== $post_type ) {
			return;
		}

		wp_enqueue_script( 'hey-notify-metabox', HEY_NOTIFY_PLUGIN_URL . '/assets/js/metabox.js', array(), HEY_NOTIFY_VERSION, true );
		wp_enqueue_style( 'hey-notify-metabox', HEY_NOTIFY_PLUGIN_URL . '/assets/css/metabox.css', array(), HEY_NOTIFY_VERSION );
	}


	/**
	 * Setup the meta box
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function setup() {
		new Builder(
			array(
				'id'       => 'hey_notify_service_metabox',
				'title'    => __( 'Service', 'hey-notify' ),
				'context'  => 'normal',
				'priority' => 'high',
				'screens'  => array(
					'hey_notify',
				),
				'fields'   => apply_filters(
					'hey_notify_service_fields',
					array(
						array(
							'field_type'    => 'select',
							'field_name'    => '_hey_notify_service',
							'field_label'   => __( 'Select a service', 'hey-notify' ),
							'default_value' => \Hey_Notify\Helpers\get_option( 'hey_notify_settings', 'default_service' ),
							'choices'       => $this->get_service_options(),
							'width'         => '100%',
						),
					),
					$this->prefix
				),
			)
		);

		new Builder(
			array(
				'id'       => 'hey_notify_event_metabox',
				'title'    => __( 'Events', 'hey-notify' ),
				'context'  => 'normal',
				'priority' => 'low',
				'screens'  => array(
					'hey_notify',
				),
				'fields'   => apply_filters(
					'hey_notify_event_fields',
					array(),
					$this->prefix
				),
			)
		);
	}

	/**
	 * Retrive the array of Services that are available
	 *
	 * @since 1.5.0
	 *
	 * @return array Available services
	 */
	private function get_service_options() {
		return apply_filters( 'hey_notify_services_select', array() );
	}
}

new MetaBox();
