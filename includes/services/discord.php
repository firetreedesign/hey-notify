<?php
/**
 * Discord
 * 
 * @package FireTreeNotify
 */

namespace FireTreeNotify\Discord;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'firetree_notify_send_message', __NAMESPACE__ . '\\send', 10, 1 );

function send( $message ) {
	// error_log('Running Discord Bot...');
	// error_log( $message['notification']->ID );
	$service = \carbon_get_post_meta( $message['notification']->ID, 'firetree_notify_service' );
	// error_log('Service: ' . $service );

	if ( 'discord' !== $service ) {
		error_log( 'Bailing out. Not for Discord.' );
		return;
	}

	$webhook_url = \carbon_get_post_meta( $message['notification']->ID, 'firetree_notify_webhook_url' );
	$username    = \carbon_get_post_meta( $message['notification']->ID, 'firetree_notify_discord_username' );
	$avatar      = \carbon_get_post_meta( $message['notification']->ID, 'firetree_notify_discord_avatar' );

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

	$body = array(
		'embeds' => array( $embed_item ),
	);

	if ( '' !== $username ) {
		$body['username'] = $username;
	}

	if ( '' !== $avatar ) {
		$avatar = wp_get_attachment_image_url( $avatar, array( 250, 250 ) );
		if ( false !== $avatar ) {
			$body['avatar_url'] = $avatar;
		}
	}

	if ( isset( $message['content'] ) && '' !== $message['content'] ) {
		$body['content'] = $message['content'];
	}

	$json = json_encode( $body );
	// error_log( $json );
	$response = wp_remote_post( $webhook_url, array(
		'headers' => array(
			'Content-Type' => 'application/json; charset=utf-8',
		),
		'body' => $json,
	) );
	
	if ( ! is_wp_error( $response ) ) {
		// The request went through successfully, check the response code against
		// what we're expecting
		if ( 204 == wp_remote_retrieve_response_code( $response ) ) {
			// error_log( 'Message sent to Discord!' );
		} else {
			// The response code was not what we were expecting, record the message
			$error_message = wp_remote_retrieve_response_message( $response );
			error_log( $error_message );
		}
	} else {
		// There was an error making the request
		$error_message = $response->get_error_message();
		error_log( $error_message );
	}
}