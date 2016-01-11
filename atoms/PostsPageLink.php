<?php
namespace CNP;

class PostsPageLink extends Link {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'posts-page-link';
		}
		$this->attributes['href'] = get_permalink( get_option( 'page_for_posts' ) );

	}
}