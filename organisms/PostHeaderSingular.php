<?php
namespace CNP;

class PostHeaderSingular extends OrganismTemplate {

	public function __construct( $data = [ ] ) {

		parent::__construct( $data );

		global $post;
		$ancestor = cnp_get_highest_ancestor();

		if ( ! isset( $data['name'] ) ) {
			$this->name = 'postheader';
		}

		if ( ! isset( $data['structure'] ) ) {

			$structure = [
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

			$title_args = [
				'title' => [
					'atom'    => 'PostTitle',
					'sibling' => 'date'
				]
			];

			// Add the post title to the start of the structure array only if the section title does not match the page title.
			if ( $ancestor['title'] !== $post->post_title && is_post_type_hierarchical( $post->post_type ) ) {
				$structure = array_merge( $title_args, $structure );
			}

			$postheader_structure_filter = $this->name . '_singular_structure';
			$this->structure             = apply_filters( $postheader_structure_filter, $structure );
			Atom::AddDebugEntry( 'Filter', $postheader_structure_filter );
		}
	}
}