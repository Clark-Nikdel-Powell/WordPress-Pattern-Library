<?php
namespace CNP;

/**
 * PostLink.
 *
 * Uses get_permalink() to return a link to a specific post.
 *
 * Children: PostTitleLink
 *
 * @since 0.3.0
 */
class PostLink extends Link {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'postlink';
		}
		$this->attributes['href'] = get_permalink( $this->post_object );

	}
}
