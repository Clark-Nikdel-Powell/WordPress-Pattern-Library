<?php
namespace CNP;

class ACF_Blurb_list extends OrganismTemplate {

	public function __construct( $data ) {

		// Set the name before the parent construct so that default classes can get added.
		if ( !isset( $data['name'] ) ) {
			$this->name = 'acf-blurblist';
		}

		parent::__construct( $data );

	}

}