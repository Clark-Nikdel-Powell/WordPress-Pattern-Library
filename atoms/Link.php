<?php
namespace CNP;

class Link extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'link';
		}
		$this->tag                = 'a';
		$this->attributes['href'] = $data['href'];

	}
}