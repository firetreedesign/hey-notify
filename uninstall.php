<?php
/**
 * Uninstall
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Uninstall;

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$remove_data = get_option( '_hey_notify_remove_data' );

if ( 'yes' === $remove_data ) {
	delete_option( '_hey_notify_remove_data' );
	delete_option( '_hey_notify_default_service' );

	$all_posts = get_posts(
		array(
			'post_type'   => 'hey_notify',
			'numberposts' => -1,
		)
	);
	foreach ( $all_posts as $single_post ) {
		wp_delete_post( $single_post->ID, true );
	}
}
