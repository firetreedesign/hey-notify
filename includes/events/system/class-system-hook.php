<?php
/**
 * System hook
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This handles all of the System actions.
 */
class System_Hook extends Hook {

	/**
	 * WordPress Update Available
	 *
	 * @return void
	 */
	public function system_core_update() {

		$update_core = \get_site_transient( 'update_core' );

		if ( false === $update_core ) {
			return;
		}

		if ( ! is_object( $update_core ) ) {
			return;
		}

		if ( ! isset( $update_core->updates ) ) {
			return;
		}

		if ( ! isset( $update_core->updates[0] ) ) {
			return;
		}

		if ( ! isset( $update_core->updates[0]->response ) ) {
			return;
		}

		if ( 'upgrade' !== $update_core->updates[0]->response ) {
			return;
		}

		if ( ! isset( $update_core->updates[0]->current ) ) {
			return;
		}

		$last_checked_version = \get_option( 'hey_notify_wordpress_version' );

		if ( $last_checked_version === $update_core->updates[0]->current ) {
			return;
		}

		$subject = \sprintf(
			/* translators: %s: Name of the site */
			\__( 'Hey, a new version of WordPress is available on %s!', 'hey-notify' ),
			\get_bloginfo( 'name' )
		);

		$fields = array(
			array(
				'name'   => \esc_html__( 'Current Version', 'hey-notify' ),
				'value'  => $update_core->version_checked,
				'inline' => true,
			),
			array(
				'name'   => \esc_html__( 'New Version', 'hey-notify' ),
				'value'  => $update_core->updates[0]->current,
				'inline' => true,
			),
		);

		$data = array(
			'subject' => $subject,
			'title'   => __( 'View the update', 'hey-notify' ),
			'url'     => \admin_url( 'update-core.php' ),
			'fields'  => $fields,
		);

		$this->send( $data );
	}

	/**
	 * WordPress Update Done
	 *
	 * @return void
	 */
	public function system_core_update_done() {

		$update_core = \get_site_transient( 'update_core' );

		if ( false === $update_core ) {
			return;
		}

		if ( ! is_object( $update_core ) ) {
			return;
		}

		if ( ! isset( $update_core->updates ) ) {
			return;
		}

		if ( ! isset( $update_core->updates[0] ) ) {
			return;
		}

		if ( ! isset( $update_core->updates[0]->response ) ) {
			return;
		}

		if ( 'upgrade' !== $update_core->updates[0]->response ) {
			return;
		}

		if ( ! isset( $update_core->updates[0]->current ) ) {
			return;
		}

		$last_checked_version = \get_option( 'hey_notify_wordpress_version' );

		if ( $last_checked_version === $update_core->updates[0]->current ) {
			return;
		}

		\update_option( 'hey_notify_wordpress_version', $update_core->updates[0]->current );
	}

	/**
	 * Theme Update Available
	 *
	 * @return void
	 */
	public function system_theme_update() {

		$update_themes = \get_site_transient( 'update_themes' );

		if ( false === $update_themes ) {
			return;
		}

		if ( ! is_object( $update_themes ) ) {
			return;
		}

		if ( ! isset( $update_themes->response ) ) {
			return;
		}

		$last_theme_versions = \get_option( 'hey_notify_theme_versions', array() );
		$fields              = array();

		foreach ( $update_themes->response as $theme_directory => $update_data ) {

			if ( $update_themes->checked[ $theme_directory ] === $update_data['new_version'] ) {
				continue;
			}

			if (
				isset( $last_theme_versions[ $theme_directory ] )
				&& $last_theme_versions[ $theme_directory ] === $update_data['new_version']
			) {
				continue;
			}

			$theme_data = \wp_get_theme( $theme_directory );

			$fields[] = array(
				'name'   => $theme_data->get( 'Name' ),
				'value'  => "{$theme_data->get( 'Version' )} --> {$update_data['new_version']}",
				'inline' => true,
			);

		}

		if ( empty( $fields ) ) {
			return;
		}

		$subject = \sprintf(
			/* translators: %s: Name of the site */
			\esc_html__( 'Hey, new theme updates are available on %s!', 'hey-notify' ),
			\get_bloginfo( 'name' )
		);

		$data = array(
			'subject' => $subject,
			'title'   => __( 'View the theme updates', 'hey-notify' ),
			'url'     => \admin_url( 'update-core.php' ),
			'fields'  => $fields,
		);

		$this->send( $data );
	}

	/**
	 * Theme Update Done
	 *
	 * @return void
	 */
	public function system_theme_update_done() {

		$update_themes = \get_site_transient( 'update_themes' );

		if ( false === $update_themes ) {
			return;
		}

		if ( ! is_object( $update_themes ) ) {
			return;
		}

		if ( ! isset( $update_themes->response ) ) {
			return;
		}

		$new_theme_versions = array();

		foreach ( $update_themes->response as $theme_directory => $update_data ) {
			$new_theme_versions[ $theme_directory ] = $update_data['new_version'];
		}

		\update_option( 'hey_notify_theme_versions', $new_theme_versions );
	}

	/**
	 * Plugin Update Available
	 *
	 * @return void
	 */
	public function system_plugin_update() {

		$update_plugins = \get_site_transient( 'update_plugins' );

		if ( false === $update_plugins ) {
			return;
		}

		if ( ! is_object( $update_plugins ) ) {
			return;
		}

		if ( ! isset( $update_plugins->response ) ) {
			return;
		}

		$last_plugin_versions = \get_option( 'hey_notify_plugin_versions', array() );
		$fields               = array();

		foreach ( $update_plugins->response as $plugin_file => $update_data ) {

			if ( $update_plugins->checked[ $plugin_file ] === $update_data->new_version ) {
				continue;
			}

			if (
				isset( $last_plugin_versions[ $plugin_file ] )
				&& $last_plugin_versions[ $plugin_file ] === $update_data->new_version
			) {
				continue;
			}

			$plugin_data = \get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_file, true, false );

			$fields[] = array(
				'name'   => $plugin_data['Name'],
				'value'  => "{$plugin_data['Version']} --> {$update_data->new_version}",
				'inline' => false,
			);

		}

		if ( empty( $fields ) ) {
			return;
		}

		$subject = \sprintf(
			/* translators: %s: Name of the site */
			\esc_html__( 'Hey, new plugin updates are available on %s!', 'hey-notify' ),
			\get_bloginfo( 'name' )
		);

		$data = array(
			'subject' => $subject,
			'title'   => __( 'View the plugin updates', 'hey-notify' ),
			'url'     => \admin_url( 'update-core.php' ),
			'fields'  => $fields,
		);

		$this->send( $data );

	}

	/**
	 * Plugin Update Done
	 *
	 * @return void
	 */
	public function system_plugin_update_done() {

		$update_plugins = \get_site_transient( 'update_plugins' );

		if ( false === $update_plugins ) {
			return;
		}

		if ( ! is_object( $update_plugins ) ) {
			return;
		}

		if ( ! isset( $update_plugins->response ) ) {
			return;
		}

		$new_plugin_versions = array();

		foreach ( $update_plugins->response as $plugin_file => $update_data ) {
			$new_plugin_versions[ $plugin_file ] = $update_data->new_version;
		}

		\update_option( 'hey_notify_plugin_versions', $new_plugin_versions );

	}
}
