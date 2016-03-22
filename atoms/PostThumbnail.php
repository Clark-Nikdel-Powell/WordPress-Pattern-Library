<?php
namespace CNP;

/**
 * PostThumbnail.
 *
 * Uses get_the_post_thumbnail() to return a post thumbnail.
 *
 * @since 0.1.0
 *
 * @param array $thumbnail_args Thumbnail-specific arguments, like the size, responsive image settings, etc.
 */
class PostThumbnail extends AtomTemplate {

	private $thumbnail_args;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'post-thumbnail';
		}

		$this->thumbnail_args = isset( $data['thumbnail_args'] ) ? $data['thumbnail_args'] : array();
		$this->tag            = isset( $data['tag'] ) ? $data['tag'] : 'div';

		$this->content = call_user_func_array( 'get_the_post_thumbnail', $this->thumbnail_args );

	}
}