<?php
namespace CNP;

class PostThumbnail extends AtomTemplate {

	public function __construct( $data, $image_args ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'post-thumbnail';
		}
		$this->tag     = 'div';
		$this->content = call_user_func_array( 'get_the_post_thumbnail', $image_args );

	}
}
