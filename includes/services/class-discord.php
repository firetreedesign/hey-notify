<?php
/**
 * Discord
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Services;

use Carbon_Fields\Field;
use Hey_Notify\Service;
use Hey_Notify\Admin\Settings;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Discord class
 */
class Discord extends Service {

	/**
	 * Service
	 *
	 * @var string
	 */
	public $service = 'discord';

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		// Actions.
		\add_action( 'hey_notify_send_message_discord', array( $this, 'send' ), 10, 3 );
		\add_action( 'admin_init', array( $this, 'settings' ) );
		\add_action( 'hey_notify_discord_settings_core', array( $this, 'get_core_settings' ), 10, 1 );

		// Filters.
		\add_filter( 'hey_notify_settings_page_tabs', array( $this, 'settings_page_tabs' ) );
		\add_filter( 'hey_notify_services_select', array( $this, 'services_select' ), 10, 1 );
		\add_filter( 'hey_notify_service_fields', array( $this, 'get_metabox_fields' ), 10, 2 );

		// Carbon fields.
		\add_action( 'hey_notify_settings_container', array( $this, 'default_settings_carbon' ), 10, 1 );
	}

	/**
	 * Populate the Services select input
	 *
	 * @param array $services Services.
	 * @return array
	 */
	public function services_select( $services ) {
		$services['discord'] = __( 'Discord', 'hey-notify' );
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
				'field_name'        => '_hey_notify_discord_webhook',
				'field_label'       => __( 'Webhook URL', 'hey-notify' ),
				'instructions'      => sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Discord channel.', 'hey-notify' ), 'https://support.discord.com/hc/en-us/articles/228383668', __( 'Learn More', 'hey-notify' ) ),
				'field_type'        => 'textinput',
				'input_type'        => 'text',
				'width'             => '100%',
				'default_value'     => \Hey_Notify\Helpers\get_option( 'hey_notify_settings_discord', 'default_webhook' ),
				'conditional_logic' => array(
					array(
						array(
							'field' => '_hey_notify_service',
							'value' => 'discord',
						),
					),
				),
			),
		);
		array_push(
			$fields,
			array(
				'field_name'        => '_hey_notify_discord_avatar',
				'field_label'       => __( 'Discord Avatar', 'hey-notify' ),
				'instructions'      => __( 'Override the default avatar of the webhook. Not required.', 'hey-notify' ),
				'field_type'        => 'imageinput',
				'width'             => '50%',
				'default_value'     => \Hey_Notify\Helpers\get_option( 'hey_notify_settings_discord', 'default_avatar' ),
				'conditional_logic' => array(
					array(
						array(
							'field' => '_hey_notify_service',
							'value' => 'discord',
						),
					),
				),
			),
		);
		array_push(
			$fields,
			array(
				'field_name'        => '_hey_notify_discord_username',
				'field_label'       => __( 'Discord Username', 'hey-notify' ),
				'instructions'      => __( 'Override the default username of the webhook. Not required.', 'hey-notify' ),
				'field_type'        => 'textinput',
				'input_type'        => 'text',
				'width'             => '50%',
				'default_value'     => \Hey_Notify\Helpers\get_option( 'hey_notify_settings_discord', 'default_username' ),
				'conditional_logic' => array(
					array(
						array(
							'field' => '_hey_notify_service',
							'value' => 'discord',
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
			'tab_id'      => 'discord',
			'settings_id' => 'hey_notify_settings_discord',
			'title'       => __( 'Discord', 'hey-notify' ),
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
		if ( false === \get_option( 'hey_notify_settings_discord' ) ) {
			\add_option( 'hey_notify_settings_discord' );
		}

		// Register the section.
		\add_settings_section(
			'hey_notify_settings_discord_section',
			__( 'Default Settings for Discord', 'hey-notify' ),
			null,
			'hey_notify_settings_discord'
		);

		/**
		 * Default Webhook URL field
		 */
		\add_settings_field(
			'default_webhook',
			'<strong>' . __( 'Webhook URL', 'hey-notify' ) . '</strong>',
			array( new Settings(), 'input_callback' ),
			'hey_notify_settings_discord',
			'hey_notify_settings_discord_section',
			array(
				'field_id' => 'default_webhook',
				'page_id'  => 'hey_notify_settings_discord',
				'size'     => 'large',
				'label'    => sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Discord channel.', 'hey-notify' ), 'https://support.discord.com/hc/en-us/articles/228383668', __( 'Learn More', 'hey-notify' ) ),
			)
		);

		/**
		 * Default Icon field
		 */
		\add_settings_field(
			'default_avatar',
			'<strong>' . __( 'Discord Avatar', 'hey-notify' ) . '</strong>',
			array( new Settings(), 'media_callback' ),
			'hey_notify_settings_discord',
			'hey_notify_settings_discord_section',
			array(
				'field_id' => 'default_avatar',
				'page_id'  => 'hey_notify_settings_discord',
				'label'    => __( 'Override the default avatar of the webhook. Not required.', 'hey-notify' ),
			)
		);

		/**
		 * Default Username field
		 */
		\add_settings_field(
			'default_username',
			'<strong>' . __( 'Discord Username', 'hey-notify' ) . '</strong>',
			array( new Settings(), 'input_callback' ),
			'hey_notify_settings_discord',
			'hey_notify_settings_discord_section',
			array(
				'field_id' => 'default_username',
				'page_id'  => 'hey_notify_settings_discord',
				'size'     => 'regular',
				'label'    => __( 'Override the default username of the webhook. Not required.', 'hey-notify' ),
			)
		);

		// Finally, we register the fields with WordPress.
		register_setting(
			'hey_notify_settings_discord', // The group name of the settings being registered.
			'hey_notify_settings_discord', // The name of the set of options being registered.
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
			'webhook_url' => \get_post_meta( $data->ID, '_hey_notify_discord_webhook', true ),
			'username'    => \get_post_meta( $data->ID, '_hey_notify_discord_username', true ),
			'icon'        => \get_post_meta( $data->ID, '_hey_notify_discord_avatar', true ),
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
		);

		return $services;
	}

	/**
	 * Service fields
	 *
	 * @param array $fields Service fields.
	 * @return array
	 */
	public function fields_carbon( $fields = array() ) {
		$fields[] = (
			Field::make( 'text', 'hey_notify_discord_webhook', __( 'Webhook URL', 'hey-notify' ) )
				->set_attribute( 'type', 'url' )
				->set_help_text( sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Discord channel.', 'hey-notify' ), 'https://support.discord.com/hc/en-us/articles/228383668', __( 'Learn More', 'hey-notify' ) ) )
				->set_default_value( \get_option( '_hey_notify_default_discord_webhook', '' ) )
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
				->set_default_value( \get_option( '_hey_notify_default_discord_avatar', '' ) )
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
				->set_default_value( \get_option( '_hey_notify_default_discord_username', '' ) )
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

		// @mention Setup.
		$at_mention_pattern       = '/@+([a-zA-Z0-9_]+)/';
		$body['allowed_mentions'] = json_decode( "{'parse': [ 'roles', 'users', 'everyone' ]}" );

		// Subject.
		if ( isset( $message['subject'] ) && '' !== $message['subject'] ) {
			$body['content'] = $message['subject'];
			// Format @mentions.
			$body['content'] = preg_replace( $at_mention_pattern, '<@$1>', $body['content'] );
		}

		// Title.
		if ( '' !== $message['title'] ) {
			$embed_item['title'] = $message['title'];
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

		if ( isset( $settings['username'] ) && '' !== $settings['username'] ) {
			$body['username'] = $settings['username'];
		}

		if ( isset( $settings['avatar'] ) && '' !== $settings['avatar'] ) {
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

	/**
	 * Default settings.
	 *
	 * @param object $settings Settings object.
	 * @return void
	 */
	public function default_settings_carbon( $settings ) {
		$settings->add_tab(
			__( 'Discord', 'hey-notify' ),
			array(
				Field::make( 'separator', 'hey_notify_discord_separator', __( 'Default Settings for Discord', 'hey-notify' ) ),
				Field::make( 'text', 'hey_notify_default_discord_webhook', __( 'Webhook URL', 'hey-notify' ) )
					->set_attribute( 'type', 'url' )
					->set_help_text( sprintf( '%1s <a href="%2s">%3s</a>', __( 'The webhook that you created for your Discord channel.', 'hey-notify' ), 'https://support.discord.com/hc/en-us/articles/228383668', __( 'Learn More', 'hey-notify' ) ) ),
				Field::make( 'image', 'hey_notify_default_discord_avatar', __( 'Discord Avatar', 'hey-notify' ) )
					->set_help_text( __( 'Override the default avatar of the webhook. Not required.', 'hey-notify' ) )
					->set_width( 50 ),
				Field::make( 'text', 'hey_notify_default_discord_username', __( 'Discord Username', 'hey-notify' ) )
					->set_help_text( __( 'Override the default username of the webhook. Not required.', 'hey-notify' ) )
					->set_width( 50 ),
			)
		);
	}
}

new Discord();
