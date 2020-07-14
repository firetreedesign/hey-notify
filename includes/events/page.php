<?php
/**
 * Page events
 * 
 * @package HeyNotify
 */

namespace HeyNotify\Events\Page;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Filters
add_filter( 'heynotify_event_types ', __NAMESPACE__ . '\\types' );
add_filter( 'heynotify_event_actions', __NAMESPACE__ . '\\actions' );

/**
 * Add Page type
 *
 * @param array $types
 * @return array
 */
function types( $types = array() ) {
	if ( ! isset( $types['page'] ) ) {
		$types['page'] = __( 'Pages', 'heynotify' );
	}
	return $types;
}

/**
 * Add Page actions
 *
 * @param array $fields
 * @return array
 */
function actions( $fields = array() ) {
	$fields[] = (
		Field::make( 'select', 'page', __( 'Action', 'heynotify' ) )
			->set_options(
				array(
					'page_published' => __( 'Page Published', 'heynotify' ),
					'page_scheduled' => __( 'Page Scheduled', 'heynotify' ),
					'page_pending'   => __( 'Page Pending', 'heynotify' ),
					'page_updated'   => __( 'Page Updated', 'heynotify' ),
					'page_trashed'   => __( 'Page Moved to Trash', 'heynotify' ),
				)
			)
			->set_conditional_logic(
				array(
					array(
						'field' => 'type',
						'value' => 'page',
					)
				)
			)
	);
	return $fields;
}