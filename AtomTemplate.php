<?php
namespace CNP;

class AtomTemplate {

	public function __construct( $data ) {

		$this->name       = $data['name'];
		$this->tag        = $data['tag'];
		$this->tag_type   = $data['tag_type'];
		$this->content    = $data['content'];
		$this->attributes = $data['attributes'];
		$this->markup     = '';

	}

	public function getMarkup() {

		$this->markup = Atom::Assemble( $this->name, $this );

	}
}