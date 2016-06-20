<?php
namespace CNP;

/**
 * PostTermLinkSingle.
 *
 * Gets the first term associated with a post and returns a link to it.
 *
 * @since 0.11.0
 *
 */
class PostTermLinkSingle extends AtomTemplate {

	public $taxonomy;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' === $this->name ) {
			$this->name = 'post-term-link-single';
		}

		$this->tag      = 'a';
		$this->tag_type = 'false_without_content';
		$this->taxonomy = $data['taxonomy'];

		$this->get_term();
	}

	public function get_term() {

		// Variables initialized.
		$post_term_link = '';

		// Get the post terms
		$post_terms = wp_get_post_terms( $this->post_object->ID, $this->taxonomy );

		if ( is_wp_error( $post_terms ) ) {
			$this->content = '';
		}

		// Get the ID of the first term.
		if ( ! empty( $post_terms ) ) {
			$post_term_arr = $post_terms[0];
		}

		// Get the term link.
		if ( ! empty( $post_term_arr ) ) {
			$post_term_link = get_term_link( $post_term_arr->term_id, $this->taxonomy );
		}

		if ( ! empty( $post_term_link ) ) {
			$this->attributes['href'] = $post_term_link;
		}

		if ( '' !== $post_term_arr->name ) {
			$this->content = $post_term_arr->name;
		}
	}
}

