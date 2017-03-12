<?php
namespace CNP;

class BackgroundVideoVide extends AtomTemplate {

	public $vide_bg;
	public $vide_options;

	public function __construct( $data ) {

		if ( '' == $data['name'] ) {
			$data['name'] = 'backgroundVideoVide';
		}

		$data['tag'] = 'div';

		$this->vide_bg = array();

		if ( ! empty( $data['mp4'] ) ) {
			$this->vide_bg['mp4'] = $data['mp4'];
		}
		if ( ! empty( $data['webm'] ) ) {
			$this->vide_bg['webm'] = $data['webm'];
		}
		if ( ! empty( $data['jpg'] ) ) {
			$this->vide_bg['jpg'] = $data['jpg'];
		}

		// Set the vide bg.
		// Could probably check this closer if we need to, i.e., check and make sure we have a video file.
		if ( ! empty( $this->vide_bg ) ) {
			$data['attributes']['data-vide-bg'] = implode( ', ', $this->vide_bg );
		}

		// Set the vide options.
		if ( ! empty( $data['vide-options'] ) ) {
			$this->vide_options = implode( ', ', $data['vide-options'] );
		} else {
			$this->vide_options = 'autoplay: true, posterType: jpg, loop: true, muted: true, position: left top';
		}

		$data['attributes']['data-vide-options'] = $this->vide_options;

		parent::__construct( $data );
	}
}
