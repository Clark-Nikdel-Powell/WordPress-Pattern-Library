<?php
namespace CNP;

class ACF_Slideshow extends OrganismTemplate {

	public $slides;
	public $slide_args;

	public function __construct( $data ) {

		// Set the name before the parent construct so that default classes can get added.
		if ( ! isset( $data['name'] ) ) {
			$this->name = 'slideshow';
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
		$this->slide_args = [
			'name'       => $this->name . $this->separator . 'slide',
			'attributes' => [ ],
			'structure'  => [
				'background' => [
					'sibling' => 'text'
				],
				'text'       => [
					'parts' => [
						'title'       => [
							'tag'     => 'h2',
							'content' => ''
						],
						'subtitle'    => [
							'tag'     => 'h3',
							'content' => ''
						],
						'description' => [
							'tag'     => 'div',
							'content' => ''
						],
						'link'        => [
							'atom'    => 'Link',
							'href'    => '',
							'content' => ''
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
			$slide_args = $this->slide_args;
			self::generateSlide( $slide_args, $slide_data, $slide_index );
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

		$slide_data = Utility::multidimensionalArrayMap( 'trim', $slide_data );

		// Put this in a separate method so that it's less to copy/paste when extending the class.
		$slide_args = self::setSlideClassesAndID( $slide_args, $slide_data );

		// Set Background
		$slide_args = self::setSlideBackground( 'background', $slide_args, $slide_data );

		// Set Title
		$slide_args = Utility::setOrUnset( $slide_data['title'], $slide_args, [ 'structure', 'text', 'parts', 'title' ], [ 'structure', 'text', 'parts', 'title', 'content' ] );

		// Set Subtitle
		$slide_args = Utility::setOrUnset( $slide_data['subtitle'], $slide_args, [ 'structure', 'text', 'parts', 'subtitle' ], [ 'structure', 'text', 'parts', 'subtitle', 'content' ] );

		// Set Description
		$slide_args = Utility::setOrUnset( $slide_data['description'], $slide_args, [ 'structure', 'text', 'parts', 'description' ], [ 'structure', 'text', 'parts', 'description', 'content' ] );

		// Set Link: set URL first, then set default text
		$slide_args = Utility::setOrUnset( $slide_data['link'], $slide_args, [ 'structure', 'text', 'parts', 'link' ], [ 'structure', 'text', 'parts', 'link', 'href' ] );

		if ( isset( $slide_args['structure']['text']['parts']['link']['href'] ) ) {
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

	public function setSlideBackground( $background_key, $slide_args, $slide_data ) {

		// Set in local variable so we don't accidentally overwrite it.
		$background_type = $slide_data['background_type'];

		// If there is no background, get rid of the atom.
		if ( 'None' === $background_type ) {
			unset( $slide_args['structure'][ $background_key ] );

			return $slide_args;
		}

		if ( 'Image' === $background_type ) {

			// If there's no image set, get rid of the atom.
			if ( empty( $slide_data['image'] ) ) {
				unset( $slide_args['structure'][ $background_key ] );

				return $slide_args;
			}

			// Items are handled one-by-one so we don't accidentally overwrite preset array values.
			$slide_args['structure'][ $background_key ]['atom'] = 'Image';

			// Attachment ID
			$slide_args['structure'][ $background_key ]['attachment_id'] = $slide_data['image'];

			// Image size: preset to full, TODO: should make sure we have a way to filter if necessary
			$slide_args['structure'][ $background_key ]['size'] = 'full';

			// Slideshow backgrounds are often 100% of the viewport.
			$slide_args['structure'][ $background_key ]['attributes'] = [
				'sizes' => '100vw'
			];

		}

		if ( 'Video' === $background_type ) {

			// If there's no video or image files, get rid of the atom.
			if ( empty( $slide_data['mp4'] ) && empty( $slide_data['webm'] ) && empty( $slide_data['jpg'] ) ) {
				unset( $slide_args['structure'][ $background_key ] );

				return $slide_args;
			}

			$slide_arg['structure'][ $background_key ]['atom'] = 'BackgroundVideo';

			if ( ! empty( $slide_data['mp4'] ) ) {
				$slide_args['structure'][ $background_key ]['mp4'] = 'mp4:' . $slide_data['mp4']['url'];
			}
			if ( ! empty( $slide_data['webm'] ) ) {
				$slide_args['structure'][ $background_key ]['webm'] = 'webm:' . $slide_data['webm']['url'];
			}
			if ( ! empty( $slide_data['jpg'] ) ) {
				$slide_args['structure'][ $background_key ]['jpg'] = 'poster:' . $slide_data['jpg']['url'];
			}
		}

		if ( 'Color' === $background_type ) {

			// If there's no color set, get rid of the atom.
			if ( empty( $slide_data['background_color'] ) ) {
				unset( $slide_args['structure'][ $background_key ] );

				return $slide_args;
			}

			$slide_args['structure'][ $background_key ]['attributes']['style'] = 'background-color: ' . $slide_data['background_color'] . ';';

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
	private function parseSlideshowOptionsAsAttribute() {

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

		if ( is_array( $boolean_settings ) ) {
			$boolean_settings = $boolean_settings['slideshow_boolean_options'];
		}

		// Overwrite the default false value with true for each checked value.
		$boolean_vars = [ ];
		foreach ( $boolean_settings as $boolean_setting_key ) {
			$boolean_vars[ $boolean_setting_key ] = true;
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