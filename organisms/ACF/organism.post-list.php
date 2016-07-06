<?php
namespace CNP;

class ACF_PostList extends PostList {

	public $post_args;
	public $data_type;
	public $link_after_content;
	public $list_title;
	public $list_link;
	public $list_link_text;

	public function __construct( $data ) {

		if ( ! isset( $data['name'] ) || empty( $data['name'] ) ) {
			$data['name'] = 'acf-postlist';
			$this->name   = $data['name'];
		}

		$this->list_title     = isset( $data['list_title'] ) ? $data['list_title'] : '';
		$this->list_link      = isset( $data['link'] ) ? $data['link'] : '';
		$this->list_link_text = isset( $data['link_text'] ) ? $data['link_text'] : '';

		if ( empty( $data['structure'] ) ) {

			$data['structure'] = [
				'listtitle' => [
					'tag_type' => 'false_without_content',
					'content'  => $data['list_title'],
				],
			];
		}

		if ( empty( $data['posts-structure'] ) ) {

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
						'ExcerptForce',
						'PostLink' => 'Read More',
					],
				],
			];
		}

		// This isn't the greatest check we could make here, but I think it'll do.
		if ( '' !== $data['link_text'] && '' !== $data['link'] ) {

			$link_name = $data['name'] . '__' . 'listlink';
			$link_args = [
				'name'     => $link_name,
				'tag_type' => 'false_without_content',
				'content'  => $data['link_text'],
				'href'     => $data['link'],
			];
			$link_obj  = new Link( $link_args );
			$link_obj->get_markup();

			if ( '' !== $link_obj->markup ) {
				$data['after_content'] = $link_obj->markup;
			}
		}

		self::get_posts( $data );

		$data['posts'] = $this->posts;

		parent::__construct( $data );

	}

	public function get_posts( $data ) {

		if ( isset( $data['data_type'] ) ) {

			if ( 'Automatic' === $data['data_type'] ) {

				$this->post_args = [
					'post_type'      => $data['post_type'],
					'posts_per_page' => $data['number_of_posts'],
				];
			}

			if ( 'Manual' === $data['data_type'] ) {
				$this->posts = $data['manual_posts'];
			}
		}

		// It'll still be empty unless we're dealing with a manual post list.
		if ( empty( $this->posts ) ) {
			$this->posts = new \WP_Query( $this->post_args );
		}
	}
}
