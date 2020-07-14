<?php
/**
 * Notifications
 * 
 * @package HeyNotify
 */

namespace HeyNotify\Notifications;

use WP_Query;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions
add_action( 'init', __NAMESPACE__ . '\\setup', 15 );

/**
 * Setup
 *
 * @return void
 */
function setup() {
	$query = get_query();

	if ( is_object( $query ) && $query->have_posts() ) {

		$notifications = $query->get_posts();

		foreach ( $notifications as $notification ) {
			// error_log( json_encode( $notification ) );
			$events = \carbon_get_post_meta( $notification->ID, 'heynotify_events' );
			if ( $events ) {
				foreach ( $events as $event ) {
					$type = $event['type'];
					// error_log( 'Do Action: ' . "heynotify_add_action_{$type}" );
					do_action( "heynotify_add_action_{$type}", $notification, $event );
				}
			}
		}
	} else {
		// error_log('No notifications set up');
	}
}

function get_query() {
	// Check the cache for the query.
	$notifications = wp_cache_get( 'heynotify_notifications' );

	// If the cache is empty, then run the query.
	if ( false === $notifications ) {
		$notifications = new WP_Query( array(
			'posts_per_page' => -1,
			'post_type'      => 'heynotify',
			'post_status'    => 'publish',
		) );

		// Save the query to the cache.
		wp_cache_set( 'heynotify_notifications', $notifications );

	}
	// error_log( json_encode( $notifications ) );
	return $notifications;
}