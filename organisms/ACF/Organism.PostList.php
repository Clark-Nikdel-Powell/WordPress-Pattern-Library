<?php
namespace CNP;

class ACF_Post_list extends PostList {

	public $post_args;

	public function __construct( $data ) {

		if ( !isset( $data['name'] ) ) {
			$this->name = 'acf-postlist';
		}

		if ( 'Automatic' === $data['data_type'] ) {

			$this->post_args = [
				'post_type' => $data['post_type'],
				'numberposts' => $data['number_of_posts']
			];

			$this->posts = new \WP_Query($post_args);
		}

		if ( 'Manual' === $data['data_type']) {
			$this->posts = $data['manual_posts'];
		}

		$this->structure = [
			'listtitle' => [
				'tag' => 'h2',
				'tag_type' => 'false_without_content',
				'content' => $data['list_title']
			]
		];

		$this->posts_structure = [
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
					'ExcerptForce',
					'PostLink' => 'Read More'
				]
			]
		];

		$link_name = $this->name . $this->separator . 'link';
		$link_args = [
			'atom' => 'Link',
			'tag_type' => 'false_without_content',
			'content' => $data['link_text'],
			'attributes' => [
				'href' => $data['link']
			]
		];

		$this->after_content = Atom::Assemble($link_name, $link_args);

		parent::__construct( $data );

	}
}