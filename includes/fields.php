<?php
/**
 * Fields
 * 
 * @package FireTreeNotify
 */

namespace FireTreeNotify\Fields;

use Carbon_Fields\Container;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions
add_action( 'after_setup_theme', __NAMESPACE__ . '\\boot' );
add_action( 'carbon_fields_register_fields', __NAMESPACE__ . '\\service_container' );
add_action( 'carbon_fields_register_fields', __NAMESPACE__ . '\\notification_container' );

/**
 * Boot up Carbon Fields
 *
 * @return void
 */
function boot() {
	\Carbon_Fields\Carbon_Fields::boot();
}

/**
 * Service post meta container
 *
 * @return void
 */
function service_container() {
	Container::make( 'post_meta', __( 'Service', 'firetree-notify' ) )
		->where( 'post_type', '=', 'firetree_notify' )
		->set_context( 'normal' )
		->set_priority( 'default' )
		->add_fields( apply_filters( 'firetree_notify_service_fields', array() ) );
}

/**
 * Notification post meta container
 * 
 * @return void
 */
function notification_container() {
	Container::make( 'post_meta', __( 'Events', 'firetree-notify' ) )
		->where( 'post_type', '=', 'firetree_notify' )
		->set_context( 'normal' )
		->set_priority( 'default' )
		->add_fields( apply_filters( 'firetree_notify_event_fields', array() ) );
}