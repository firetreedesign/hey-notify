<?php
/**
 * Email
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Email class
 */
class Email extends Service {

	/**
	 * Service options
	 *
	 * @param array $services Services.
	 * @return array
	 */
	public function services( $services = array() ) {
		$services[] = array(
			'value' => 'email',
			'label' => __( 'Email', 'hey-notify' ),
			'image' => HEY_NOTIFY_PLUGIN_URL . 'images/services/email.png',
		);

		return $services;
	}

	/**
	 * Service fields
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function fields( $fields = array() ) {
		$fields[] = (
			Field::make( 'complex', 'hey_notify_email_addresses', __( 'Send notifications to', 'hey-notify' ) )
				->add_fields(
					array(
						Field::make( 'text', 'email', __( 'Email Address', 'hey-notify' ) ),
					)
				)
				->setup_labels(
					array(
						'plural_name'   => __( 'Email Addresses', 'hey-notify' ),
						'singular_name' => __( 'Email Address', 'hey-notify' ),
					)
				)
				->set_header_template( '<%- email %>' )
				->set_collapsed( true )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'email',
						),
					)
				)
		);

		return $fields;
	}

	/**
	 * Get service settings
	 *
	 * @param array $data Data.
	 * @return array
	 */
	public function get_settings( $data ) {
		if ( ! isset( $data['notification'] ) ) {
			return false;
		}
		$settings = array(
			'service'         => \carbon_get_post_meta( $data['notification']->ID, 'hey_notify_service' ),
			'email_addresses' => \carbon_get_post_meta( $data['notification']->ID, 'hey_notify_email_addresses' ),
		);
		return $settings;
	}

	/**
	 * Prepare the message
	 *
	 * @param array $message Message.
	 * @param array $settings Settings.
	 * @return string
	 */
	private function prepare( $message, $settings ) {

		$body = '';

		// Title.
		if ( isset( $message['title'] ) && '' !== $message['title'] ) {
			$body .= "{$message['title']}\r\n";
		}

		// URL.
		if ( isset( $message['url'] ) && '' !== $message['url'] ) {
			$body .= "{$message['url']}\r\n\r\n";
		}

		// Content.
		if ( isset( $message['content'] ) && '' !== $message['content'] ) {
			$body .= "{$message['content']}\r\n\r\n";
		}

		// Fields.
		if ( isset( $message['fields'] ) && is_array( $message['fields'] ) ) {
			foreach ( $message['fields'] as $field ) {
				$body .= "{$field['name']}: {$field['value']}\r\n";
			}
		}

		// Footer.
		if ( isset( $message['footer'] ) && '' !== $message['footer'] ) {
			$body .= "{$message['footer']}";
		}

		return $body;
	}

	/**
	 * Send the message
	 *
	 * @param array $data Data.
	 * @return void
	 */
	public function send( $data ) {

		$settings = $this->get_settings( $data );

		if ( false === $settings ) {
			return;
		}

		if ( ! isset( $settings['service'] ) || 'email' !== $settings['service'] ) {
			return;
		}

		$to_email = array();
		if ( $settings['email_addresses'] ) {
			foreach ( $settings['email_addresses'] as $email ) {
				if ( '' !== trim( $email['email'] ) ) {
					$to_email[] = $email['email'];
				}
			}
		}
		if ( 0 === count( $to_email ) ) {
			return; // No addresses to send to.
		}

		$from_email = \get_option( 'admin_email' );
		$from_name  = \__( 'Hey Notify', 'hey-notify' );

		// Subject.
		if ( isset( $data['message']['subject'] ) && '' !== $data['message']['subject'] ) {
			$subject = $data['message']['subject'];
		} else {
			$subject = __( "Hey, here's your notification!", 'hey-notify' );
		}

		$body = $this->prepare( $data['message'], $settings );

		$headers = array(
			"From: {$from_name} <{$from_email}>",
		);

		$result = wp_mail( $to_email, $subject, $body, $headers );

		do_action( 'hey_notify_message_sent', $body, $result );
	}
}

new Email();
