<?php
namespace CNP;

class ACF_Content extends AtomTemplate {

	public function __construct( $data = [ ] ) {

		parent::__construct( $data );

		$this->tag_type = 'false_without_content';

		if ( '' === $this->name ) {
			$this->name = 'acf-content';
		}

		$this->tag = 'div';

		if ( '' !== $data['content'] ) {
			$this->content = $data['content'];
		}
	}
}