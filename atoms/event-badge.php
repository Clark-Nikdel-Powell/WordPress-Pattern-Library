<?php
namespace CNP;

/**
 * EventBadge.
 *
 * Returns abbreviated event date information for use in a "badge" format, like "Mar 10."
 *
 * Parent: EventDate
 *
 * @since 0.5.0
 *
 * @param array $badge_pieces An array of the pieces to the badge. Keyed by the date part type (Month, Day, Year, etc)
 */
class EventBadge extends EventDate {

	public $badge_pieces;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( ! isset( $data['name'] ) ) {
			$this->name = 'eventbadge';
		}

		$this->tag = 'p';

		if ( isset( $data['badge_pieces'] ) ) {
			$badge_pieces_arr = $data['badge_pieces'];
		} else {
			$badge_pieces_arr = [
				'month' => date( 'F', $this->event_start_date ),
				'day'   => date( 'd', $this->event_start_date ),
			];
		}

		$badge_pieces_arr_filter = $this->name . '_badge_pieces_arr';
		$badge_pieces_arr        = apply_filters( $badge_pieces_arr_filter, $badge_pieces_arr );
		Atom::add_debug_entry( 'Filter', $badge_pieces_arr_filter );

		$badge_pieces_markup_arr = array();

		foreach ( $badge_pieces_arr as $badge_label => $badge_piece ) {

			$format                                  = '<span class="' . $this->name . '__%1$s">%2$s</span>';
			$badge_pieces_markup_arr[ $badge_label ] = sprintf( $format, $badge_label, $badge_piece );

		}

		$this->badge_pieces = $badge_pieces_markup_arr;

		$this->content = implode( '', $this->badge_pieces );

	}
}
