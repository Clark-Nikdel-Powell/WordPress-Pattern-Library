<?php
namespace CNP;

class ACF_Blurb_list extends OrganismTemplate {

	public function __construct( $data ) {

		if ( !isset( $data['name'] ) ) {
			$this->name = 'acf-blurblist';
		}

		parent::__construct( $data );
	}

}