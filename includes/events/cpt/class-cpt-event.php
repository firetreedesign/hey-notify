<?php
/**
 * Custom Post Type events
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom Post Type class
 */
class CPT_Event extends Event {

	/**
	 * Sets the event names for different system events.
	 *
	 * @param string $type Event type.
	 */
	public function set_event_names( $type ) {
		$post_type = get_post_types_object( $type );

		$this->event_name_array = array(
			"{$type}_draft"     => \wp_sprintf(
				/* translators: %s: Singular name of the custom post type */
				\__( '%s Draft', 'hey-notify' ),
				$post_type->labels->singular_name
			),
			"{$type}_pending"   => \wp_sprintf(
				/* translators: %s: Singular name of the custom post type */
				\__( '%s Pending', 'hey-notify' ),
				$post_type->labels->singular_name
			),
			"{$type}_published" => \wp_sprintf(
				/* translators: %s: Singular name of the custom post type */
				\__( '%s Published', 'hey-notify' ),
				$post_type->labels->singular_name
			),
			"{$type}_scheduled" => \wp_sprintf(
				/* translators: %s: Singular name of the custom post type */
				\__( '%s Scheduled', 'hey-notify' ),
				$post_type->labels->singular_name
			),
			"{$type}_updated"   => \wp_sprintf(
				/* translators: %s: Singular name of the custom post type */
				\__( '%s Updated', 'hey-notify' ),
				$post_type->labels->singular_name
			),
			"{$type}_trashed"   => \wp_sprintf(
				/* translators: %s: Singular name of the custom post type */
				\__( '%s Moved to Trash', 'hey-notify' ),
				$post_type->labels->singular_name
			),
		);
	}

	/**
	 * Add Custom Post Types to the $types array
	 *
	 * @param array $types Event types.
	 * @return array
	 */
	public function types( $types = array() ) {
		if ( ! isset( $types[ $this->type ] ) ) {
			$post_type            = get_post_types_object( $this->type );
			$types[ $this->type ] = sprintf( '%s (Post Type)', $post_type->labels->name );
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
				'field_name'        => $this->type,
				'field_label'       => __( 'Action', 'hey-notify' ),
				'choices'           => $this->event_name_array,
				'width'             => '50%',
				'conditional_logic' => array(
					array(
						array(
							'field' => 'type',
							'value' => $this->type,
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
			case "{$this->type}_draft":
				add_action( 'auto-draft_to_draft', array( $hook, 'draft' ), 10, 1 );
				add_action( 'future_to_draft', array( $hook, 'draft' ), 10, 1 );
				add_action( 'new_to_draft', array( $hook, 'draft' ), 10, 1 );
				add_action( 'pending_to_draft', array( $hook, 'draft' ), 10, 1 );
				add_action( 'private_to_draft', array( $hook, 'draft' ), 10, 1 );
				add_action( 'publish_to_draft', array( $hook, 'draft' ), 10, 1 );
				add_action( 'trash_to_draft', array( $hook, 'draft' ), 10, 1 );
				break;
			case "{$this->type}_published":
				add_action( 'auto-draft_to_publish', array( $hook, 'published' ), 10, 1 );
				add_action( 'draft_to_publish', array( $hook, 'published' ), 10, 1 );
				add_action( 'future_to_publish', array( $hook, 'published' ), 10, 1 );
				add_action( 'new_to_publish', array( $hook, 'published' ), 10, 1 );
				add_action( 'pending_to_publish', array( $hook, 'published' ), 10, 1 );
				add_action( 'private_to_publish', array( $hook, 'published' ), 10, 1 );
				add_action( 'trash_to_publish', array( $hook, 'published' ), 10, 1 );
				break;
			case "{$this->type}_scheduled":
				add_action( 'auto-draft_to_future', array( $hook, 'scheduled' ), 10, 1 );
				add_action( 'draft_to_future', array( $hook, 'scheduled' ), 10, 1 );
				add_action( 'new_to_future', array( $hook, 'scheduled' ), 10, 1 );
				add_action( 'pending_to_future', array( $hook, 'scheduled' ), 10, 1 );
				add_action( 'private_to_future', array( $hook, 'scheduled' ), 10, 1 );
				add_action( 'publish_to_future', array( $hook, 'scheduled' ), 10, 1 );
				add_action( 'trash_to_future', array( $hook, 'scheduled' ), 10, 1 );
				break;
			case "{$this->type}_pending":
				add_action( 'auto-draft_to_pending', array( $hook, 'pending' ), 10, 1 );
				add_action( 'draft_to_pending', array( $hook, 'pending' ), 10, 1 );
				add_action( 'future_to_pending', array( $hook, 'pending' ), 10, 1 );
				add_action( 'new_to_pending', array( $hook, 'pending' ), 10, 1 );
				add_action( 'private_to_pending', array( $hook, 'pending' ), 10, 1 );
				add_action( 'publish_to_pending', array( $hook, 'pending' ), 10, 1 );
				add_action( 'trash_to_pending', array( $hook, 'pending' ), 10, 1 );
				break;
			case "{$this->type}_updated":
				add_action( 'publish_to_publish', array( $hook, 'updated' ), 10, 1 );
				break;
			case "{$this->type}_trashed":
				add_action( 'trashed_post', array( $hook, 'trashed' ), 10, 1 );
				break;
		}
	}
}
