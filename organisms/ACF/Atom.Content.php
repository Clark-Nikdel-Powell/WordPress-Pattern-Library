<?php
namespace CNP;

class ACF_Content extends AtomTemplate {

	public function __construct( $data ) {

		if ( '' === $this->name ) {
			$this->name = 'acf-content';
		}

		parent::__construct( $data );

		$this->tag = 'div';
		$this->tag_type = 'false_without_content';

		if ( '' !== $data['content'] ) {
			$this->content = $data['content'];
		}
	}
}