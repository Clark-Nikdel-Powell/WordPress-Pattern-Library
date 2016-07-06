<?php
namespace CNP;

class ACF_Map extends OrganismTemplate {

	public $title;
	public $map_attributes;
	public $markers;

	public function __construct( $data = array() ) {

		if ( ! isset( $data['name'] ) || empty( $data['name'] ) ) {
			$data['name'] = 'acf-map';
			$this->name   = $data['name'];
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
			'allow_zooming',
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
		if ( isset( $data['zoom_level'] ) && '' !== $data['zoom_level'] ) {
			$this->map_attributes['data-zoom_level'] = $data['zoom_level'];
		} else {
			$this->map_attributes['data-zoom_level'] = 12;
		}

		/*——————————————————————————————————————————————————————————
		/  Title Setup
		——————————————————————————————————————————————————————————*/
		if ( isset( $data['title'] ) && '' !== trim( $data['title'] ) ) {
			$this->title = $data['title'];
		}

		/*——————————————————————————————————————————————————————————
		/  Structure Setup
		——————————————————————————————————————————————————————————*/
		$markers_key = 'markers';

		// Standard class for standard JavaScript, namespaced class for good measure
		$this->map_attributes['class'] = [ 'acf-map__markers', $this->name . $this->separator . 'markers' ];

		$this->structure = [
			$markers_key => [
				'attributes' => $this->map_attributes,
				// The content is set by a markers loop in the get_markup method below.
				'content'    => '',
			],
		];

		if ( isset( $this->title ) ) {

			$title_structure_array = [
				'title' => [
					'content' => $this->title,
					'sibling' => $markers_key,
				],
			];

			$this->structure = array_merge( $title_structure_array, $this->structure );

		}

	}

	public function get_markup() {

		// Every map has markers. You can choose not to show them, however.
		if ( empty( $this->markers ) ) {
			return false;
		}

		foreach ( $this->markers as $marker_index => $marker ) {

			$marker = $marker['marker'];

			$marker_atom_args = [
				'attributes' => [
					'data-lat' => $marker['lat'],
					'data-lng' => $marker['lng'],
					'class'    => [ 'acf-map__marker', $this->name . $this->separator . 'marker' ],
				],
				'content'    => '<p>' . $marker['address'] . '</p>',
			];

			$this->structure['markers']['content'] .= Atom::assemble( $this->name . $this->separator . 'marker', $marker_atom_args );

		}

		parent::get_markup();
	}
}
