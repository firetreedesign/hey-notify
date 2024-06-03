<?php
/**
 * Hey Notify Admin Settings - Licenses
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
class Licenses extends Settings {

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
		if ( false === get_option( 'hey_notify_settings_licenses' ) ) {
			add_option( 'hey_notify_settings_licenses' );
		}

		// Register the section.
		add_settings_section(
			'hey_notify_settings_licenses_section',
			__( 'Licenses', 'hey-notify' ),
			array( $this, 'licenses_section_callback' ),
			'hey_notify_settings_licenses'
		);

		$license_keys = apply_filters( 'hey_notify_license_keys', array() );
		foreach ( $license_keys as $license ) {
			add_settings_field(
				$license['id'] . '_license_key',
				'<strong>' . $license['name'] . '</strong>',
				array( $this, 'license_key_callback' ),
				'hey_notify_settings_licenses',
				'hey_notify_settings_licenses_section',
				array(
					'field_id' => $license['id'] . '_license_key',
					'page_id'  => 'hey_notify_settings_licenses',
					'size'     => 'regular',
					'label'    => $license['notes'],
				)
			);
		}

		// Finally, we register the fields with WordPress.
		register_setting(
			'hey_notify_settings_licenses', // The group name of the settings being registered.
			'hey_notify_settings_licenses', // The name of the set of options being registered.
			array( $this, 'sanitize_callback' ) // The name of the function responsible for validating the fields.
		);
	}

	/**
	 * Uninstall section callback
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function licenses_section_callback() {
		echo '<p>' . esc_html__( 'Please enter your license keys in order to receive updates and support.', 'hey-notify' ) . '</p>';
	}

	/**
	 * Sanitize callback
	 *
	 * @since 1.5.0
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

new Licenses();
