<?php
/**
 * Email
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

use Hey_Notify\Service;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Email class
 */
class Email extends Service {

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		// Actions.
		\add_action( 'hey_notify_send_message_email', array( $this, 'send' ), 10, 3 );
		\add_filter( 'hey_notify_email_settings_core', array( $this, 'get_core_settings' ), 10, 1 );

		// Filters.
		\add_filter( 'hey_notify_services_select', array( $this, 'services_select' ), 10, 1 );
		\add_filter( 'hey_notify_service_fields', array( $this, 'get_metabox_fields' ), 10, 2 );
	}

	/**
	 * Populate the Services select input
	 *
	 * @param array $services Services.
	 * @return array
	 */
	public function services_select( $services ) {
		$services['email'] = __( 'Email', 'hey-notify' );
		return $services;
	}

	/**
	 * Populate the Metabox fields
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function get_metabox_fields( $fields ) {
		return array_merge(
			$fields,
			array(
				array(
					'field_name'          => '_hey_notify_email_addresses',
					'field_label'         => __( 'Send notifications to', 'hey-notify' ),
					'field_type'          => 'repeater',
					'insert_button_label' => __( 'Add Email Address', 'hey-notify' ),
					'placeholder_label'   => __( 'There are email addresses yet.', 'hey-notify' ),
					'fields'              => array(
						array(
							'field_name'  => 'email',
							'field_label' => __( 'Email Address', 'hey-notify' ),
							'field_type'  => 'textinput',
							'input_type'  => 'email',
						),
					),
					'conditional_logic'   => array(
						array(
							array(
								'field' => '_hey_notify_service',
								'value' => 'email',
							),
						),
					),
				),
			)
		);
	}

	/**
	 * Get service settings
	 *
	 * @param object $data Data.
	 * @return boolean|array
	 */
	public function get_core_settings( $data ) {

		if ( ! is_object( $data ) || ! isset( $data->ID ) ) {
			return false;
		}

		return array(
			'email_addresses' => json_decode( \get_post_meta( $data->ID, '_hey_notify_email_addresses', true ) ),
		);
	}

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
		);

		return $services;
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
	 * @param array  $message Message.
	 * @param string $trigger Trigger.
	 * @param mixed  $data Data.
	 * @return void
	 */
	public function send( $message, $trigger, $data ) {
		$settings = \apply_filters( "hey_notify_email_settings_{$trigger}", $data );

		if ( false === $settings ) {
			return;
		}

		if ( ! is_array( $settings ) || ! isset( $settings['email_addresses'] ) ) {
			return;
		}

		$to_email = array();
		if ( $settings['email_addresses'] ) {
			foreach ( $settings['email_addresses'] as $email ) {
				if ( '' !== trim( $email->email ) ) {
					$to_email[] = $email->email;
				}
			}
		}
		if ( 0 === count( $to_email ) ) {
			return; // No addresses to send to.
		}

		$from_email = \get_option( 'admin_email' );
		$from_name  = \__( 'Hey Notify', 'hey-notify' );

		// Subject.
		if ( isset( $message['subject'] ) && '' !== $message['subject'] ) {
			$subject = $message['subject'];
		} else {
			$subject = \__( "Hey, here's your notification!", 'hey-notify' );
		}

		$body = $this->prepare( $message, $settings );

		$headers = array(
			"From: {$from_name} <{$from_email}>",
		);

		$result = \wp_mail( $to_email, $subject, $body, $headers );

		\do_action(
			'hey_notify_message_sent',
			array(
				'service'  => 'email',
				'response' => $result,
				'trigger'  => $trigger,
				'message'  => $message,
				'data'     => $data,
			)
		);
	}
}

new Email();
