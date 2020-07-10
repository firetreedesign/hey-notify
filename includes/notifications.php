<?php
/**
 * Notifications
 * 
 * @package FireTreeNotify
 */

namespace FireTreeNotify\Notifications;

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
			$events = \carbon_get_post_meta( $notification->ID, 'firetree_notify_events' );
			if ( $events ) {
				foreach ( $events as $event ) {
					$type = $event['type'];
					do_action( "firetree_notify_add_action_{$type}", $notification, $event );
				}
			}
		}
	}
}

function get_query() {
	// Check the cache for the query.
	$notifications = wp_cache_get( 'firetree_notify_notifications' );

	// If the cache is empty, then run the query.
	if ( false === $notifications ) {
		$notifications = new WP_Query( array(
			'posts_per_page' => -1,
			'post_type'      => 'firetree_notify',
			'post_status'    => 'publish',
			'meta_query' => array(
				array(
					'key'     => 'firetree_notify_webhook_url',
					'value'   => '',
					'compare' => '!=',
				),
			),
		) );

		// Save the query to the cache.
		wp_cache_set( 'firetree_notify_notifications', $notifications );

	}

	return $notifications;
}