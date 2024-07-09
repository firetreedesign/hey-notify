<?php
/**
 * Select Field
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Admin\Metabox\Fields;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class for rendering the Select field
 */
class Select {

	/**
	 * Field Type
	 *
	 * @var string
	 */
	public $field_type = 'select';

	/**
	 * Class construct
	 *
	 * @return void
	 */
	public function __construct() {
		\add_filter( 'hey_notify_metabox_fields', array( $this, 'render' ), 10, 4 );
	}

	/**
	 * Select field
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
			$this_field_name = "{$field['repeater_field_name']}[{$field['repeater_index']}][{$field['field_name']}]";
		} else {
			$this_field_name = $field['field_name'];
		}

		// Create the input markup.
		$input .= \sprintf(
			'<select class="hey-notify-%s-field-input" name="%s" data-field-name="%s">',
			$this->field_type,
			$this_field_name,
			$field['field_name']
		);

		foreach ( $field['choices'] as $key => $label ) {
			$input .= \sprintf(
				'<option%s value="%s">%s</option>',
				\selected( $the_current_value, $key, false ),
				$key,
				$label
			);
		}

		$input .= '</select>';

		// Return the field.
		return $input;
	}
}

new Select();
