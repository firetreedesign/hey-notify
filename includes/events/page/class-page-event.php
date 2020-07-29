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

/**
 * Page Event class
 */
class Page_Event extends Event {

	/**
	 * Add 'Pages' to the $types array
	 *
	 * @param array $types Event types.
	 * @return array
	 */
	public function types( $types = array() ) {
		if ( ! isset( $types['page'] ) ) {
			$types['page'] = __( 'Pages', 'hey-notify' );
		}
		return $types;
	}

	/**
	 * Page events
	 *
	 * @param array $fields Action fields.
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
						),
					)
				)
				->set_width( 50 )
		);
		return $fields;
	}

	/**
	 * Add the event actions
	 *
	 * @param object $notification Notification post object.
	 * @param object $event Event object.
	 * @return void
	 */
	public function watch( $notification, $event ) {
		$hook = new $this->hook( $notification, $event );

		switch ( $event->{$event->type} ) {
			case 'page_draft':
				add_action( 'auto-draft_to_draft', array( $hook, 'page_draft' ), 10, 1 );
				break;
			case 'page_published':
				add_action( 'auto-draft_to_publish', array( $hook, 'page_published' ), 10, 1 );
				add_action( 'draft_to_publish', array( $hook, 'page_published' ), 10, 1 );
				add_action( 'future_to_publish', array( $hook, 'page_published' ), 10, 1 );
				add_action( 'pending_to_publish', array( $hook, 'page_published' ), 10, 1 );
				break;
			case 'page_scheduled':
				add_action( 'auto-draft_to_future', array( $hook, 'page_scheduled' ), 10, 1 );
				add_action( 'draft_to_future', array( $hook, 'page_scheduled' ), 10, 1 );
				break;
			case 'page_pending':
				add_action( 'auto-draft_to_pending', array( $hook, 'page_pending' ), 10, 1 );
				add_action( 'draft_to_pending', array( $hook, 'page_pending' ), 10, 1 );
				break;
			case 'page_updated':
				add_action( 'publish_to_publish', array( $hook, 'page_updated' ), 10, 1 );
				break;
			case 'page_trashed':
				add_action( 'trashed_post', array( $hook, 'page_trashed' ), 10, 1 );
				break;
		}
	}

}

new Page_Event( 'page', '\Hey_Notify\Page_Hook' );
