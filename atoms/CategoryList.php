<?php
namespace CNP;

class CategoryList extends AtomTemplate {

	public $delimiter;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'category-list';
		}
		$this->delimiter = $data['delimiter'];
		if ( '' == $this->delimiter ) {
			$this->delimiter = ', ';
		}

		$this->tag     = 'p';
		$this->content = get_the_category_list( $this->delimiter, '', $data['post'] );

	}
}