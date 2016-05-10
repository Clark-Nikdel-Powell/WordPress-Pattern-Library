<?php
namespace CNP;

class ACF_BlurbList extends OrganismTemplate {

	public $label;
	public $background_type;
	public $link_type;
	public $link_location;
	public $elements;
	public $list_title;
	public $list_intro;
	public $blurb_classes;
	public $blurbs;
	public $list_link;
	public $list_link_text;
	public $class;
	public $id;

	public $structure;
	public $blurb_organism_args;

	public function __construct( $data ) {

		if ( ! isset( $data['name'] ) ) {
			$data['name'] = 'acf-blurblist';
			$this->name   = $data['name'];
		}

		parent::__construct( $data );

		$this->label           = $data['label'];
		$this->background_type = $data['background_type'];
		$this->link_type       = $data['link_type'];
		$this->link_location   = $data['link_location'];
		$this->elements        = $data['elements'];
		$this->list_title      = $data['list_title'];
		$this->list_intro      = $data['list_intro'];
		$this->blurb_classes   = $data['blurb_classes'];
		$this->blurbs          = ! empty( $data['blurbs'] ) ? $data['blurbs'] : [ ];
		$this->list_link       = $data['list_link'];
		$this->list_link_text  = $data['list_link_text'];
		$this->class           = $data['class'];
		$this->id              = $data['id'];

		$this->structure = [
			'blurbs' => [
				'tag_type' => 'content-only',
				'content'  => ''
			]
		];

		$this->blurb_organism_args = [
			'name'       => $this->name . $this->separator . 'blurb',
			'attributes' => [ ],
			'structure'  => [
				'inside' => [
					'atom'  => '',
					'parts' => [
						'image' => [
							'atom'     => 'Image',
							'tag_type' => 'false_without_content',
							'sibling'  => 'text'
						],
						'title' => [
							'tag'      => 'h2',
							'tag_type' => 'false_without_content',
							'content'  => ''
						],
						'text'  => [
							'tag_type' => 'content-only',
							'content'  => ''
						],
						'link'  => [
							'atom'     => 'Link',
							'tag_type' => 'false_without_content'
						]
					]
				]
			]
		];

	}

	public function getMarkup() {

		try {

			if ( empty( $this->blurbs ) ) {
				throw new \Exception( 'No blurbs defined.' );
			}

			foreach ( $this->blurbs as $blurb_index => $blurb_data ) {
				$this->generate_blurb( $blurb_data );
			}

			parent::getMarkup();

		} catch ( \Exception $e ) {

			echo '<!-- BlurbList failed: ', $e->getMessage(), '-->', "\n";

		}

	}

	private function generate_blurb( $blurb_data ) {

		// Trim all slide data first, so that an empty space doesn't get used as content by mistake.
		$blurb_data = Utility::multidimensionalArrayMap( 'trim', $blurb_data );

		$blurb_args = $this->blurb_organism_args;

		$blurb_args['structure']['inside']['parts']['image']['attachment_id'] = $blurb_data['foreground_image'];
		$blurb_args['structure']['inside']['parts']['title']['content']       = $blurb_data['title'];
		$blurb_args['structure']['inside']['parts']['text']['content']        = $blurb_data['text'];

		$blurb_args = $this->do_background_link( $blurb_args, $blurb_data );
		$blurb_args = $this->do_background( $blurb_args, $blurb_data );
		$blurb_args = $this->do_button( $blurb_args, $blurb_data );
		$blurb_args = $this->do_classes( $blurb_args, $blurb_data );
		$blurb_args = $this->do_id( $blurb_args, $blurb_data );

		$blurb = new OrganismTemplate( $blurb_args );
		$blurb->getMarkup();
		$this->structure['blurbs']['content'] .= $blurb->markup;

	}

	private function has_background() {
		return $this->background_type !== 'None';
	}

	private function is_color_background() {
		return $this->background_type === 'Color';
	}

	private function is_image_background() {
		return $this->background_type === 'Image';
	}

	private function is_background_link() {
		return $this->link_type === 'Background';
	}

	private function is_button_link() {
		return $this->link_type === 'Button';
	}

	private function is_internal_links() {
		return $this->link_location === 'Internal';
	}

	private function is_external_links() {
		return $this->link_location === 'External';
	}

	private function get_link( $data ) {

		$link = false;
		if ( $this->is_internal_links() && isset( $data['page_link'] ) && ! empty( $data['page_link'] ) ) {
			$link = $data['page_link'];
		}
		if ( $this->is_external_links() && isset( $data['link'] ) && ! empty( $data['link'] ) ) {
			$link = $data['link'];
		}

		return $link;

	}

	private function do_classes( $args, $data ) {

		$classes       = $this->blurb_classes . ',' . $data['class'];
		$args['class'] = Utility::parseClassesAsArray( $classes );

		return $args;

	}

	private function do_id( $args, $data ) {

		if ( isset( $data['id'] ) && ! empty( $data['id'] ) ) {
			$args['id'] = $data['id'];
		}

		return $args;
	}

	private function do_button( $args, $data ) {

		$link = $this->get_link( $data );
		$text = isset( $data['link_text'] ) && ! empty( $data['link_text'] ) ? $data['link_text'] : 'Learn More';

		if ( $this->is_button_link() && $link ) {
			$args['structure']['inside']['parts']['link']['content'] = $text;
			$args['structure']['inside']['parts']['link']['class']   = 'button';
			$args['structure']['inside']['parts']['link']['href']    = $link;
		}

		return $args;

	}

	private function do_background_link( $args, $data ) {

		$link = $this->get_link( $data );

		// If the background is a link, add a Link atom
		if ( $this->is_background_link() && $link ) {
			$args['structure']['inside']['atom'] = 'Link';
			$args['structure']['inside']['href'] = $link;
		}

		return $args;

	}

	private function do_background( $args, $data ) {

		if ( $this->has_background() ) {
			if ( $this->is_image_background() && isset( $data['background_image'] ) && ! empty( $data['background_image'] ) ) {
				$background = [
					'background' => [
						'atom'          => 'Image',
						'attachment_id' => $data['background_image']
					]
				];

				// This prepends the background atom array into inside parts
				$args['structure']['inside']['parts'] = $background + $args['structure']['inside']['parts'];
			}
			if ( $this->is_color_background() && isset( $data['background_color'] ) && ! empty( $data['background_color'] ) ) {
				Utility::arraySetPath( 'background-color:' . $data['background_color'], $args, 'structure/inside/attributes/style', '/' );
			}
		}

		return $args;

	}

}
