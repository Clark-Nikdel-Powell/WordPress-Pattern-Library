<?php
namespace CNP;

/**
 * EmailShare.
 *
 * Returns an Email share link.
 *
 * @since 0.15.6
 *
 * @param string $share  The url to share.
 * @param string $body   The body of the email message.
 * @param string $target The link target window. Defaults to "_blank".
 */
class EmailShare extends AtomTemplate {

	private $href = 'mailto:?';

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' === $this->name ) {
			$this->name = 'email-share';
		}

		$this->tag                  = 'a';
		$this->attributes['href']   = $this->share_href( $data );
		$this->attributes['target'] = $this->link_target( $data );
	}

	private function share_href( $data ) {

		global $post;
		$body_arr = array();

		if ( isset( $data['body'] ) ) {
			$body_arr[] = trim( $data['body'] );
		}

		if ( isset( $data['share'] ) ) {
			$body_arr[] = $this->href . $data['share'];
		} elseif ( ! $post ) {
			$body_arr[] = $this->href . get_site_url();
		} elseif ( $post ) {
			$body_arr[] = $this->href . get_the_permalink();
		}

		return $this->encode_body( $body_arr );
	}

	private function link_target( $data ) {

		if ( ! isset( $data['target'] ) ) {
			return '_blank';
		}

		return $data['target'];
	}

	private function encode_body( $status_arr ) {

		$status_share = implode( ' ', $status_arr );

		return preg_replace( '/ /', '+', $status_share );
	}
}
