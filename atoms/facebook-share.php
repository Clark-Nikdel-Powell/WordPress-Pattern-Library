<?php
namespace CNP;

/**
 * FacebookShare.
 *
 * Returns a Facebook share link.
 *
 * @since 0.15.6
 *
 * @param string $share  The url to share.
 * @param string $target The link target window. Defaults to "_blank".
 */
class FacebookShare extends AtomTemplate {

	private $href = 'https://www.facebook.com/sharer/sharer.php?u=';

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' === $this->name ) {
			$this->name = 'facebook-share';
		}

		$this->tag                  = 'a';
		$this->attributes['href']   = $this->share_href( $data );
		$this->attributes['target'] = $this->link_target( $data );
	}

	/**
	 * share_href
	 *
	 * Get the href attribute value for the link. If not defined, check the WordPress post object.
	 *
	 * @since 0.15.6
	 *
	 * @param $data
	 *
	 * @return string
	 */
	private function share_href( $data ) {

		if ( isset( $data['share'] ) ) {
			return $this->href . $data['share'];
		}

		global $post;
		if ( ! $post ) {
			return $this->href . get_site_url();
		}
		if ( $post ) {
			return $this->href . get_the_permalink();
		}

		return $this->href;
	}

	/**
	 * link_target
	 *
	 * Get the target attribute value for the link. Defaults to "_blank".
	 *
	 * @since 0.15.6
	 *
	 * @param $data
	 *
	 * @return string
	 */
	private function link_target( $data ) {

		if ( ! isset( $data['target'] ) ) {
			return '_blank';
		}

		return $data['target'];
	}
}
