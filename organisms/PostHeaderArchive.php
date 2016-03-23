<?php
namespace CNP;

class PostHeaderArchive extends OrganismTemplate {

	public function __construct( $data = [ ] ) {

		parent::__construct( $data );

		if ( ! isset( $data['name'] ) ) {
			$this->name = 'postheader';
		}

		if ( ! isset( $data['structure'] ) ) {

			$structure = [
				'title' => [
					'atom' => 'PostTitleLink',
					'sibling' => 'date'
				],
				'date'   => [
					'atom'    => 'PostDate',
					'sibling' => 'author'
				],
				'author' => [
					'atom' => 'PostAuthor',
					'sibling' => 'image'
				],
				'image'  => [
					'atom'     => 'PostThumbnail',
					'size'     => 'medium',
					'tag_type' => 'false_without_content'
				]
			];

			$postheader_structure_filter = $this->name . '_archive_structure';
			$this->structure             = apply_filters( $postheader_structure_filter, $structure );
			Atom::AddDebugEntry( 'Filter', $postheader_structure_filter );
		}
	}
}