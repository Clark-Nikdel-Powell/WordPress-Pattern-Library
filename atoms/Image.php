<?php
namespace CNP;

/**
 * Image.
 *
 * Returns a responsive image. Pass 'srcset' and 'sizes' values in with the 'attributes' parameter.
 *
 * @since 0.6.0
 */
class Image extends AtomTemplate {

	private $image_size;
	private $icon;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'image';
		}
		// wp_get_attachment_image gives us an image tag, so the atom passes its attributes on to that function.
		$this->tag_type = 'content-only';

		// Set up attachment ID
		if ( isset( $data['image_object'] ) ) {
			$this->attachment_id = $data['image_object']->ID;
		}

		// Set up attachment ID if it isn't set yet. This gives us flexibility for ACF fields.
		if ( empty( $this->attachment_id ) && isset( $data['attachment_id'] ) ) {
			$this->attachment_id = $data['attachment_id'];
		}

		// Set the image size
		$this->image_size = '';
		if ( '' !== $data['size'] ) {
			$this->image_size = $data['size'];
		}

		// Set up icon argument
		$this->icon = '';
		if ( '' !== $data['icon'] ) {
			$this->icon = $data['icon'];
		}
	}

	public function getMarkup() {
		$this->markup = wp_get_attachment_image( $this->attachment_id, $this->image_size, $this->icon, $this->attributes );
	}
}