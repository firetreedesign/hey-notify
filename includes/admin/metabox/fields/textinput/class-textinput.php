<?php
/**
 * Text Input Field
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Admin\Metabox\Fields;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for rendering the Text Input field
 */
class TextInput {

	/**
	 * Field Type
	 *
	 * @var string
	 */
	public $field_type = 'textinput';

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		\add_filter( 'hey_notify_metabox_fields', array( $this, 'render' ), 10, 4 );
	}

	/**
	 * Text Input field
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

		// Get the default value.
		$default_value = isset( $field['default_value'] ) ? $field['default_value'] : '';

		// Get the current value.
		$the_current_value = $current_value ? $current_value : $default_value;

		// Calculate the field name.
		if ( isset( $field['repeater_field_name'] ) ) {
			$the_field_name = "{$field['repeater_field_name']}[{$field['repeater_index']}][{$field['field_name']}]";
		} else {
			$the_field_name = $field['field_name'];
		}

		// Create the input markup.
		$input .= \sprintf(
			'<input class="hey-notify-%s-field-input" type="%s" name="%s" value="%s" data-field-name="%s" />',
			$this->field_type,
			$field['input_type'],
			$the_field_name,
			$the_current_value,
			$field['field_name']
		);

		// Return the field.
		return $input;
	}
}

new TextInput();
