<?php
/**
 * Post events
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post Event class
 */
class Post_Event extends Event {

	/**
	 * Sets the event names for different system events.
	 *
	 * @param string $type Event type.
	 */
	public function set_event_names( $type ) {
		$this->event_name_array = array(
			'post_draft'     => __( 'Post Draft', 'hey-notify' ),
			'post_pending'   => __( 'Post Pending', 'hey-notify' ),
			'post_published' => __( 'Post Published', 'hey-notify' ),
			'post_scheduled' => __( 'Post Scheduled', 'hey-notify' ),
			'post_updated'   => __( 'Post Updated', 'hey-notify' ),
			'post_trashed'   => __( 'Post Moved to Trash', 'hey-notify' ),
		);
	}

	/**
	 * Add 'Posts' to the $types array
	 *
	 * @param array $types Event types.
	 * @return array
	 */
	public function types( $types = array() ) {
		if ( ! isset( $types['post'] ) ) {
			$types['post'] = __( 'Posts', 'hey-notify' );
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
		array_push(
			$fields,
			array(
				'field_type'        => 'select',
				'field_name'        => 'post',
				'field_label'       => __( 'Action', 'hey-notify' ),
				'choices'           => $this->event_name_array,
				'width'             => '50%',
				'conditional_logic' => array(
					array(
						array(
							'field' => 'type',
							'value' => 'post',
						),
					),
				),
			)
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
			case 'post_draft':
				add_action( 'auto-draft_to_draft', array( $hook, 'post_draft' ), 10, 1 );
				add_action( 'future_to_draft', array( $hook, 'post_draft' ), 10, 1 );
				add_action( 'new_to_draft', array( $hook, 'post_draft' ), 10, 1 );
				add_action( 'pending_to_draft', array( $hook, 'post_draft' ), 10, 1 );
				add_action( 'private_to_draft', array( $hook, 'post_draft' ), 10, 1 );
				add_action( 'publish_to_draft', array( $hook, 'post_draft' ), 10, 1 );
				add_action( 'trash_to_draft', array( $hook, 'post_draft' ), 10, 1 );
				break;
			case 'post_published':
				add_action( 'auto-draft_to_publish', array( $hook, 'post_published' ), 10, 1 );
				add_action( 'draft_to_publish', array( $hook, 'post_published' ), 10, 1 );
				add_action( 'future_to_publish', array( $hook, 'post_published' ), 10, 1 );
				add_action( 'new_to_publish', array( $hook, 'post_published' ), 10, 1 );
				add_action( 'pending_to_publish', array( $hook, 'post_published' ), 10, 1 );
				add_action( 'private_to_publish', array( $hook, 'post_published' ), 10, 1 );
				add_action( 'trash_to_publish', array( $hook, 'post_published' ), 10, 1 );
				break;
			case 'post_scheduled':
				add_action( 'auto-draft_to_future', array( $hook, 'post_scheduled' ), 10, 1 );
				add_action( 'draft_to_future', array( $hook, 'post_scheduled' ), 10, 1 );
				add_action( 'new_to_future', array( $hook, 'post_scheduled' ), 10, 1 );
				add_action( 'pending_to_future', array( $hook, 'post_scheduled' ), 10, 1 );
				add_action( 'private_to_future', array( $hook, 'post_scheduled' ), 10, 1 );
				add_action( 'publish_to_future', array( $hook, 'post_scheduled' ), 10, 1 );
				add_action( 'trash_to_future', array( $hook, 'post_scheduled' ), 10, 1 );
				break;
			case 'post_pending':
				add_action( 'auto-draft_to_pending', array( $hook, 'post_pending' ), 10, 1 );
				add_action( 'draft_to_pending', array( $hook, 'post_pending' ), 10, 1 );
				add_action( 'future_to_pending', array( $hook, 'post_pending' ), 10, 1 );
				add_action( 'new_to_pending', array( $hook, 'post_pending' ), 10, 1 );
				add_action( 'private_to_pending', array( $hook, 'post_pending' ), 10, 1 );
				add_action( 'publish_to_pending', array( $hook, 'post_pending' ), 10, 1 );
				add_action( 'trash_to_pending', array( $hook, 'post_pending' ), 10, 1 );
				break;
			case 'post_updated':
				add_action( 'publish_to_publish', array( $hook, 'post_updated' ), 10, 1 );
				break;
			case 'post_trashed':
				add_action( 'trashed_post', array( $hook, 'post_trashed' ), 10, 1 );
				break;
		}
	}
}

new Post_Event( 'post', '\Hey_Notify\Post_Hook' );
