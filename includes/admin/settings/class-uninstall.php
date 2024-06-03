<?php
/**
 * Hey Notify Admin Settings - Uninstall
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
class Uninstall extends Settings {

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
		if ( false === get_option( 'hey_notify_settings_uninstall' ) ) {
			add_option( 'hey_notify_settings_uninstall' );
		}

		// Register the section.
		add_settings_section(
			'hey_notify_settings_uninstall_section',
			__( 'Uninstall', 'hey-notify' ),
			array( $this, 'uninstall_section_callback' ),
			'hey_notify_settings_uninstall'
		);

		// The Remove Data field.
		add_settings_field(
			'remove_data',
			'<strong>' . __( 'Hey Notify', 'hey-notify' ) . '</strong>',
			array( $this, 'checkbox_callback' ),
			'hey_notify_settings_uninstall',
			'hey_notify_settings_uninstall_section',
			array(
				'field_id' => 'remove_data',
				'page_id'  => 'hey_notify_settings_uninstall',
				'label'    => __( 'Remove all of its data when the plugin is deleted.', 'hey-notify' ),
			)
		);

		// Any add-on plugins.
		$uninstall_settings = apply_filters( 'hey_notify_uninstall_settings', array() );
		foreach ( $uninstall_settings as $setting ) {
			add_settings_field(
				$setting['id'] . '_remove_data',
				'<strong>' . $setting['name'] . '</strong>',
				array( $this, 'checkbox_callback' ),
				'hey_notify_settings_uninstall',
				'hey_notify_settings_uninstall_section',
				array(
					'field_id' => $setting['id'] . '_remove_data',
					'page_id'  => 'hey_notify_settings_uninstall',
					'label'    => __( 'Remove all of its data when the plugin is deleted.', 'hey-notify' ),
				)
			);
		}
	}

	/**
	 * Uninstall section callback
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function uninstall_section_callback() {
		echo '<p>' . esc_html__( 'Upon deletion of Hey Notify, you can optionally remove any custom tables, settings, and license keys that have been entered.', 'hey-notify' ) . '</p>';
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

new Uninstall();
