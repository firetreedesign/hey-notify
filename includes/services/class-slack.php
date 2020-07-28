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

/**
 * Slack class
 */
class Slack extends Service {

	/**
	 * Add the service
	 *
	 * @param array $services Services.
	 * @return array
	 */
	public function services( $services = array() ) {

		$services[] = array(
			'value' => 'slack',
			'label' => __( 'Slack', 'hey-notify' ),
			'image' => HEY_NOTIFY_PLUGIN_URL . 'images/services/slack.png',
		);

		return $services;
	}

	/**
	 * Add the fields specific to this service
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function fields( $fields = array() ) {

		$fields[] = (
			Field::make( 'text', 'hey_notify_slack_webhook', __( 'Webhook URL', 'hey-notify' ) )
				->set_attribute( 'type', 'url' )
				->set_help_text( sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Slack channel.', 'hey-notify' ), 'https://api.slack.com/messaging/webhooks', __( 'Learn More', 'hey-notify' ) ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'slack',
						),
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
						),
					)
				)
				->set_width( 33 )
		);
		$fields[] = (
			Field::make( 'text', 'hey_notify_slack_username', __( 'Slack Username', 'hey-notify' ) )
				->set_help_text( __( 'Override the default username of the webhook. Not required.', 'hey-notify' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'slack',
						),
					)
				)
				->set_width( 33 )
		);
		$fields[] = (
			Field::make( 'color', 'hey_notify_slack_color', __( 'Color', 'hey-notify' ) )
				->set_help_text( __( 'Select a color to use for the message attachment.', 'hey-notify' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'slack',
						),
					)
				)
				->set_width( 33 )
		);
		return $fields;
	}

	/**
	 * Sanitize a string for Slack
	 *
	 * @param string $string Text.
	 * @return string
	 */
	private function sanitize( $string ) {
		$string = str_replace( '&', '&amp;', $string );
		$string = str_replace( '<', '&lt;', $string );
		$string = str_replace( '>', '&gt;', $string );
		return $string;
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
			'service'     => \carbon_get_post_meta( $data['notification']->ID, 'hey_notify_service' ),
			'webhook_url' => \carbon_get_post_meta( $data['notification']->ID, 'hey_notify_slack_webhook' ),
			'username'    => \carbon_get_post_meta( $data['notification']->ID, 'hey_notify_slack_username' ),
			'icon'        => \carbon_get_post_meta( $data['notification']->ID, 'hey_notify_slack_icon' ),
			'color'       => \carbon_get_post_meta( $data['notification']->ID, 'hey_notify_slack_color' ),
		);
		return $settings;
	}

	/**
	 * Prepare the message
	 *
	 * @param array $message Message.
	 * @param array $settings Settings.
	 * @return array
	 */
	private function prepare( $message, $settings ) {

		$subject = $this->sanitize( $message['subject'] );
		$title   = $this->sanitize( $message['title'] );
		$content = $this->sanitize( $message['content'] );
		$footer  = $this->sanitize( $message['footer'] );
		$image   = $message['image'];
		$url     = $message['url'];

		$blocks = array();

		// Subject.
		if ( isset( $subject ) && '' !== $subject ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => $subject,
				),
			);
		}

		// Title and URL.
		if ( '' !== $title && '' !== $url ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => "*<{$url}|{$title}>*",
				),
			);
		} elseif ( '' !== $title && '' === $url ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => "*{$title}*",
				),
			);
		} elseif ( '' === $title && '' !== $url ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => "*<{$url}|{$url}>*",
				),
			);
		}

		// Content.
		if ( isset( $content ) && '' !== $content ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => $content,
				),
			);
		}

		// Fields.
		if ( isset( $message['fields'] ) && is_array( $message['fields'] ) ) {
			$fields = array();
			foreach ( $message['fields'] as $field ) {
				$fields[] = array(
					'type' => 'mrkdwn',
					'text' => "*{$this->sanitize( $field['name'] )}*\n{$this->sanitize( $field['value'] )}",
				);
			}
			$fields_array = array(
				'type'   => 'section',
				'fields' => $fields,
			);

			if ( isset( $image ) && '' !== $image ) {
				$fields_array['accessory'] = array(
					'type'      => 'image',
					'image_url' => $image,
					'alt_text'  => isset( $title ) ? $title : __( 'Attached image', 'hey-notify' ),
				);
			}

			$blocks[] = $fields_array;
		}

		// Footer.
		if ( isset( $footer ) && '' !== $footer ) {
			$blocks[] = array(
				'type' => 'section',
				'text' => array(
					'type' => 'mrkdwn',
					'text' => $footer,
				),
			);
		}

		$body = array();

		$body['attachments'][] = array(
			'color'  => $settings['color'],
			'blocks' => $blocks,
		);

		if ( '' !== $settings['username'] ) {
			$body['username'] = $settings['username'];
		}

		if ( '' !== $settings['icon'] ) {
			$icon_url = \wp_get_attachment_image_url( $settings['icon'], array( 250, 250 ) );
			if ( false !== $icon_url ) {
				$body['icon_url'] = $icon_url;
			}
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

		if (
			! isset( $settings['service'] )
			|| 'slack' !== $settings['service'] ) {
			return;
		}

		$body = $this->prepare( $data['message'], $settings );

		$json     = \wp_json_encode( $body );
		$response = \wp_remote_post(
			$settings['webhook_url'],
			array(
				'headers' => array(
					'Content-Type' => 'application/json; charset=utf-8',
				),
				'body'    => $json,
			)
		);

		do_action( 'hey_notify_message_sent', $json, $response );

		if ( ! \is_wp_error( $response ) ) {
			if ( 200 !== \wp_remote_retrieve_response_code( $response ) ) {
				$error_message = \wp_remote_retrieve_response_message( $response );
			}
		} else {
			// There was an error making the request.
			$error_message = $response->get_error_message();
		}
	}

}

new Slack();
