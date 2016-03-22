<?php
namespace CNP;

/**
 * Subnav.
 *
 * Build a context-aware subnav.
 *
 * @since 0.5.0
 *
 * Issues:
 * - Need a way to pass in Organism args based on behavior.
 * - Need a way to specify title, better title defaults.
 * - Need a way to add manual items.
 * - Need to add support for the Foundation Drilldown/Accordion navs: probably add an Organism arg for that.
 * X Need to pass along OrganismName as a prefix to the sub-atoms for the list items.
 */
class Subnav extends OrganismTemplate {

	public $behavior;
	public $settings;
	public $title = 'In this Section';
	public $list = '';
	public $settings_by_content;

	public function __construct( $data = [ ] ) {

		parent::__construct( $data );

		if ( ! isset( $data['name'] ) ) {
			$this->name = 'subnav';
		}

		// May need to refactor the way the SubnavType is determined in order for settings like this to function properly
		$default_behaviors = [
			'front-page'       => [
				'behavior' => 'none'
			],
			'home'             => [
				'behavior' => 'archive-post_type',
				'taxonomy' => 'category',
				'title'    => 'All Post Categories'
			],
			'404' => [
				'behavior' => 'none'
			],
			'search' => [
				'behavior' => 'none'
			],
			'post'             => [
				'single' => [
					'behavior' => 'archive-post_type',
					'taxonomy' => 'category',
					'title'    => 'All Post Categories'
				]
			],
			'category'         => [
				'behavior' => 'none'
			],
			'tag'              => [
				'behavior' => 'none'
			],
			'page'             => [
				'single' => [
					'title' => 'Pages in this Section'
				]
			],
			'tribe_events'     => [
				'single'  => [
					'behavior' => 'archive-post_type',
					'taxonomy' => 'tribe_events_cat',
					'title'    => 'Event Categories'
				],
				'archive' => [
					'taxonomy' => 'tribe_events_cat',
					'title'    => 'Event Categories'
				]
			],
			'tribe_events_cat' => [
				'behavior' => 'none'
			]
		];

		if ( isset( $data['settings_by_content'] ) ) {
			$this->settings_by_content = wp_parse_args( $default_behaviors, $data['settings_by_content'] );
		} else {
			$this->settings_by_content = $default_behaviors;
		}
	}

	public function getMarkup() {

		// Match up the subnav settings with the current page.
		self::determineSubnavSettings();

		if ( isset($this->settings['behavior'])) {
			$this->behavior = $this->settings['behavior'];
		} else {
			self::determineFallbackSubnavType();
		}

		if ( 'none' === $this->behavior ) {
			return false;
		}

		if ( isset($this->settings['title']) ) {
			$this->title = $this->settings['title'];
		}

		// Get the subnav items, based on the type of subnav
		self::getSubnavItems();

		if ( '' === $this->list ) {
			return false;
		}

		if ( ! isset( $data['structure'] ) ) {

			$this->structure = [
				'title' => [
					'tag'     => 'h4',
					'content' => $this->title,
					'sibling' => 'items'
				]
			];

		}

		$this->structure['items'] = $this->list;

		parent::getMarkup();

	}

	private function determineSubnavSettings() {

		$queried_object = get_queried_object();

		$post_type = '';
		$taxonomy = '';
		if ( isset( $queried_object->post_type ) ) {
			$post_type = $queried_object->post_type;
		}
		if ( isset( $queried_object->taxonomy ) ) {
			$taxonomy = $queried_object->taxonomy;
		}

		$settings = '';

		if ( is_front_page() && isset( $this->settings_by_content['front-page'] ) ) {
			$settings = $this->settings_by_content['front-page'];
		}
		if ( is_home() && isset( $this->settings_by_content['home'] ) ) {
			$settings = $this->settings_by_content['home'];
		}
		if ( is_singular() && '' !== $post_type && isset( $this->settings_by_content[ $post_type ]['single'] ) ) {
			$settings = $this->settings_by_content[ $post_type ]['single'];
		}
		if ( is_post_type_archive() && '' !== $post_type && isset( $this->settings_by_content[ $post_type ]['archive'] ) ) {
			$settings = $this->settings_by_content[ $post_type ]['archive'];
		}
		if ( is_tax() && '' !== $taxonomy && isset( $this->settings_by_content[ $taxonomy ] ) ) {
			$settings = $this->settings_by_content[ $taxonomy ];
		}
		if ( is_category() && isset( $this->settings_by_content['category'] ) ) {
			$settings = $this->settings_by_content['category'];
		}
		if ( is_tag() && isset( $this->settings_by_content['tag'] ) ) {
			$settings = $this->settings_by_content['tag'];
		}
		if ( is_404() && isset( $this->settings_by_content['404'] ) ) {
			$settings = $this->settings_by_content['404'];
		}
		if ( is_search() && isset( $this->settings_by_content['search'] ) ) {
			$settings = $this->settings_by_content['search'];
		}

		if ( '' !== $settings ) {
			$this->settings = $settings;
		}
	}

	private function determineFallbackSubnavType() {

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
		Atom::AddDebugEntry( 'Filter,', 'subnav_location' );

		$subnav_location_filter = $this->name . '_subnav_location';
		$behavior               = apply_filters( $subnav_location_filter, $behavior );
		Atom::AddDebugEntry( 'Filter,', $subnav_location_filter );

		if ( '' === $behavior ) {
			return false;
		} else {
			$this->behavior = $behavior;
		}

	}

	private function getSubnavItems() {

		$queried_object = get_queried_object();

		$list_atom = '';
		$list_args = [
			'tag_type' => 'false_without_content'
		];

		switch ( $this->behavior ) {

			case 'archive-home':

				$list_atom             = 'ListTerms';
				$list_atom_slug        = 'list-terms';
				$list_args['taxonomy'] = 'category';

				break;

			case 'archive-post_type':

				$list_atom      = 'ListTerms';
				$list_atom_slug = 'list-terms';

				$hierarchical_taxonomies = [ ];


				if ( ! empty( $queried_object->taxonomies ) ) {

					foreach ( $queried_object->taxonomies as $taxonomy_name ) {

						if ( is_taxonomy_hierarchical( $taxonomy_name ) ) {
							$hierarchical_taxonomies[] = $taxonomy_name;
						}
					}

					if ( ! empty( $hierarchical_taxonomies ) ) {
						$list_args['taxonomy'] = $hierarchical_taxonomies[0];
					}
				}

				break;

			case 'archive-taxonomy':

				$list_atom             = 'ListTerms';
				$list_atom_slug        = 'list-terms';
				$list_args['taxonomy'] = $queried_object->taxonomy;

				break;

			case 'single-nonhierarchical':

				$list_atom              = 'TaxonomyList';
				$list_atom_slug         = 'taxonomy-list';
				$list_args['tag']       = 'ul';
				$list_args['before']    = '<li>';
				$list_args['separator'] = '</li><li>';
				$list_args['after']     = '</li>';
				// TaxonomyList auto-detects the taxonomy based on the post.

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

				$list_args['list_args']['child_of'] = $id;

				break;
		}

		$list_atom_class = 'CNP\\' . $list_atom;

		$namespaced_list_atom_slug = $this->name . $this->separator . $list_atom_slug;
		$list_args['name']         = $list_atom_slug;

		$list = new $list_atom_class( $list_args );
		$list->getMarkup();

		$this->list = trim( $list->markup );

	}
}