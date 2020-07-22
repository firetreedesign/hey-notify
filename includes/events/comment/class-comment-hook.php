<?php
/**
 * Comment hook
 *
 * @package Hey_Notify
 */

namespace Hey_Notify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This handles all of the Comment actions.
 */
class Comment_Hook extends Hook {

	/**
	 * When a new comment is made.
	 *
	 * @param int    $comment_id Comment ID.
	 * @param string $approved Comment status.
	 * @return void
	 */
	public function comment_new( $comment_id, $approved ) {

		if ( 'spam' === $approved ) {
			return;
		}

		$comment = get_comment( $comment_id );

		if ( is_wp_error( $comment ) ) {
			return;
		}

		$this->prepare_data( \__( 'Hey, a new comment has been posted!', 'hey-notify' ), $comment );
	}

	/**
	 * Prepare the data
	 *
	 * @param string $subject The subject of the message.
	 * @param object $comment Comment object.
	 * @return void
	 */
	private function prepare_data( $subject, $comment ) {

		$fields = array(
			array(
				'name'   => \esc_html__( 'Author', 'hey-notify' ),
				'value'  => $comment->comment_author,
				'inline' => true,
			),
			array(
				'name'   => \esc_html__( 'Email', 'hey-notify' ),
				'value'  => $comment->comment_author_email,
				'inline' => true,
			),
		);

		$data = array(
			'subject' => $subject,
			'title'   => \get_the_title( $comment->comment_post_ID ),
			'url'     => \get_permalink( $comment->comment_post_ID ),
			'fields'  => $fields,
			'footer'  => $comment->comment_content,
		);

		$this->send( $data );
	}
}
