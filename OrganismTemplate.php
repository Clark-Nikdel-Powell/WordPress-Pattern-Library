<?php

class OrganismTemplate {

	public function __construct( $data ) {

		$this->name            = $data['name'];
		$this->tag             = 'div';
		$this->tag_type        = 'split';
		$this->attributes      = $data['attributes'];
		$this->before_content  = $data['before_content'];
		$this->after_content   = $data['after_content'];
		$this->structure       = $data['structure'];
		$this->posts           = $data['posts'];
		$this->posts_structure = $data['posts-structure'];
		$this->markup          = '';
		$this->markup_array    = [ ];

	}

	/**
	 *
	 */
	public function getMarkup() {

		$markup_pieces = [];

		if ( '' !== $this->before_content ) {
			$markup_pieces[] = $this->before_content;
		}

		if ( ! empty( $this->structure ) ) {
			$markup_pieces[] = self::setupMarkupArray( $this->structure );
		}

		if ( ! empty( $this->posts ) ) {
			$markup_pieces[] = self::loopPosts();
		}

		if ( '' !== $this->after_content ) {
			$markup_pieces[] = $this->after_content;
		}

		apply_filters( $this->name . 'markup_pieces_order', $markup_pieces );

		$this->markup = implode('', $markup_pieces);

	}

	/**
	 * @return array
	 */
	protected function loopPosts() {

		$post_atoms = array();

		while ( $this->posts->have_posts() ) {

			$this->posts->the_post();

			// TODO: figure out how I'm passing the post object in, exactly. Does it work just because we're in the loop?
			$post_atoms[] = self::setupMarkupArray( $this->posts_structure, $post );

		}
		wp_reset_postdata();

		return $post_atoms;
	}

	/**
	 * @param $structure_pieces
	 * @param string $post
	 *
	 * @return string
	 */
	protected function setupMarkupArray( $structure_pieces, $post = '' ) {

		$markup_arr = [ ];

		foreach ( $structure_pieces as $organism_name => $organism_contents ) {

			$args = [ 'tag_type' => 'split' ];

			if ( is_array( $organism_contents ) ) {
				$atom_name = $organism_name;
			}
			if ( is_string( $organism_contents ) ) {
				$atom_name = $organism_contents;
			}

			// First, get the markup for the item we're on.
			$markup_arr[ $atom_name ] = self::getStructurePart( $atom_name, $args, $post );

			if ( isset( $organism_contents['children'] ) ) {

				$markup_arr[ $atom_name ]['children'] = $organism_contents['children'];

			}

			if ( isset( $organism_contents['parts'] ) ) {

				foreach ( $organism_contents['parts'] as $sub_atom_name => $sub_atom_args ) {

					if ( is_array( $sub_atom_args ) ) {
						$sub_atom_valid_name = $sub_atom_name;
					}
					if ( is_string( $sub_atom_args ) ) {
						$sub_atom_valid_name = $sub_atom_args;
					}

					$markup_arr[ $atom_name ]['parts'][ $sub_atom_valid_name ] = self::getStructurePart( $sub_atom_valid_name, $sub_atom_args, $post );
				}
			}
		}

		$this->markup_array = $markup_arr;

		$first_part = array_shift( $markup_arr );

		$string = self::recursiveAssembleOrganism( $first_part );

		return $string;

	}

	/**
	 * @param $organism_part
	 * @param string $string
	 *
	 * @return string
	 */
	protected function recursiveAssembleOrganism( $organism_part, $string = '' ) {

		if ( isset( $organism_part['open'] ) ) {

			$string .= $organism_part['open'];

		}

		if ( isset( $organism_part['children'] ) ) {

			$child = $organism_part['children'];

			if ( is_string( $child ) ) {
				$string .= self::recursiveAssembleOrganism( $this->markup_array[ $child ] );
			}

			if ( is_array( $child ) ) {

				foreach ( $child as $piece ) {
					$string .= self::recursiveAssembleOrganism( $this->markup_array[ $piece ] );
				}
			}
		}

		if ( isset( $organism_part['parts'] ) ) {

			foreach ( $organism_part['parts'] as $piece ) {
				$string .= $piece;
			}
		}

		if ( isset( $organism_part['close'] ) ) {

			$string .= $organism_part['close'];

		}

		return $string;

	}

	/**
	 * @param $atom_name
	 * @param $atom_args
	 * @param $post
	 *
	 * @return mixed
	 */
	protected function getStructurePart( $atom_name, $atom_args, $post ) {

		$namespaced_atom_name = $this->name . '-' . $atom_name;
		$class_atom_name      = 'CNP\\' . $atom_name;

		if ( class_exists( $class_atom_name ) ) {

			$atom_object       = new $class_atom_name( $post, $atom_args );
			$atom_object->name = $namespaced_atom_name;
			$atom_object->getMarkup();

			return $atom_object->markup;

		}

		if ( ! class_exists( $class_atom_name ) ) {

			$atom = Atom::Assemble( $namespaced_atom_name, $atom_args );

			return $atom;
		}
	}
}