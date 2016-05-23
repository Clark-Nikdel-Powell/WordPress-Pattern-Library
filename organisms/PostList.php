<?php
namespace CNP;

class PostList extends OrganismTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( ! isset( $data['name'] ) && '' === $this->name ) {
			$this->name = 'postlist';
		}

		if ( ! isset( $data['posts-structure'] ) && empty( $this->posts_structure ) ) {

			$posts_structure = [
				'PostClass' => [
					'children' => [ 'image', 'text' ],
				],
				'image'     => [
					'parts' => [
						'PostThumbnail'
					]
				],
				'text'      => [
					'parts' => [
						'PostTitleLink',
						'PostDate' => 'm/d/Y',
						'CategoryList',
						'ExcerptForce',
						'PostLink' => 'Read More'
					]
				]
			];

			$postlist_posts_structure_filter = $this->name . '_posts_structure';
			$this->posts_structure           = apply_filters( $postlist_posts_structure_filter, $posts_structure );
			Atom::AddDebugEntry( 'Filter', $postlist_posts_structure_filter );
		}
	}
}