<?php
/**
 * Repeater Field
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Admin\Metabox\Fields;

use Hey_Notify\Admin\Metabox\Builder;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for rendering the Repeater field
 */
class Repeater {

	/**
	 * Field Type
	 *
	 * @var string
	 */
	public $field_type = 'repeater';

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		\add_filter( 'hey_notify_metabox_fields', array( $this, 'render' ), 10, 4 );
		\add_action( "hey_notify_metabox_enqueue_scripts_{$this->field_type}", array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue styles and scripts
	 *
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'hey-notify-metabox-repeater', plugin_dir_url( __FILE__ ) . '/repeater.js', array(), HEY_NOTIFY_VERSION, true );
		wp_enqueue_style( 'hey-notify-metabox-repeater', plugin_dir_url( __FILE__ ) . '/repeater.css', array(), HEY_NOTIFY_VERSION );
	}

	/**
	 * Repeater field
	 *
	 * @since 1.5.0
	 *
	 * @param string $input The input markup.
	 * @param object $post The post object.
	 * @param array  $field The field settings.
	 * @param string $current_value The current value.
	 *
	 * @return string The field output
	 */
	public function render( $input, $post, $field, $current_value ) {
		// Check for this field.
		if ( $field['field_type'] !== $this->field_type ) {
			return $input;
		}

		// Metabox builer.
		$repeater = new Builder( null );

		$current_value = json_decode( $current_value, true );

		if ( is_array( $field['fields'] ) ) {
			$input .= <<<END
			<div data-repeater-field-name='{$field['field_name']}' data-repeater-field-index='' class="hey-notify-repeater-template" hidden>
				<div class='hey-notify-repeater-field-toolbar'>
					<div class="hey-notify-repeater-field-toolbar-counter">
						<span class="hey-notify-repeater-field-count"></span>
					</div>
					<div class="hey-notify-repeater-field-toolbar-action remove">
						<span class="dashicons dashicons-trash"></span>
					</div>
				</div>
				<div class='hey-notify-repeater-fields'>
			END;
			foreach ( $field['fields'] as $the_field ) {
				$the_field['repeater_field_name'] = $field['field_name'];
				$the_field['repeater_index']      = null;
				$input                           .= $repeater->build_field( $post, $the_field );
			}
			$input .= '</div></div>';
		}

		$input .= '<div class="hey-notify-repeater-field-inner">';
		if ( isset( $field['placeholder_label'] ) ) {
			$input .= '<p class="hey-notify-repeater-field-placeholder">' . $field['placeholder_label'] . '</p>';
		}

		if ( is_array( $current_value ) && is_array( $field['fields'] ) ) {
			foreach ( $current_value as $key => $group ) {
				$repeater_number = $key + 1;
				$input          .= <<<END
				<div data-repeater-field-name='{$field['field_name']}' data-repeater-field-index='{$key}'>
					<div class='hey-notify-repeater-field-toolbar'>
						<div class="hey-notify-repeater-field-toolbar-counter">
							<span class="hey-notify-repeater-field-count">{$repeater_number}</span>
						</div>
						<div class="hey-notify-repeater-field-toolbar-action remove">
							<span class="dashicons dashicons-trash"></span>
						</div>
					</div>
					<div class='hey-notify-repeater-fields'>
				END;
				foreach ( $field['fields'] as $the_field ) {
					$the_field['repeater_field_name'] = $field['field_name'];
					$the_field['repeater_index']      = $key;
					$input                           .= $repeater->build_field( $post, $the_field );
				}
				$input .= '</div></div>';
			}
		}

		$input .= '</div>';
		$input .= "<div><input type='button' class='button-secondary hey-notify-metabox-repeater-insert' value='{$field['insert_button_label']}' /></div>";

		// Return the field.
		return $input;
	}
}

new Repeater();
