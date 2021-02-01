<?php
/**
 * Page hook
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This handles all of the Page actions.
 */
class Page_Hook extends Hook {

	/**
	 * When a page enters a DRAFT state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_draft( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'page' !== $post->post_type ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a page was drafted on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a page was drafted by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_page_draft_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a page enters the PUBLISH state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_published( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'page' !== $post->post_type ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a page was published on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a page was published by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_page_published_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a page enters the FUTURE state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_scheduled( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'page' !== $post->post_type ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a page was scheduled on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a page was scheduled by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_page_scheduled_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a page enters the PENDING state.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_pending( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'page' !== $post->post_type ) {
			return;
		}

		$subject = \wp_sprintf(
			/* translators: %s: Name of the site */
			\__( 'Hey, a page is pending on %s!', 'hey-notify' ),
			\get_bloginfo( 'name' )
		);

		$subject = apply_filters( 'hey_notify_page_pending_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a page is updated.
	 *
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_updated( $post ) {

		if ( empty( $post ) || ! is_object( $post ) ) {
			return;
		}

		if ( 'page' !== $post->post_type ) {
			return;
		}

		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a page was updated on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a page was updated by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_page_updated_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * When a page is trashed.
	 *
	 * @param int $id Post ID.
	 * @return void
	 */
	public function page_trashed( $id ) {

		$post = get_post( $id );

		if ( is_wp_error( $post ) ) {
			return;
		}

		if ( 'page' !== $post->post_type ) {
			return;
		}

		$current_user = \wp_get_current_user();

		if ( 0 === $current_user ) {
			$subject = \wp_sprintf(
				/* translators: %s: Name of the site */
				\__( 'Hey, a page was deleted on %s!', 'hey-notify' ),
				\get_bloginfo( 'name' )
			);
		} else {
			$subject = \wp_sprintf(
				/* translators: 1: Name of the user 2: Name of the site */
				\__( 'Hey, a page was deleted by %1$s on %2$s!', 'hey-notify' ),
				\esc_html( $current_user->display_name ),
				\get_bloginfo( 'name' )
			);
		}

		$subject = apply_filters( 'hey_notify_page_trashed_subject', $subject, $post );

		$this->prepare_data( $subject, $post );
	}

	/**
	 * Prepare the data
	 *
	 * @param string $subject The subject of the message.
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
