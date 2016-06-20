<?php
namespace CNP;

class SectionHeader extends OrganismTemplate {

	public function __construct( $data = array() ) {

		parent::__construct( $data );

		if ( ! isset( $data['name'] ) ) {
			$this->name = 'section';
		}
		if ( ! isset( $data['tag'] ) ) {
			$this->name = 'header';
		}

		if ( ! isset( $data['structure'] ) ) {

			$ancestor = cnp_get_highest_ancestor();

			$section_title_filter = $this->name . '_title';
			$title                = apply_filters( $section_title_filter, $ancestor['title'] );
			Atom::add_debug_entry( 'Filter,', $section_title_filter );

			$this->structure = [
				'row'    => [
					'children' => [ 'column' ],
				],
				'column' => [
					'parts' => [
						'title' => [
							'tag'     => 'h2',
							'content' => $title,
						],
					],
				],
			];
		}
	}
}
