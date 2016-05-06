<?php
namespace CNP;

class ACF_Blurb extends OrganismTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->structure = [
			'inside' => [
				'parts' => [
					'image' => [
						'atom' => 'image'
					],
					'title' => [
						'tag'      => 'h3',
						'tag_type' => 'false_without_content',
						'content'  => $data['title']
					],
					'text'  => [
						'tag'      => 'p',
						'tag_type' => 'false_without_content',
						'content'  => $data['text']
					],
					'link'  => [
						'atom'     => 'Link',
						'tag_type' => 'false_without_content',
						'href'     => $data['link'],
						'content'  => $data['link_text']
					]
				]
			]
		];

		/*——————————————————————————————————————————
		/  Image- handled separately because it's multiple pieces.
		——————————————————————————————————————————*/
		if ( '' !== $data['foreground_image'] ) {

			$attachment_id        = '';
			$image_position_class = $data['media_placement'];

			if ( is_int( $data['foreground_image'] ) ) {
				$attachment_id = $data['foreground_image'];
			}

			if ( is_array( $data['foreground_image'] ) ) {
				$attachment_id = $data['foreground_image']['ID'];
			}

			if ( '' !== $attachment_id ) {

				$this->structure['inside']['parts']['image'] = [
					'attachment_id' => $attachment_id,
					'class'         => [ $image_position_class ]
				];
			} else {
				unset( $this->structure['inside']['parts']['image'] );
			}
		}
	}
}