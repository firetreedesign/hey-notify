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
