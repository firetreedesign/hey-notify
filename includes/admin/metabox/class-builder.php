<?php
/**
 * Hey Notify Meta Box Builder
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Admin\Metabox;

use function Hey_Notify\Helpers\get_repeater_meta;
use function Hey_Notify\Helpers\get_allowed_tags;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'fields/select/class-select.php';
require_once plugin_dir_path( __FILE__ ) . 'fields/colorpicker/class-colorpicker.php';
require_once plugin_dir_path( __FILE__ ) . 'fields/textinput/class-textinput.php';
require_once plugin_dir_path( __FILE__ ) . 'fields/imageinput/class-imageinput.php';
require_once plugin_dir_path( __FILE__ ) . 'fields/repeater/class-repeater.php';

/**
 * Class that handles the rendering of the metabox
 */
class Builder {

	/**
	 * ID
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Title
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * Context
	 *
	 * @var string
	 */
	private $context = 'advanced';

	/**
	 * Priority
	 *
	 * @var string
	 */
	private $priority = 'default';

	/**
	 * Screens
	 *
	 * @var array
	 */
	private $screens = array();

	/**
	 * Fields
	 *
	 * @var array
	 */
	private $fields = array();

	/**
	 * Class constructor
	 *
	 * @since 1.0.0
	 *
	 * @param array $metabox Array of metabox settings.
	 */
	public function __construct( $metabox ) {

		if ( ! is_array( $metabox ) ) {
			return;
		}

		if ( isset( $metabox['id'] ) ) {
			$this->id = $metabox['id'];
		}

		if ( isset( $metabox['title'] ) ) {
			$this->title = $metabox['title'];
		}

		if ( isset( $metabox['context'] ) ) {
			$this->context = $metabox['context'];
		}

		if ( isset( $metabox['priority'] ) ) {
			$this->priority = $metabox['priority'];
		}

		if ( isset( $metabox['screens'] ) ) {
			$this->screens = $metabox['screens'];
		}

		if ( isset( $metabox['fields'] ) ) {
			$this->fields = $metabox['fields'];
		}

		$this->actions();
	}

	/**
	 * Meta Box Actions
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function actions() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @since 1.5.0
	 * @return void
	 */
	public function enqueue() {
		if ( ! $this->is_screen() ) {
			return;
		}

		if ( ! $this->has_fields() ) {
			return;
		}

		foreach ( $this->fields as $field ) {
			if ( ! isset( $field['field_type'] ) ) {
				continue;
			}

			do_action( "hey_notify_metabox_enqueue_scripts_{$field['field_type']}" );
		}
	}

	/**
	 * Check if we are on the correct screen
	 *
	 * @return boolean
	 */
	private function is_screen() {
		$screen = \get_current_screen();
		if ( ! $screen ) {
			return false;
		}
		if ( ! in_array( $screen->post_type, $this->screens, true ) ) {
			return false;
		}
		if ( 'post' !== $screen->base ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the metabox has fields
	 *
	 * @return boolean
	 */
	private function has_fields() {
		if ( ! isset( $this->fields ) ) {
			return false;
		}

		if ( ! is_array( $this->fields ) ) {
			return false;
		}

		if ( 0 === count( $this->fields ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Creates a meta box for each screen
	 *
	 * @since 1.5.0
	 */
	public function add_meta_boxes() {

		foreach ( $this->screens as $screen ) {

			add_meta_box(
				$this->id,
				$this->title,
				array( $this, 'add_meta_box_callback' ),
				$screen,
				$this->context,
				$this->priority
			);

		}
	}

	/**
	 * Generates the meta box output
	 *
	 * @since 1.5.0
	 * @param object $post The post object.
	 */
	public function add_meta_box_callback( $post ) {
		wp_nonce_field( $this->id . '_metabox', $this->id . '_metabox' );
		$this->build_fields( $post );
	}

	/**
	 * Builds the specified fields
	 *
	 * @since 1.5.0
	 * @param  object $post The post object.
	 * @return void
	 */
	public function build_fields( $post ) {
		$output = '';
		foreach ( $this->fields as $field ) {
			$output .= $this->build_field( $post, $field );
		}
		echo '<div class="hey-notify-metabox widefat">' . wp_kses( $output, get_allowed_tags() ) . '</div>';
	}

	/**
	 * Build the field
	 *
	 * @param object $post The post object.
	 * @param object $field The field object.
	 * @return string
	 */
	public function build_field( $post, $field ) {
		$label = "<label for='{$field['field_name']}'>{$field['field_label']}</label>";

		$instructions = '';
		if ( isset( $field['instructions'] ) ) {
			$instructions = $field['instructions'];
		}

		$conditional_logic = null;
		if ( isset( $field['conditional_logic'] ) ) {
			$conditional_logic = wp_json_encode( $field['conditional_logic'] );
		}

		if ( ! isset( $post ) ) {
			$current_value = '';
		} elseif ( isset( $field['repeater_field_name'] ) && isset( $field['repeater_index'] ) ) {
			$current_value = get_repeater_meta( $field['repeater_field_name'], $field['repeater_index'], $field['field_name'], $post->ID );
		} elseif ( isset( $field['repeater_field_name'] ) ) {
			$current_value = '';
		} else {
			$current_value = get_post_meta( $post->ID, $field['field_name'], true );
		}

		return $this->row_format( $field, $label, $instructions, apply_filters( 'hey_notify_metabox_fields', '', $post, $field, $current_value ), $conditional_logic );
	}

	/**
	 * Builds a string of attributes for a field
	 *
	 * @since 1.0.0
	 * @param  array $field The field attributes.
	 * @return string        The string of field attributes
	 */
	private function get_attributes( $field ) {
		if ( ! isset( $field['attributes'] ) || ! is_array( $field['attributes'] ) ) {
			return '';
		}

		$attr_string = '';
		foreach ( $field['attributes'] as $key => $value ) {
			$attr_string .= ' ' . $key . '="' . $value . '"';
		}
		return $attr_string;
	}

	/**
	 * Build the row formatting
	 *
	 * @since 1.5.0
	 * @param array  $field The field.
	 * @param string $label The field label.
	 * @param string $instructions The field instructions.
	 * @param string $input The field HTML.
	 * @param string $conditional_logic Conditional logic.
	 * @return string The row HTML
	 */
	public static function row_format( $field, $label, $instructions, $input, $conditional_logic ) {
		$the_conditional_logic = $conditional_logic ? " data-conditional-logic='{$conditional_logic}'" : '';
		$hidden                = $conditional_logic ? ' hidden' : '';
		$width                 = isset( $field['width'] ) ? "style='flex-basis: {$field['width']};'" : "style='flex-basis: 100%;'";

		return <<<END
		<div class="hey-notify-metabox-field-wrapper" {$the_conditional_logic} {$hidden} {$width}>
			<div class="hey-notify-label">{$label}</div>
			<div class="hey-notify-{$field['field_type']}-field-container">{$input}</div>
			<div class="hey-notify-instructions">{$instructions}</div>
		</div>
		END;
	}

	/**
	 * Save the field data
	 *
	 * @since 1.0.0
	 * @param  string $post_id The post ID.
	 * @return mixed
	 */
	public function save_post( $post_id ) {
		if ( ! isset( $_POST[ $this->id . '_metabox' ] ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $this->id . '_metabox' ] ) ), $this->id . '_metabox' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		foreach ( $this->fields as $field ) {
			if ( isset( $_POST[ $field['field_name'] ] ) ) {
				if ( is_array( $_POST[ $field['field_name'] ] ) ) {
					$field_value = filter_input( INPUT_POST, $field['field_name'], FILTER_CALLBACK, array( 'options' => 'sanitize_text_field' ) );
					update_post_meta( $post_id, $field['field_name'], wp_json_encode( $field_value ) );
				} else {
					update_post_meta( $post_id, $field['field_name'], sanitize_text_field( wp_unslash( $_POST[ $field['field_name'] ] ) ) );
				}
			} else {
				switch ( $field['field_type'] ) {
					case 'checkbox':
						update_post_meta( $post_id, $field['field_name'], '0' );
						break;
				}
			}
		}
	}
}
