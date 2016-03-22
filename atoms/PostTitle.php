<?php
namespace CNP;

/**
 * PostTitle.
 *
 * Uses get_the_title() to return a post title in an H2 tag.
 *
 * Children: PostTitleLink.
 *
 * @since 0.1.0
 */
class PostTitle extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'posttitle';
		}
		$this->tag     = isset( $data['tag'] ) ? $data['tag'] : 'h2';
		$this->content = get_the_title( $this->post_object );

	}
}
