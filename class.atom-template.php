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
		$this->before     = isset( $data['before'] ) ? $data['before'] : '';
		$this->after      = isset( $data['after'] ) ? $data['after'] : '';
		$this->attributes = isset( $data['attributes'] ) ? $data['attributes'] : array();
		$this->markup     = '';

		if ( isset( $data['post'] ) ) {
			$this->post_object = $data['post'];
		} else {
			global $post;
			$this->post_object = $post;
		}

	}

	public function get_markup() {
		$this->markup = Atom::assemble( $this->name, $this );
	}
}
