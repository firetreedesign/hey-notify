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
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_draft( $new_status, $old_status, $post ) {

		if ( 'page' !== $post->post_type ) {
			return;
		}

		if ( 'draft' === $old_status ) {
			return;
		}

		if ( 'draft' !== $new_status ) {
			return;
		}

		$this->prepare_data( \__( 'Hey, a new page was drafted!', 'hey-notify' ), $post );
	}

	/**
	 * When a page enters the PUBLISH state.
	 *
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_published( $new_status, $old_status, $post ) {

		if ( 'page' !== $post->post_type ) {
			return;
		}

		$valid = false;
		switch ( $old_status ) {
			case 'new':
			case 'draft':
			case 'auto-draft':
			case 'pending':
				$valid = true;
				break;
		}

		if ( false === $valid || 'publish' !== $new_status ) {
			return;
		}

		$this->prepare_data( \__( 'Hey, a new page was published!', 'hey-notify' ), $post );
	}

	/**
	 * When a page enters the FUTURE state.
	 *
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_scheduled( $new_status, $old_status, $post ) {

		if ( 'page' !== $post->post_type ) {
			return;
		}

		$valid = false;
		switch ( $old_status ) {
			case 'new':
			case 'draft':
			case 'auto-draft':
				$valid = true;
				break;
		}

		if ( false === $valid || 'future' !== $new_status ) {
			return;
		}

		$this->prepare_data( \__( 'Hey, a new page was scheduled!', 'hey-notify' ), $post );
	}

	/**
	 * When a page enters the PENDING state
	 *
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_pending( $new_status, $old_status, $post ) {

		if ( 'page' !== $post->post_type ) {
			return;
		}

		$valid = false;
		switch ( $old_status ) {
			case 'new':
			case 'draft':
			case 'auto-draft':
			case 'publish':
				$valid = true;
				break;
		}

		if ( false === $valid || 'pending' !== $new_status ) {
			return;
		}

		$this->prepare_data( \__( 'Hey, a new page is pending!', 'hey-notify' ), $post );
	}

	/**
	 * When a page is updated.
	 *
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_updated( $new_status, $old_status, $post ) {

		if ( 'page' !== $post->post_type ) {
			return;
		}

		if ( $old_status !== $new_status ) {
			return;
		}

		$this->prepare_data( \__( 'Hey, a page was updated!', 'hey-notify' ), $post );
	}

	/**
	 * When a page is trashed.
	 *
	 * @param string $new_status New status.
	 * @param string $old_status Old status.
	 * @param object $post Post object.
	 * @return void
	 */
	public function page_trashed( $new_status, $old_status, $post ) {

		if ( 'page' !== $post->post_type ) {
			return;
		}

		if ( 'trash' !== $new_status ) {
			return;
		}

		$this->prepare_data( \__( 'Hey, a page was deleted!', 'hey-notify' ), $post );
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
