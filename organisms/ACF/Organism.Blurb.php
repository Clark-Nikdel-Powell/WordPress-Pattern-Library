<?php
namespace CNP;

class ACF_Blurb extends OrganismTemplate {

	public function __construct( $data ) {

		// Set the name before the parent construct so that default classes can get added.
		if ( ! isset( $data['name'] ) ) {
			$this->name = 'acf-blurb';
		}

		parent::__construct( $data );

		$this->structure = [
			'inside' => [
				'parts' => [
					'image' => [
						'tag_type' => 'false_without_content',
					],
					'icon'  => [
						'tag_type' => 'false_without_content',
						'content'  => ''
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

		$media_position_class = 'position--' . $data['media_placement'];

		/*——————————————————————————————————————————
		/  Image- handled separately because it's multiple pieces.
		——————————————————————————————————————————*/
		if ( '' !== $data['foreground_image'] ) {

			$attachment_id = '';

			if ( is_int( $data['foreground_image'] ) ) {
				$attachment_id = $data['foreground_image'];
			}

			if ( is_array( $data['foreground_image'] ) ) {
				$attachment_id = $data['foreground_image']['ID'];
			}

			if ( '' !== $attachment_id ) {

				$this->structure['inside']['parts']['image'] = [
					'atom'          => 'Image',
					'attachment_id' => $attachment_id,
					'class'         => [ $media_position_class ]
				];
			} else {
				unset( $this->structure['inside']['parts']['image'] );
			}
		}

		/*——————————————————————————————————————————
		/  Icon generates after check
		——————————————————————————————————————————*/
		if ( '' !== $data['icon_name'] ) {
			$this->structure['inside']['parts']['icon']['content'] = Utility::getSvgIcon( $data['icon_name'] );
			$this->structure['inside']['parts']['icon']['class']   = $media_position_class;
		}
	}
}