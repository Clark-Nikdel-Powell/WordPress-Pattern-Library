<?php
namespace CNP;

/**
 * PostAuthor.
 *
 * Returns a post author in a paragraph tag. Accepts "prefix" and "suffix" parameters.
 *
 * @since 0.2.0
 */
class PostAuthor extends AtomTemplate {

	private $prefix;
	private $suffix;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'postauthor';
		}
		$this->tag      = isset( $data['tag'] ) ? $data['tag'] : 'p';

		$this->prefix = isset( $data['prefix'] ) ? $data['prefix'] : 'By: ';
		$this->suffix = isset( $data['suffix'] ) ? $data['suffix'] : '.';

		$author = get_the_author();

		$this->content = $this->prefix . $author . $this->suffix;
	}
}