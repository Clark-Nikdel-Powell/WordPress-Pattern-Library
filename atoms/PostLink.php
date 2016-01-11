<?php
namespace CNP;

class PostLink extends Link {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'postlink';
		}
		$this->attributes['href'] = get_permalink( $data['post'] );

	}
}
