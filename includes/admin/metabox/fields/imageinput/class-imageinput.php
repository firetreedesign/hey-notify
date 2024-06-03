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
 * Class for rendering the Image Input field
 */
class ImageInput {

	/**
	 * Field Type
	 *
	 * @var string
	 */
	public $field_type = 'imageinput';

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
		wp_enqueue_script( 'hey-notify-metabox-imageinput', plugin_dir_url( __FILE__ ) . '/imageinput.js', array(), HEY_NOTIFY_VERSION, true );
		wp_enqueue_style( 'hey-notify-metabox-imageinput', plugin_dir_url( __FILE__ ) . '/imageinput.css', array(), HEY_NOTIFY_VERSION );
	}

	/**
	 * Image Input field
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

		wp_enqueue_media();

		// Get the default value.
		$default_value = isset( $field['default_value'] ) ? $field['default_value'] : '';

		// Calculate the field name.
		if ( isset( $field['repeater_field_name'] ) ) {
			$this_field_name = "{$field['repeater_field_name']}[{$field['repeater_index']}][{$field['field_name']}]";
		} else {
			$this_field_name = $field['field_name'];
		}

		// Setup the variables.
		$image_id    = $current_value ? $current_value : $default_value;
		$image_url   = '' !== $image_id ? wp_get_attachment_image( $image_id, 'thumbnail' ) : '';
		$hidden      = '' !== $image_id ? '' : ' hidden';
		$button_text = esc_attr__( 'Select Image', 'hey-notify' );

		// Create the input markup.
		$input = <<<END
		<input type="hidden" class="hey-notify-{$this->field_type}-field-input" name="{$this_field_name}" value="{$image_id}"  data-field-name="{$field['field_name']}" />
		<span class="dashicons dashicons-no hey-notify-{$this->field_type}-field-remove{$hidden}"></span>
		<div class="hey-notify-{$this->field_type}-field-preview">{$image_url}</div>
		<input type="button" class="button-secondary hey-notify-{$this->field_type}-field-button" value="{$button_text}" />
		END;

		// Return the field.
		return $input;
	}
}

new ImageInput();
