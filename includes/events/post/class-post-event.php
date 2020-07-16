<?php
/**
 * Post events
 * 
 * @package Hey_Notify
 */

namespace Hey_Notify;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post_Event extends Event {

	public function types( $types = array() ) {
		if ( ! isset( $types['post'] ) ) {
			$types['post'] = __( 'Posts', 'hey-notify' );
		}
		return $types;
	}

	/**
	 * Post events
	 *
	 * @param array $fields
	 * @return array
	 */
	public function actions( $fields = array() ) {
		$fields[] = (
			Field::make( 'select', 'post', __( 'Action', 'hey-notify' ) )
				->set_options(
					array(
						'post_draft'     => __( 'Post Draft', 'hey-notify' ),
						'post_pending'   => __( 'Post Pending', 'hey-notify' ),
						'post_published' => __( 'Post Published', 'hey-notify' ),
						'post_scheduled' => __( 'Post Scheduled', 'hey-notify' ),
						'post_updated'   => __( 'Post Updated', 'hey-notify' ),
						'post_trashed'   => __( 'Post Moved to Trash', 'hey-notify' ),
					)
				)
				->set_conditional_logic(
					array(
						array(
							'field' => 'type',
							'value' => 'post',
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
			case 'post_draft':
				add_action( 'transition_post_status', array( $hook, 'post_draft' ), 10, 3 );
				break;
			case 'post_published':
				add_action( 'transition_post_status', array( $hook, 'post_published' ), 10, 3 );
				break;
			case 'post_scheduled':
				add_action( 'transition_post_status', array( $hook, 'post_scheduled' ), 10, 3 );
				break;
			case 'post_pending':
				add_action( 'transition_post_status', array( $hook, 'post_pending' ), 10, 3 );
				break;
			case 'post_updated':
				add_action( 'transition_post_status', array( $hook, 'post_updated' ), 10, 3 );
				break;
			case 'post_trashed':
				add_action( 'transition_post_status', array( $hook, 'post_trashed' ), 10, 3 );
				break;
		}
	}

}

new Post_Event( 'post', '\Hey_Notify\Post_Hook' );