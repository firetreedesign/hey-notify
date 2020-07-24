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

		$update_core = get_site_transient( 'update_core' );

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

		$last_checked_version = get_option( 'hey_notify_wordpress_version' );

		if ( $last_checked_version === $update_core->updates[0]->current ) {
			return;
		}

		update_option( 'hey_notify_wordpress_version', $update_core->updates[0]->current );

		$subject = \sprintf(
			'%1s %2s!',
			\__( 'Hey, a new version of WordPress is available on', 'hey-notify' ),
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
}
