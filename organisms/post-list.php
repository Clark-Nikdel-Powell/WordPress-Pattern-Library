<?php
namespace CNP;

class PostList extends OrganismTemplate {

	public function __construct( $data ) {

		if ( ! isset( $data['name'] ) && '' === $this->name ) {
			$this->name = 'postlist';
		}

		if ( ! isset( $data['posts-structure'] ) || empty( $data['posts-structure'] ) ) {

			$data['posts-structure'] = [
				'PostClass' => [
					'children' => [ 'image', 'text' ],
				],
				'image'     => [
					'parts' => [
						'PostThumbnail',
					],
				],
				'text'      => [
					'parts' => [
						'PostTitleLink',
						'PostDate' => 'm/d/Y',
						'CategoryList',
						'ExcerptForce',
						'PostLink' => 'Read More',
					],
				],
			];
		}

		parent::__construct( $data );
	}
}
