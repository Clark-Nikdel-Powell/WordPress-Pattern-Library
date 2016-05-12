<?php
namespace CNP;

class ACF_Content extends AtomTemplate {

	public function __construct( $data ) {

		if ( ! isset( $data['name'] ) ) {
			$data['name'] = 'acf-content';
			$this->name   = $data['name'];
		}

		parent::__construct( $data );

		$this->tag = 'div';
		$this->tag_type = 'false_without_content';

		if ( '' !== $data['content'] ) {
			$this->content = $data['content'];
		}
	}
}