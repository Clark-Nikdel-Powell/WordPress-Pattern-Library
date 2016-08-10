<?php
namespace CNP;

class ACF_Tabs extends OrganismTemplate {

	public $tabs;
	public $tab_heading_args;
	public $tab_organism_args;

	public function __construct( $data ) {

		if ( ! isset( $data['name'] ) || empty( $data['name'] ) ) {
			$data['name'] = 'acf-tabs';
		}

		$this->tabs = ! empty( $data['tabs'] ) ? $data['tabs'] : array();

		// Isset check in case this class is extended.
		if ( ! isset( $data['structure'] ) ) {
			$data['structure'] = [
				'background' => [
					'tag_type' => 'false_without_content',
					'sibling'  => 'headings',
				],
				'headings'   => [
					'tag'        => 'ul',
					'attributes' => [
						'data-tabs' => '',
						'id'        => '',
						'class'     => [ 'tabs' ],
					],
					'content'    => '',
					'sibling'    => 'content',
				],
				'content'    => [
					'attributes' => [
						'data-tabs-content' => '',
						'class'             => [ 'tabs-content' ],
					],
					'content'    => '',
				],
			];
		}

		if ( ! isset( $data['tab_heading_args'] ) ) {
			$data['tab_heading_args'] = [
				'name'       => $this->name . $this->separator . 'heading',
				'attributes' => [
					'class' => [ 'tabs-title' ],
				],
				'tag'        => 'li',
				'separator'  => '-',
				'structure'  => [
					'link' => [
						'atom'     => 'Link',
						'tag_type' => 'false_without_content',
						'href'     => '#panel',
						'content'  => '',
					],
				],
			];
		}

		// Isset check in case this class is extended.
		if ( ! isset( $data['tab_organism_args'] ) ) {
			$data['tab_organism_args'] = [
				'name'       => $this->name . $this->separator . 'tab',
				'separator'  => '-',
				'attributes' => [
					'id'    => 'panel',
					'class' => [ 'tabs-panel' ],
				],
				'tag'        => 'div',
				'structure'  => [
					'content' => [
						'parts' => [
							'title'    => [
								'tag_type' => 'false_without_content',
								'content'  => '',
							],
							'subtitle' => [
								'tag_type' => 'false_without_content',
								'content'  => '',
							],
							'text'     => [
								'tag_type' => 'false_without_content',
								'content'  => '',
							],
							'link'     => [
								'atom'     => 'Link',
								'tag_type' => 'false_without_content',
							],
						],
					],
				],
			];
		}

		parent::__construct( $data );

		// Set the property now that we've had a chance to filter it
		$this->tab_heading_args  = $data['tab_heading_args'];
		$this->tab_organism_args = $data['tab_organism_args'];
	}

	/**
	 * get_markup
	 *
	 * Standard get_markup function, adds a check for tabs and generates tabs.
	 *
	 * @throws \Exception
	 */
	public function get_markup() {

		// Test for exceptions before we begin.
		try {

			if ( empty( $this->tabs ) ) {
				throw new \Exception( 'No tabs found.' );
			}
		} catch ( \Exception $e ) {
			echo '<!-- Tabs failed: ', $e->getMessage(), '-->', "\n";
		}

		foreach ( $this->tabs as $tab_index => $tab_data ) {
			$tab_heading_args = $this->tab_heading_args;
			$tab_args         = $this->tab_organism_args;
			$this::generate_tab( $tab_heading_args, $tab_args, $tab_data, $tab_index );
		}

		parent::get_markup();

	}

	/**
	 * generate_tab
	 *
	 * Generates tab markup, then adds it to the tabs container.
	 *
	 * @param $tab_args
	 * @param $tab_data
	 * @param $tab_index
	 */
	public function generate_tab( $tab_heading_args, $tab_args, $tab_data, $tab_index ) {

		$tab_number = $tab_index + 1;

		// Trim all tab data first, so that an empty space doesn't get used as content by mistake.
		$tab_data = Utility::multidimensional_array_map( 'trim', $tab_data );

		$tab_slug = str_replace( ' ', '-', preg_replace( '/[^A-Za-z0-9 ]/', '', strtolower( $tab_data['tab_title'] ) ) );

		// Set ID on Heading & Content
		$tab_heading_args['structure']['link']['href'] = '#' . $tab_slug;
		$tab_args['attributes']['id']                  = $tab_slug;

		// Set "is-active" class for first tab
		if ( 1 === $tab_number ) {
			$tab_heading_args['attributes']['class'][] = 'is-active';
			$tab_args['attributes']['class'][]         = 'is-active';
		}

		/*——————————————————————————————————————————
		/  Tab Heading
		——————————————————————————————————————————*/

		// Set Tab Title
		$tab_heading_args['structure']['link']['content'] = $tab_data['tab_title'];

		$tab_heading = new OrganismTemplate( $tab_heading_args );
		$tab_heading->get_markup();

		$this->structure['headings']['content'] .= $tab_heading->markup;

		/*——————————————————————————————————————————
		/  Tab Content
		——————————————————————————————————————————*/

		// Set Title
		$tab_args['structure']['content']['parts']['title']['content'] = $tab_data['title'];

		// Set Subtitle
		$tab_args['structure']['content']['parts']['subtitle']['content'] = $tab_data['subtitle'];

		// Set Text
		$tab_args['structure']['content']['parts']['text']['content'] = $tab_data['text'];

		// Set Link: set URL first, then set default text
		$tab_args['structure']['content']['parts']['link']['href'] = $tab_data['link'];

		if ( ! empty( $tab_args['structure']['content']['parts']['link']['href'] ) ) {
			// An empty array is used for the unset value because the backup text will be used if content is not present.
			$tab_args = Utility::set_or_unset( $tab_data['link_text'], $tab_args, array(), [ 'structure', 'content', 'parts', 'link', 'content' ], 'Click Here' );
		}

		$tab = new OrganismTemplate( $tab_args );
		$tab->get_markup();

		$this->structure['content']['content'] .= $tab->markup;
	}
}
