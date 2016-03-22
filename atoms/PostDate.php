<?php
namespace CNP;

/**
 * PostDate.
 *
 * Uses get_the_date() to return the post date in a paragraph.
 *
 * @since 0.4.0
 *
 * @param string $date_format Set a custom date format.
 */
class PostDate extends AtomTemplate {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'postdate';
		}

		$format = 'F j, Y';
		if ( isset( $data['date_format'] ) ) {
			$format = $data['date_format'];
		}

		$this->tag     = isset( $data['tag'] ) ? $data['tag'] : 'p';;
		$this->content = get_the_date( $format, $this->post_object );

	}
}