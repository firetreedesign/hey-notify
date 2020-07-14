<?php
/**
 * Post hook
 * 
 * @package HeyNotify
 */

namespace HeyNotify;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This handles all of the Post actions.
 */
class PostHook extends Hook {
	
	/**
	 * When a post enters a DRAFT state.
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param object $post
	 * @return void
	 */
	public function post_draft( $new_status, $old_status, $post ) {
		
		if ( 'draft' === $old_status ) {
			return;
		}
		if ( 'draft' !== $new_status ) {
			return;
		}

		$this->send_notification( \__( 'A new post was drafted!', 'heynotify' ), $post );
	}

	/**
	 * When a post enters the PUBLISH state.
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param object $post
	 * @return void
	 */
	public function post_published( $new_status, $old_status, $post ) {
		
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

		$this->send_notification( \__( 'A new post was published!', 'heynotify' ), $post );
	}

	/**
	 * When a post enters the FUTURE state.
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param object $post
	 * @return void
	 */
	public function post_scheduled( $new_status, $old_status, $post ) {
		
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

		$this->send_notification( \__( 'A new post was scheduled!', 'heynotify' ), $post );
	}

	public function post_pending( $new_status, $old_status, $post ) {
		
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

		$this->send_notification( \__( 'A new post is pending!', 'heynotify' ), $post );
	}

	/**
	 * When a post is updated.
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param object $post
	 * @return void
	 */
	public function post_updated( $new_status, $old_status, $post ) {
		
		if ( $old_status !== $new_status ) {
			return;
		}

		$this->send_notification( \__( 'A post was updated!', 'heynotify' ), $post );
	}

	/**
	 * When a post is trashed.
	 *
	 * @param string $new_status
	 * @param string $old_status
	 * @param object $post
	 * @return void
	 */
	public function post_trashed( $new_status, $old_status, $post ) {
		
		if ( 'trash' !== $new_status ) {
			return;
		}

		$this->send_notification( \__( 'A post was deleted!', 'heynotify' ), $post );
	}

	private function send_notification( $title, $post ) {
		
		$attachments = array(
			array(
				'name'   => \esc_html__( 'Author', 'heynotify' ),
				'value'  => \get_the_author_meta( 'display_name', $post->post_author ),
				'inline' => true,
			),
			array(
				'name'   => \esc_html__( 'Date', 'heynotify' ),
				'value'  => \get_the_date( null, $post->ID ),
				'inline' => true,
			)
		);

		$categories = \strip_tags( \get_the_term_list( $post->ID, 'category', '', ', ', '' ) );
		if ( '' !== $categories && ! is_wp_error( $categories ) ) {
			$attachments[] = array(
				'name' => \esc_html__( 'Categories', 'heynotify' ),
				'value' => $categories,
				'inline' => false,
			);
		}

		$tags = \strip_tags( \get_the_tag_list( '', ', ', '', $post->ID ) );
		if ( '' !== $tags && ! is_wp_error( $tags ) ) {
			$attachments[] = array(
				'name' => \esc_html__( 'Tags', 'heynotify' ),
				'value' => $tags,
				'inline' => false,
			);
		}

		$url = get_permalink( $post->ID );

		do_action(
			'heynotify_send_message',
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