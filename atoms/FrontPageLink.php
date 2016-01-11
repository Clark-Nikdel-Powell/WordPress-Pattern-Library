<?php
namespace CNP;

class FrontPageLink extends Link {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'front-page-link';
		}
		$this->attributes['href'] = get_permalink( get_option( 'page_on_front' ) );

	}

}