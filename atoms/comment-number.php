<?php
namespace CNP;

class CommentNumber extends AtomTemplate {

	private $prefix;
	private $suffix;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( empty( $this->name ) ) {
			$this->name = 'comment-number';
		}

		$this->prefix = isset( $data['prefix'] ) ? $data['prefix'] : '';
		$this->suffix = isset( $data['suffix'] ) ? $data['suffix'] : '';

		$comment_count = 0;

		if ( isset( $this->post_object ) ) {
			$comment_count = get_comments_number( $this->post_object->ID );
		}

		$this->tag     = 'div';
		$this->content = $this->prefix . $comment_count . $this->suffix;
	}
}
