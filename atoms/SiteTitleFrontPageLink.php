<?php
namespace CNP;

class SiteTitleFrontPageLink extends AtomTemplate {

	private $link;
	private $link_data;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'site-title';
		}

		if ( ! isset( $this->link_data['name'] ) ) {
			$this->link_data['name'] = $this->name . '-link';
		}
		$this->link_data['content'] = get_bloginfo( 'site_title' );

		$this->link = new FrontPageLink( $this->link_data );
		$this->link->getMarkup();

		if ( '' == $data['tag'] ) {
			$this->tag = 'h2';
		}
		$this->content = $this->link->markup;

	}
}