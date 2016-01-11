<?php
namespace CNP;


class ForceExcerpt extends Excerpt {

	public function __construct( $data ) {
		parent::__construct( $data );

		$this->content = get_the_excerpt();

	}
}