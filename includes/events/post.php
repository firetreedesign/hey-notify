<?php
/**
 * Post events
 * 
 * @package FireTreeNotify
 */

namespace FireTreeNotify\Events\Post;

use Carbon_Fields\Field;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Filters
add_filter( 'firetree_notify_event_types ', __NAMESPACE__ . '\\types' );
add_filter( 'firetree_notify_event_actions', __NAMESPACE__ . '\\post' );

// Actions
add_action( 'firetree_notify_add_action_post', __NAMESPACE__ . '\\add', 10, 2 );

function types( $types = array() ) {
	if ( ! isset( $types['post'] ) ) {
		$types['post'] = __( 'Posts', 'firetree-notify' );
	}
	return $types;
}

/**
 * Post events
 *
 * @param array $fields
 * @return array
 */
function post( $fields = array() ) {
	$fields[] = (
		Field::make( 'select', 'post', __( 'Action', 'firetree-notify' ) )
			->set_options(
				array(
					'post_published' => __( 'Post Published', 'firetree-notify' ),
					'post_scheduled' => __( 'Post Scheduled', 'firetree-notify' ),
					'post_pending'   => __( 'Post Pending', 'firetree-notify' ),
					'post_updated'   => __( 'Post Updated', 'firetree-notify' ),
					'post_trashed'   => __( 'Post Moved to Trash', 'firetree-notify' ),
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
	);
	return $fields;
}

function add( $notification, $event ) {
	$hook = new hook( $notification, $event );

	switch( $event[ $event['type'] ] ) {
		case 'post_published':
			add_action( 'transition_post_status', array( $hook, 'post_published' ), 10, 3 );
			break;
		case 'post_scheduled':
			// Do something.
			break;
		case 'post_pending':
			// Do something.
			break;
		case 'post_updated':
			// Do something.
			break;
		case 'post_trashed':
			// Do something.
			break;
	}
}

class hook {
	private $notification;
	private $event;

	function __construct( $notification, $event ) {
		$this->notification = $notification;
		$this->event = $event;
	}

	public function post_published( $new_status, $old_status, $post ) {
		if ( 'new' !== $old_status && 'draft' !== $old_status && 'auto-draft' !== $old_status ) {
			return;
		}
		if ( 'publish' !== $new_status ) {
			return;
		}

		$title = \__( 'A new post was published!', 'firetree-notify' );
		$this->send_notification( $title, $post );
	}

	private function send_notification( $title, $post ) {
		
		$attachments = array(
			array(
				'name'   => \esc_html__( 'Author', 'firetree-notify' ),
				'value'  => \get_the_author_meta( 'display_name', $post->post_author ),
				'inline' => true,
			),
			array(
				'name'   => \esc_html__( 'Date', 'firetree-notify' ),
				'value'  => \get_the_date( null, $post->ID ),
				'inline' => true,
			)
		);

		$categories = \get_the_term_list( $post->ID, 'category', '', ', ', '' );
		if ( '' !== $categories && ! is_wp_error( $categories ) ) {
			$attachments[] = array(
				'name' => \esc_html__( 'Categories', 'firetree-notify' ),
				'value' => \strip_tags( $categories ),
				'inline' => false,
			);
		}

		$tags = \get_the_tag_list( '', ', ', '', $post->ID );
		if ( '' !== $tags && ! is_wp_error( $tags ) ) {
			$attachments[] = array(
				'name' => \esc_html__( 'Tags', 'firetree-notify' ),
				'value' => \strip_tags( $tags ),
				'inline' => false,
			);
		}

		$url = get_permalink( $post->ID );

		do_action(
			'firetree_notify_send_message',
			array(
				'notification' => $this->notification,
				'content'      => $title,
				'url_title'    => $post->post_title,
				'url'          => $url,
				'attachments'  => $attachments
			)
		);
	}
}