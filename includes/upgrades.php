<?php
/**
 * Upgrades
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Upgrades;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Actions.
add_action( 'init', __NAMESPACE__ . '\\version_check', 20 );

/**
 * Version check
 *
 * @return void
 */
function version_check() {

	// Get the version from the options.
	$version = get_option( 'hey_notify_version' );

	// If it's empty, assign it a version number.
	if ( ! $version ) {
		$version = '1.2.0';
	}

	// Check if we've already run this.
	if ( version_compare( $version, HEY_NOTIFY_VERSION, '=' ) ) {
		return;
	}

	// Version is before 1.2.1.
	if ( version_compare( $version, '1.2.1', '<' ) ) {
		v1_2_1_upgrade();
	}

	// Version is before 1.5.0.
	if ( version_compare( $version, '1.5.0', '<' ) ) {
		v1_5_0_upgrade();
	}

	update_option( 'hey_notify_version', HEY_NOTIFY_VERSION );
}
/**
 * Version 1.2.1 upgrade
 *
 * @return void
 */
function v1_2_1_upgrade() {
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
}

/**
 * Version 1.5.0 upgrade
 *
 * @return void
 */
function v1_5_0_upgrade() {
	/**
	 * General settings
	 */
	$settings = \get_option( 'hey_notify_settings', array() );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}
	$settings['default_service'] = \get_option( '_hey_notify_default_service', 'email' );
	$show_public_cpt             = \get_option( '_hey_notify_show_public_cpt', 'no' );
	if ( 'yes' === $show_public_cpt ) {
		$settings['show_public_cpt'] = 1;
	}
	\update_option( 'hey_notify_settings', $settings );

	/**
	 * Slack settings
	 */
	$slack = \get_option( 'hey_notify_settings_slack', array() );
	if ( ! is_array( $slack ) ) {
		$slack = array();
	}
	$slack['default_webhook']  = \get_option( '_hey_notify_default_slack_webhook', '' );
	$slack['default_icon']     = \get_option( '_hey_notify_default_slack_icon', '' );
	$slack['default_username'] = \get_option( '_hey_notify_default_slack_username', '' );
	$slack['default_color']    = \get_option( '_hey_notify_default_slack_color', '' );
	\update_option( 'hey_notify_settings_slack', $slack );

	/**
	 * Discord settings
	 */
	$discord = \get_option( 'hey_notify_settings_discord', array() );
	if ( ! is_array( $discord ) ) {
		$discord = array();
	}
	$discord['default_webhook']  = \get_option( '_hey_notify_default_discord_webhook', '' );
	$discord['default_avatar']   = \get_option( '_hey_notify_default_discord_avatar', '' );
	$discord['default_username'] = \get_option( '_hey_notify_default_discord_username', '' );
	\update_option( 'hey_notify_settings_discord', $discord );

	/**
	 * Microsoft Teams settings
	 */
	$microsoft_teams = \get_option( 'hey_notify_settings_microsoft_teams', array() );
	if ( ! is_array( $microsoft_teams ) ) {
		$microsoft_teams = array();
	}
	$microsoft_teams['default_webhook'] = \get_option( '_hey_notify_default_microsoft_teams_webhook', '' );
	$microsoft_teams['default_color']   = \get_option( '_hey_notify_default_microsoft_teams_color', '' );
	\update_option( 'hey_notify_settings_microsoft_teams', $microsoft_teams );

	/**
	 * Uninstall settings
	 */
	$uninstall = \get_option( 'hey_notify_settings_uninstall', array() );
	if ( ! is_array( $uninstall ) ) {
		$uninstall = array();
	}
	$remove_data = \get_option( '_hey_notify_remove_data', 'no' );
	if ( 'yes' === $remove_data ) {
		$uninstall['remove_data'] = 1;
	}
	\update_option( 'hey_notify_settings_uninstall', $uninstall );

	/**
	 * Hey Notify CPT metadata
	 */
	$notifications = \get_posts(
		array(
			'numberposts' => -1,
			'post_type'   => 'hey_notify',
		)
	);
	foreach ( $notifications as $notification ) {
		$events = \get_post_meta( $notification->ID, '_hey_notify_events_json', true );
		\update_post_meta( $notification->ID, '_hey_notify_events', $events );
	}
}
