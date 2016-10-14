<?php
namespace CNP;

/**
 * TaxonomyList.
 *
 * Uses get_the_term_list() to return a comma-delimited list of custom taxonomy terms in a paragraph tag.
 *
 * @since 0.1.0
 *
 * @param string $delimiter The separator for the taxonomy term links.
 */
class TaxonomyList extends AtomTemplate {

	public $taxonomy;
	public $before_list;
	public $separator;
	public $after_list;
	public $include_links;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' === $this->name ) {
			$this->name = 'taxonomy-list';
		}

		// If a taxonomy wasn't passed in, assume the first taxonomy associated with the post type.
		if ( ! isset( $data['taxonomy'] ) ) {

			$post_object_taxonomies = get_object_taxonomies( $this->post_object );

			$this->taxonomy = array_shift( $post_object_taxonomies );

		} else {
			$this->taxonomy = $data['taxonomy'];
		}

		$this->before_list = isset( $data['before-list'] ) ? $data['before-list'] : '';
		$this->separator   = isset( $data['separator'] ) ? $data['separator'] : ', ';
		$this->after_list  = isset( $data['after-list'] ) ? $data['after-list'] : '';

		$this->include_links = isset( $data['include-links'] ) ? $data['include-links'] : true;

		$this->tag = isset( $data['tag'] ) ? $data['tag'] : 'p';

		if ( true === $this->include_links ) {
			$this->content = get_the_term_list( $this->post_object->ID, $this->taxonomy, $this->before_list, $this->separator, $this->after_list );
		} else {
			if ( ! $this->post_object ) {
				return;
			}

			$terms_arr      = get_the_terms( $this->post_object->ID, $this->taxonomy );
			$term_names_arr = array();

			if ( ! empty( $terms_arr ) ) {

				foreach ( $terms_arr as $term_obj ) {
					$term_names_arr[] = '<span class="name">' . $term_obj->name . '</span>';
				}

				$terms_list = implode( $this->separator, $term_names_arr );

				$this->content = $this->before_list . $terms_list . $this->after_list;
			} else {
				$this->content = '';
			}
		}
	}
}
