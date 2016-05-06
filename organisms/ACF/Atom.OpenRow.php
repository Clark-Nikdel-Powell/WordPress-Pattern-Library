<?php
namespace CNP;

/**
 * Class ACF_Open_row
 *
 * A simple open row atom.
 *
 * @package CNP
 */
class ACF_Open_row extends AtomTemplate {

	public function __construct( $data ) {

		// Set the name before the parent construct so that default classes can get added.
		if ( ! isset( $data['name'] ) ) {
			$this->name = 'acf-openrow';
		}

		parent::__construct( $data );

		$this->tag = 'div';
		$this->tag_type = 'split';

	}

	public function getMarkup() {

		parent::getMarkup();

		// Reset the markup with just the open tag.
		$this->markup = $this->markup['open'];
	}
}