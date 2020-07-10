<?php
/**
 * Page events
 * 
 * @package FireTreeNotify
 */

namespace FireTreeNotify\Events\Page;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Filters
add_filter( 'firetree_notify_event_types ', __NAMESPACE__ . '\\types' );
add_filter( 'firetree_notify_event_actions', __NAMESPACE__ . '\\page' );

function types( $types = array() ) {
	if ( ! isset( $types['page'] ) ) {
		$types['page'] = __( 'Pages', 'firetree-notify' );
	}
	return $types;
}

/**
 * Page events
 *
 * @param array $fields
 * @return array
 */
function page( $fields = array() ) {
	$fields[] = (
		Field::make( 'select', 'page', __( 'Action', 'firetree-notify' ) )
			->set_options(
				array(
					'page_published' => __( 'Page Published', 'firetree-notify' ),
					'page_scheduled' => __( 'Page Scheduled', 'firetree-notify' ),
					'page_pending'   => __( 'Page Pending', 'firetree-notify' ),
					'page_updated'   => __( 'Page Updated', 'firetree-notify' ),
					'page_trashed'   => __( 'Page Moved to Trash', 'firetree-notify' ),
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