<?php
/**
 * Discord
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
 * Discord class
 */
class Discord extends Service {

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		\add_action( 'hey_notify_send_message_discord', array( $this, 'send' ), 10, 3 );
		\add_action( 'hey_notify_discord_settings_core', array( $this, 'get_core_settings' ), 10, 1 );
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
			'webhook_url' => \carbon_get_post_meta( $data->ID, 'hey_notify_discord_webhook' ),
			'username'    => \carbon_get_post_meta( $data->ID, 'hey_notify_discord_username' ),
			'icon'        => \carbon_get_post_meta( $data->ID, 'hey_notify_discord_avatar' ),
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
			'value' => 'discord',
			'label' => __( 'Discord', 'hey-notify' ),
			'image' => HEY_NOTIFY_PLUGIN_URL . 'images/services/discord.png',
		);

		return $services;
	}

	/**
	 * Service fields
	 *
	 * @param array $fields Service fields.
	 * @return array
	 */
	public function fields( $fields = array() ) {
		$fields[] = (
			Field::make( 'text', 'hey_notify_discord_webhook', __( 'Webhook URL', 'hey-notify' ) )
				->set_attribute( 'type', 'url' )
				->set_help_text( sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Discord channel.', 'hey-notify' ), 'https://support.discord.com/hc/en-us/articles/228383668', __( 'Learn More', 'hey-notify' ) ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'discord',
						),
					)
				)
		);
		$fields[] = (
			Field::make( 'image', 'hey_notify_discord_avatar', __( 'Discord Avatar', 'hey-notify' ) )
				->set_help_text( __( 'Override the default avatar of the webhook. Not required.', 'hey-notify' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'discord',
						),
					)
				)
				->set_width( 50 )
		);
		$fields[] = (
			Field::make( 'text', 'hey_notify_discord_username', __( 'Discord Username', 'hey-notify' ) )
				->set_help_text( __( 'Override the default username of the webhook. Not required.', 'hey-notify' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'discord',
						),
					)
				)
				->set_width( 50 )
		);

		return $fields;
	}

	/**
	 * Prepare the message
	 *
	 * @param array $message Message.
	 * @param array $settings Settings.
	 * @return array
	 */
	private function prepare( $message, $settings ) {

		$body       = array();
		$embed_item = array();

		// Subject.
		if ( isset( $message['subject'] ) && '' !== $message['subject'] ) {
			$body['content'] = $message['subject'];
		}

		// Title.
		if ( '' !== $message['subject'] ) {
			$embed_item['title'] = $message['subject'];
		}

		// URL.
		if ( '' !== $message['url'] ) {
			$embed_item['url'] = $message['url'];
		}

		// Fields.
		if ( isset( $message['fields'] ) && is_array( $message['fields'] ) ) {
			$fields = array();
			foreach ( $message['fields'] as $field ) {
				$fields[] = array(
					'name'   => $field['name'],
					'value'  => $field['value'],
					'inline' => $field['inline'],
				);
			}
			$embed_item['fields'] = $fields;
		}

		// Image.
		if ( isset( $message['image'] ) ) {
			$embed_item['thumbnail'] = array(
				'url' => $message['image'],
			);
		}

		// Content.
		if ( isset( $message['content'] ) && '' !== $message['content'] ) {
			$embed_item['description'] = $message['content'];
		}

		// Footer.
		if ( isset( $message['footer'] ) && '' !== $message['footer'] ) {
			$embed_item['footer'] = array(
				'text' => $message['content'],
			);
		}

		$body['embeds'] = array( $embed_item );

		if ( '' !== $settings['username'] ) {
			$body['username'] = $settings['username'];
		}

		if ( '' !== $settings['avatar'] ) {
			if ( filter_var( $settings['avatar'], FILTER_VALIDATE_URL ) ) {
				$body['avatar_url'] = $settings['avatar'];
			} else {
				$avatar = \wp_get_attachment_image_url( $settings['avatar'], array( 250, 250 ) );
				if ( false !== $avatar ) {
					$body['avatar_url'] = $avatar;
				}
			}
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

		$settings = \apply_filters( "hey_notify_discord_settings_{$trigger}", $data );

		if ( ! is_array( $settings ) || ! isset( $settings['webhook_url'] ) ) {
			return;
		}

		$body = $this->prepare( $message, $settings );

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

		\do_action(
			'hey_notify_message_sent',
			array(
				'service'  => 'discord',
				'json'     => $json,
				'response' => $response,
				'trigger'  => $trigger,
				'message'  => $message,
				'data'     => $data,
			)
		);

		if ( ! \is_wp_error( $response ) ) {
			if ( 204 !== \wp_remote_retrieve_response_code( $response ) ) {
				// There was an error making the request.
				$error_message = \wp_remote_retrieve_response_message( $response );
			}
		} else {
			// There was an error making the request.
			$error_message = $response->get_error_message();
		}
	}
}

new Discord();
