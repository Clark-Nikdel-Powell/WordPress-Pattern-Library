<?php
namespace CNP;

class PostClass extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->name     = 'postclass';
		$this->tag      = 'article';
		$this->tag_type = 'split';

		$classes = [ ];

		if ( isset( $data['attributes']['class'] ) ) {
			$classes = $data['attributes']['class'];
		}

		$this->attributes['class'] = get_post_class( $classes, $data['post']->ID );
	}
}