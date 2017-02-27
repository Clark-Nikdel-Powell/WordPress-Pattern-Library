<?php
namespace CNP;

/**
 * PostParentLink.
 *
 * Uses get_permalink() to return a link to a specific post.
 *
 * @since 0.3.0
 */
class PostParentLink extends Link {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'postlink';
		}
		if ( isset( $data['parent-id'] ) && ! empty( $data['parent-id'] ) ) {
			$this->attributes['href'] = get_permalink( $data['parent-id'] );
		} elseif ( isset( $this->post_object->post_parent ) && 0 !== $this->post_object->post_parent ) {
			$this->attributes['href'] = get_permalink( $this->post_object->post_parent );
		}

		if ( ! isset( $this->attributes['href'] ) ) {
			$this->attributes['href'] = '#';
		}
	}
}
