<?php
namespace CNP;

/**
 * ImageBackground.
 *
 * @since 0.6.0
 */
class ImageBackground extends Image {

	private $image_url;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'image-background';
		}
		// wp_get_attachment_image gives us an image tag, so the atom passes its attributes on to that function.
		$this->tag_type = '';
		$this->tag      = 'div';

		if ( ! empty( $this->attachment_id ) ) {
			$img_array                 = wp_get_attachment_image_src( $this->attachment_id, $this->image_size, $this->icon );
			$this->image_url           = $img_array[0];
			$this->attributes['style'] = 'background-image: url(' . $this->image_url . ')';
		}
	}

	public function get_markup() {

		$this->markup = Atom::assemble( $this->name, $this );
	}
}
