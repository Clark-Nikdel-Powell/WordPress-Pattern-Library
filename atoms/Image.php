<?php
namespace CNP;

/**
 * Image.
 *
 * Returns a responsive image.
 * TODO: replace most of this with the function I used for the CNP Case Study
 *
 * @since 0.6.0
 */
class Image extends AtomTemplate {

	private $image_object;
	private $attachment_id;
	private $image_meta;
	private $base_size;

	private $size_array;
	private $image_src;

	private $sizes;
	private $srcset;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'image';
		}
		$this->tag = 'img';

		// Set up attachment ID
		if ( isset( $data['image_object'] ) ) {
			$this->image_object  = $data['image_object'];
			$this->attachment_id = $data['image_object']->ID;
		}

		// Set up attachment ID if it isn't set yet. This gives us flexibility for ACF fields.
		if ( empty( $this->attachment_id ) && isset( $data['attachment_id'] ) ) {
			$this->attachment_id = $data['attachment_id'];
		}

		// Set up image meta
		if ( isset( $data['image_meta'] ) ) {
			$this->image_meta = $data['image_meta'];
		} else {
			$this->image_meta = wp_get_attachment_metadata( $this->attachment_id );
		}

		// Set up the base size
		if ( isset( $data['base_size'] ) ) {
			$this->base_size = $data['base_size'];
		} else {
			$this->base_size = 'full';
		}

		// Set up size_array
		if ( 'full' === $this->base_size ) {
			$this->size_array = [ $this->image_meta['width'], $this->image_meta['height'] ];
		}
		else {
			$this->size_array = [ $this->image_meta['sizes'][$this->base_size]['width'], $this->image_meta['sizes'][$this->base_size]['height'] ];
		}

		// Set up file
		$this->image_src = $this->image_meta['file'];


		// Get the src attribute
		$img_url_sizes_arr = wp_get_attachment_image_src($this->attachment_id, $this->base_size);
		$this->attributes['src'] = $img_url_sizes_arr[0];

		// Get the sizes attribute
		if ( isset( $data['sizes'] ) ) {
			$this->sizes = implode( ', ', $data['sizes'] );
		} else {
			$this->sizes = wp_calculate_image_sizes( $this->base_size, $this->image_src, $this->image_meta, $this->attachment_id );
		}

		// Get the srcset attribute
		$this->srcset = wp_calculate_image_srcset( $this->size_array, $this->image_src, $this->image_meta, $this->attachment_id );

		$this->attributes['sizes'] = $this->sizes;
		$this->attributes['srcset'] = $this->srcset;
	}
}