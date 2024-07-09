<?php
/**
 * Filters
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Filters;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Filters.
add_filter( 'hey_notify_event_fields', __NAMESPACE__ . '\\event_fields', 5 );

/**
 * Event fields
 *
 * @param array $fields Fields.
 * @return array
 */
function event_fields( $fields = array() ) {
	$fields[] = array(
		'field_name'          => '_hey_notify_events',
		'field_label'         => __( 'Notification Events', 'hey-notify' ),
		'field_type'          => 'repeater',
		'insert_button_label' => __( 'Add Event', 'hey-notify' ),
		'placeholder_label'   => __( 'There are no events yet.', 'hey-notify' ),
		'fields'              => apply_filters(
			'hey_notify_event_actions',
			array(
				array(
					'field_type'  => 'select',
					'field_name'  => 'type',
					'field_label' => __( 'Event Type', 'hey-notify' ),
					'choices'     => apply_filters( 'hey_notify_event_types', array() ),
					'width'       => '50%',
				),
			),
		),
	);

	return $fields;
}
