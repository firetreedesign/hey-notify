<?php
/**
 * Filters
 * 
 * @package HeyNotify
 */

namespace HeyNotify\Filters;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Filters
add_filter( 'heynotify_service_fields', __NAMESPACE__ . '\\service_fields', 5 );
add_filter( 'heynotify_services_options', __NAMESPACE__ . '\\services_options', 5 );
add_filter( 'heynotify_event_fields', __NAMESPACE__ . '\\event_fields', 5 );

/**
 * Service fields
 *
 * @param array $fields
 * @return array
 */
function service_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'radio_image', 'heynotify_service', __( 'Select a service', 'heynotify' ) )
			->set_options( apply_filters( 'heynotify_services_options', array() ) )
	);
	return $fields;
}

/**
 * Service options
 *
 * @param array $services
 * @return array
 */
function services_options( $services = array() ) {
	if ( ! isset( $services['slack'] ) ) {
		$services['slack'] = HEYNOTIFY_PLUGIN_URL . '/images/services/slack.png';
	}

	return $services;
}

/**
 * Event fields
 *
 * @param array $fields
 * @return array
 */
function event_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'complex', 'heynotify_events', __( 'Notification Events', 'heynotify' ) )
			->setup_labels(
				array(
					'plural_name' => __( 'Events', 'heynotify' ),
					'singular_name' => __( 'Event', 'heynotify' )
				)
			)
			->add_fields(
				array_merge(
					array(
						Field::make( 'select', 'type', __( 'Event Type', 'heynotify' ) )
							->set_options( apply_filters( 'heynotify_event_types ', array() ) )
							->set_width( 50 )
					),
					apply_filters( 'heynotify_event_actions', array() )
				)
			)
			->set_header_template( '
				Event: <%- type %>
			' )
	);
	return $fields;
}