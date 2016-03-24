<?php
namespace CNP;

/**
 * Excerpt.
 *
 * Returns a post excerpt in a paragraph tag.
 *
 * Children: ExcerptForce, ExcerptSearch
 *
 * @since 0.1.0
 */
class Excerpt extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'excerpt';
		}
		$this->tag     = isset( $data['tag'] ) ? $data['tag'] : 'p';
		$this->content = isset( $this->post_object->post_excerpt ) ? $this->post_object->post_excerpt : '';

	}
}