<?php
namespace CNP;

class PostTitle extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'posttitle';
		}
		$this->tag     = 'h2';
		$this->content = get_the_title( $data['post'] );

	}
}
