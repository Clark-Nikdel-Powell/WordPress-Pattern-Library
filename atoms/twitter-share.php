<?php
namespace CNP;

/**
 * TwitterShare.
 *
 * Returns a Twitter share link.
 *
 * @since 0.15.6
 *
 * @param string $share  The url to share.
 * @param string $status Status message.
 * @param string $target The link target window. Defaults to "_blank".
 */
class TwitterShare extends AtomTemplate {

	private $href = 'https://twitter.com/home?status=';

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' === $this->name ) {
			$this->name = 'twitter-share';
		}

		$this->tag                  = 'a';
		$this->attributes['href']   = $this->share_href( $data );
		$this->attributes['target'] = $this->link_target( $data );
	}

	private function share_href( $data ) {

		global $post;
		$status_arr = array();

		if ( isset( $data['status'] ) ) {
			$status_arr[] = trim( $data['status'] );
		}

		if ( isset( $data['share'] ) ) {
			$status_arr[] = $this->href . $data['share'];
		} elseif ( ! $post ) {
			$status_arr[] = $this->href . get_site_url();
		} elseif ( $post ) {
			$status_arr[] = $this->href . get_the_permalink();
		}

		return $this->encode_status( $status_arr );
	}

	private function link_target( $data ) {

		if ( ! isset( $data['target'] ) ) {
			return '_blank';
		}

		return $data['target'];
	}

	private function encode_status( $status_arr ) {

		$status_share = implode( ' ', $status_arr );

		return preg_replace( '/ /', '+', $status_share );
	}
}
