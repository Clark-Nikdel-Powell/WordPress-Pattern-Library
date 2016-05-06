<?php
namespace CNP;

class ACF_Header extends OrganismTemplate {

	public function __construct( $data ) {

		// Set the name before the parent construct so that default classes can get added.
		if ( ! isset( $data['name'] ) ) {
			$this->name = 'acf-header';
		}

		parent::__construct( $data );

		$this->structure = [
			'background' => [
				'tag'     => 'div',
				'sibling' => 'text'
			],
			'text'       => [
				'parts' => [
					'title'       => [
						'tag'      => 'h2',
						'tag_type' => 'false_without_content',
						'content'  => $data['title']
					],
					'subtitle'    => [
						'tag'      => 'h3',
						'tag_type' => 'false_without_content',
						'content'  => $data['subtitle']
					],
					'description' => [
						'tag'      => 'div',
						'tag_type' => 'false_without_content',
						'content'  => $data['description']
					],
					'link'        => [
						'atom'     => 'Link',
						'tag_type' => 'false_without_content',
						'href'     => $data['link'],
						'content'  => $data['link_text']
					]
				]
			]
		];

		$this->structure = Helpers::setBackgroundOnStructureArray( $data, 'background', $this->structure );

	}
}
