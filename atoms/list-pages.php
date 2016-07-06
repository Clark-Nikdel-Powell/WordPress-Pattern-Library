<?php
namespace CNP;

/**
 * ListPages.
 *
 * Uses wp_list_pages() to return an unordered list of hierarchical posts. Does not assume subnav logic-- that is left
 * for a subnav organism.
 *
 * @since 0.5.0
 *
 * @param array $list_args wp_list_pages() list arguments.
 */
class ListPages extends AtomTemplate {

	public $list_args;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' === $this->name ) {
			$this->name = 'list-pages';
		}

		$this->tag = isset( $data['tag'] ) ? $data['tag'] : 'ul';

		// Set up default list args.
		$list_args_defaults_arr = [
			'post_type' => 'page',
			'echo'     => 0,
			'title_li' => '',
		];

		// Parse supplied args from the organism setup.
		if ( isset( $data['list_args'] ) ) {
			$list_args_arr = wp_parse_args( $data['list_args'], $list_args_defaults_arr );
		} else {
			$list_args_arr = $list_args_defaults_arr;
		}

		/**
		 * list_pages_list_args.
		 *
		 * A generic filter for site-wide use.
		 *
		 * @since 0.5.0
		 *
		 * @param array $list_args_arr An array of list arguments.
		 */
		$list_args_arr = apply_filters( 'list_pages_list_args', $list_args_arr );
		Atom::add_debug_entry( 'Filter', 'list_pages_list_args' );

		/**
		 * $this->name_list_pages_list_args.
		 *
		 * An atom-specific wp_list_categories args filter.
		 *
		 * @since 0.5.0
		 *
		 * @param array $list_args_arr An array of list arguments.
		 */
		$list_args_arr_filter = $this->name . '_list_pages_list_args';
		$list_args_arr        = apply_filters( $list_args_arr_filter, $list_args_arr );
		Atom::add_debug_entry( 'Filter', $list_args_arr_filter );

		// Assign the resolved args to the object.
		$this->list_args = $list_args_arr;

		$this->content = wp_list_pages( $this->list_args );

	}
}
