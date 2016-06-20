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

	private $size;
	private $attr;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'post-thumbnail';
		}

		$this->size = isset( $data['size'] ) ? $data['size'] : 'post-thumbnail';
		$this->attr = isset( $data['attr'] ) ? $data['attr'] : array();
		$this->tag  = isset( $data['tag'] ) ? $data['tag'] : 'div';

		$this->content = call_user_func( 'get_the_post_thumbnail', $this->post_object, $this->size, $this->attr );
	}
}
