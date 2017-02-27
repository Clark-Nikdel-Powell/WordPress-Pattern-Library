<?php
namespace CNP;

class BackgroundVideo extends OrganismTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'backgroundVideo';
		}

		$this->tag = 'div';

		$this->structure = [
			'container' => [
				'children' => [ 'poster', 'video' ],
			],
			'poster'    => [
				'tag'        => 'div',
				'attributes' => [
					'style' => 'background-image: url(' . str_replace( 'poster:', '', $data['jpg'] ) . ')',
				],
			],
			'video'     => [
				'tag'        => 'video',
				'attributes' => [
					'autoplay'           => 'true',
					'loop'               => 'true',
					'muted'              => 'true',
					'webkit-playsinline' => '',
					'playsinline'        => '',
					'src'                => str_replace( 'mp4:', '', $data['mp4'] ),
				],
			],
		];
	}
}
