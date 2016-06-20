<?php
namespace CNP;

class ContentSourceLink extends AtomTemplate {

	private $flag_type;

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->name = '';

		if ( '' == $this->name ) {
			$this->name = 'content-source-link';
		}
		$this->name = 'content-source-link';

		$this->tag_type = 'false_without_content';

		// If there's a link, we can set the tag, href, and target.
		if ( isset( $data['href'] ) ) {
			$this->tag                  = 'a';
			$this->attributes['href']   = site_url() . $data['href'];
			$this->attributes['target'] = '_blank';
		} else {
			$this->tag = 'div';
		}

		if ( isset( $data['type'] ) ) {

			if ( 'h' === $data['type'] ) {
				$this->flag_type             = 'Hardcoded';
				$this->attributes['class'][] = 'hardcoded';
			}
			if ( 'd' === $data['type'] ) {
				$this->flag_type             = 'Dynamic';
				$this->attributes['class'][] = 'dynamic';
			}
			if ( 'e' === $data['type'] ) {
				$this->flag_type             = 'Editable';
				$this->attributes['class'][] = 'editable';
			}

		}

		if ( isset( $data['parent'] ) ) {
			$this->attributes['data-parent'] = $data['parent'];
		}

		$this->attributes['title'] = isset( $data['title'] ) ? $data['title'] : '';

		$this->content = '<span class="content">'. $this->content . ':</span> <span class="type">' . $this->flag_type .'</span>';

	}
}