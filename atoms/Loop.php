<?php
namespace CNP;

class Loop extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'loop';
		}

		$this->tag = 'div';

		if ( isset( $data['array'] ) && isset( $data['format'] ) ) {

			foreach ( $data['array'] as $loop_item ) {
				$this->content .= vsprintf($data['format'], $loop_item );
			}
		}
	}
}