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

$remove_data = \get_option( '_hey_notify_remove_data' );
if ( 'yes' === $remove_data ) {
	delete_options();
	delete_posts();
}

/**
 * Delete options
 *
 * @return void
 */
function delete_options() {
	\delete_option( '_hey_notify_remove_data' );
	\delete_option( '_hey_notify_default_service' );
	\delete_option( '_hey_notify_show_public_cpt' );
	\delete_option( 'hey_notify_wordpress_version' );
	\delete_option( 'hey_notify_theme_versions' );
	\delete_option( 'hey_notify_plugin_versions' );
	\delete_option( 'hey_notify_custom_post_types' );
}

/**
 * Delete posts
 *
 * @return void
 */
function delete_posts() {
	$all_posts = \get_posts(
		array(
			'post_type'   => 'hey_notify',
			'numberposts' => -1,
		)
	);
	foreach ( $all_posts as $single_post ) {
		\wp_delete_post( $single_post->ID, true );
	}
}