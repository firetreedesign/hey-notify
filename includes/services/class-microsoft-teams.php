<?php
/**
 * Microsoft Teams
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

use Carbon_Fields\Field;
use stdClass;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slack class
 */
class Microsoft_Teams extends Service {

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		\add_action( 'hey_notify_send_message_microsoft_teams', array( $this, 'send' ), 10, 3 );
		\add_filter( 'hey_notify_microsoft_teams_settings_core', array( $this, 'get_core_settings' ), 10, 1 );
		\add_action( 'hey_notify_settings_container', array( $this, 'default_settings' ), 10, 1 );
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
			'webhook_url' => \carbon_get_post_meta( $data->ID, 'hey_notify_microsoft_teams_webhook' ),
			'icon'        => \carbon_get_post_meta( $data->ID, 'hey_notify_microsoft_teams_icon' ),
			'color'       => \carbon_get_post_meta( $data->ID, 'hey_notify_microsoft_teams_color' ),
		);
	}

	/**
	 * Add the service
	 *
	 * @param array $services Services.
	 * @return array
	 */
	public function services( $services = array() ) {

		$services[] = array(
			'value' => 'microsoft_teams',
			'label' => __( 'Microsoft Teams', 'hey-notify' ),
			'image' => HEY_NOTIFY_PLUGIN_URL . 'images/services/microsoft_teams.png',
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
			Field::make( 'text', 'hey_notify_microsoft_teams_webhook', __( 'Webhook URL', 'hey-notify' ) )
				->set_attribute( 'type', 'url' )
				->set_help_text( sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Microsoft Teams channel.', 'hey-notify' ), 'https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook', __( 'Learn More', 'hey-notify' ) ) )
				->set_default_value( \get_option( '_hey_notify_default_microsoft_teams_webhook', '' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'hey_notify_service',
							'value' => 'microsoft_teams',
						),
					)
				)
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

		$subject = $message['subject'];
		$title   = $message['title'];
		$content = $message['content'];
		$footer  = $message['footer'];
		$image   = $message['image'];
		$url     = $message['url'];

		// Create the card.
		$card              = new stdClass();
		$card->type        = 'message';
		$card->attachments = array();

		// Create the attachment.
		$attachment                  = new stdClass();
		$attachment->{'contentType'} = 'application/vmd.microsoft.card.adaptive';
		$attachment->{'contentUrl'}  = null;

		// Create the content.
		$content              = new stdClass();
		$content->type        = 'AdaptiveCard';
		$content->{'$schema'} = 'http://adaptivecards.io/schemas/adaptive-card.json';
		$content->version     = '1.2';
		$content->body        = array();

		// Subject.
		if ( isset( $subject ) && '' !== $subject ) {
			$subject_body         = new stdClass();
			$subject_body->type   = 'TextBlock';
			$subject_body->size   = 'Medium';
			$subject_body->weight = 'Bolder';
			$subject_body->text   = $subject;
			// Add it to the body.
			$content->body[] = $subject_body;
		}

		// Image.
		if ( isset( $image ) && '' !== $image ) {
			$image_body       = new stdClass();
			$image_body->type = 'Image';
			$image_body->url  = $image;
			// Add it to the body.
			$content->body[] = $image_body;
		}

		// Content.
		if ( isset( $content ) && '' !== $content ) {
			$content_body       = new stdClass();
			$content_body->type = 'TextBlock';
			$content_body->text = $content;
			$content_body->wrap = true;
			// Add it to the body.
			$content->body[] = $content_body;
		}

		// Fields.
		if ( isset( $message['fields'] ) && is_array( $message['fields'] ) ) {
			$fields_body        = new stdClass();
			$fields_body->type  = 'FactSet';
			$fields_body->facts = array();

			foreach ( $message['fields'] as $field ) {
				$fact        = new stdClass();
				$fact->title = $field['name'];
				$fact->value = $field['value'];
				// Add it to the facts.
				$section->facts[] = $fact;
			}

			// Add it to the body.
			$content->body[] = $fields_body;
		}

		// Footer.
		if ( isset( $footer ) && '' !== $footer ) {
			$footer_body       = new stdClass();
			$footer_body->type = 'TextBlock';
			$footer_body->text = $footer;
			$footer_body->wrap = true;
			// Add it to the body.
			$content->body[] = $footer_body;
		}

		// URL.
		if ( isset( $url ) && '' !== $url ) {
			$url_body          = new stdClass();
			$url_body->type    = 'ActionSet';
			$url_body->actions = array();
			// Setup the action.
			$action        = new stdClass();
			$action->type  = 'Action.OpenUrl';
			$action->title = ( isset( $title ) && '' !== $title ) ? $title : __( 'View', 'hey-notify' );
			$action->url   = $url;
			// Add it to the actions.
			$url_body->actions[] = $action;
			// Add it to the body.
			$content->body[] = $url_body;
		}

		// Add the content to the attachment.
		$attachment->content = $content;

		// Add the attachment to the card.
		$card->attachments[] = $attachment;

		return $card;
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

		$settings = \apply_filters( "hey_notify_microsoft_teams_settings_{$trigger}", $data );

		if ( ! is_array( $settings ) || ! isset( $settings['webhook_url'] ) ) {
			return;
		}

		$body = $this->prepare( $message, $settings );

		$json     = \wp_json_encode( $body, JSON_FORCE_OBJECT );
		$response = \wp_remote_post(
			$settings['webhook_url'],
			array(
				'headers' => array(
					'Content-Type' => 'application/json;',
				),
				'body'    => $json,
			)
		);

		\do_action(
			'hey_notify_message_sent',
			array(
				'service'  => 'microsoft_teams',
				'json'     => $json,
				'response' => $response,
				'trigger'  => $trigger,
				'message'  => $message,
				'data'     => $data,
			)
		);

		if ( ! \is_wp_error( $response ) ) {
			if ( 200 !== \wp_remote_retrieve_response_code( $response ) ) {
				$error_message = \wp_remote_retrieve_response_message( $response );
			}
		} else {
			// There was an error making the request.
			$error_message = $response->get_error_message();
		}
	}

	/**
	 * Default settings.
	 *
	 * @param object $settings Settings object.
	 * @return void
	 */
	public function default_settings( $settings ) {
		$settings->add_tab(
			__( 'Microsoft Teams', 'hey-notify' ),
			array(
				Field::make( 'separator', 'hey_notify_microsoft_teams_separator', __( 'Default Settings for Microsoft Teams', 'hey-notify' ) ),
				Field::make( 'text', 'hey_notify_default_microsoft_teams_webhook', __( 'Webhook URL', 'hey-notify' ) )
					->set_attribute( 'type', 'url' )
					->set_help_text( sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Microsoft Teams channel.', 'hey-notify' ), 'https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook#add-an-incoming-webhook-to-a-teams-channel', __( 'Learn More', 'hey-notify' ) ) )
			)
		);
	}

}

new Microsoft_Teams();
