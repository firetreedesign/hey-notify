<?php
/**
 * CPT events
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

\add_action( 'activated_plugin', __NAMESPACE__ . '\\schedule_find_custom_post_types', 10, 2 );
\add_action( 'deactivated_plugin', __NAMESPACE__ . '\\schedule_find_custom_post_types', 10, 2 );
\add_action( 'hey_notify_find_custom_post_types', __NAMESPACE__ . '\\find_custom_post_types', 10, 2 );

require_once plugin_dir_path( __FILE__ ) . 'class-cpt-hook.php';
require_once plugin_dir_path( __FILE__ ) . 'class-cpt-event.php';

$custom_post_types = get_post_types_object();

foreach ( $custom_post_types as $custom_post_type ) {
	new CPT_Event( "cpt_{$custom_post_type->name}", '\Hey_Notify\CPT_Hook' );
}

/**
 * Run find_custom_post_types on the init hook
 *
 * @param object  $plugin Plugin object.
 * @param boolean $network_wide Was network activated.
 * @return void
 */
function schedule_find_custom_post_types( $plugin, $network_wide ) {
	\wp_schedule_single_event( time(), 'hey_notify_find_custom_post_types', array( $plugin, $network_wide ) );
}

/**
 * Find Custom Post Types and save them to the Options table
 *
 * @param object  $plugin Plugin object.
 * @param boolean $network_wide Was network activated.
 * @return void
 */
function find_custom_post_types( $plugin, $network_wide ) {
	$custom_post_types = \get_post_types(
		array(
			'_builtin' => false,
		),
		'objects',
		'and'
	);

	if ( true === $network_wide ) {
		$sites = \get_sites();
		foreach ( $sites as $site ) {
			\switch_to_blog( $site->blog_id );
			\update_option(
				'hey_notify_custom_post_types',
				\wp_json_encode( $custom_post_types )
			);
			\restore_current_blog();
		}
	} else {
		\update_option(
			'hey_notify_custom_post_types',
			\wp_json_encode( $custom_post_types )
		);
	}
}

/**
 * Get the Custom Post Types from the Options table
 *
 * @param string $post_type Post type name.
 * @return array
 */
function get_post_types_object( $post_type = null ) {
	$post_types = get_option(
		'hey_notify_custom_post_types',
		\wp_json_encode( array() )
	);

	$decoded_post_types = \json_decode( $post_types );
	$show_public        = \get_option( '_hey_notify_show_public_cpt', 'no' );

	if ( 'yes' === $show_public ) {
		foreach ( $decoded_post_types as $decoded_post_type ) {
			if ( ! $decoded_post_type->public ) {
				unset( $decoded_post_types->{$decoded_post_type->name} );
			}
		}
	}

	if ( null === $post_type ) {
		return $decoded_post_types;
	}

	// Remove the "cpt_" prefix.
	$post_type = \substr( $post_type, 4 );

	return $decoded_post_types->{$post_type};
}
