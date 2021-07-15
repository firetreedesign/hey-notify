<?php
/**
 * Post hook
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This handles all of the Post actions.
 */
class Post_Hook extends Hook {

	/**
	 * When a post enters a DRAFT state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function post_draft( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'post' !== $post->post_type ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a post was drafted on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a post was drafted by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_post_draft_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post enters the PUBLISH state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function post_published( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'post' !== $post->post_type ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a post was published on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a post was published by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_post_published_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post enters the FUTURE state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function post_scheduled( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'post' !== $post->post_type ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a post was scheduled on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a post was scheduled by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_post_scheduled_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post enters the PENDING state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function post_pending( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'post' !== $post->post_type ) {
			return;
		}

		$subject = \sprintf(
			/* translators: %s: Name of the site */
			\__( 'Hey, a post is pending on %s!', 'hey-notify' ),
			\get_bloginfo( 'name' )
		);

		$subject = apply_filters( 'hey_notify_post_pending_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post is updated.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function post_updated( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'post' !== $post->post_type ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a post was updated on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a post was updated by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_post_updated_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post is trashed.
	 *
	 * @param int $id Post ID.
	 * @return void
	 */
	public function post_trashed( $id ) {

		$post = get_post( $id );

		if ( is_wp_error( $post ) ) {
			return;
		}

		if ( 'post' !== $post->post_type ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a post was deleted on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a post was deleted by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_post_trashed_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * Prepare the data
	 *
	 * @param string $subject Message subject.
	 * @param object $post Post object.
	 * @return void
	 */
	private function prepare_data( $subject, $post ) {

		$fields = array(
			array(
				'name'   => \esc_html__( 'Author', 'hey-notify' ),
				'value'  => \get_the_author_meta( 'display_name', $post->post_author ),
				'inline' => true,
			),
			array(
				'name'   => \esc_html__( 'Date', 'hey-notify' ),
				'value'  => \get_the_date( null, $post->ID ),
				'inline' => true,
			),
		);

		$categories = \wp_strip_all_tags( \get_the_term_list( $post->ID, 'category', '', ', ', '' ) );
		if ( '' !== $categories && ! \is_wp_error( $categories ) ) {
			$fields[] = array(
				'name'   => \esc_html__( 'Categories', 'hey-notify' ),
				'value'  => $categories,
				'inline' => false,
			);
		}

		$tags = \wp_strip_all_tags( \get_the_tag_list( '', ', ', '', $post->ID ) );
		if ( '' !== $tags && ! \is_wp_error( $tags ) ) {
			$fields[] = array(
				'name'   => \esc_html__( 'Tags', 'hey-notify' ),
				'value'  => $tags,
				'inline' => false,
			);
		}

		$image = '';
		if ( \has_post_thumbnail( $post ) ) {
			$image_id = \get_post_thumbnail_id( $post );
			$image    = \wp_get_attachment_image_url( $image_id, 'thumbnail' );
		}

		$data = array(
			'subject' => $subject,
			'title'   => $post->post_title,
			'url'     => \get_permalink( $post->ID ),
			'image'   => $image,
			'fields'  => $fields,
		);

		$this->send( $data );
	}
}
