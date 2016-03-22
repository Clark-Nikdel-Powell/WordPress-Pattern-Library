<?php
namespace CNP;

/**
 * ListTerms.
 *
 * Uses wp_list_categories() to return an unordered list of taxonomy terms. Includes some logic
 * for determining taxonomy if a specific taxonomy is not included.
 *
 * @since 0.5.0
 *
 * @param array $list_args wp_list_categories() list arguments.
 */
class ListTerms extends AtomTemplate {

	public $taxonomy;
	public $list_args;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' === $this->name ) {
			$this->name = 'list-terms';
		}

		$this->tag = isset( $data['tag'] ) ? $data['tag'] : 'ul';

		// If a taxonomy wasn't passed in, assume either "category" or the first taxonomy associated with the post type.
		if ( isset( $data['taxonomy'] ) ) {
			$this->taxonomy = $data['taxonomy'];
		} else {

			if ( 'post' === $this->post_object->post_type ) {
				$this->taxonomy = 'category';
			} else {
				$post_object_taxonomies = get_object_taxonomies( $this->post_object );

				$this->taxonomy = array_shift( $post_object_taxonomies );
			}
		}

		// Set up default list args.
		$list_args_defaults_arr = [
			'taxonomy' => $this->taxonomy,
			'echo'     => 0,
			'title_li' => ''
		];

		// Parse supplied args from the organism setup.
		if ( isset( $data['list_args'] ) ) {
			$list_args_arr = wp_parse_args( $list_args_defaults_arr, $data['list_args'] );
		} else {
			$list_args_arr = $list_args_defaults_arr;
		}

		/**
		 * list_terms_list_args.
		 *
		 * A generic filter for site-wide use.
		 *
		 * @since 0.5.0
		 *
		 * @param array $list_args_arr An array of list arguments.
		 */
		$list_args_arr = apply_filters( 'list_terms_list_args', $list_args_arr );
		Atom::AddDebugEntry( 'Filter', 'list_terms_list_args' );

		/**
		 * $this->name_list_terms_list_args.
		 *
		 * An atom-specific wp_list_categories args filter.
		 *
		 * @since 0.5.0
		 *
		 * @param array $list_args_arr An array of list arguments.
		 */
		$list_args_arr_filter = $this->name . '_list_terms_list_args';
		$list_args_arr        = apply_filters( $list_args_arr_filter, $list_args_arr );
		Atom::AddDebugEntry( 'Filter', $list_args_arr_filter );

		// Assign the resolved args to the object.
		$this->list_args = $list_args_arr;

		$this->content = wp_list_categories( $this->list_args );

	}
}