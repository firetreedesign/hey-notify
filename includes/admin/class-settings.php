<?php
/**
 * Events
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Admin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class
 */
class Settings {

	/**
	 * Text Input Field
	 *
	 * @package Hey_Notify
	 * @since 1.5.0
	 *
	 * @param array $args Arguments to pass to the function.
	 *
	 * @return void
	 */
	public function input_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'field_id'     => null,
			'page_id'      => null,
			'label'        => null,
			'type'         => 'text',
			'size'         => 'regular',
			'before'       => '',
			'after'        => '',
			'autocomplete' => null,
		);

			// Parse the arguments.
			$args = wp_parse_args( $args, $defaults );

			// Get the saved values from WordPress.
			$options = get_option( $args['page_id'] );

			// Start the output buffer.
			ob_start();
		?>
			<?php echo wp_kses_post( $args['before'] ); ?>
			<input type="<?php echo esc_attr( $args['type'] ); ?>" id="<?php echo esc_attr( $args['field_id'] ); ?>" name="<?php echo esc_attr( $args['page_id'] ); ?>[<?php echo esc_attr( $args['field_id'] ); ?>]" value="<?php echo wp_kses_post( isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : '' ); ?>" class="<?php echo esc_attr( $args['size'] ); ?>-text"<?php echo ( 'off' === $args['autocomplete'] ) ? ' autocomplete="off"' : ''; ?> />
			<?php echo wp_kses_post( $args['after'] ); ?>
			<?php if ( '' !== $args['label'] ) : ?>
				<p class="description"><?php echo wp_kses_post( $args['label'] ); ?></p>
			<?php endif; ?>

			<?php
			// Print the output.
			echo wp_kses(
				ob_get_clean(),
				\Hey_Notify\Helpers\get_allowed_tags()
			);
	} // input_callback.

	/**
	 * License Key Field
	 *
	 * @param array $args Arguments to pass to the function. (See below).
	 *
	 * @return void
	 */
	public function license_key_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'field_id' => null,
			'page_id'  => null,
			'label'    => null,
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		// Get the saved values from WordPress.
		$options = get_option( $args['page_id'] );

		$license_data = get_option( $args['field_id'] . '_status', '' );
		$license_data = json_decode( $license_data );

		// Start the output buffer.
		ob_start();
		?>
		<?php wp_nonce_field( $args['field_id'] . '-nonce', $args['field_id'] . '-nonce' ); ?>
		<input type="text" id="<?php echo esc_attr( $args['field_id'] ); ?>" name="<?php echo esc_attr( $args['page_id'] ); ?>[<?php echo esc_attr( $args['field_id'] ); ?>]" value="<?php echo ( isset( $options[ $args['field_id'] ] ) ? esc_attr( $options[ $args['field_id'] ] ) : '' ); ?>" class="regular-text" />
		<?php if ( isset( $license_data->license ) && 'valid' === $license_data->license ) : ?>
			<input type="submit" class="button-secondary" name="<?php echo esc_attr( $args['field_id'] . '_deactivate' ); ?>" value="<?php echo esc_attr( __( 'Deactivate License', 'hey-notify' ) ); ?>">
			<?php if ( isset( $license_data->expires ) && 'lifetime' === $license_data->expires ) : ?>
				<p class="description"><?php esc_html_e( 'Your license key never expires.', 'hey-notify' ); ?></p>
			<?php else : ?>
				<?php // translators: Date of expiration. ?>
				<p class="description"><?php echo esc_html( sprintf( __( 'Your license key expires on %s.', 'hey-notify' ), (string) date( 'F jS, Y', strtotime( $license_data->expires ) ) ) ); // phpcs:ignore ?></p>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( '' !== $args['label'] ) : ?>
			<p class="description"><?php echo esc_html( $args['label'] ); ?></p>
		<?php endif; ?>

		<?php
		// Print the output.
		echo wp_kses(
			ob_get_clean(),
			\Hey_Notify\Helpers\get_allowed_tags()
		);
	} // license_key_callback

	/**
	 * Checkbox Input Field
	 *
	 * @param array $args Arguments to pass to the function.
	 *
	 * @return void
	 */
	public function checkbox_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'field_id' => null,
			'page_id'  => null,
			'value'    => '1',
			'label'    => null,
			'before'   => '',
			'after'    => '',
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		// Get the saved values from WordPress.
		$options = get_option( $args['page_id'] );

		// Start the output buffer.
		ob_start();
		?>
		<?php echo wp_kses_post( $args['before'] ); ?>
		<input type="checkbox" id="<?php echo esc_attr( $args['field_id'] ); ?>" name="<?php echo esc_attr( $args['page_id'] ); ?>[<?php echo esc_attr( $args['field_id'] ); ?>]" value="<?php echo esc_attr( $args['value'] ); ?>" <?php checked( isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : '', 1 ); ?>/>
		<?php if ( '' !== $args['label'] ) : ?>
			<label for="<?php echo esc_attr( $args['field_id'] ); ?>" class="description"><?php echo esc_html( $args['label'] ); ?></label>
		<?php endif; ?>
		<?php echo wp_kses_post( $args['after'] ); ?>

		<?php
		// Print the output.
		echo wp_kses(
			ob_get_clean(),
			\Hey_Notify\Helpers\get_allowed_tags()
		);
	} // checkbox_callback

	/**
	 * Select Input Field
	 *
	 * @param array $args Arguments to pass to the function.
	 *
	 * @return void
	 */
	public function select_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'field_id' => null,
			'page_id'  => null,
			'label'    => null,
			'default'  => '',
			'options'  => array(),
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		// Get the saved values from WordPress.
		$options = get_option( $args['page_id'] );

		ob_start();
		?>
		<select id="<?php echo esc_attr( $args['field_id'] ); ?>" name="<?php echo esc_attr( $args['page_id'] ); ?>[<?php echo esc_attr( $args['field_id'] ); ?>]">
		<?php
		// Loop through all of the available options.
		foreach ( $args['options'] as $key => $value ) :
			?>
			<option <?php echo selected( ( empty( $options[ $args['field_id'] ] ) ? $args['default'] : $options[ $args['field_id'] ] ), $key, false ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
		<?php endforeach; ?>
		</select>
		<span class="description"><?php echo esc_html( $args['label'] ); ?></span>
		<?php
		// Print the output.
		echo wp_kses(
			ob_get_clean(),
			\Hey_Notify\Helpers\get_allowed_tags()
		);
	} // select_callback

	/**
	 * Textarea Input Field
	 *
	 * @param array $args Arguments to pass to the function.
	 *
	 * @return void
	 */
	public function textarea_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'field_id'     => null,
			'page_id'      => null,
			'textarea_id'  => null,
			'media_upload' => true,
			'rows'         => get_option( 'default_post_edit_rows', 10 ),
			'cols'         => 40,
			'minimal'      => false,
			'wysiwyg'      => false,
			'wpautop'      => false,
			'label'        => null,
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		// Get the saved values from WordPress.
		$options      = get_option( $args['page_id'] );
		$editor_value = isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : '';

		// Checks if it should display the WYSIWYG editor.
		if ( true === $args['wysiwyg'] ) {

			wp_editor(
				$editor_value,
				$args['textarea_id'],
				array(
					'textarea_name' => $args['page_id'] . '[' . $args['field_id'] . ']',
					'media_buttons' => $args['media_upload'],
					'textarea_rows' => $args['rows'],
					'wpautop'       => $args['wpautop'],
					'teeny'         => $args['minimal'],
				)
			);
		} else {
			// Display the plain textarea field.
			echo '<textarea rows="' . esc_attr( $args['rows'] ) . '" cols="' . esc_attr( $args['cols'] ) . '" name="' . esc_attr( $args['page_id'] ) . '[' . esc_attr( $args['field_id'] ) . ']" id="' . esc_attr( $args['textarea_id'] ) . '" class="rockpress large-text code">' . esc_html( $editor_value ) . '</textarea>';
		}

		if ( '' !== $args['label'] ) {
			echo '<p class="description">' . esc_html( $args['label'] ) . '</p>';
		}
	} // textarea_callback

	/**
	 * Text
	 *
	 * @param array $args Arguments to pass to the function.
	 *
	 * @return void
	 */
	public function text_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'header'  => 'h2',
			'title'   => null,
			'content' => null,
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		ob_start();
		// Check that the title and header_type are not blank.
		if ( ! is_null( $args['title'] ) ) {
			echo '<' . esc_attr( $args['header'] ) . '>' . esc_html( $args['title'] ) . '</' . esc_attr( $args['header'] ) . '>';
		}

		// Check that the content is not blank.
		if ( ! is_null( $args['content'] ) ) {
			echo wp_kses_post( $args['content'] );
		}

		// Print the output.
		echo wp_kses(
			ob_get_clean(),
			\Hey_Notify\Helpers\get_allowed_tags()
		);
	} // text_callback

	/**
	 * Media
	 *
	 * @param array $args Arguments to pass to the function.
	 * @return void
	 */
	public function media_callback( $args ) {
		wp_enqueue_media();
		wp_enqueue_script( 'hey-notify-metabox-imageinput', HEY_NOTIFY_PLUGIN_URL . 'includes/admin/metabox/fields/imageinput/imageinput.js', array(), HEY_NOTIFY_VERSION, true );
		wp_enqueue_style( 'hey-notify-metabox-imageinput', HEY_NOTIFY_PLUGIN_URL . 'includes/admin/metabox/fields/imageinput/imageinput.css', array(), HEY_NOTIFY_VERSION );

		// Set the defaults.
		$defaults = array(
			'field_id' => null,
			'page_id'  => null,
			'label'    => null,
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		// Get the saved values from WordPress.
		$options = get_option( $args['page_id'] );

		// Setup the variables.
		$this_field_name = "{$args['page_id']}[{$args['field_id']}]";
		$image_id        = isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : '';
		$image_url       = '' !== $image_id ? wp_get_attachment_image( $image_id, 'thumbnail' ) : '';
		$hidden          = '' !== $image_id ? '' : ' hidden';
		$button_text     = esc_attr__( 'Select Image', 'hey-notify' );

		// Create the input markup.
		$input = <<<END
		<div class="hey-notify-imageinput-field-container">
		<input type="hidden" class="hey-notify-imageinput-field-input" name="{$this_field_name}" value="{$image_id}"  data-field-name="{$args['field_id']}" />
		<span class="dashicons dashicons-no hey-notify-imageinput-field-remove{$hidden}"></span>
		<div class="hey-notify-imageinput-field-preview">{$image_url}</div>
		<input type="button" class="button-secondary hey-notify-imageinput-field-button" value="{$button_text}" />
		</div>
		END;

		echo wp_kses(
			$input,
			\Hey_Notify\Helpers\get_allowed_tags()
		);
	}

	/**
	 * Color Picker
	 *
	 * @param array $args Arguments to pass to the function.
	 * @return void
	 */
	public function color_picker_callback( $args ) {

		// Set the defaults.
		$defaults = array(
			'field_id'      => null,
			'page_id'       => null,
			'label'         => null,
			'default_value' => '',
		);

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		// Get the saved values from WordPress.
		$options = get_option( $args['page_id'] );

		// Enqueue the styles.
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		/**
		 * Enqueue the scripts.
		 */
		ob_start();
		?>
		jQuery(document).ready(function($){
			$("input#<?php echo esc_attr( $args['field_id'] ); ?>").wpColorPicker();
		});
		<?php
		$script = ob_get_clean();
		wp_register_script( esc_attr( $args['field_id'] ) . '_color_picker_script', '', array( 'jquery' ), HEY_NOTIFY_VERSION, true );
		wp_enqueue_script( esc_attr( $args['field_id'] ) . '_color_picker_script' );
		wp_add_inline_script( esc_attr( $args['field_id'] ) . '_color_picker_script', $script );

		/**
		 * Capture the field output
		 */
		ob_start();
		?>
		<input type="text" id="<?php echo esc_attr( $args['field_id'] ); ?>" name="<?php echo esc_attr( $args['page_id'] ); ?>[<?php echo esc_attr( $args['field_id'] ); ?>]" value="<?php echo wp_kses_post( isset( $options[ $args['field_id'] ] ) ? $options[ $args['field_id'] ] : $args['default_value'] ); ?>" data-default-color="<?php echo esc_attr( $args['default_value'] ); ?>" />
		<?php
		if ( '' !== $args['label'] ) {
			echo '<p class="description">' . esc_html( $args['label'] ) . '</p>';
		}
		?>
		<?php
		// Print the output.
		echo wp_kses(
			ob_get_clean(),
			\Hey_Notify\Helpers\get_allowed_tags()
		);
	}
}