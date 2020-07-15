<?php
/**
 * Email
 * 
 * @package HeyNotify
 */

namespace HeyNotify;

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
			$services['email'] = HEYNOTIFY_PLUGIN_URL . '/images/services/email.png';
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
			Field::make( 'complex', 'heynotify_email_addresses', __( 'Send notifications to', 'heynotify' ) )
				->add_fields( array(
					Field::make( 'text', 'email', __( 'Email Address', 'heynotify' ) ),
				) )
				->setup_labels(
					array(
						'plural_name' => __( 'Email Addresses', 'heynotify' ),
						'singular_name' => __( 'Email Address', 'heynotify' )
					)
				)
				->set_conditional_logic(
					array(
						array(
							'field' => 'heynotify_service',
							'value' => 'email',
						)
					)
				)
		);

		return $fields;
	}

	/**
	 * Process the message
	 *
	 * @param array $message
	 * @return void
	 */
	public function message( $message ) {

		$service = \carbon_get_post_meta( $message['notification']->ID, 'heynotify_service' );
	
		if ( 'email' !== $service ) {
			return;
		}
	
		$email_addresses = \carbon_get_post_meta( $message['notification']->ID, 'heynotify_email_addresses' );
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
		$from_name = \__( 'Hey Notify', 'heynotify' );
		$subject = __( "Hey, here's your notification!", 'heynotify' );
		if ( isset( $message['content'] ) && '' !== $message['content'] ) {
			$subject = $message['content'];
		}

		$body = '';
		
		if ( '' !== $message['url_title'] ) {
			$body .= "{$message['url_title']}\r\n";
		}

		if ( '' !== $message['url'] ) {
			$body .= "{$message['url']}\r\n\r\n";
		}
	
		if ( isset( $message['attachments'] ) && is_array( $message['attachments'] ) ) {
			foreach( $message['attachments'] as $field ) {
				$body .= "{$field['name']}: {$field['value']}\r\n";
			}
		}

		$headers = array(
			"From: {$from_name} <{$from_email}>"
		);
	
		$result = wp_mail( $to_email, $subject, $body, $headers );
	}
}

new Email();
