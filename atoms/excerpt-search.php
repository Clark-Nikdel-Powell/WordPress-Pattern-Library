<?php
namespace CNP;

/**
 * ExcerptSearch.
 *
 * Searches the post content and finds the search terms.
 *
 * Children: ForceExcerpt
 *
 * @since 0.5.0
 */
class ExcerptSearch extends Excerpt {

	public $chars_before;
	public $chars_total;

	public function __construct( $data ) {

		parent::__construct( $data );

		// Get the search term
		$search_term = get_query_var( 's' );

		// Sanitize the search term
		$key = esc_html( $search_term, 1 );

		// Number of characters before the highlighted text.
		$this->chars_before = isset( $data['chars_before'] ) ? $data['chars_before'] : 100;

		// Excerpt total characters
		$this->chars_total = isset( $data['chars_total'] ) ? $data['chars_total'] : 250;

		// Retrieve content and strip out all HTML
		$content = strip_shortcodes( strip_tags( get_the_content() ) );
		$content = preg_replace( '/\b(https?):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $content );
		$content = preg_replace( '|www\.[a-z\.0-9]+|i', '', $content );

		// Get the position of the key inside of the content.
		$key_position = stripos( $content, $key );
		$start        = 0;
		$length       = $this->chars_total;
		$before       = '';
		$after        = ' &hellip;';

		// If the key position is somewhere inside the content,
		// the starting position is calculated based on the charsBefore value,
		// and the SearchExcerpt needs a ellipsis prepended to it.
		if ( $key_position >= $this->chars_before ) {
			$start  = $key_position - $this->chars_before;
			$before = '&hellip; ';
		}

		// If our projected length is longer than the content string, then we don't need an ellipsis afterward,
		// and the length of the substr needs to be adjusted.
		if ( ( $start + $this->chars_total ) > strlen( $content ) ) {
			$length = strlen( $content ) - $start;
			$after  = '';
		}

		// Get the part of the content that we'll use for the SearchExcerpt
		$search_excerpt_raw = substr( $content, $start, $length );

		// Find matches for the search term.
		preg_match_all( "/$key+/i", $search_excerpt_raw, $matches );

		$search_excerpt_highlights = $search_excerpt_raw;

		// If we have matches (we should), add a span to each match for special styles.
		if ( is_array( $matches[0] ) && count( $matches[0] ) >= 1 ) {
			foreach ( $matches[0] as $match ) {
				$search_excerpt_highlights = str_replace( $match, '<strong class="highlighted">' . $match . '</strong>', $search_excerpt_raw );
			}
		}

		// Build the search excerpt.
		$search_excerpt = $before . $search_excerpt_highlights . $after;

		$this->content = $search_excerpt;

	}
}
