<?php
namespace CNP;

/**
 * Loop.
 *
 * Runs a basic loop by using vsprintf().
 *
 * @since 0.3.0
 *
 * @param array $array The array of items to loop through.
 * @param string $format The format to apply to each item in $array.
 */
class Loop extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'loop';
		}

		if ( '' == $data['tag'] ) {
			$this->tag = 'div';
		}

		if ( isset( $data['array'] ) && isset( $data['format'] ) ) {

			foreach ( $data['array'] as $loop_item ) {
				$this->content .= vsprintf($data['format'], $loop_item );
			}
		}
	}
}