<?php
namespace CNP;

class ACF_Slideshow extends OrganismTemplate {

	public $slides;
	public $slide_organism_args;

	public function __construct( $data ) {

		// Set the name before the parent construct so that default classes can get added.
		if ( ! isset( $data['name'] ) ) {
			$this->name = 'acf-slideshow';
		}

		parent::__construct( $data );

		/*——————————————————————————————————————————————————————————
		/  Set Slides Property: the slides data
		——————————————————————————————————————————————————————————*/
		$this->slides = ! empty( $data['slides'] ) ? $data['slides'] : [ ];

		/*——————————————————————————————————————————————————————————
		/  Set Slick Attribute
		——————————————————————————————————————————————————————————*/
		$this->attribute_quote_style = "'";
		self::parseSlideshowOptionsAsAttribute();

		/*——————————————————————————————————————————————————————————
		/  Set Default Slideshow Structure
		————————————————————————————————————————————————————————————
		The "content-only" part here is super-important. This way, "slides" doesn't get output directly, but its content does.
		This fits the single-nesting setup that Slick adheres to.
		*/
		$this->structure = [
			'slides' => [
				'tag_type' => 'content-only',
				'content'  => '',
			]
		];

		/*——————————————————————————————————————————————————————————
		/  Set Default Slide Structure
		——————————————————————————————————————————————————————————*/
		$this->slide_organism_args = [
			'name'       => $this->name . $this->separator . 'slide',
			'attributes' => [ ],
			'structure'  => [
				'background' => [
					'sibling' => 'text'
				],
				'text'       => [
					'parts' => [
						'title'       => [
							'tag'      => 'h2',
							'tag_type' => 'false_without_content',
							'content'  => ''
						],
						'subtitle'    => [
							'tag'      => 'h3',
							'tag_type' => 'false_without_content',
							'content'  => ''
						],
						'description' => [
							'tag'      => 'div',
							'tag_type' => 'false_without_content',
							'content'  => ''
						],
						'link'        => [
							'atom'     => 'Link',
							'tag_type' => 'false_without_content',
							'href'     => '',
							'content'  => ''
						]
					]
				]
			]
		];
	}

	/**
	 * getMarkup
	 *
	 * Standard getMarkup function, adds a check for slides and generates slides.
	 *
	 * @throws \Exception
	 */
	public function getMarkup() {

		// Test for exceptions before we begin.
		try {

			if ( empty( $this->slides ) ) {
				throw new \Exception( 'No slides found.' );
			}

		} catch ( Exception $e ) {
			echo '<!-- Slideshow failed: ', $e->getMessage(), '-->', "\n";
		}

		foreach ( $this->slides as $slide_index => $slide_data ) {
			$slide_args = $this->slide_organism_args;
			$this::generateSlide( $slide_args, $slide_data, $slide_index );
		}

		parent::getMarkup();

	}

	/**
	 * This function is meant to be overwritten wholesale for any non-standard
	 *
	 * @param $slide_args
	 * @param $slide_data
	 * @param $slide_index
	 */
	public function generateSlide( $slide_args, $slide_data, $slide_index ) {

		// Trim all slide data first, so that an empty space doesn't get used as content by mistake.
		$slide_data = Utility::multidimensionalArrayMap( 'trim', $slide_data );

		// Put this in a separate method so that it's less to copy/paste when extending the class.
		$slide_args = self::setSlideClassesAndID( $slide_args, $slide_data );

		// Set Background
		$slide_args['structure'] = Helpers::setBackgroundOnStructureArray( $slide_data, 'background', $slide_args['structure'] );

		// Set Title
		$slide_args['structure']['text']['parts']['title']['content'] = $slide_data['title'];

		// Set Subtitle
		$slide_args['structure']['text']['parts']['subtitle']['content'] = $slide_data['subtitle'];

		// Set Description
		$slide_args['structure']['text']['parts']['description']['content'] = $slide_data['description'];

		// Set Link: set URL first, then set default text
		$slide_args['structure']['text']['parts']['link']['href'] = $slide_data['link'];

		if ( ! empty( $slide_args['structure']['text']['parts']['link']['href'] ) ) {
			// An empty array is used for the unset value because the backup text will be used if content is not present.
			$slide_args = Utility::setOrUnset( $slide_data['link_text'], $slide_args, [ ], [ 'structure', 'text', 'parts', 'link', 'content' ], 'Click Here' );
		}

		$slide = new OrganismTemplate( $slide_args );
		$slide->getMarkup();

		$this->structure['slides']['content'] .= $slide->markup;

	}

	public function setSlideClassesAndID( $slide_args, $slide_data ) {

		$slide_classes = Utility::parseClassesAsArray( $slide_data['class'] );

		if ( false !== ( $slide_classes ) ) {
			$slide_args['attributes']['class'] = $slide_classes;
		}

		if ( '' !== $slide_data['id'] ) {
			$id = Atom::getID( $slide_args['name'], $slide_data['id'] );

			if ( '' !== $id ) {
				$slide_args['attributes']['id'] = $id;
			}
		}

		return $slide_args;

	}

	/**
	 * parseSlideshowOptionsAsAttribute
	 *
	 * Finds settings from a Slideshow settings
	 *
	 * These settings don't come from the $data (i.e., from the page itself), but rather from a centralized ACF Options
	 * Page for site-wide Slideshow Settings. If options aren't available from the ACF Options page, they could still
	 * be filtered in or Slick can use the defaults.
	 */
	public function parseSlideshowOptionsAsAttribute() {

		/**
		 * Intialize all the booleans to false-- anything that's checked is set to true.
		 */
		$boolean_defaults = [
			'accessibility'    => false,
			'autoplay'         => false,
			'centerMode'       => false,
			'draggable'        => false,
			'fade'             => false,
			'arrows'           => false,
			'mobileFirst'      => false,
			'infinite'         => false,
			'pauseOnHover'     => false,
			'pauseOnDotsHover' => false,
			'swipe'            => false,
			'swipeToSlide'     => false,
			'touchMove'        => false,
			'useCSS'           => false,
			'variableWidth'    => false,
			'vertical'         => false,
			'verticalSwiping'  => false,
			'rtl'              => false,
			'dots'             => false
		];

		// This will return any key that is set to true.
		$boolean_settings = Utility::getAcfFieldsAsArray( [ 'slideshow_boolean_options' ], true );

		if ( is_null( $boolean_settings ) ) {
			return false;
		}

		if ( is_array( $boolean_settings ) ) {
			$boolean_settings = $boolean_settings['slideshow_boolean_options'];
		}

		// Overwrite the default false value with true for each checked value.
		$boolean_vars = [ ];
		if ( ! empty( $boolean_settings ) ) {
			foreach ( $boolean_settings as $boolean_setting_key ) {
				$boolean_vars[ $boolean_setting_key ] = true;
			}
		}

		// Merge the defaults (everything false) with the true values from our settings.
		$boolean_vars = array_merge( $boolean_defaults, $boolean_vars );

		$settings_keys = [
			'slidesToShow',
			'slidesToScroll',
			'initialSlide',
			'rows',
			'slidesPerRow',
			'pagination_type',
			'cssEase',
			'easing',
			'speed',
			'touchThreshold',
			'edgeFriction',
			'lazyLoad',
			'respondTo',
			'autoplaySpeed',
			'centerPadding',
			'dotsClass'
		];

		// Retrieve string settings data
		$string_vars = Utility::getAcfFieldsAsArray( $settings_keys, true );

		// If both arrays come back empty, something's gone wrong, and we don't need to go through the rest.
		if ( empty( $boolean_vars ) && empty( $string_vars ) ) {
			return false;
		}

		/*——————————————————————————————————————————————————————————
		/  Combine and Encode Slideshow Options
		——————————————————————————————————————————————————————————*/
		$slideshow_vars = array_merge( $boolean_vars, $string_vars );

		if ( 'none' !== $slideshow_vars['pagination_type'] ) {
			$slideshow_vars['dots'] = true;
		}

		// Filter before we switch to JSON
		$slideshow_vars = apply_filters( 'slideshow_organism_vars', $slideshow_vars );
		Atom::AddDebugEntry( 'Filter,', 'slideshow_organism_vars' );

		$slideshow_vars_filter = $this->name . '_slideshow_vars';
		$slideshow_vars        = apply_filters( $slideshow_vars_filter, $slideshow_vars );
		Atom::AddDebugEntry( 'Filter,', $slideshow_vars_filter );

		$acf_slideshow_settings_json = json_encode( $slideshow_vars, JSON_NUMERIC_CHECK );

		$this->attributes['data-slick'] = $acf_slideshow_settings_json;

	}
}
