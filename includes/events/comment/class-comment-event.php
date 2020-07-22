<?php
/**
 * Comment events
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
 * Comment Event class
 */
class Comment_Event extends Event {

	/**
	 * Add 'Comments' to the $types array
	 *
	 * @param array $types Event types.
	 * @return array
	 */
	public function types( $types = array() ) {
		if ( ! isset( $types['comment'] ) ) {
			$types['comment'] = __( 'Comments', 'hey-notify' );
		}
		return $types;
	}

	/**
	 * Comment events
	 *
	 * @param array $fields Action fields.
	 * @return array
	 */
	public function actions( $fields = array() ) {
		$fields[] = (
			Field::make( 'select', 'comment', __( 'Action', 'hey-notify' ) )
				->set_options(
					array(
						'comment_post' => __( 'New Comment', 'hey-notify' ),
					)
				)
				->set_conditional_logic(
					array(
						array(
							'field' => 'type',
							'value' => 'comment',
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
			case 'comment_post':
				add_action( 'comment_post', array( $hook, 'comment_new' ), 10, 2 );
				break;
		}
	}

}

new Comment_Event( 'comment', '\Hey_Notify\Comment_Hook' );
