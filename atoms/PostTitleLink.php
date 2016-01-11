<?php
namespace CNP;

class PostTitleLink extends PostTitle {

	private $link;
	private $link_data;

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->link_data['post']    = $data['post'];
		$this->link_data['name']    = $this->name . '-link';
		$this->link_data['content'] = $this->content;

		$this->link = new PostLink( $this->link_data );
		$this->link->getMarkup();

		$this->content = $this->link->markup;

	}
}