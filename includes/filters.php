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

// Filters.
add_filter( 'hey_notify_service_fields', __NAMESPACE__ . '\\service_fields', 5 );
add_filter( 'hey_notify_event_fields', __NAMESPACE__ . '\\event_fields', 5 );
add_filter( 'hey_notify_settings_uninstall', __NAMESPACE__ . '\\settings_uninstall_fields', 5 );
add_filter( 'hey_notify_settings_general', __NAMESPACE__ . '\\settings_general_fields', 5 );

/**
 * Service fields
 *
 * @param array $fields Fields.
 * @return array
 */
function service_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'radio_image', 'hey_notify_service', __( 'Select a service', 'hey-notify' ) )
			->set_options( get_service_options() )
			->set_default_value( \get_option( '_hey_notify_default_service' ) )
	);
	return $fields;
}

/**
 * Event fields
 *
 * @param array $fields Fields.
 * @return array
 */
function event_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'complex', 'hey_notify_events', __( 'Notification Events', 'hey-notify' ) )
			->setup_labels(
				array(
					'plural_name'   => __( 'Events', 'hey-notify' ),
					'singular_name' => __( 'Event', 'hey-notify' ),
				)
			)
			->add_fields(
				array_merge(
					array(
						Field::make( 'select', 'type', __( 'Event Type', 'hey-notify' ) )
							->set_options( apply_filters( 'hey_notify_event_types', array() ) )
							->set_width( 50 ),
					),
					apply_filters( 'hey_notify_event_actions', array() )
				)
			)
			->set_header_template( 'Event: <%- type %>' )
	);
	return $fields;
}

/**
 * Settings - Uninstall fields
 *
 * @param array $fields Fields.
 * @return array
 */
function settings_uninstall_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'separator', 'hey_notify_uninstall_separator', __( 'Uninstall Settings', 'hey-notify' ) )
	);
	$fields[] = (
		Field::make( 'checkbox', 'hey_notify_remove_data', __( 'Remove all data when Hey Notify is deleted.', 'hey-notify' ) )
	);
	return $fields;
}

/**
 * Settings - General fields
 *
 * @param array $fields Fields.
 * @return array
 */
function settings_general_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'separator', 'hey_notify_general_separator', __( 'General Settings', 'hey-notify' ) )
	);
	$fields[] = (
		Field::make( 'radio_image', 'hey_notify_default_service', __( 'Default service:', 'hey-notify' ) )
			->set_options( get_service_options() )
	);

	$fields = settings_cpt_fields( $fields );

	if ( has_filter( 'hey_notify_settings_uninstall' ) ) {
		$fields = array_merge( $fields, apply_filters( 'hey_notify_settings_uninstall', array() ) );
	}

	if ( has_filter( 'hey_notify_settings_licenses' ) ) {
		$fields[] = (
			Field::make( 'separator', 'hey_notify_licenses_separator', __( 'License Keys', 'hey-notify' ) )
		);

		$fields = array_merge( $fields, apply_filters( 'hey_notify_settings_licenses', array() ) );
	}
	return $fields;
}

/**
 * Settings - Custom Post Type fields
 *
 * @param array $fields Fields.
 * @return array
 */
function settings_cpt_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'separator', 'hey_notify_cpt_separator', __( 'Custom Post Type Settings', 'hey-notify' ) )
	);
	$fields[] = (
		Field::make( 'checkbox', 'hey_notify_show_public_cpt', __( 'Only display public Custom Post Types', 'hey-notify' ) )
			->set_option_value( 'yes' )
			->set_default_value( 'yes' )
	);
	$fields[] = (
		Field::make( 'html', 'hey_notify_cpt_refresh' )
			->set_html(
				sprintf(
					'<a class="button" id="hey-notify-cpt-refresh">%s</a><span id="hey-notify-cpt-refresh-status"></span>',
					__( 'Refresh custom post types', 'hey-notify' )
				)
			)
	);
	return $fields;
}

/**
 * Get the service options from the filter
 *
 * @return array
 */
function get_service_options() {

	$services = apply_filters( 'hey_notify_services_options', array() );
	$options  = array();

	foreach ( $services as $service ) {
		if ( isset( $service['image'] ) ) {
			$options[ $service['value'] ] = $service['image'];
		}
	}

	return $options;
}
