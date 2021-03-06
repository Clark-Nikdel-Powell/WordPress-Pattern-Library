<?php
namespace CNP;

/**
 * Class OrganismTemplate
 * @package CNP
 *
 * @since 0.1.0
 *
 */
class OrganismTemplate {

	public $name;
	public $separator;
	public $tag;
	public $tag_type;
	public $attributes;
	public $attribute_quote_style;
	public $before_content;
	public $after_content;
	public $hide;
	public $suppress_filters;

	public function __construct( $data ) {

		if ( isset( $data['name'] ) ) {
			$this->name = sanitize_html_class( $data['name'] );
		}

		/*——————————————————————————————————————————
		/  Miscellaneous Attributes
		——————————————————————————————————————————*/

		$this->tag = isset( $data['tag'] ) ? $data['tag'] : 'div';

		// The separator is the character(s) that separate the organism name from an element name.
		// In BEM methodology, they use "__"
		$this->separator = isset( $data['separator'] ) ? $data['separator'] : '__';

		$this->tag_type = 'split';

		$this->attributes            = isset( $data['attributes'] ) ? $data['attributes'] : array();
		$this->attribute_quote_style = isset( $data['attribute_quote_style'] ) ? $data['attribute_quote_style'] : '"';

		// Hide is used mostly for ACF integrations, so that we can turn off a layout without deleting it.
		$this->hide = isset( $data['hide'] ) ? $data['hide'] : false;

		// Suppress filters if we don't need them.
		$this->suppress_filters = isset( $data['suppress_filters'] ) ? $data['suppress_filters'] : true;

		/*——————————————————————————————————————————
		/  Class Settings
		——————————————————————————————————————————*/

		// Ensures that the 'class' attribute is set if it wasn't passed in with attributes.
		if ( ! isset( $this->attributes['class'] ) ) {
			$this->attributes['class'] = array();
		}

		// Add the Organism name as a class
		$this->attributes['class'][] = $this->name;

		// Now we take the class from $data and add it in to the attributes.
		// The helper function takes either a string or array, and returns an array.
		if ( ! empty( $data['class'] ) ) {

			$classes_arr = Utility::parse_classes_as_array( $data['class'] );

			if ( ! empty( $classes_arr ) ) {
				$this->attributes['class'] = array_merge( $this->attributes['class'], $classes_arr );
			}
		}
		unset( $this->class );

		// ID shorthand, sanitized for user-input, in case it's coming from ACF
		if ( ! empty( $data['id'] ) ) {
			$this->attributes['id'] = trim( $data['id'] );
		}
		unset( $this->id );

		$this->before_content = isset( $data['before_content'] ) ? $data['before_content'] : '';
		$this->after_content  = isset( $data['after_content'] ) ? $data['after_content'] : '';

		$this->structure = isset( $data['structure'] ) ? $data['structure'] : array();

		$this->markup_array = array();

		$this->post_args       = isset( $data['post-args'] ) ? $data['post-args'] : array();
		$this->posts           = isset( $data['posts'] ) ? $data['posts'] : array();
		$this->posts_structure = isset( $data['posts-structure'] ) ? $data['posts-structure'] : array();

		$this->markup = '';

		// Filter the Organism structure. This is the one filter that cannot be suppressed.
		$organism_name_structure_filter = $this->name . '_structure_filter';
		$this->structure                = apply_filters( $organism_name_structure_filter, $this->structure, $this, $data );
		Atom::add_debug_entry( 'Filter', $organism_name_structure_filter );

		// Filter the Post Args
		if ( ! empty( $this->post_args ) && false === $this->suppress_filters ) {
			$organism_name_post_args_filter = $this->name . '_post_args_filter';
			$this->post_args                = apply_filters( $organism_name_post_args_filter, $this->post_args, $this );
			Atom::add_debug_entry( 'Filter', $organism_name_post_args_filter );
		}

		if ( ! empty( $this->posts_structure ) && isset( $this->posts->found_posts ) && 0 === $this->posts->found_posts ) {
			$this->attributes['class'][] = $this->name . '--noPosts';
		}
	}

	/**
	 * get_markup
	 *
	 * Assembles the organism based on the structure and posts_structure, as well as before_content and after_content.
	 *
	 * @since 0.1.0
	 *
	 * string $before_content  Markup to place before the content
	 * string $after_content  Markup to place after the content
	 * array $structure  The array that determines how the atoms are nested and compiled
	 * array $posts  An array of WP Post Objects to loop through
	 * array $posts-structure  The structure array for each individual post.
	 *
	 * @return string Markup of the organism
	 */
	public function get_markup() {

		$markup_pieces = array();

		if ( '' !== $this->before_content ) {
			$markup_pieces[] = $this->before_content;
		}

		if ( ! empty( $this->structure ) ) {
			$markup_pieces[] = self::setup_markup_array( $this->structure );
		}

		if ( '' !== $this->after_content ) {
			$markup_pieces[] = $this->after_content;
		}

		if ( false === $this->suppress_filters ) {
			$organism_name_markup_pieces_order_filter = $this->name . '_markup_pieces_order';
			$markup_pieces                            = apply_filters( $organism_name_markup_pieces_order_filter, $markup_pieces );
			Atom::add_debug_entry( 'Filter', $organism_name_markup_pieces_order_filter );
		}

		$wrapper_args = [
			'tag'                   => $this->tag,
			'tag_type'              => $this->tag_type,
			'attributes'            => $this->attributes,
			'attribute_quote_style' => $this->attribute_quote_style,
		];

		// Suppression args on the organism filter down to all atoms.
		if ( true === $this->suppress_filters ) {
			$wrapper_args['suppress_filters'] = true;
		}

		$wrapper = Atom::assemble( $this->name, $wrapper_args );

		if ( ! empty( $markup_pieces ) ) {
			$this->markup = $wrapper['open'] . implode( '', $markup_pieces ) . $wrapper['close'];
		} else {
			$this->markup = '';
		}
	}

	/**
	 * loop_posts
	 *
	 * Given an array of posts, it loops through them and builds the markup for each post based on the posts_structure
	 * property.
	 *
	 * @since 0.1.0
	 *
	 * array $posts  An array of WP Post Objects to loop through
	 * array $posts-structure  The structure array for each individual post.
	 *
	 * @return array
	 */
	public function loop_posts() {

		$post_atoms_arr = array();

		while ( $this->posts->have_posts() ) {

			// the_post advances onto the next post and sets the global $post variable.
			$this->posts->the_post();

			// Access the current $post object.
			global $post;
			$current_post_index = $this->posts->current_post;
			$total_posts_count  = $this->posts->post_count;

			// Find any items before this post
			$post_atoms_arr[] = self::get_post_pieces( 'before', 'string', $current_post_index, $total_posts_count );

			// Find the correct post structure for this post.
			$posts_structure  = self::get_post_pieces( 'structure', 'array', $current_post_index, $total_posts_count );
			$post_atoms_arr[] = self::setup_markup_array( $posts_structure, $post );

			// Find any items after this post.
			$post_atoms_arr[] = self::get_post_pieces( 'after', 'string', $current_post_index, $total_posts_count );

		}
		wp_reset_postdata();

		return $post_atoms_arr;
	}

	/**
	 * get_post_pieces
	 *
	 * Function for looking
	 *
	 * @param $type
	 * @param string $return_format
	 * @param $current_post_index
	 * @param $total_posts_count
	 *
	 * @return array|string
	 */
	protected function get_post_pieces( $type, $return_format = 'string', $current_post_index, $total_posts_count ) {

		if ( 'string' === $return_format ) {
			$return = '';
		}
		if ( 'array' === $return_format ) {
			$return = array();
		}

		$current_post_index_type_property = 'post_' . $current_post_index . '_' . $type;
		$post_first_type_property         = 'post_first_' . $type;
		$post_last_type_property          = 'post_last_' . $type;
		$post_even_type_property          = 'post_even_' . $type;
		$post_odd_type_property           = 'post_odd_' . $type;

		// Check for first post.
		if ( isset( $this->$post_first_type_property ) && 0 === $current_post_index ) {
			$return = $this->$post_first_type_property;
		}

		// Check for last post.
		if ( isset( $this->$post_last_type_property ) && $total_posts_count - 1 === $current_post_index ) {
			$return = $this->$post_last_type_property;
		}

		// Check for even post.
		if ( isset( $this->$post_even_type_property ) && 0 === $current_post_index % 2 ) {
			$return = $this->$post_even_type_property;
		}

		// Check for odd post.
		if ( isset( $this->$post_odd_type_property ) && 0 !== $current_post_index % 2 ) {
			$return = $this->$post_odd_type_property;
		}

		// Check for specific index.
		if ( isset( $this->$current_post_index_type_property ) && ! empty( $this->$current_post_index_type_property ) ) {
			$return = $this->$current_post_index_type_property;
		}

		// This is the one non-agnostic part of this function, so that we're always assured to get the posts structure.
		if ( 'structure' === $type && empty( $return ) ) {
			$return = $this->posts_structure;
		}

		// Add a filter for good measure.
		$organism_name_post_type_filter = $this->name . '_post_' . $type;
		$return                         = apply_filters( $organism_name_post_type_filter, $return, $current_post_index, $total_posts_count );
		Atom::add_debug_entry( 'Filter', $organism_name_post_type_filter );

		return $return;
	}

	protected function determine_piece_type( $piece_name, $piece_args_and_content ) {

		$piece_type = '';

		/*
		 * Part 1: Determine piece type. If $piece_args_and_content is a string, then it's either the atom content (and should be passed through in
		 * the atom atom_args), or it's the atom name. We test this by checking if $piece_name is a string.
		 */
		if ( is_string( $piece_args_and_content ) ) {

			// 0 => 'footer'
			if ( is_int( $piece_name ) ) {
				$piece_type = 'name-only';
			}

			// 'title' => 'Title Text'
			if ( is_string( $piece_name ) ) {
				$piece_type = 'self-content-only';
			}
		}

		/*
		 * If $piece_args_and_content is an array, it's a more complex piece. We determine *how* complex by testing
		 * the array keys of $piece_args_and_content. If 'children' exists, then we know this is a parent item, and
		 * it resolves to $piece_type = 'split-with-children'. If 'parts' exists, it resolves to 'split-with-parts'.
		 */
		if ( '' === $piece_type && is_array( $piece_args_and_content ) ) {

			/*
			 * @EXIT: prevents atom_args like '' => ['content' => 'Content'] from passing through
			 */
			if ( empty( $piece_name ) ) {
				$piece_type = '';
			}

			if ( isset( $piece_args_and_content['children'] ) ) {
				$piece_type = 'split-with-children';
			}

			if ( isset( $piece_args_and_content['parts'] ) ) {
				$piece_type = 'split-with-parts';
			}

			if ( isset( $piece_args_and_content['content'] ) ) {
				$piece_type = 'self-with-content';
			}
		}

		/*
		 * This if statement will catch both 'posts-loop' and 'posts-loop' => [ 'additional-setting' => '' ]
		 */
		if ( 'posts-loop' === $piece_name || 'posts-loop' === $piece_args_and_content ) {
			$piece_type = 'posts-loop';
		}

		if ( empty( $piece_type ) ) {

			if ( is_string( $piece_name ) && is_array( $piece_args_and_content ) ) {
				$piece_type = 'self-with-content';
			}
		}

		return $piece_type;
	}

	/**
	 * setup_markup_array
	 *
	 * The markup array holds the markup for each individual atom from the structure array in keys based on the atom's
	 * name. The markup array is then sent off for assembly in the compile_markup_array method.
	 *
	 * @since 0.1.0
	 *
	 * @param array $structure_pieces Either the structure property or posts_structure property
	 * @param object $post_obj A WP Post Object
	 * @param string $markup_array_name The posts markup array is kept separate from the main markup array.
	 *                                   Either 'markup_array' or 'posts_markup_array' is acceptable. $this->$markup_array_name = $markup_arr;
	 *
	 * @return array $markup_array  The markup array
	 */
	protected function setup_markup_array( $structure_pieces, $post_obj = null, $markup_array_name = 'markup_array' ) {

		$markup_arr = array();

		foreach ( $structure_pieces as $piece_name => $piece_args_and_content ) {

			/*
			 * @EXIT Sanity check: $piece_args_and_content is required to move forward, as it will either be the atom's content and arguments
			 * ('title' => 'Item Title' / 'PostClass' => ['children' => ['image', 'text', 'metatext']]) or the atom's
			 * name in the case of simple atoms ('item').
			 */
			$piece_type = self::determine_piece_type( $piece_name, $piece_args_and_content );

			$atom_args = array();
			if ( is_array( $piece_args_and_content ) ) {
				$atom_args = $piece_args_and_content;
			}

			// Sanity check: we can't go any further if $piece_type isn't resolved.
			if ( empty( $piece_type ) ) {
				continue;
			}

			/*
			 * Part 2: Switch through $piece_type. Now that we know what type of piece we're dealing with, we can get the
			 * markup for the piece and add the markup to the markup_array in the right way.
			 */
			switch ( $piece_type ) {

				case 'posts-loop':

					$atom_name = 'posts-loop';

					break;

				case 'name-only':

					$atom_name = $piece_args_and_content;

					break;

				case 'self-content-only':

					$atom_name            = $piece_name;
					$atom_args['content'] = $piece_args_and_content;

					break;

				case 'split-with-children':

					$atom_name             = $piece_name;
					$atom_args['tag_type'] = 'split';

					break;

				case 'split-with-parts':

					$atom_name             = $piece_name;
					$atom_args['tag_type'] = 'split';

					break;

				case 'self-with-content':

					$atom_name = $piece_name;

					break;
			}

			// Sanity check: we can't go any further if $atom_name isn't resolved.
			if ( empty( $atom_name ) ) {
				continue;
			}

			/*
			 * Part 3: Get markup and add it to markup_array.
			 */
			$markup_arr[ $atom_name ] = array();

			switch ( $piece_type ) {

				case 'posts-loop':

					$atom_args['tag_type']    = 'split';
					$markup_arr[ $atom_name ] = self::get_structure_part( $atom_name, $atom_args, $post_obj );

					$posts_arr                         = self::loop_posts();
					$markup_arr[ $atom_name ]['parts'] = $posts_arr;

					break;

				case 'name-only':

					$markup_arr[ $atom_name ]['content'] = self::get_structure_part( $atom_name, $atom_args, $post_obj );

					break;

				case 'self-content-only':

					$markup_arr[ $atom_name ]['parts'][ $atom_name ] = self::get_structure_part( $atom_name, $atom_args, $post_obj );

					break;

				case 'split-with-children':

					$markup_arr[ $atom_name ] = self::get_structure_part( $atom_name, $atom_args, $post_obj );

					$markup_arr[ $atom_name ]['children'] = $piece_args_and_content['children'];

					break;

				case 'split-with-parts':

					$markup_arr[ $atom_name ] = self::get_structure_part( $atom_name, $atom_args, $post_obj );

					foreach ( $piece_args_and_content['parts'] as $subatom_name => $subatom_args ) {

						// Reset subatom valid name and args.
						$subatom_valid_name = '';
						$subatom_valid_args = array();

						// TODO: atom name resolution refactor
						if ( is_array( $subatom_args ) ) {
							$subatom_valid_name = $subatom_name;
							$subatom_valid_args = $subatom_args;
						}
						if ( is_int( $subatom_name ) ) {
							$subatom_valid_name = $subatom_args;
							$subatom_valid_args = array();
						}
						if ( is_string( $subatom_name ) && is_string( $subatom_args ) ) {
							$subatom_valid_name            = $subatom_name;
							$subatom_valid_args['content'] = $subatom_args;
						}

						$markup_arr[ $atom_name ]['parts'][ $subatom_valid_name ] = self::get_structure_part( $subatom_valid_name, $subatom_valid_args, $post_obj );

					}

					break;

				case 'self-with-content':

					$markup_arr[ $atom_name ]['content'] = self::get_structure_part( $atom_name, $atom_args, $post_obj );

					break;
			}

			/*
			 * self-content-only atoms need a sibling property in order to reference the next piece when compiling the markup.
			 */
			if ( ! empty( $previous_atom_name ) ) {

				if ( 'self-content-only' == $markup_arr[ $previous_atom_name ]['piece_type'] || 'name-only' == $markup_arr[ $previous_atom_name ]['piece_type'] ) {
					$markup_arr[ $previous_atom_name ]['sibling'] = $atom_name;
				}
			}

			$markup_arr[ $atom_name ]['name'] = $atom_name;

			/*
			 * The piece type is added to the atom information, which is useful in the case of 'self-content-only' parts,
			 * which need a dynamic 'sibling' setting for the recursive assembly function to work.
			 */
			$markup_arr[ $atom_name ]['piece_type'] = $piece_type;

			if ( isset( $piece_args_and_content['sibling'] ) ) {
				$markup_arr[ $atom_name ]['sibling'] = $piece_args_and_content['sibling'];
			}

			/*
			 * Set for the case of 'self-content-only' atoms.
			 */
			$previous_atom_name = $atom_name;

		} // foreach $structure_pieces

		/*
		 * The markup array is dynamically set based on the $markup_array_name argument. Useful for separating the
		 * organism structure and posts structure.
		 */
		$this->$markup_array_name = $markup_arr;

		return self::compile_markup_array( $markup_array_name );

	}

	/**
	 * get_structure_part
	 *
	 * Get a part of the structure: returns either a plain atom or a named atom based on the args.
	 *
	 * @since 0.1.0
	 * @see CNP/Atom
	 *
	 * @param string $atom_name The base atom name, modified to be namespaced by the organism name.
	 * @param array $atom_args The atom args
	 * @param WP_Post $post_obj Post object
	 *
	 * @return mixed
	 */
	protected function get_structure_part( $atom_name, $atom_args, $post_obj ) {

		// First, namespace the atom based on the organism name.
		if ( isset( $atom_args['name'] ) ) {
			$namespaced_atom_name = $this->name . $this->separator . $atom_args['name'];
		}
		if ( ! isset( $atom_args['name'] ) ) {
			$namespaced_atom_name = $this->name . $this->separator . $atom_name;
		}

		$class_atom_suffix = $atom_name;

		if ( isset( $atom_args['atom'] ) ) {
			$class_atom_suffix = $atom_args['atom'];
		}

		// Set up the class to check against
		$class_atom_name = 'CNP\\' . $class_atom_suffix;

		// If class isn't set already, then it defaults to an array.
		if ( ! isset( $atom_args['attributes']['class'] ) ) {
			$atom_args['attributes']['class'] = array();
		}

		// Shorthand for class
		if ( isset( $atom_args['class'] ) ) {

			// The utility function takes either a string or array, and returns an array.
			$classes_arr = Utility::parse_classes_as_array( $atom_args['class'] );

			if ( ! empty( $classes_arr ) ) {
				// $atom_args['attributes']['class'] has been pre-set as an array, so the merge here is safe.
				$atom_args['attributes']['class'] = array_merge( $atom_args['attributes']['class'], $classes_arr );
			}
		}

		// Add the $post to $atom_args, if it is present.
		if ( isset( $post_obj ) ) {
			$atom_args['post'] = $post_obj;
		}

		// Set up the atom class.
		$atom_args['attributes']['class'][] = $namespaced_atom_name;

		// Set up atom filter suppression, or not
		$atom_args['suppress_filters'] = $this->suppress_filters;


		// If the class exists, then it's a named atom, and we need to
		// run the get_markup method based on the namespaced atom name.
		if ( class_exists( $class_atom_name ) ) {
			$atom_args['name'] = $namespaced_atom_name;
			$atom_object       = new $class_atom_name( $atom_args );
			$atom_object->get_markup();

			return $atom_object->markup;

		}

		// If the class does not exist, then it's a generic atom.
		// Run it through the Atom class to assemble it
		if ( ! class_exists( $class_atom_name ) ) {
			$atom = Atom::assemble( $namespaced_atom_name, $atom_args );

			return $atom;
		}

		return true;
	}

	/**
	 * compile_markup_array
	 *
	 * Compiles the markup array by passing off the first piece to a recursive function, and returns the compiled markup string.
	 *
	 * @since 0.2.0
	 *
	 * @param string $markup_array_name The name of the markup array to compile.
	 *
	 * @return string $compiled_markup  The compiled markup.
	 */
	protected function compile_markup_array( $markup_array_name ) {

		$markup_array = $this->$markup_array_name;

		$first_piece = array_shift( $markup_array );

		$compiled_markup = self::recursive_assemble_pieces( $first_piece, $markup_array_name );

		return $compiled_markup;

	}


	/**
	 * recursive_assemble_pieces
	 *
	 * Put a whole markup_array together by recursively calling the function for children and siblings.
	 *
	 * @since 0.1.0
	 *
	 * @param string|array $organism_part The organism part to compile.
	 * @param string $markup_array_name The name of the markup array to access.
	 * @param string $markup The markup is passed in on recursive calls.
	 *
	 * @return string $markup  Returns the completed markup.
	 */
	protected function recursive_assemble_pieces( $organism_part, $markup_array_name, $markup = '' ) {

		$markup_array = $this->$markup_array_name;

		if ( isset( $organism_part['open'] ) ) {
			$markup .= $organism_part['open'];
		}

		switch ( $organism_part['piece_type'] ) {

			case 'name-only':

				// Markup keys: 'open' and 'close', and 'content'
				if ( isset( $organism_part['content'] ) ) {
					$markup .= $organism_part['content'];
				}

				break;

			case 'self-content-only':

				// Markup keys: 'parts'
				if ( isset( $organism_part['parts'] ) ) {

					foreach ( $organism_part['parts'] as $piece ) {
						$markup .= $piece;
					}
				}

				break;

			case 'split-with-children':

				// Markup keys: 'open', 'close' and 'children'
				if ( isset( $organism_part['children'] ) ) {

					$child = $organism_part['children'];

					if ( is_string( $child ) ) {
						$markup .= self::recursive_assemble_pieces( $markup_array[ $child ], $markup_array_name );
					}

					if ( is_array( $child ) ) {

						foreach ( $child as $piece ) {

							$markup .= self::recursive_assemble_pieces( $markup_array[ $piece ], $markup_array_name );
						}
					}
				}

				break;

			case 'split-with-parts':

				// Markup keys: 'open', 'close' and 'parts'
				if ( isset( $organism_part['parts'] ) ) {

					foreach ( $organism_part['parts'] as $piece ) {
						$markup .= $piece;
					}
				}

				break;

			case 'self-with-content':

				// Markup keys: 'content'
				if ( isset( $organism_part['content'] ) ) {
					$markup .= $organism_part['content'];
				}

				break;

			case 'posts-loop':

				// Markup keys: 'open', 'close' and 'parts'
				if ( isset( $organism_part['parts'] ) ) {

					foreach ( $organism_part['parts'] as $piece ) {
						$markup .= $piece;
					}
				}

				break;
		}

		if ( isset( $organism_part['close'] ) ) {
			$markup .= $organism_part['close'];
		}

		// Siblings are placed after the current item is done.
		if ( isset( $organism_part['sibling'] ) ) {
			$markup .= self::recursive_assemble_pieces( $markup_array[ $organism_part['sibling'] ], $markup_array_name );
		}

		// Return the completed string.
		return $markup;

	}
}
