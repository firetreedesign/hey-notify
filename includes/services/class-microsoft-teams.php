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
		$fields[] = (
			Field::make( 'color', 'hey_notify_microsoft_teams_color', __( 'Color', 'hey-notify' ) )
				->set_help_text( __( 'Select a color to use for the message attachment.', 'hey-notify' ) )
				->set_default_value( \get_option( '_hey_notify_default_microsoft_teams_color', '' ) )
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
		$card                      = new stdClass();
		$card->{'@type'}           = 'MessageCard';
		$card->{'@context'}        = 'http://schema.org/extensions';
		$card->sections            = array();
		$card->{'potentialAction'} = array();
		$card->{'themeColor'}      = ( '' !== $settings['color'] && null !== $settings['color'] ) ? str_replace( '#', '', $settings['color'] ) : '009bff';

		$card_section           = new stdClass();
		$card_section->markdown = true;

		// Subject.
		if ( isset( $subject ) && '' !== $subject ) {
			$card->title   = $subject;
			$card->summary = $subject;
		} else {
			$card->title   = __( 'Notification from Hey Notify', 'hey-notify' );
			$card->summary = __( 'Notification from Hey Notify', 'hey-notify' );
		}

		// Image.
		if ( isset( $image ) && '' !== $image ) {
			$card_images        = new stdClass();
			$card_images->image = $image;
			$card_images->title = '' !== $subject ? $subject : __( 'Notification from Hey Notify', 'hey-notify' );

			$card_section->images = array(
				$card_images,
			);
		}

		// Content.
		if ( isset( $content ) && '' !== $content ) {
			$card->text = $content;
		}

		// Fields.
		if ( isset( $message['fields'] ) && is_array( $message['fields'] ) ) {
			$card_section->facts = array();

			foreach ( $message['fields'] as $field ) {
				$fact        = new stdClass();
				$fact->name  = $field['name'];
				$fact->value = $field['value'];
				// Add it to the facts.
				$card_section->facts[] = $fact;
			}
		}

		// Add the main section to the card.
		$card->sections[] = $card_section;

		// Footer.
		if ( isset( $footer ) && '' !== $footer ) {
			$card_footer           = new stdClass();
			$card_footer->text     = $footer;
			$card_footer->markdown = true;
			// Add it to the sections.
			$card->sections[] = $card_footer;
		}

		// URL.
		if ( isset( $url ) && '' !== $url ) {
			$card_url            = new stdClass();
			$card_url->{'@type'} = 'OpenUri';
			$card_url->name      = ( isset( $title ) && '' !== $title ) ? $title : __( 'View', 'hey-notify' );
			$card_url->targets   = array();

			$target      = new stdClass();
			$target->os  = 'default';
			$target->uri = $url;

			// Add it to the actions.
			$card_url->targets[] = $target;
			// Add it to the body.
			$card->{'potentialAction'}[] = $card_url;
		}

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
		$json     = \wp_json_encode( $body );

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
					->set_help_text( sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Microsoft Teams channel.', 'hey-notify' ), 'https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook#add-an-incoming-webhook-to-a-teams-channel', __( 'Learn More', 'hey-notify' ) ) ),
				Field::make( 'color', 'hey_notify_default_microsoft_teams_color', __( 'Color', 'hey-notify' ) )
					->set_help_text( __( 'Select a color to use for the message attachment.', 'hey-notify' ) )
					->set_default_value( '#009bff' ),
			)
		);
	}

}

new Microsoft_Teams();
