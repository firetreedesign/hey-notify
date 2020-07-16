<?php
/**
 * Notifications
 * 
 * @package Hey_Notify
 */

namespace Hey_Notify\Notifications;

use WP_Query;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions
add_action( 'plugins_loaded', __NAMESPACE__ . '\\setup' );

/**
 * Setup
 *
 * @return void
 */
function setup() {
	$query = get_query();
	if ( \is_object( $query ) && $query->have_posts() ) {
		$notifications = $query->get_posts();
		foreach ( $notifications as $notification ) {
			$events = \json_decode( \get_post_meta( $notification->ID, '_hey_notify_events_json', true ) );
			if ( $events ) {
				foreach ( $events as $event ) {
					\do_action( "hey_notify_add_action_{$event->type}", $notification, $event );
				}
			}
		}
	}
}

/**
 * Get the query data
 *
 * @return object
 */
function get_query() {
	// Check the cache for the query.
	$notifications = \wp_cache_get( 'hey_notify_notifications' );

	// If the cache is empty, then run the query.
	if ( false === $notifications ) {
		$notifications = new WP_Query( array(
			'posts_per_page' => -1,
			'post_type'      => 'hey_notify',
			'post_status'    => 'publish',
		) );

		// Save the query to the cache.
		\wp_cache_set( 'hey_notify_notifications', $notifications );

	}
	return $notifications;
}