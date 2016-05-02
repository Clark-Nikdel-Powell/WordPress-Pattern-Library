<?php
namespace CNP;

/**
 * Class ACF_Close_row
 *
 * A simple close row atom.
 *
 * @package CNP
 */
class ACF_Close_row extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->tag_type = 'split';

	}

	public function getMarkup() {

		parent::getMarkup();

		// Reset the markup with the closing tag.
		$this->markup = $this->markup['close'];
	}
}