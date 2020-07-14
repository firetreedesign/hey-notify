<?php
/**
 * Slack
 * 
 * @package HeyNotify
 */

namespace HeyNotify;

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
			$services['slack'] = HEYNOTIFY_PLUGIN_URL . '/images/services/slack.png';
		}

		return $services;
	}

	function fields( $fields = array() ) {
		$fields[] = (
			Field::make( 'text', 'heynotify_slack_webhook', __( 'Webhook URL', 'heynotify' ) )
				->set_attribute( 'type', 'url' )
				->set_help_text( __( 'The webhook that was generated for you by your preferred service.', 'heynotify' ) )
				->set_conditional_logic(
					array(
						array(
							'field' => 'heynotify_service',
							'value' => 'slack',
						)
					)
				)
		);
		return $fields;
	}

	function message( $message ) {
		// TODO
	}

}

new Slack();