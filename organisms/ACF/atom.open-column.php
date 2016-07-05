<?php
namespace CNP;

/**
 * Class ACF_OpenColumn
 *
 * A simple open column atom.
 *
 * @package CNP
 */
class ACF_OpenColumn extends AtomTemplate {

	private $data;

	public function __construct( $data ) {

		$this->data = $data;

		// Set the name before the parent construct so that default classes can get added.
		if ( ! isset( $data['name'] ) || empty( $data['name'] ) ) {
			$data['name'] = 'acf-opencolumn';
			$this->name   = $data['name'];
		}

		parent::__construct( $data );

		$this->tag      = 'div';
		$this->tag_type = 'split';

		$standard_classes = [ $this->name, 'column' ];
		$data_classes     = array();
		if ( ! empty( $data['class'] ) ) {
			$data_classes = Utility::parse_classes_as_array( $data['class'] );
		}
		$this->attributes['class'] = array_merge( $standard_classes, (array) $data_classes );

		if ( ! empty( $data['id'] ) ) {
			$this->attributes['id'] = $data['id'];
		}
	}

	public function get_inside() {

		$data = [
			'name'     => $this->name . '__inside',
			'tag'      => 'div',
			'tag_type' => 'split',
		];

		$default_classes = [ $data['name'] ];
		$inside_classes  = array();
		if ( isset( $this->data['inside_class'] ) ) {
			$inside_classes = Utility::parse_classes_as_array( $this->data['inside_class'] );
		}
		$data['attributes']['class'] = array_merge( $default_classes, (array) $inside_classes );

		$inside = new AtomTemplate( $data );
		$inside->get_markup();

		return $inside->markup;
	}

	public function get_markup() {

		parent::get_markup();

		$inside       = $this->get_inside();
		$this->markup = $this->markup['open'] . $inside['open'];
	}
}
