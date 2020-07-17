<?php
/**
 * Slack
 * 
 * @package Hey_Notify
 */

namespace Hey_Notify;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Slack extends Service {

	/**
	 * Add the service
	 *
	 * @param array $services
	 * @return array
	 */
	public function services( $services = array() ) {
		if ( ! isset( $services['slack'] ) ) {
			$services['slack'] = HEY_NOTIFY_PLUGIN_URL . '/images/services/slack.png';
		}

		return $services;
	}

	/**
	 * Add the fields specific to this service
	 * 
	 * @param array $fields
	 * @return array
	 */
	function fields( $fields = array() ) {
		$fields[] = (
			Field::make( 'text', 'hey_notify_slack_webhook', __( 'Webhook URL', 'hey-notify' ) )
				->set_attribute( 'type', 'url' )
				->set_help_text( sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Slack channel.', 'hey-notify' ), 'https://api.slack.com/messaging/webhooks', __( 'Learn More', 'hey-notify' ) ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'slack',
						)
					)
				)
		);
		$fields[] = (
			Field::make( 'image', 'hey_notify_slack_icon', __( 'Slack Icon', 'hey-notify' ) )
				->set_help_text( __( 'Override the default icon of the webhook. Not required.', 'hey-notify' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'slack',
						)
					)
				)
				->set_width( 50 )
		);
		$fields[] = (
			Field::make( 'text', 'hey_notify_slack_username', __( 'Slack Username', 'hey-notify' ) )
				->set_help_text( __( 'Override the default username of the webhook. Not required.', 'hey-notify' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'slack',
						)
					)
				)
				->set_width( 50 )
		);
		return $fields;
	}

	/**
	 * Prepare the message
	 * 
	 * @param array $message
	 * @return array
	 */
	function prepare( $message ) {
		$clean_message = array(
			'notification' => $message['notification'],
			'subject'      => $this->sanitize( $message['subject'] ),
			'title'        => $this->sanitize( $message['title'] ),
			'url'          => $message['url'],
			'image'        => $message['image'],
			'content'      => $this->sanitize( $message['content'] ),
			'footer'       => $this->sanitize( $message['footer'] ),
		);

		$clean_fields = array();
		foreach ( $message['fields'] as $field ) {
			$clean_fields[] = array(
				'name' => $this->sanitize( $field['name'] ),
				'value' => $this->sanitize( $field['value'] ),
				'inline' => $field['inline']
			);
		}

		$clean_message['fields'] = $clean_fields;

		return $clean_message;
	}

	/**
	 * Sanitize a string for Slack
	 * 
	 * @param string $string
	 * @return string
	 */
	function sanitize( $string ) {
		$string = str_replace( '&', '&amp;', $string );
		$string = str_replace( '<', '&lt;', $string );
		$string = str_replace( '>', '&gt;', $string );
		return $string;
	}

	/**
	 * Send the message
	 * 
	 * @param array $message
	 * @return void
	 */
	function send( $message ) {
		$service = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_service' );
	
		if ( 'slack' !== $service ) {
			return;
		}

		$message = $this->prepare( $message );

		$webhook_url = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_slack_webhook' );
		$username    = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_slack_username' );
		$icon        = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_slack_icon' );
		
		$blocks = array();

		// Subject
		if ( isset( $message['subject'] ) && '' !== $message['subject'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => $message['subject']
				)
			);
		}

		// Title and URL
		if ( '' !== $message['title'] && '' !== $message['url'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => "*<{$message['url']}|{$message['title']}>*"
				)
			);
		} elseif ( '' !== $message['title'] && '' === $message['url'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => "*{$message['title']}*"
				)
			);
		} elseif ( '' === $message['title'] && '' !== $message['url'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => "*<{$message['url']}|{$message['url']}>*"
				)
			);
		}

		// Content
		if ( isset( $message['content'] ) && '' !== $message['content'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => $message['content']
				)
			);
		}
	
		// Fields
		if ( isset( $message['fields'] ) && is_array( $message['fields'] ) ) {
			$fields = array();
			foreach( $message['fields'] as $field ) {
				$fields[] = array(
					'type' => 'mrkdwn',
					'text' => "*{$field['name']}*\n{$field['value']}"
				);
			}
			$fields_array = array(
				'type' => 'section',
				'fields' => $fields
			);

			if ( isset( $message['image'] ) && '' !== $message['image'] ) {
				$fields_array['accessory'] = array(
					'type' => 'image',
					'image_url' => $message['image'],
					'alt_text' => isset( $message['title'] ) ? $message['title'] : __( 'Attached image', 'hey-notify' )
				);
			}

			$blocks[] = $fields_array;
		}

		// Footer
		if ( isset( $message['footer'] ) && '' !== $message['footer'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => $message['footer']
				)
			);
		}

		$body = array();
		$body['attachments'] = array();
		$body['attachments'][]['blocks'] = $blocks;

		if ( '' !== $username ) {
			$body['username'] = $username;
		}
	
		if ( '' !== $icon ) {
			$icon_url = \wp_get_attachment_image_url( $icon, array( 250, 250 ) );
			if ( false !== $icon_url ) {
				$body['icon_url'] = $icon_url;
			}
		}

		$json = \json_encode( $body );
		$response = \wp_remote_post( $webhook_url, array(
			'headers' => array(
				'Content-Type' => 'application/json; charset=utf-8',
			),
			'body' => $json,
		) );
		
		if ( ! \is_wp_error( $response ) ) {
			if ( 200 == \wp_remote_retrieve_response_code( $response ) ) {
				// error_log( 'Message sent to Slack!' );
			} else {
				$error_message = \wp_remote_retrieve_response_message( $response );
				// error_log( $error_message );
			}
		} else {
			// There was an error making the request
			$error_message = $response->get_error_message();
			// error_log( $error_message );
		}
	}

}

new Slack();