<?php
namespace CNP;

class AtomTemplate {

	public $name;
	public $tag;
	public $tag_type;
	public $content;
	public $attributes;
	public $markup;
	public $post_object;

	public function __construct( $data ) {

		$this->name       = isset( $data['name'] ) ? $data['name'] : '';
		$this->tag        = isset( $data['tag'] ) ? $data['tag'] : '';
		$this->tag_type   = isset( $data['tag_type'] ) ? $data['tag_type'] : '';
		$this->content    = isset( $data['content'] ) ? $data['content'] : '';
		$this->attributes = isset( $data['attributes'] ) ? $data['attributes'] : '';
		$this->markup     = '';

		if ( isset( $data['post'] ) ) {
			$this->post_object = $data['post'];
		} else {
			global $post;
			$this->post_object = $post;
		}

	}

	public function getMarkup() {
		$this->markup = Atom::Assemble( $this->name, $this );
	}
}