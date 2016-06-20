<?php
namespace CNP;

/**
 * PostTitleLink.
 *
 * Uses PostLink to build a link to a post, and then depends on PostTitle's arguments to complete the output.
 *
 * @since 0.1.0
 */
class PostTitleLink extends PostTitle {

	private $link;
	private $link_data;

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->link_data['name']    = $this->name . 'Anchor';
		$this->link_data['content'] = $this->content;

		$this->link = new PostLink( $this->link_data );
		$this->link->getMarkup();

		$this->content = $this->link->markup;

	}
}