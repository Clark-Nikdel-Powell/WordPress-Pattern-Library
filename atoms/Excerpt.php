<?php
namespace CNP;

class Excerpt extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'excerpt';
		}
		$this->tag     = 'p';
		$this->content = $data['post']->post_excerpt;

	}
}