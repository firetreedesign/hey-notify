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
	 * Service options
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

	function message( $message ) {
		$service = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_service' );
	
		if ( 'slack' !== $service ) {
			return;
		}

		$webhook_url = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_slack_webhook' );
		$username    = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_slack_username' );
		$icon        = \carbon_get_post_meta( $message['notification']->ID, 'hey_notify_slack_icon' );
		
		$blocks = array();

		if ( isset( $message['content'] ) && '' !== $message['content'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => $message['content']
				)
			);
		}

		if ( '' !== $message['url_title'] && '' !== $message['url'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => "*<{$message['url']}|{$message['url_title']}>*"
				)
			);
		} elseif ( '' !== $message['url_title'] && '' === $message['url'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => "*{$message['url_title']}*"
				)
			);
		} elseif ( '' === $message['url_title'] && '' !== $message['url'] ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => "*<{$message['url']}|{$message['url']}>*"
				)
			);
		}
	
		if ( isset( $message['attachments'] ) && is_array( $message['attachments'] ) ) {
			$fields = array();
			foreach( $message['attachments'] as $field ) {
				$fields[] = array(
					'type' => 'mrkdwn',
					'text' => "*{$field['name']}*\n{$field['value']}"
				);
			}
			$fields_array = array(
				'type' => 'section',
				'fields' => $fields
			);

			if ( isset( $message['image'] ) ) {
				$fields_array['accessory'] = array(
					'type' => 'image',
					'image_url' => $message['image'],
					'alt_text' => isset( $message['url_title'] ) ? $message['url_title'] : __( 'Attached image', 'hey-notify' )
				);
			}

			$blocks[] = $fields_array;
		}

		$body = array();
		$body['blocks'] = $blocks;

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
			}
		} else {
			// There was an error making the request
			$error_message = $response->get_error_message();
		}
	}

}

new Slack();