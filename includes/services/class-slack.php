<?php
/**
 * Slack
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Services;

use Hey_Notify\Service;
use Hey_Notify\Admin\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Slack class
 */
class Slack extends Service {

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		// Actions.
		\add_action( 'hey_notify_send_message_slack', array( $this, 'send' ), 10, 3 );
		\add_action( 'admin_init', array( $this, 'settings' ) );
		\add_action( 'hey_notify_slack_settings_core', array( $this, 'get_core_settings' ), 10, 1 );

		// Filters.
		\add_filter( 'hey_notify_settings_page_tabs', array( $this, 'settings_page_tabs' ) );
		\add_filter( 'hey_notify_services_select', array( $this, 'services_select' ), 10, 1 );
		\add_filter( 'hey_notify_service_fields', array( $this, 'get_metabox_fields' ), 10, 2 );
	}

	/**
	 * Populate the Services select input
	 *
	 * @param array $services Services.
	 * @return array
	 */
	public function services_select( $services ) {
		$services['slack'] = __( 'Slack', 'hey-notify' );
		return $services;
	}

	/**
	 * Populate the Metabox fields
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function get_metabox_fields( $fields ) {
		return array_merge(
			$fields,
			array(
				array(
					'field_name'        => '_hey_notify_slack_webhook',
					'field_label'       => __( 'Webhook URL', 'hey-notify' ),
					'instructions'      => sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Slack channel.', 'hey-notify' ), 'https://api.slack.com/messaging/webhooks', __( 'Learn More', 'hey-notify' ) ),
					'field_type'        => 'textinput',
					'input_type'        => 'text',
					'width'             => '100%',
					'default_value'     => \Hey_Notify\Helpers\get_option( 'hey_notify_settings_slack', 'default_webhook' ),
					'conditional_logic' => array(
						array(
							array(
								'field' => '_hey_notify_service',
								'value' => 'slack',
							),
						),
					),
				),
			)
		);
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
			'tab_id'      => 'slack',
			'settings_id' => 'hey_notify_settings_slack',
			'title'       => __( 'Slack', 'hey-notify' ),
			'submit'      => true,
		);

		return $tabs;
	}

	/**
	 * Slack settings
	 *
	 * @return void
	 */
	public function settings() {

		// If the option does not exist, then add it.
		if ( false === \get_option( 'hey_notify_settings_slack' ) ) {
			\add_option( 'hey_notify_settings_slack' );
		}

		// Register the section.
		\add_settings_section(
			'hey_notify_settings_slack_section',
			__( 'Default Settings for Slack', 'hey-notify' ),
			null,
			'hey_notify_settings_slack'
		);

		/**
		 * Default Webhook URL field
		 */
		\add_settings_field(
			'default_webhook',
			'<strong>' . __( 'Webhook URL', 'hey-notify' ) . '</strong>',
			array( new Settings(), 'input_callback' ),
			'hey_notify_settings_slack',
			'hey_notify_settings_slack_section',
			array(
				'field_id' => 'default_webhook',
				'page_id'  => 'hey_notify_settings_slack',
				'size'     => 'large',
				'label'    => sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Slack channel.', 'hey-notify' ), 'https://api.slack.com/messaging/webhooks', __( 'Learn More', 'hey-notify' ) ),
			)
		);

		// Finally, we register the fields with WordPress.
		\register_setting(
			'hey_notify_settings_slack', // The group name of the settings being registered.
			'hey_notify_settings_slack', // The name of the set of options being registered.
			array( $this, 'sanitize_settings_callback' ) // The name of the function responsible for validating the fields.
		);
	}

	/**
	 * Sanitize callback
	 *
	 * @since 1.5.0
	 *
	 * @param  array $input Input values.
	 *
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
				$output[ $key ] = \wp_strip_all_tags( stripslashes( $input[ $key ] ) );
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
			'webhook_url' => \get_post_meta( $data->ID, '_hey_notify_slack_webhook', true ),
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
			'value' => 'slack',
			'label' => __( 'Slack', 'hey-notify' ),
		);

		return $services;
	}

	/**
	 * Sanitize a string for Slack
	 *
	 * @param string $value Text.
	 * @return string
	 */
	private function sanitize( $value ) {
		$value = null === $value ? '' : $value;
		$value = str_replace( '&', '&amp;', $value );
		$value = str_replace( '<', '&lt;', $value );
		$value = str_replace( '>', '&gt;', $value );
		return $value;
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

		$body           = array();
		$body['blocks'] = $blocks;

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
		$settings = \apply_filters( "hey_notify_slack_settings_{$trigger}", $data );

		if ( ! is_array( $settings ) || ! isset( $settings['webhook_url'] ) ) {
			return;
		}

		$body = $this->prepare( $message, $settings );

		$json     = \wp_json_encode( $body );
		$response = \wp_safe_remote_post(
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
				'service'  => 'slack',
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
}

new Slack();
