<?php
/**
 * Fields
 * 
 * @package FireTreeNotify
 */

namespace FireTreeNotify\Fields;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions
add_action( 'after_setup_theme', __NAMESPACE__ . '\\boot' );
add_action( 'carbon_fields_register_fields', __NAMESPACE__ . '\\notification_container' );

// Filters
add_filter( 'firetree_notify_services_options', __NAMESPACE__ . '\\services_options', 5 );

/**
 * Boot up Carbon Fields
 *
 * @return void
 */
function boot() {
	\Carbon_Fields\Carbon_Fields::boot();
}

/**
 * Notification post meta container
 *
 * @return void
 */
function notification_container() {
	Container::make( 'post_meta', __( 'FireTree Notify', 'firetree-notify' ) )
		->where( 'post_type', '=', 'firetree_notify' )
		->set_context( 'normal' )
		->set_priority( 'default' )
		->add_tab(
			__( 'Service', 'firetree-notify' ),
			array(
				Field::make( 'radio_image', 'firetree_notify_service', __( 'Select a service', 'firetree-notify' ) )
					->set_options( apply_filters( 'firetree_notify_services_options', array() ) ),
				Field::make( 'text', 'firetree_notify_webhook_url', __( 'Webhook URL' ) )
					->set_attribute( 'type', 'url' )
					->set_help_text( __( 'The webhook that was generated for you by your preferred service.', 'firetree-notify' ) ),
				Field::make( 'text', 'firetree_notify_discord_username', __( 'Discord Username' ) )
					->set_help_text( __( 'Override the default username of the webhook. Not required.', 'firetree-notify' ) )
					->set_conditional_logic( array(
                        array(
                            'field' => 'firetree_notify_service',
                            'value' => 'discord',
                        )
					) ),
				Field::make( 'image', 'firetree_notify_discord_avatar', __( 'Discord Avatar' ) )
					->set_help_text( __( 'Override the default avatar of the webhook. Not required.', 'firetree-notify' ) )
					->set_conditional_logic(
						array(
							array(
								'field' => 'firetree_notify_service',
								'value' => 'discord',
							)
						)
					),
			)
		)
		->add_tab(
			__( 'Notifications', 'firetree-notify' ),
			array(
				Field::make( 'separator', 'crb_separator', __( 'Separator' ) )
			)
		);
}

function services_options( $services = array() ) {
	if ( ! $services['slack'] ) {
		$services['slack'] = FIRETREE_NOTIFY_PLUGIN_URL . '/images/services/slack.png';
	}

	if ( ! $services['discord'] ) {
		$services['discord'] = FIRETREE_NOTIFY_PLUGIN_URL . '/images/services/discord.png';
	}

	return $services;
}