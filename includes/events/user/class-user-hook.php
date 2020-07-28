<?php
/**
 * User hook
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This handles all of the User actions.
 */
class User_Hook extends Hook {

	/**
	 * New user registration
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	public function user_new( $user_id ) {

		$user = \get_user_by( 'id', $user_id );

		if ( false === $user ) {
			return;
		}

		$subject = \sprintf(
			/* translators: %s: Name of the site */
			\__( 'Hey, a new user just registered on %s!', 'hey-notify' ),
			\get_bloginfo( 'name' )
		);

		$this->prepare_data( $subject, $user );

	}

	/**
	 * Administrator login
	 *
	 * @param string $user_login Username.
	 * @param object $user WP_User object.
	 * @return void
	 */
	public function user_admin_login( $user_login, $user ) {

		if ( ! \in_array( 'administrator', $user->roles, true ) ) {
			return;
		}

		$subject = \sprintf(
			/* translators: %s: Name of the site */
			\__( 'Hey, an administrator just logged in to %s!', 'hey-notify' ),
			\get_bloginfo( 'name' )
		);

		$this->prepare_data( $subject, $user );

	}

	/**
	 * Administrator failed login
	 *
	 * @param string $username Username or email address.
	 * @param object $error WP_Error object.
	 * @return void
	 */
	public function user_admin_login_failed( $username, $error ) {

		if ( \filter_var( $username, FILTER_VALIDATE_EMAIL ) ) {
			$user = \get_user_by( 'email', $username );
		} else {
			$user = \get_user_by( 'login', $username );
		}

		if ( false === $user ) {
			return;
		}

		if ( ! \in_array( 'administrator', $user->roles, true ) ) {
			return;
		}

		$subject = \sprintf(
			/* translators: %s: Name of the site */
			\__( 'Hey, an administrator just failed to log in to %s!', 'hey-notify' ),
			\get_bloginfo( 'name' )
		);

		$this->prepare_data( $subject, $user );
	}

	/**
	 * Prepare the data
	 *
	 * @param string $subject The subject of the message.
	 * @param object $user User object.
	 * @return void
	 */
	private function prepare_data( $subject, $user ) {

		$fields = array(
			array(
				'name'   => \esc_html__( 'Display Name', 'hey-notify' ),
				'value'  => $user->display_name,
				'inline' => true,
			),
			array(
				'name'   => \esc_html__( 'Username', 'hey-notify' ),
				'value'  => $user->user_login,
				'inline' => true,
			),
			array(
				'name'   => \esc_html__( 'Email', 'hey-notify' ),
				'value'  => $user->user_email,
				'inline' => true,
			),
			array(
				'name'   => \esc_html__( 'IP Address', 'hey-notify' ),
				'value'  => $this->get_ip_address(),
				'inline' => true,
			),
		);

		$image = \get_avatar_url( $user->ID );
		if ( false === $image ) {
			$image = '';
		}

		$data = array(
			'subject' => $subject,
			'title'   => __( "View user's profile", 'hey-notify' ),
			'url'     => \add_query_arg( 'user_id', $user->ID, \self_admin_url( 'user-edit.php' ) ),
			'image'   => $image,
			'fields'  => $fields,
		);

		$this->send( $data );
	}

	/**
	 * Get the IP address of the user.
	 *
	 * @return string
	 */
	private function get_ip_address() {

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && '::1' !== $_SERVER['HTTP_X_FORWARDED_FOR'] ) {
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		} else {
			return __( 'Unknown', 'hey-notify' );
		}

	}
}
