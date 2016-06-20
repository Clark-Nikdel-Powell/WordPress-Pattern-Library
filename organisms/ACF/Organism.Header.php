<?php
namespace CNP;

class ACF_Header extends OrganismTemplate {

	public function __construct( $data ) {

		// Set the name before the parent construct so that default classes can get added.
		if ( ! isset( $data['name'] ) || empty( $data['name'] ) ) {
			$data['name'] = 'acf-header';
			$this->name   = $data['name'];
		}

		$data['structure'] = [
			'background' => [
				'sibling' => 'text',
			],
			'text'       => [
				'parts' => [
					'title'       => [
						'tag'      => 'h2',
						'tag_type' => 'false_without_content',
						'content'  => $data['title'],
					],
					'subtitle'    => [
						'tag'      => 'h3',
						'tag_type' => 'false_without_content',
						'content'  => $data['subtitle'],
					],
					'description' => [
						'tag'      => 'div',
						'tag_type' => 'false_without_content',
						'content'  => $data['description'],
					],
					'link'        => [
						'atom'     => 'Link',
						'tag_type' => 'false_without_content',
						'href'     => $data['link'],
						'content'  => $data['link_text'],
					],
				],
			],
		];

		$data['structure'] = Helpers::set_background_on_structure_array( $data, 'background', $data['structure'] );

		parent::__construct( $data );

	}
}
