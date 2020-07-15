<?php
/**
 * Discord
 * 
 * @package HeyNotify
 */

namespace HeyNotify;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Discord extends Service {

	/**
	 * Service options
	 *
	 * @param array $services
	 * @return array
	 */
	public function services( $services = array() ) {
		if ( ! isset( $services['discord'] ) ) {
			$services['discord'] = HEYNOTIFY_PLUGIN_URL . '/images/services/discord.png';
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
			Field::make( 'text', 'heynotify_discord_webhook', __( 'Webhook URL', 'heynotify' ) )
				->set_attribute( 'type', 'url' )
				->set_help_text( sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Discord channel.', 'heynotify' ), 'https://support.discord.com/hc/en-us/articles/228383668', __( 'Learn More', 'heynotify' ) ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'heynotify_service',
							'value' => 'discord',
						)
					)
				)
		);
		$fields[] = (
			Field::make( 'image', 'heynotify_discord_avatar', __( 'Discord Avatar', 'heynotify' ) )
				->set_help_text( __( 'Override the default avatar of the webhook. Not required.', 'heynotify' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'heynotify_service',
							'value' => 'discord',
						)
					)
				)
				->set_width( 50 )
		);
		$fields[] = (
			Field::make( 'text', 'heynotify_discord_username', __( 'Discord Username', 'heynotify' ) )
				->set_help_text( __( 'Override the default username of the webhook. Not required.', 'heynotify' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'heynotify_service',
							'value' => 'discord',
						)
					)
				)
				->set_width( 50 )
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
	
		if ( 'discord' !== $service ) {
			return;
		}
	
		$webhook_url = \carbon_get_post_meta( $message['notification']->ID, 'heynotify_discord_webhook' );
		$username    = \carbon_get_post_meta( $message['notification']->ID, 'heynotify_discord_username' );
		$avatar      = \carbon_get_post_meta( $message['notification']->ID, 'heynotify_discord_avatar' );
	
		$embed_item = array();
	
		if ( '' !== $message['url_title'] ) {
			$embed_item['title'] = $message['url_title'];
		}
	
		if ( '' !== $message['url'] ) {
			$embed_item['url'] = $message['url'];
		}
	
		if ( isset( $message['attachments'] ) && is_array( $message['attachments'] ) ) {
			$fields = array();
			foreach( $message['attachments'] as $field ) {
				$fields[] = array(
					'name' => $field['name'],
					'value' => $field['value'],
					'inline' => $field['inline']
				);
			}
			$embed_item['fields'] = $fields;
		}

		if ( isset( $message['image'] ) ) {
			$embed_item['thumbnail'] = array(
				'url' => $message['image']
			);
		}
	
		$body = array(
			'embeds' => array( $embed_item ),
		);
	
		if ( '' !== $username ) {
			$body['username'] = $username;
		}
	
		if ( '' !== $avatar ) {
			$avatar = \wp_get_attachment_image_url( $avatar, array( 250, 250 ) );
			if ( false !== $avatar ) {
				$body['avatar_url'] = $avatar;
			}
		}
	
		if ( isset( $message['content'] ) && '' !== $message['content'] ) {
			$body['content'] = $message['content'];
		}
	
		$json = \json_encode( $body );
		$response = \wp_remote_post( $webhook_url, array(
			'headers' => array(
				'Content-Type' => 'application/json; charset=utf-8',
			),
			'body' => $json,
		) );
		
		if ( ! \is_wp_error( $response ) ) {
			if ( 204 == \wp_remote_retrieve_response_code( $response ) ) {
				// error_log( 'Message sent to Discord!' );
			} else {
				$error_message = \wp_remote_retrieve_response_message( $response );
			}
		} else {
			// There was an error making the request
			$error_message = $response->get_error_message();
		}
	}
}

new Discord();
