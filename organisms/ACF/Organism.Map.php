<?php
namespace CNP;

class ACF_Map extends OrganismTemplate {

	public $title;
	public $map_attributes;
	public $markers;

	public function __construct( $data = [ ] ) {

		if ( '' === $this->name ) {
			$this->name = 'acf-map';
		}
		
		parent::__construct( $data );

		/*——————————————————————————————————————————————————————————
		/  Markers Setup
		——————————————————————————————————————————————————————————*/
		$this->markers = $data['markers'];

		/*——————————————————————————————————————————————————————————
		/  Map Options Setup
		——————————————————————————————————————————————————————————*/
		$option_keys = array(
			'show_markers',
			'disable_controls',
			'allow_panning',
			'allow_dragging',
			'allow_zooming'
		);

		// Initializes all options to false.
		foreach ( $option_keys as $option_name ) {
			$this->map_attributes[ 'data-' . $option_name ] = 'false';
		}

		// Overwrites default options for each key present in the option data.
		if ( ! empty( $data['options'] ) ) {

			foreach ( $data['options'] as $option_name ) {
				$this->map_attributes[ 'data-' . $option_name ] = 'true';
			}
		}

		// Sets the zoom level
		if ( '' !== $data['zoom_level'] ) {
			$this->map_attributes['data-zoom_level'] = $data['zoom_level'];
		}

		/*——————————————————————————————————————————————————————————
		/  Title Setup
		——————————————————————————————————————————————————————————*/
		if ( '' !== trim( $data['title'] ) ) {
			$this->title = $data['title'];
		}

		/*——————————————————————————————————————————————————————————
		/  Structure Setup
		——————————————————————————————————————————————————————————*/
		$markers_key = 'markers';

		$this->structure = [
			$markers_key => [
				'attributes' => $this->map_attributes,
				// The content is set by a markers loop in the getMarkup method below.
				'content'    => ''
			]
		];

		if ( isset( $this->title ) ) {

			$title_structure_array = [
				'title' => [
					'tag'     => 'h2',
					'content' => $this->title,
					'sibling' => $markers_key
				]
			];

			$this->structure = array_merge( $title_structure_array, $this->structure );

		}

	}

	public function getMarkup() {

		// Every map has markers. You can choose not to show them, however.
		if ( empty( $this->markers ) ) {
			return false;
		}

		foreach ( $this->markers as $marker_index => $marker ) {

			$marker = $marker['marker'];

			$marker_atom_args = [
				'attributes' => [
					'data-lat' => $marker['lat'],
					'data-lng' => $marker['lng']
				],
				'content'    => '<p>' . $marker['address'] . '</p>'
			];

			$this->structure['markers']['content'] .= Atom::Assemble( 'marker', $marker_atom_args );

		}

		parent::getMarkup();
	}
}