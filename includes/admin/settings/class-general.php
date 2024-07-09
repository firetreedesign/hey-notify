<?php
/**
 * Hey Notify Admin Settings - General
 *
 * @package Hey_Notify
 * @version 1.5.0
 */

namespace Hey_Notify\Admin\Settings;

use Hey_Notify\Admin\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Settings class
 */
class General extends Settings {

	/**
	 * Class construct
	 *
	 * @since 1.5.0
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'initialize' ) );
	}

	/**
	 * Initialize the class
	 *
	 * @return void
	 */
	public function initialize() {

		// If the option does not exist, then add it.
		if ( false === get_option( 'hey_notify_settings' ) ) {
			add_option( 'hey_notify_settings' );
		}

		// Register the section.
		add_settings_section(
			'hey_notify_settings_general_section',
			__( 'General Settings', 'hey-notify' ),
			array( $this, 'general_section_callback' ),
			'hey_notify_settings_general'
		);

		// Default Service.
		add_settings_field(
			'default_service',
			'<strong>' . __( 'Default Service', 'hey-notify' ) . '</strong>',
			array( $this, 'select_callback' ),
			'hey_notify_settings_general',
			'hey_notify_settings_general_section',
			array(
				'field_id' => 'default_service',
				'page_id'  => 'hey_notify_settings',
				'label'    => __( 'Select a default service.', 'hey-notify' ),
				'options'  => call_user_func(
					function ( array $a ) {
						asort( $a );
						return $a;
					},
					apply_filters( 'hey_notify_services_select', array() )
				),
			)
		);

		// Register the section.
		add_settings_section(
			'hey_notify_settings_general_cpt_section',
			__( 'Custom Post Type Settings', 'hey-notify' ),
			array( $this, 'cpt_section_callback' ),
			'hey_notify_settings_general'
		);

		// Public Custom Post Types.
		add_settings_field(
			'show_public_cpt',
			'<strong>' . __( 'Custom Post Types', 'hey-notify' ) . '</strong>',
			array( $this, 'checkbox_callback' ),
			'hey_notify_settings_general',
			'hey_notify_settings_general_cpt_section',
			array(
				'field_id' => 'show_public_cpt',
				'page_id'  => 'hey_notify_settings',
				'label'    => __( 'Only display public Custom Post Types.', 'hey-notify' ),
			)
		);

		add_settings_field(
			'refresh_public_cpt',
			'<strong>' . __( 'Refresh', 'hey-notify' ) . '</strong>',
			array( $this, 'text_callback' ),
			'hey_notify_settings_general',
			'hey_notify_settings_general_cpt_section',
			array(
				'header'  => null,
				'title'   => null,
				'content' => '<a class="button" id="hey-notify-cpt-refresh">Refresh custom post types</a>',
			)
		);

		// Finally, we register the fields with WordPress.
		register_setting(
			'hey_notify_settings_general', // The group name of the settings being registered.
			'hey_notify_settings_general', // The name of the set of options being registered.
			array( $this, 'sanitize_callback' ) // The name of the function responsible for validating the fields.
		);
	}

	/**
	 * General section callback
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function general_section_callback() {
		// Do nothing.
	}

	/**
	 * CPT section callback
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function cpt_section_callback() {
		// Do nothing.
	}

	/**
	 * Sanitize callback
	 *
	 * @since 1.0.0
	 *
	 * @param  array $input Input values.
	 *
	 * @return array
	 */
	public function sanitize_callback( $input ) {

		// Define all of the variables that we'll be using.
		$output = array();

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {

			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[ $key ] ) ) {

				// Strip all HTML and PHP tags and properly handle quoted strings.
				$output[ $key ] = wp_strip_all_tags( stripslashes( $input[ $key ] ) );

			}
		}

		// Return the array.
		return $output;
	}
}

new General();
