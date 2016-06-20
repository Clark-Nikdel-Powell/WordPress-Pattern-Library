<?php
namespace CNP;

/**
 * PostClass.
 *
 * Applies get_post_class() to an article, along with other classes.
 *
 * @since 0.2.0
 */
class PostClass extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->name     = 'postclass';
		$this->tag      = isset( $data['tag'] ) ? $data['tag'] : 'article';
		$this->tag_type = 'split';

		$classes = array();

		if ( isset( $data['attributes']['class'] ) ) {
			$classes = $data['attributes']['class'];
		}

		$this->attributes['class'] = get_post_class( $classes, $this->post_object->ID );
	}
}
