<?php
/**
 * CPT hook
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This handles all of the CPT actions.
 */
class CPT_Hook extends Hook {

	/**
	 * When a post enters a DRAFT state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function draft( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( substr( $this->event->type, 4 ) !== $post->post_type ) {
			return;
		}

		$cpt          = get_post_types_object( $this->event->type );
		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2. Name of the site */
				\__( 'Hey, a %1$s was drafted on %2$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2: Name of the user 3: Name of the site */
				\__( 'Hey, a %1$s was drafted by %2$s on %3$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_cpt_draft_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post enters the PUBLISH state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function published( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( substr( $this->event->type, 4 ) !== $post->post_type ) {
			return;
		}

		$cpt          = get_post_types_object( $this->event->type );
		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2. Name of the site */
				\__( 'Hey, a %1$s was published on %2$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2: Name of the user 3: Name of the site */
				\__( 'Hey, a %1$s was published by %2$s on %3$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_cpt_published_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post enters the FUTURE state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function scheduled( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( substr( $this->event->type, 4 ) !== $post->post_type ) {
			return;
		}

		$cpt          = get_post_types_object( $this->event->type );
		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2. Name of the site */
				\__( 'Hey, a %1$s was scheduled on %2$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2: Name of the user 3: Name of the site */
				\__( 'Hey, a %1$s was scheduled by %2$s on %3$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_cpt_scheduled_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post enters the PENDING state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function pending( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( substr( $this->event->type, 4 ) !== $post->post_type ) {
			return;
		}

		$cpt = get_post_types_object( $this->event->type );

		$subject = \wp_sprintf(
			/* translators: 1: Name of the custom post type 2. Name of the site */
			\__( 'Hey, a %1$s is pending on %2$s!', 'hey-notify' ),
			\strtolower( $cpt->labels->singular_name ),
			\get_bloginfo( 'name' )
		);

		$subject = apply_filters( 'hey_notify_cpt_pending_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post is updated.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function updated( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( substr( $this->event->type, 4 ) !== $post->post_type ) {
			return;
		}

		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			return;
		}

		$cpt          = get_post_types_object( $this->event->type );
		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2. Name of the site */
				\__( 'Hey, a %1$s was updated on %2$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2: Name of the user 3: Name of the site */
				\__( 'Hey, a %1$s was updated by %2$s on %3$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_cpt_updated_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a post is trashed.
	 *
	 * @param int $id Post ID.
	 * @return void
	 */
	public function trashed( $id ) {

		$post = get_post( $id );

		if ( is_wp_error( $post ) ) {
			return;
		}

		if ( substr( $this->event->type, 4 ) !== $post->post_type ) {
			return;
		}

		$cpt          = get_post_types_object( $this->event->type );
		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2. Name of the site */
				\__( 'Hey, a %1$s was deleted on %2$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the custom post type 2: Name of the user 3: Name of the site */
				\__( 'Hey, a %1$s was deleted by %2$s on %3$s!', 'hey-notify' ),
				\strtolower( $cpt->labels->singular_name ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_cpt_trashed_subject', $subject, $post );

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
