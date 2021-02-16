<?php
/**
 * Rest API
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\RestAPI;

use WP_REST_Request;
use WP_REST_Response;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', __NAMESPACE__ . '\\init' );

/**
 * Initialize the endpoint
 *
 * @return void
 */
function init() {
	register_rest_route(
		'heynotify/v1',
		'/cptrefresh',
		array(
			'methods'             => 'POST',
			'callback'            => __NAMESPACE__ . '\\refresh_cpt',
			'permission_callback' => '__return_true',
		)
	);
}

/**
 * Group Search
 *
 * @since 1.2.0
 * @param  WP_REST_Request $request Request object.
 * @return string
 */
function refresh_cpt( WP_REST_Request $request ) {

	$custom_post_types = \get_post_types(
		array(
			'_builtin' => false,
		),
		'objects',
		'and'
	);

	\update_option(
		'hey_notify_custom_post_types',
		\wp_json_encode( $custom_post_types )
	);

	return new WP_REST_Response( 'true', 200 );
}
