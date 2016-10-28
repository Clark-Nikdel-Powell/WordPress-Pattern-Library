<?php
namespace CNP;

class AtomTemplate {

	public $name;
	public $tag;
	public $tag_type;
	public $content;
	public $attributes;
	public $markup;
	public $post_object;
	public $hide;
	public $suppress_filters;

	public function __construct( $data ) {

		$this->name             = isset( $data['name'] ) ? $data['name'] : '';
		$this->tag              = isset( $data['tag'] ) ? $data['tag'] : '';
		$this->tag_type         = isset( $data['tag_type'] ) ? $data['tag_type'] : '';
		$this->content          = isset( $data['content'] ) ? $data['content'] : '';
		$this->before           = isset( $data['before'] ) ? $data['before'] : '';
		$this->after            = isset( $data['after'] ) ? $data['after'] : '';
		$this->attributes       = isset( $data['attributes'] ) ? $data['attributes'] : array();
		$this->hide             = isset( $data['hide'] ) ? $data['hide'] : false;
		$this->suppress_filters = isset( $data['suppress_filters'] ) ? $data['suppress_filters'] : true;
		$this->markup           = '';

		if ( isset( $data['post'] ) ) {
			$this->post_object = $data['post'];
		} else {
			$this->post_object = get_post();
		}

		// Ensures that the 'class' attribute is set if it wasn't passed in with attributes.
		if ( ! isset( $this->attributes['class'] ) ) {
			$this->attributes['class'] = array();
		}

		// Add the Atom name as a class
		$this->attributes['class'][] = $this->name;

		if ( ! empty( $data['class'] ) ) {

			$classes_arr = Utility::parse_classes_as_array( $data['class'] );

			if ( ! empty( $classes_arr ) ) {
				$this->attributes['class'] = array_merge( $this->attributes['class'], $classes_arr );
			}
		}
		unset( $this->class );

		// Filter the Atom properties.
		$atom_structure_filter = $this->name . '_properties_filter';
		apply_filters( $atom_structure_filter, $this, $data );
		Atom::add_debug_entry( 'Filter', $atom_structure_filter );
	}

	public function get_markup() {

		$this->markup = Atom::assemble( $this->name, $this );
	}
}
