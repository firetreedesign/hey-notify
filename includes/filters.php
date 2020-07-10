<?php
/**
 * Filters
 * 
 * @package FireTreeNotify
 */

namespace FireTreeNotify\Filters;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Filters
add_filter( 'firetree_notify_service_fields', __NAMESPACE__ . '\\service_fields', 5 );
add_filter( 'firetree_notify_services_options', __NAMESPACE__ . '\\services_options', 5 );
add_filter( 'firetree_notify_event_fields', __NAMESPACE__ . '\\event_fields', 5 );

/**
 * Service fields
 *
 * @param array $fields
 * @return array
 */
function service_fields( $fields = array() ) {
	$fields[] = (
		Field::make( 'radio_image', 'firetree_notify_service', __( 'Select a service', 'firetree-notify' ) )
			->set_options( apply_filters( 'firetree_notify_services_options', array() ) )
	);
	$fields[] = (
		Field::make( 'text', 'firetree_notify_webhook_url', __( 'Webhook URL' ) )
			->set_attribute( 'type', 'url' )
			->set_help_text( __( 'The webhook that was generated for you by your preferred service.', 'firetree-notify' ) )
	);
	$fields[] = (
		Field::make( 'text', 'firetree_notify_discord_username', __( 'Discord Username' ) )
			->set_help_text( __( 'Override the default username of the webhook. Not required.', 'firetree-notify' ) )
			->set_conditional_logic(
				array(
					array(
						'field' => 'firetree_notify_service',
						'value' => 'discord',
					)
				)
			)
	);
	$fields[] = (
		Field::make( 'image', 'firetree_notify_discord_avatar', __( 'Discord Avatar' ) )
			->set_help_text( __( 'Override the default avatar of the webhook. Not required.', 'firetree-notify' ) )
			->set_conditional_logic(
				array(
					array(
						'field' => 'firetree_notify_service',
						'value' => 'discord',
					)
				)
			)
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
		$services['slack'] = FIRETREE_NOTIFY_PLUGIN_URL . '/images/services/slack.png';
	}

	if ( ! isset( $services['discord'] ) ) {
		$services['discord'] = FIRETREE_NOTIFY_PLUGIN_URL . '/images/services/discord.png';
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
		Field::make( 'complex', 'firetree_notify_events', __( 'Notification Events', 'firetree-notify' ) )
			->setup_labels(
				array(
					'plural_name' => __( 'Events', 'firetree-notify' ),
					'singular_name' => __( 'Event', 'firetree-notify' )
				)
			)
			->add_fields(
				array_merge(
					array(
						Field::make( 'select', 'type', __( 'Event Type', 'firetree-notify' ) )
							->set_options( apply_filters( 'firetree_notify_event_types ', array() ) )
					),
					apply_filters( 'firetree_notify_event_actions', array() )
				)
			)
			->set_header_template( '
				Event: <%- type %>
			' )
	);
	return $fields;
}