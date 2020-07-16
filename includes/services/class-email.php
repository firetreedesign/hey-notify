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

class Email extends Service {

	/**
	 * Service options
	 *
	 * @param array $services
	 * @return array
	 */
	public function services( $services = array() ) {
		if ( ! isset( $services['email'] ) ) {
			$services['email'] = HEY_NOTIFY_PLUGIN_URL . '/images/services/email.png';
		}

		return $services;
	}

	/**
	 * Service fields
	 *
	 * @param array $fields
	 * @return array
	 */
	public function fields( $fields = array() ) {
		$fields[] = (
			Field::make( 'complex', 'hey_notify_email_addresses', __( 'Send notifications to', 'hey-notify' ) )
				->add_fields( array(
					Field::make( 'text', 'email', __( 'Email Address', 'hey-notify' ) ),
				) )
				->setup_labels(
					array(
						'plural_name' => __( 'Email Addresses', 'hey-notify' ),
						'singular_name' => __( 'Email Address', 'hey-notify' )
					)
				)
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'email',
						)
					)
				)
		);

		return $fields;
	}

	/**
	 * Send the message
	 *
	 * @param array $message
	 * @return void
	 */
	public function send( $message ) {

		$service = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_service' );
	
		if ( 'email' !== $service ) {
			return;
		}
	
		$email_addresses = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_email_addresses' );
		$to_email = array();
		if ( $email_addresses ) {
			foreach ( $email_addresses as $email ) {
				if ( '' !== trim( $email['email'] ) ) {
					$to_email[] = $email['email'];
				}
			}
		}
		if ( 0 === count( $to_email ) ) {
			return; // No addresses to send to.
		}

		$from_email = \get_option('admin_email');
		$from_name = \__( 'Hey Notify', 'hey-notify' );

		// Subject
		if ( isset( $message['subject'] ) && '' !== $message['subject'] ) {
			$subject = $message['subject'];
		} else {
			$subject = __( "Hey, here's your notification!", 'hey-notify' );
		}

		$body = '';
		
		// Title
		if ( isset( $message['title'] ) && '' !== $message['title'] ) {
			$body .= "{$message['title']}\r\n";
		}

		// URL
		if ( isset( $message['url'] ) && '' !== $message['url'] ) {
			$body .= "{$message['url']}\r\n\r\n";
		}

		// Content
		if ( isset( $message['content'] ) && '' !== $message['content'] ) {
			$body .= "{$message['content']}\r\n\r\n";
		}
	
		// Fields
		if ( isset( $message['fields'] ) && is_array( $message['fields'] ) ) {
			foreach( $message['fields'] as $field ) {
				$body .= "{$field['name']}: {$field['value']}\r\n";
			}
		}

		// Footer
		if ( isset( $message['footer'] ) && '' !== $message['footer'] ) {
			$body .= "{$message['footer']}";
		}

		$headers = array(
			"From: {$from_name} <{$from_email}>"
		);
	
		$result = wp_mail( $to_email, $subject, $body, $headers );
	}
}

new Email();
