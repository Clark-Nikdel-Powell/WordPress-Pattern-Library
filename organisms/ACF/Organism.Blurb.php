<?php
namespace CNP;

class ACF_Blurb extends OrganismTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->structure = [
			'inside' => [
				'parts' => [ ]
			]
		];

		/*
		 * Most of the blurb content fields are optional. So, we check that we have data first before setting the structure piece.
		 * For this reason, all the pieces of the blurb are set up as "parts" of inside, which means we don't have to do advanced
		 * checking for siblings or children, parts just runs right through it.
		 */

		/*——————————————————————————————————————————
		/  Image
		——————————————————————————————————————————*/
		if ( '' !== $data['image'] ) {

			$attachment_id        = '';
			$image_position_class = $data['media_placement'];

			if ( is_int( $data['image'] ) ) {
				$attachment_id = $data['image'];
			}

			if ( is_array( $data['image'] ) ) {
				$attachment_id = $data['image']['ID'];
			}

			if ( '' !== $attachment_id ) {

				$this->structure['inside']['parts']['image'] = [
					'atom'          => 'image',
					'attachment_id' => $attachment_id,
					'class'         => [ $image_position_class ]
				];
			}
		}

		/*——————————————————————————————————————————
		/  Title
		——————————————————————————————————————————*/
		if ( '' !== $data['title'] ) {

			$this->structure['inside']['parts']['title'] = [
				'tag'     => 'h3',
				'content' => $data['title']
			];
		}

		/*——————————————————————————————————————————
		/  Text
		——————————————————————————————————————————*/
		if ( '' !== $data['text'] ) {

			$this->structure['inside']['parts']['text'] = [
				'tag'     => 'p',
				'content' => $data['text']
			];
		}

		/*——————————————————————————————————————————
		/  Link
		——————————————————————————————————————————*/
		if ( '' !== $data['link'] ) {

			if ( '' !== $data['link_text'] ) {
				$link_text = $data['link_text'];
			} else {
				$link_text = 'Read More';
			}

			$this->structure['inside']['parts']['link'] = [
				'atom'    => 'Link',
				'href'    => $data['link'],
				'content' => $link_text
			];
		}
	}
}