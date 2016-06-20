<?php
namespace CNP;

/**
 * FronPageLink.
 *
 * Returns a link to the home page of the site.
 *
 * Parent: Link
 * Classes that use this class: SiteTitleFrontPageLink
 *
 * @since 0.2.0
 */
class FrontPageLink extends Link {

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'front-page-link';
		}
		$this->attributes['href'] = home_url();

	}

}