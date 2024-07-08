<?php
/**
 * Helper functions
 *
 * @package Hey_Notify
 */

namespace Hey_Notify\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper to get an option
 *
 * @param string $name Name.
 * @param string $key Key.
 * @return mixed
 */
function get_option( $name, $key ) {
	$option = \get_option( $name, array() );
	if ( ! is_array( $option ) ) {
		return '';
	}
	if ( ! isset( $option[ $key ] ) ) {
		return '';
	}
	return $option[ $key ];
}

/**
 * Get repeater field meta
 *
 * @param string  $repeater_name Repeater name.
 * @param integer $index Repeater index.
 * @param string  $field_name Field name.
 * @param integer $post_id Post ID.
 * @return mixed
 */
function get_repeater_meta( $repeater_name, $index, $field_name, $post_id ) {
	$repeater_value = json_decode( get_post_meta( $post_id, $repeater_name, true ), true );
	if ( ! is_array( $repeater_value ) ) {
		return null;
	}
	if ( ! is_numeric( $index ) ) {
		return null;
	}
	if ( ! isset( $repeater_value[ $index ] ) ) {
		return null;
	}
	if ( ! isset( $repeater_value[ $index ][ $field_name ] ) ) {
		return null;
	}
	return $repeater_value[ $index ][ $field_name ];
}

/**
 * Get allowed tags for wp_kses()
 *
 * @return array
 */
function get_allowed_tags() {
	$allowed = wp_kses_allowed_html( 'post' );
	// iframe.
	$allowed['iframe'] = array(
		'src'             => true,
		'height'          => true,
		'width'           => true,
		'frameborder'     => true,
		'allowfullscreen' => true,
		'style'           => true,
	);
	// form fields - input.
	$allowed['input'] = array(
		'class'  => true,
		'id'     => true,
		'name'   => true,
		'value'  => true,
		'type'   => true,
		'style'  => true,
		'data-*' => true,
	);
	// select.
	$allowed['select'] = array(
		'class'  => true,
		'id'     => true,
		'name'   => true,
		'value'  => true,
		'type'   => true,
		'style'  => true,
		'data-*' => true,
	);
	// select options.
	$allowed['option'] = array(
		'selected' => true,
		'value'    => true,
	);
	// style.
	$allowed['style'] = array(
		'types' => true,
	);
	$allowed['table'] = array(
		'data-*' => true,
		'class'  => true,
		'style'  => true,
	);

	return $allowed;
}

/**
 * Outputs the admin header HTML content.
 */
function admin_header() {
	$all_tabs        = \apply_filters( 'hey_notify_settings_page_tabs', array() );
	$active_tab      = isset( $_GET['tab'] ) ? $_GET['tab'] : ( count( $all_tabs ) > 0 ? $all_tabs[0]['tab_id'] : '' ); // phpcs:ignore
	$all_tab_actions = \apply_filters( 'hey_notify_settings_page_actions', array() );
	$has_tab_actions = false;

	foreach ( $all_tab_actions as $tab_action ) {
		if ( isset( $tab_action['tab_id'] ) && $tab_action['tab_id'] === $active_tab ) {
			$has_tab_actions = true;
		}
	}

	// phpcs:ignore
	$current_page = ! empty( $_GET['page'] ) ? $_GET['page'] : '';
	$page_title   = __( 'Notifications', 'hey-notify' );
	switch ( $current_page ) {
		case 'settings':
			$page_title = __( 'Settings', 'easy-digital-downloads' );
			break;
	}
	?>
		<style>
			.wrap h1.wp-heading-inline {
				display: none;
			}
			.page-title-action {
				visibility: hidden;
			}
			.hey-notify-admin-header-container {
				background-color: #fff;
				margin-left: -20px;
				padding: 10px 20px;
				border-bottom: 1px solid #c3c4c7;
			}
			.hey-notify-admin-header-inner {
				display: flex;
				gap: 15px;
				align-items: center;
			}
			@media screen and (max-width: 760px) {
				.hey-notify-admin-header-inner {
					flex-direction: column;
					align-items: flex-start;
					justify-content: center;
				}
				
			}
			.hey-notify-admin-logo {
				display: flex;
				gap: 5px;
				align-items: center;
			}
			@media screen and (max-width: 760px) {
				.hey-notify-admin-logo {
					display: none;
				}
			}
			.hey-notify-admin-header-page-title {
				margin: 0;
				font-size: 20px;
				font-weight: normal;
				margin: 0;
			}
		</style>
		<script>
		jQuery(document).ready(function($){
			const addNew = $( '.page-title-action:visible' );

			if ( addNew.length ) {
				addNew.appendTo( '.hey-notify-admin-actions' ).addClass( 'button' ).css( 'visibility', 'unset' );
			}
		});
		</script>
	<div class="hey-notify-admin-header-container">
		<div class="hey-notify-admin-header-inner">
			<div class="hey-notify-admin-logo">
				<img src="<?php echo esc_url( HEY_NOTIFY_PLUGIN_URL . 'images/logo.png' ); ?>" style="width: 120px; height: auto; margin-top: 4px;" />
				<?php do_action( 'hey_notify_settings_page_logo' ); ?>
			</div>
			<h1 class="hey-notify-admin-header-page-title"><?php echo esc_html( $page_title ); ?></h1>
			<div class="hey-notify-admin-actions">
				<?php if ( $has_tab_actions ) : ?>
					<?php foreach ( $all_tab_actions as $tab_action ) : ?>
						<?php if ( isset( $tab_action['tab_id'] ) && $tab_action['tab_id'] === $active_tab ) : ?>
							<a class="button<?php echo is_null( $tab_action['class'] ) ? esc_attr( '' ) : esc_attr( ' ' . $tab_action['class'] ); ?>" href="<?php echo esc_url( $tab_action['link'] ); ?>"<?php echo ( is_null( $tab_action['target'] ) ) ? '' : ' target="' . esc_attr( $tab_action['target'] ) . '"'; ?>><?php echo esc_html( $tab_action['title'] ); ?></a>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}
