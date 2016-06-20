<?php
namespace CNP;

/**
 * ForceExcerpt.
 *
 * Returns an excerpt using get_the_excerpt(). Use the excerpt_length() filter to adjust the length of the output.
 *
 * @since 0.1.0
 */
class ExcerptForce extends Excerpt {

	public function __construct( $data ) {
		parent::__construct( $data );

		$this->content = get_the_excerpt();

	}
}
