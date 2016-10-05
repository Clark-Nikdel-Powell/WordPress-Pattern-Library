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

	private $attachment_id;
	private $image_size;
	private $icon;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'image';
		}
		// wp_get_attachment_image gives us an image tag, so the atom passes its attributes on to that function.
		$this->tag_type = 'content-only';

		// Convert any classes to a string. We do this because the $attr variable of wp_get_attachment_image is set
		// to take a single-dimensional array, not a multi-dimensional array.
		if ( isset( $this->attributes['class'] ) && is_array( $this->attributes['class'] ) && ! empty( $this->attributes['class'] ) ) {
			$this->attributes['class'] = implode( ' ', $this->attributes['class'] );
		}

		// Set up attachment ID
		$this->attachment_id = '';

		if ( isset( $data['image_object'] ) && is_object( $data['image_object'] ) ) {
			$this->attachment_id = $data['image_object']->ID;
		}

		if ( empty( $this->attachment_id ) && isset( $data['image_object'] ) && is_array( $data['image_object'] ) ) {
			$this->attachment_id = $data['image_object']['ID'];
		}

		// Set up attachment ID if it isn't set yet. This gives us flexibility for ACF fields.
		if ( empty( $this->attachment_id ) && isset( $data['attachment_id'] ) ) {
			$this->attachment_id = $data['attachment_id'];
		}

		// Set the image size
		$this->image_size = '';
		if ( ! empty( $data['size'] ) ) {
			$this->image_size = $data['size'];
		}

		// Set up icon argument
		$this->icon = false;
		if ( ! empty( $data['icon'] ) ) {
			$this->icon = $data['icon'];
		}

		// Attributes will have already been handled in the main AtomTemplate class.
	}

	public function get_markup() {

		if ( empty( $this->attachment_id ) ) {
			$this->markup = '';
		}

		$this->markup = wp_get_attachment_image( $this->attachment_id, $this->image_size, $this->icon, $this->attributes );
	}
}
