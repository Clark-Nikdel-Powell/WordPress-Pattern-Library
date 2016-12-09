<?php
namespace CNP;

/**
 * PostTermSingle.
 *
 * Gets the first term associated with a post and returns a link to it.
 *
 * @since 0.11.0
 *
 */
class PostTermSingle extends AtomTemplate {

	public $taxonomy;

	public function __construct( $data ) {

		if ( '' === $this->name ) {
			$this->name = 'post-term-single';
		}

		$data['tag']      = 'div';
		$data['tag_type'] = 'false_without_content';
		$this->taxonomy = $data['taxonomy'];

		parent::__construct( $data );

		$this->get_term();
	}

	public function get_term() {

		// Get the post terms
		$post_terms = wp_get_post_terms( $this->post_object->ID, $this->taxonomy );

		if ( is_wp_error( $post_terms ) ) {
			$this->content = '';
		}

		// Get the ID of the first term.
		if ( ! empty( $post_terms ) ) {
			$post_term_arr = $post_terms[0];
		}

		if ( '' !== $post_term_arr->name ) {
			$this->content = $post_term_arr->name;
		}
	}
}

