<?php
/**
 * Page events
 * 
 * @package Hey_Notify
 */

namespace Hey_Notify;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Page_Event extends Event {

	public function types( $types = array() ) {
		if ( ! isset( $types['page'] ) ) {
			$types['page'] = __( 'Pages', 'hey-notify' );
		}
		return $types;
	}

	/**
	 * Page events
	 *
	 * @param array $fields
	 * @return array
	 */
	public function actions( $fields = array() ) {
		$fields[] = (
			Field::make( 'select', 'page', __( 'Action', 'hey-notify' ) )
				->set_options(
					array(
						'page_draft'     => __( 'Page Draft', 'hey-notify' ),
						'page_pending'   => __( 'Page Pending', 'hey-notify' ),
						'page_published' => __( 'Page Published', 'hey-notify' ),
						'page_scheduled' => __( 'Page Scheduled', 'hey-notify' ),
						'page_updated'   => __( 'Page Updated', 'hey-notify' ),
						'page_trashed'   => __( 'Page Moved to Trash', 'hey-notify' ),
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
				->set_width( 50 )
		);
		return $fields;
	}

	public function watch( $notification, $event ) {
		$hook = new $this->hook( $notification, $event );
	
		switch( $event[ $event['type'] ] ) {
			case 'page_draft':
				add_action( 'transition_post_status', array( $hook, 'page_draft' ), 10, 3 );
				break;
			case 'page_published':
				add_action( 'transition_post_status', array( $hook, 'page_published' ), 10, 3 );
				break;
			case 'page_scheduled':
				add_action( 'transition_post_status', array( $hook, 'page_scheduled' ), 10, 3 );
				break;
			case 'page_pending':
				add_action( 'transition_post_status', array( $hook, 'page_pending' ), 10, 3 );
				break;
			case 'page_updated':
				add_action( 'transition_post_status', array( $hook, 'page_updated' ), 10, 3 );
				break;
			case 'page_trashed':
				add_action( 'transition_post_status', array( $hook, 'page_trashed' ), 10, 3 );
				break;
		}
	}

}

new Page_Event( 'page', '\Hey_Notify\Page_Hook' );