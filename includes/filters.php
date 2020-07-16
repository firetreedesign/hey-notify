<?php
/**
 * Filters
 * 
 * @package Hey_Notify
 */

namespace Hey_Notify\Filters;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Filters
add_filter( 'hey_notify_service_fields', __NAMESPACE__ . '\\service_fields', 5 );
add_filter( 'hey_notify_event_fields', __NAMESPACE__ . '\\event_fields', 5 );
add_filter( 'hey_notify_settings_uninstall', __NAMESPACE__ . '\\settings_uninstall_fields', 5 );
add_filter( 'hey_notify_settings_general', __NAMESPACE__ . '\\settings_general_fields', 5 );

/**
 * Service fields
 *
 * @param array $fields
 * @return array
 */
function service_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'radio_image', 'hey_notify_service', __( 'Select a service', 'hey-notify' ) )
			->set_options( apply_filters( 'hey_notify_services_options', array() ) )
			->set_default_value( \get_option( '_hey_notify_default_service' ) )
	);
	return $fields;
}

/**
 * Event fields
 *
 * @param array $fields
 * @return array
 */
function event_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'complex', 'hey_notify_events', __( 'Notification Events', 'hey-notify' ) )
			->setup_labels(
				array(
					'plural_name' => __( 'Events', 'hey-notify' ),
					'singular_name' => __( 'Event', 'hey-notify' )
				)
			)
			->add_fields(
				array_merge(
					array(
						Field::make( 'select', 'type', __( 'Event Type', 'hey-notify' ) )
							->set_options( apply_filters( 'hey_notify_event_types ', array() ) )
							->set_width( 50 )
					),
					apply_filters( 'hey_notify_event_actions', array() )
				)
			)
			->set_header_template( '
				Event: <%- type %>
			' )
	);
	return $fields;
}

/**
 * Settings - Uninstall fields
 *
 * @param array $fields
 * @return void
 */
function settings_uninstall_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'html', 'hey_notify_uninstall_heading' )
    		->set_html(
				sprintf(
					'<p>%1s</p>',
					__( 'Upon deletion of the plugin, you can optionally remove all custom data, settings, etc.', 'hey-notify' )
				)
			)
	);
	$fields[] = (
		Field::make( 'checkbox', 'hey_notify_remove_data', __( 'Remove all data when Hey Notify is deleted.', 'hey-notify' ) )
	);
	return $fields;
}

/**
 * Settings - Services fields
 *
 * @param array $fields
 * @return void
 */
function settings_general_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'html', 'hey_notify_services_heading' )
			->set_html(
				sprintf(
					'<p>%1s</p>',
					__( 'General settings for Hey Notify.', 'hey-notify' )
				)
			)
	);
	$fields[] = (
		Field::make( 'radio_image', 'hey_notify_default_service', __( 'Default service:', 'hey-notify' ) )
			->set_options( apply_filters( 'hey_notify_services_options', array() ) )
	);
	return $fields;
}