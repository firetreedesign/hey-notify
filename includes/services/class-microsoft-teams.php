<?php
/**
 * Microsoft Teams
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Services;

use Carbon_Fields\Field;
use stdClass;
use Hey_Notify\Service;
use Hey_Notify\Admin\Settings;
use Hey_Notify\Admin\Metabox\Builder;

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

		// Actions.
		\add_action( 'hey_notify_send_message_microsoft_teams', array( $this, 'send' ), 10, 3 );
		\add_action( 'admin_init', array( $this, 'settings' ) );

		// Filters.
		\add_filter( 'hey_notify_settings_page_tabs', array( $this, 'settings_page_tabs' ) );
		\add_filter( 'hey_notify_services_select', array( $this, 'services_select' ), 10, 1 );
		\add_filter( 'hey_notify_service_fields', array( $this, 'get_metabox_fields' ), 10, 2 );

		// Carbon fields.
		\add_filter( 'hey_notify_microsoft_teams_settings_core', array( $this, 'get_core_settings' ), 10, 1 );
		\add_action( 'hey_notify_settings_container', array( $this, 'default_settings' ), 10, 1 );
	}

	/**
	 * Populate the Services select input
	 *
	 * @param array $services Services.
	 * @return array
	 */
	public function services_select( $services ) {
		$services['microsoft_teams'] = __( 'Microsoft Teams', 'hey-notify' );
		return $services;
	}

	/**
	 * Populate the Metabox fields
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function get_metabox_fields( $fields ) {
		array_push(
			$fields,
			array(
				'field_name'        => '_hey_notify_microsoft_teams_webhook',
				'field_label'       => __( 'Webhook URL', 'hey-notify' ),
				'instructions'      => sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Microsoft Teams channel.', 'hey-notify' ), 'https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook', __( 'Learn More', 'hey-notify' ) ),
				'field_type'        => 'textinput',
				'input_type'        => 'text',
				'width'             => '100%',
				'default_value'     => \Hey_Notify\Helpers\get_option( 'hey_notify_settings_microsoft_teams', 'default_webhook' ),
				'conditional_logic' => array(
					array(
						array(
							'field' => '_hey_notify_service',
							'value' => 'microsoft_teams',
						),
					),
				),
			),
		);
		array_push(
			$fields,
			array(
				'field_name'        => '_hey_notify_microsoft_teams_color',
				'field_label'       => __( 'Color', 'hey-notify' ),
				'instructions'      => __( 'Select a color to use for the message attachment.', 'hey-notify' ),
				'field_type'        => 'colorpicker',
				'width'             => '100%',
				'default_value'     => \Hey_Notify\Helpers\get_option( 'hey_notify_settings_microsoft_teams', 'default_color' ),
				'conditional_logic' => array(
					array(
						array(
							'field' => '_hey_notify_service',
							'value' => 'microsoft_teams',
						),
					),
				),
			),
		);
		return $fields;
	}

	/**
	 * Settings Page Tabs
	 *
	 * @since 1.5.0
	 * @param  array $tabs Tabs array.
	 * @return array       New tabs array
	 */
	public function settings_page_tabs( $tabs ) {

		$tabs[] = array(
			'tab_id'      => 'microsoft_teams',
			'settings_id' => 'hey_notify_settings_microsoft_teams',
			'title'       => __( 'Microsoft Teams', 'hey-notify' ),
			'submit'      => true,
		);

		return $tabs;
	}


	/**
	 * Admin settings
	 *
	 * @return void
	 */
	public function settings() {

		// If the option does not exist, then add it.
		if ( false === \get_option( 'hey_notify_settings_microsoft_teams' ) ) {
			\add_option( 'hey_notify_settings_microsoft_teams' );
		}

		// Register the section.
		\add_settings_section(
			'hey_notify_settings_microsoft_teams_section',
			__( 'Default Settings for Microsoft Teams', 'hey-notify' ),
			null,
			'hey_notify_settings_microsoft_teams'
		);

		/**
		 * Default Webhook URL field
		 */
		\add_settings_field(
			'default_webhook',
			'<strong>' . __( 'Webhook URL', 'hey-notify' ) . '</strong>',
			array( new Settings(), 'input_callback' ),
			'hey_notify_settings_microsoft_teams',
			'hey_notify_settings_microsoft_teams_section',
			array(
				'field_id' => 'default_webhook',
				'page_id'  => 'hey_notify_settings_microsoft_teams',
				'size'     => 'large',
				'label'    => sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Microsoft Teams channel.', 'hey-notify' ), 'https://docs.microsoft.com/en-us/microsoftteams/platform/webhooks-and-connectors/how-to/add-incoming-webhook#add-an-incoming-webhook-to-a-teams-channel', __( 'Learn More', 'hey-notify' ) ),
			)
		);

		/**
		 * Default Color field
		 */
		\add_settings_field(
			'default_color',
			'<strong>' . __( 'Color', 'hey-notify' ) . '</strong>',
			array( new Settings(), 'color_picker_callback' ),
			'hey_notify_settings_microsoft_teams',
			'hey_notify_settings_microsoft_teams_section',
			array(
				'field_id'      => 'default_color',
				'page_id'       => 'hey_notify_settings_microsoft_teams',
				'label'         => __( 'Select a color to use for the message attachment.', 'hey-notify' ),
				'default_value' => '#009bff',
			)
		);

		// Finally, we register the fields with WordPress.
		register_setting(
			'hey_notify_settings_microsoft_teams', // The group name of the settings being registered.
			'hey_notify_settings_microsoft_teams', // The name of the set of options being registered.
			array( $this, 'sanitize_settings_callback' ) // The name of the function responsible for validating the fields.
		);
	}

	/**
	 * Sanitize callback
	 *
	 * @since 1.5.0
	 * @param  array $input Input values.
	 * @return array
	 */
	public function sanitize_settings_callback( $input ) {
		// Define all of the variables that we'll be using.
		$output = array();

		// Loop through each of the incoming options.
		foreach ( $input as $key => $value ) {
			// Check to see if the current option has a value. If so, process it.
			if ( isset( $input[ $key ] ) ) {
				// Strip all HTML and PHP tags and properly handle quoted strings.
				$output[ $key ] = wp_strip_all_tags( stripslashes( $input[ $key ] ) );
			}
		}
		// Return the array.
		return $output;
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
	public function fields_carbon( $fields = array() ) {

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
		$json = \wp_json_encode( $body );

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
