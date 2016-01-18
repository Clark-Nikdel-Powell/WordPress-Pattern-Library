<?php
namespace CNP;

/**
 * Class OrganismTemplate
 * @package CNP
 *
 * @since 0.1.0
 *
 * TODO: formalize the way that we pass in the structure. Here are the types I'm thinking of now:
 * Basic: 'item' ---> <div class="$organism_name-item"></div>
 * Basic with content:  'title' => 'Atom Title' ---> <div class="$organism_name-title">Atom Title</div>
 * ^^ maybe this should be: 'title' => ['content' => 'Atom Title']
 * Named: 'PostTitle'
 * Named with content: 'Button' => 'View the Blog'
 * ^^ maybe this should be: 'Button' => ['content' => 'View the Blog']
 * Basic with children ('children' is for nesting purposes): 'inside' => ['children' => ['image', 'text', 'metatext']]
 * Named with children ('children' is for nesting purposes): 'PostClass' => ['children' => ['image', 'text', 'metatext']]
 * Basic with parts ('parts' is for markup purposes)
 * Named with parts ('parts' is for markup purposes)
 * How can we pass through Atom args for basic and named atoms? E.g., PostThumbnail size. Maybe a special argument is
 * appropriate ---> like 'image_size'. Make it something specific for each named Atom. Basic Atoms wouldn't have
 * special arguments like this.
 */
class OrganismTemplate {

	public function __construct( $data ) {

		$this->name       = $data['name'];
		$this->tag        = 'div';
		$this->tag_type   = 'split';
		$this->attributes = $data['attributes'];

		$this->before_content = $data['before_content'];
		$this->after_content  = $data['after_content'];

		$this->structure    = $data['structure'];
		$this->markup_array = [ ];

		$this->posts           = $data['posts'];
		$this->posts_structure = $data['posts-structure'];

		$this->posts_markup_array = [ ];
		$this->markup             = '';

	}

	/**
	 * getMarkup
	 *
	 * Assembles the organism based on the structure and posts_structure, as well as before_content and after_content.
	 *
	 * @since 0.1.0
	 *
	 * @internal string $before_content  Markup to place before the content
	 * @internal string $after_content  Markup to place after the content
	 * @internal array $structure  The array that determines how the atoms are nested and compiled
	 * @internal array $posts  An array of WP Post Objects to loop through
	 * @internal array $posts-structure  The structure array for each individual post.
	 *
	 * @return string Markup of the organism
	 */
	public function getMarkup() {

		$markup_pieces = [ ];

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

		$markup_pieces = apply_filters( $this->name . 'markup_pieces_order', $markup_pieces );

		$wrapper_args = [
			'tag'        => $this->tag,
			'tag_type'   => $this->tag_type,
			'attributes' => $this->attributes
		];

		$wrapper = Atom::Assemble( $this->name, $wrapper_args );

		$this->markup = $wrapper['open'] . implode( '', $markup_pieces ) . $wrapper['close'];

	}

	/**
	 * loopPosts
	 *
	 * Given an array of posts, it loops through them and builds the markup for each post based on the posts_structure
	 * property.
	 *
	 * @since 0.1.0
	 *
	 * @internal array $posts  An array of WP Post Objects to loop through
	 * @internal array $posts-structure  The structure array for each individual post.
	 *
	 * @return array
	 */
	protected function loopPosts() {

		$post_atoms_arr = [ ];

		while ( $this->posts->have_posts() ) {

			$this->posts->the_post();

			// TODO: figure out how I'm passing the post object in, exactly. Does it work just because we're in the loop?
			$post_atoms_arr[] = self::setupMarkupArray( $this->posts_structure, $post, 'posts_markup_array' );

		}
		wp_reset_postdata();

		$post_atoms = implode( '', $post_atoms_arr );

		return $post_atoms;
	}

	/**
	 * setupMarkupArray
	 *
	 * The markup array holds the markup for each individual atom from the structure array in keys based on the atom's
	 * name. The markup array is then sent off for assembly in the compileMarkupArray method.
	 *
	 * @since 0.1.0
	 *
	 * @param array $structure_pieces Either the structure property or posts_structure property
	 * @param object $post A WP Post Object
	 * @param string $markup_array_name The posts markup array is kept separate from the main markup array.
	 *                                   Either 'markup_array' or 'posts_markup_array' is acceptable. $this->$markup_array_name = $markup_arr;
	 *
	 * @return array $markup_array  The markup array
	 */
	protected function setupMarkupArray( $structure_pieces, $post = null, $markup_array_name = 'markup_array' ) {

		$markup_arr = [ ];

		foreach ( $structure_pieces as $piece_name => $piece_args_and_content ) {

			/*
			 * @EXIT Sanity check: $piece_args_and_content is required to move forward, as it will either be the atom's content and arguments
			 * ('title' => 'Item Title' / 'PostClass' => ['children' => ['image', 'text', 'metatext']]) or the atom's
			 * name in the case of simple atoms ('item').
			 */
			$piece_type = '';
			$atom_args  = [];

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
					$piece_type = 'name-and-content';
				}
			}

			/*
			 * If $piece_args_and_content is an array, it's a more complex piece. We determine *how* complex by testing
			 * the array keys of $piece_args_and_content. If 'children' exists, then we know this is a parent item, and
			 * it resolves to $piece_type = 'split-parent'. If 'parts' exists, it resolves to 'split-container'.
			 */
			if ( is_array( $piece_args_and_content ) ) {

				$atom_args = $piece_args_and_content;

				/*
				 * @EXIT: prevents atom_args like '' => ['content' => 'Content'] from passing through
				 */
				if ( empty( $piece_name ) ) {
					continue;
				}

				if ( isset( $piece_args_and_content['children'] ) ) {
					$piece_type = 'split-parent';
				}

				if ( isset( $piece_args_and_content['parts'] ) ) {
					$piece_type = 'split-container';
				}
			}

			/*
			 * Part 2: Switch through $piece_type. Now that we know what type of piece we're dealing with, we can get the
			 * markup for the piece and add the markup to the markup_array in the right way.
			 */
			switch ( $piece_type ) {

				case 'name-only':

					$atom_name             = $piece_args_and_content;
					$atom_args['tag_type'] = 'split';

					break;

				case 'name-and-content':

					$atom_name            = $piece_name;
					$atom_args['content'] = $piece_args_and_content;

					break;

				case 'split-parent':

					$atom_name             = $piece_name;
					$atom_args['tag_type'] = 'split';

					break;

				case 'split-container':

					$atom_name             = $piece_name;
					$atom_args['tag_type'] = 'split';

					break;

			}

			/*
			 * Part 3: Get markup and add it to markup_array.
			 */
			$markup_arr[ $atom_name ] = [ ];

			switch ( $piece_type ) {

				case 'name-only':

					$markup_arr[ $atom_name ] = self::getStructurePart( $atom_name, $atom_args, $post );

					break;

				case 'name-and-content':

					$markup_arr[ $atom_name ]['parts'][ $atom_name ] = self::getStructurePart( $atom_name, $atom_args, $post );

					break;

				case 'split-parent':

					$markup_arr[ $atom_name ] = self::getStructurePart( $atom_name, $atom_args, $post );

					$markup_arr[ $atom_name ]['children'] = $piece_args_and_content['children'];

					break;

				case 'split-container':

					$markup_arr[ $atom_name ] = self::getStructurePart( $atom_name, $atom_args, $post );

					foreach ( $piece_args_and_content['parts'] as $subatom_name => $subatom_args ) {

						if ( is_array( $subatom_args ) ) {
							$subatom_valid_name = $subatom_name;
							$subatom_valid_args = $subatom_args;
						}
						if ( is_string( $subatom_args ) ) {
							$subatom_valid_name = $subatom_args;
							$subatom_valid_args = [ ];
						}

						$markup_arr[ $atom_name ]['parts'][ $subatom_valid_name ] = self::getStructurePart( $subatom_valid_name, $subatom_valid_args, $post );

					}

					break;
			}

			/*
			 * name-and-content atoms need a sibling property in order to reference the next piece when compiling the markup.
			 */
			if ( ! empty( $previous_atom_name ) ) {

				if ( 'name-and-content' == $markup_arr[ $previous_atom_name ]['piece_type'] ) {
					$markup_arr[ $previous_atom_name ]['sibling'] = $atom_name;
				}
			}

			/*
			 * The piece type is added to the atom information, which is useful in the case of 'name-and-content' parts,
			 * which need a dynamic 'sibling' setting for the recursive assembly function to work.
			 */
			$markup_arr[ $atom_name ]['piece_type'] = $piece_type;

			/*
			 * Set for the case of 'name-and-content' atoms.
			 */
			$previous_atom_name = $atom_name;

		} // foreach $structure_pieces

		/*
		 * The markup array is dynamically set based on the $markup_array_name argument. Useful for separating the
		 * organism structure and posts structure.
		 */
		$this->$markup_array_name = $markup_arr;

		return self::compileMarkupArray( $markup_array_name );

	}

	/**
	 * getStructurePart
	 *
	 * Get a part of the structure: returns either a plain atom or a named atom based on the args.
	 *
	 * @since 0.1.0
	 * @see CNP/Atom
	 *
	 * @param string $atom_name The base atom name, modified to be namespaced by the organism name.
	 * @param array $atom_args The atom args
	 * @param WP_Post $post Post object
	 *
	 * @return mixed
	 */
	protected function getStructurePart( $atom_name, $atom_args, $post ) {

		// First, namespace the atom based on the organism name.
		$namespaced_atom_name = $this->name . '-' . $atom_name;

		// Set up the class to check against
		$class_atom_name = 'CNP\\' . $atom_name;


		// Parse atom arguments
		if ( ! isset( $atom_args['attributes']['class'] ) ) {
			$atom_args['attributes']['class'] = [ ];
		}

		// Shorthand for class
		if ( isset( $atom_args['class'] ) ) {

			if ( is_string( $atom_args['class'] ) ) {
				$atom_args['attributes']['class'][] = $atom_args['class'];
			}

			if ( is_array( $atom_args['class'] ) ) {

				foreach ( $atom_args['class'] as $class ) {
					$atom_args['attributes']['class'][] = $class;
				}
			}
		}

		// Set up the atom class.
		$atom_args['attributes']['class'][] = $namespaced_atom_name;

		// If the class exists, then it's a named atom, and we need to
		// run the getMarkup method based on the namespaced atom name.
		if ( class_exists( $class_atom_name ) ) {

			$atom_object       = new $class_atom_name( $atom_args );
			$atom_object->name = $namespaced_atom_name;
			$atom_object->getMarkup();

			return $atom_object->markup;

		}

		// If the class does not exist, then it's a generic atom.
		// Run it through the Atom class to assemble it
		if ( ! class_exists( $class_atom_name ) ) {

			$atom = Atom::Assemble( $namespaced_atom_name, $atom_args );

			return $atom;
		}
	}

	/**
	 * compileMarkupArray
	 *
	 * Compiles the markup array by passing off the first piece to a recursive function, and returns the compiled markup string.
	 *
	 * @since 0.2.0
	 *
	 * @param string $markup_array_name The name of the markup array to compile.
	 *
	 * @return string $compiled_markup  The compiled markup.
	 */
	protected function compileMarkupArray( $markup_array_name ) {

		$markup_array = $this->$markup_array_name;

		$first_piece = array_shift( $markup_array );

		$compiled_markup = self::recursiveAssemblePieces( $first_piece, $markup_array_name );

		return $compiled_markup;

	}


	/**
	 * recursiveAssemblePieces
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
	protected function recursiveAssemblePieces( $organism_part, $markup_array_name, $markup = '' ) {

		$markup_array = $this->$markup_array_name;

		if ( isset( $organism_part['open'] ) ) {
			$markup .= $organism_part['open'];
		}

		switch ( $organism_part['piece_type'] ) {

			case 'name-only':

				// Keys: 'open' and 'close'

				break;

			case 'name-and-content':

				// Keys: 'parts' and 'sibling'
				if ( isset( $organism_part['parts'] ) ) {

					foreach ( $organism_part['parts'] as $piece ) {
						$markup .= $piece;
					}

					if ( isset( $organism_part['sibling'] ) ) {
						$markup .= self::recursiveAssemblePieces( $markup_array[ $organism_part['sibling'] ], $markup_array_name );
					}
				}

				break;

			case 'split-parent':

				// Keys: 'open', 'close' and 'children'
				if ( isset( $organism_part['children'] ) ) {

					$child = $organism_part['children'];

					if ( is_string( $child ) ) {
						$markup .= self::recursiveAssemblePieces( $markup_array[ $child ], $markup_array_name );
					}

					if ( is_array( $child ) ) {

						foreach ( $child as $piece ) {

							$markup .= self::recursiveAssemblePieces( $markup_array[ $piece ], $markup_array_name );
						}
					}
				}

				break;

			case 'split-container':

				// Keys: 'open', 'close' and 'parts'
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

		// Return the completed string.
		return $markup;

	}
}