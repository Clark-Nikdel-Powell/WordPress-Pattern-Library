<?php
namespace CNP;

/**
 * CategoryList.
 *
 * Uses get_the_category_list() to return a comma-delimited list of categories in a paragraph.
 *
 * @since 0.1.0
 *
 * @param string $delimiter The separator for the category links.
 */
class CategoryList extends AtomTemplate {

	private $prefix;
	private $suffix;
	public $separator;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' === $this->name ) {
			$this->name = 'category-list';
		}

		$this->separator = isset( $data['separator'] ) ? $data['separator'] : ', ';
		$this->tag       = isset( $data['tag'] ) ? $data['tag'] : 'p';

		$this->prefix = isset( $data['prefix'] ) ? $data['prefix'] : '<strong>Categories:</strong> ';
		$this->suffix = isset( $data['suffix'] ) ? $data['suffix'] : '';

		$this->content = $this->prefix . get_the_category_list( $this->separator, '', $this->post_object ) . $this->suffix;

	}
}
