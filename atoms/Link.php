<?php
namespace CNP;

/**
 * Link.
 *
 * Returns a link.
 *
 * Children: FrontPageLink, PostLink, PostsPageLink
 * Classes that use this class: PostTitleLink
 *
 * @since 0.1.0
 *
 * @param string $href The link href attribute.
 */
class Link extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'link';
		}
		$this->tag = 'a';
		if ( isset( $data['href'] ) ) {
			$this->attributes['href'] = $data['href'];
		}
	}
}
