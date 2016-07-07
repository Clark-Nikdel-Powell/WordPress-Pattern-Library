<?php
namespace CNP;

/**
 * Subnav.
 *
 * Build a context-aware subnav.
 *
 * You can configure the subnav behavior for all of the site's views by using the "settings_by_content_type" parameter.
 *
 * The "behavior" parameter passed in through the settings is used so that we don't have to specify all the atom arguments
 * for every different view. The current defined behaviors are:
 *
 * archive-home: uses ListTerms to build a list of terms for a given taxonomy. Defaults to 'category'.
 * archive-post_type: uses ListTerms to build a list of terms for a given taxonomy. Defaults to first taxonomy associated with the post type, if one is not supplied.
 * archive-taxonomy: uses ListTerms to build a list of terms for the current taxonomy page.
 * single-nonhierarchical: uses TaxonomyList to build a list of taxonomy terms based on terms the post has.
 * single-hierarchical: uses ListPages to build a list of posts that are children of the current post.
 *
 * @since 0.5.0
 *
 * Issues:
 * - Need a way to add manual items.
 * - Need to add support for the Foundation Drilldown/Accordion navs: probably add an Organism arg for that.
 * X Need to pass along OrganismName as a prefix to the sub-atoms for the list items.
 */
class Subnav extends OrganismTemplate {

	private $list_args;

	public $behavior;
	public $settings;
	public $title = 'In this Section';
	public $list = '';
	public $settings_by_content_type;
	public $manual_additions;

	public function __construct( $data = array() ) {

		parent::__construct( $data );

		if ( ! isset( $data['name'] ) ) {
			$this->name = 'subnav';
		}

		// Add the Organism name as a class
		$this->attributes['class'][] = $this->name;

		// May need to refactor the way the SubnavType is determined in order for settings like this to function properly
		$default_behaviors = [
			'front-page'       => [
				'behavior' => 'none',
			],
			'home'             => [
				'behavior' => 'archive-post_type',
				'taxonomy' => 'category',
				'title'    => 'All Post Categories',
			],
			'404'              => [
				'behavior' => 'none',
			],
			'search'           => [
				'behavior' => 'none',
			],
			'post'             => [
				'single' => [
					'behavior' => 'single-nonhierarchical',
					'taxonomy' => 'category',
					'title'    => 'Post Categories',
				],
			],
			'category'         => [
				'behavior' => 'none',
			],
			'tag'              => [
				'behavior' => 'none',
			],
			'page'             => [
				'single' => [
					'title' => 'Pages in this Section',
				],
			],
			'tribe_events'     => [
				'single'  => [
					'behavior' => 'archive-post_type',
					'taxonomy' => 'tribe_events_cat',
					'title'    => 'Event Categories',
				],
				'archive' => [
					'taxonomy' => 'tribe_events_cat',
					'title'    => 'Event Categories',
				],
			],
			'tribe_events_cat' => [
				'behavior' => 'none',
			],
		];

		// Parse custom content settings.
		if ( isset( $data['settings_by_content_type'] ) ) {
			$this->settings_by_content_type = array_replace_recursive( $default_behaviors, $data['settings_by_content_type'] );
		} else {
			$this->settings_by_content_type = $default_behaviors;
		}

		// Figure out which page we're dealing with here.
		self::determine_subnav_settings();

		// Parse global list args.
		if ( isset( $data['list_args'] ) ) {
			$this->list_args = $data['list_args'];
		}
		// Merge any post-type view-specific list args into the defaults.
		if ( isset( $this->settings['list_args'] ) ) {
			$this->list_args = array_merge( $this->list_args, $this->settings['list_args'] );
		}

		if ( isset( $this->settings['behavior'] ) ) {
			$this->behavior = $this->settings['behavior'];
		} else {
			self::determine_fallback_subnav_type();
		}

		/* @EXIT: "none" tells us that there isn't supposed to be a subnanv here. */
		if ( 'none' === $this->behavior ) {
			return false;
		}

		if ( isset( $this->settings['title'] ) ) {
			$this->title = $this->settings['title'];
		}

		$this->manual_additions = array();
		if ( isset( $data['manual_additions'] ) ) {
			$this->manual_additions = $data['manual_additions'];
		}

		// Get the subnav items, based on the type of subnav
		self::get_subnav_items();

		// Return false if there is no list AND no manual additions.
		if ( '' === $this->list && empty( $this->manual_additions ) ) {
			return false;
		}

		// Structure setup is left till here so that we don't return an empty list by accident.
		if ( ! isset( $data['structure'] ) ) {

			// 'List' is initialized here so that manual items can be added after it.
			$this->structure = [
				'title' => [
					'tag'     => 'h4',
					'content' => $this->title,
					'sibling' => 'items',
				],
				'items' => [
					'parts' => [
						'list',
					],
				],
			];

			// Add a separator and the manual items after the main list.
			if ( ! empty( $this->manual_additions ) ) {
				$this->structure['items']['parts']['separator'] = '';
				$this->structure['items']['parts']['manual']    = $this->manual_additions;
			}
		} else {
			$this->structure = $data['structure'];
		}

		// Add the list in separately, so that different structure can be passed in independent from the list.
		// It is up to the dev to take care that "items" is listed as a child or sibling.
		$this->structure['items']['parts']['list'] = $this->list;

		return true;

	}

	private function determine_subnav_settings() {

		$queried_object = get_queried_object();

		$post_type = '';
		$taxonomy  = '';
		// Get the post type from a WP_Post object
		if ( isset( $queried_object->post_type ) ) {
			$post_type = $queried_object->post_type;
		}
		// Get the post type from a WP_Post_Type object.
		if ( is_post_type_archive() && isset( $queried_object->name ) ) {
			$post_type = $queried_object->name;
		}
		// Get the taxonomy from a WP_Term object
		if ( isset( $queried_object->taxonomy ) ) {
			$taxonomy = $queried_object->taxonomy;
		}

		$settings = array();

		// There's probably a better way to write these checks...
		if ( is_front_page() && isset( $this->settings_by_content_type['front-page'] ) ) {
			$settings = $this->settings_by_content_type['front-page'];
		}
		if ( is_home() && isset( $this->settings_by_content_type['home'] ) ) {
			$settings = $this->settings_by_content_type['home'];
		}
		if ( is_singular() && '' !== $post_type && isset( $this->settings_by_content_type[ $post_type ]['single'] ) ) {
			$settings = $this->settings_by_content_type[ $post_type ]['single'];
		}
		if ( is_post_type_archive() && '' !== $post_type && isset( $this->settings_by_content_type[ $post_type ]['archive'] ) ) {
			$settings = $this->settings_by_content_type[ $post_type ]['archive'];
		}
		if ( is_tax() && '' !== $taxonomy && isset( $this->settings_by_content_type[ $taxonomy ] ) ) {
			$settings = $this->settings_by_content_type[ $taxonomy ];
		}
		if ( is_category() && isset( $this->settings_by_content_type['category'] ) ) {
			$settings = $this->settings_by_content_type['category'];
		}
		if ( is_tag() && isset( $this->settings_by_content_type['tag'] ) ) {
			$settings = $this->settings_by_content_type['tag'];
		}
		if ( is_404() && isset( $this->settings_by_content_type['404'] ) ) {
			$settings = $this->settings_by_content_type['404'];
		}
		if ( is_search() && isset( $this->settings_by_content_type['search'] ) ) {
			$settings = $this->settings_by_content_type['search'];
		}

		if ( '' !== $settings ) {
			$this->settings = $settings;
		}
	}

	private function determine_fallback_subnav_type() {

		global $post;

		$behavior = '';

		if ( is_home() ) {
			$behavior = 'archive-home';
		}
		if ( is_post_type_archive() ) {
			$behavior = 'archive-post_type';
		}
		if ( is_tax() || is_category() ) {
			$behavior = 'archive-taxonomy';
		}
		if ( is_singular() ) {

			if ( is_post_type_hierarchical( $post->post_type ) ) {
				$behavior = 'single-hierarchical';
			} else {
				$behavior = 'single-nonhierarchical';
			}
		}

		$behavior = apply_filters( 'subnav_location', $behavior );
		Atom::add_debug_entry( 'Filter,', 'subnav_location' );

		$subnav_location_filter = $this->name . '_subnav_location';
		$behavior               = apply_filters( $subnav_location_filter, $behavior );
		Atom::add_debug_entry( 'Filter,', $subnav_location_filter );

		if ( '' === $behavior ) {
			return false;
		} else {
			$this->behavior = $behavior;
		}

	}

	private function get_subnav_items() {

		$queried_object = get_queried_object();

		$list_atom      = '';
		$list_atom_slug = 'list-terms';
		$list_atom_args = [
			'tag_type'  => 'false_without_content',
			'list_args' => '',
		];

		switch ( $this->behavior ) {

			case 'archive-home':

				$list_atom = 'ListTerms';

				break;

			case 'archive-post_type':

				$list_atom = 'ListTerms';

				break;

			case 'archive-taxonomy':

				$list_atom = 'ListTerms';

				break;

			case 'single-nonhierarchical':

				$list_atom                   = 'TaxonomyList';
				$list_atom_slug              = 'taxonomy-list';
				$list_atom_args['tag']       = 'ul';
				$list_atom_args['before']    = '<li>';
				$list_atom_args['separator'] = '</li><li>';
				$list_atom_args['after']     = '</li>';

				break;

			case 'single-hierarchical':

				$list_atom      = 'ListPages';
				$list_atom_slug = 'list-pages';

				$ancestors = get_post_ancestors( $queried_object->ID );

				if ( ! empty( $ancestors ) ) {
					$id = array_pop( $ancestors );
				} else {
					$id = $queried_object->ID;
				}

				$list_atom_args['list_args']['child_of'] = $id;

				break;
		}

		if ( isset( $this->settings['taxonomy'] ) ) {
			$list_atom_args['taxonomy'] = $this->settings['taxonomy'];
		}

		$list_atom_class = 'CNP\\' . $list_atom;

		$namespaced_list_atom_slug = $this->name . $this->separator . $list_atom_slug;
		$list_atom_args['name']    = $namespaced_list_atom_slug;

		// Pass the parsed list_args into the org args
		$list_atom_args['list_args'] = $this->list_args;

		$list = new $list_atom_class( $list_atom_args );
		$list->get_markup();

		$this->list = trim( $list->markup );

	}
}
