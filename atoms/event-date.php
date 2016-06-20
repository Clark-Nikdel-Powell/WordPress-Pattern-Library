<?php
namespace CNP;

/**
 * EventDate.
 *
 * Returns a formatted event date based on whether the event is an all-day event, whether it's happening right now,
 * and whether the event start date and end date are on the same day.
 *
 * Children: EventBadge
 *
 * @since 0.5.0
 *
 * @param string $start_date_function The function used to get the event start date.
 * @param string $end_date_function The function used to get the event end date.
 * @param string $all_day_function The function used to check whether the event is an all-day event.
 */
class EventDate extends AtomTemplate {

	private $event_functions;
	public $event_date_type;
	public $event_start_date;
	public $event_end_date;
	public $event_all_day;
	public $event_date_formatted;

	public function __construct( $data ) {

		parent::__construct( $data );

		$this->tag = isset( $data['tag'] ) ? $data['tag'] : 'p';

		$post_id = $this->post_object->ID;

		self::setEventFunctions( $data );

		$this->event_start_date = call_user_func_array( $this->event_functions['start_date_function'], $this->event_functions['date_function_arguments'] );
		$this->event_end_date   = call_user_func_array( $this->event_functions['end_date_function'], $this->event_functions['date_function_arguments'] );
		$this->event_all_day    = call_user_func( $this->event_functions['all_day_function'], $post_id );

		self::setDateType();
		self::setDateFormat();

		$this->content = $this->event_date_formatted;

	}

	private function setEventFunctions( $data ) {

		// Developers pass in functions to get the event start/end date, so that the Atom can be plugin-agnostic.
		$event_functions_arr = array();

		if ( isset( $data['start_date_function'] ) ) {
			$event_functions_arr['start_date_function'] = $data['start_date_function'];
		}
		if ( isset( $data['end_date_function'] ) ) {
			$event_functions_arr['end_date_function'] = $data['end_date_function'];
		}
		if ( isset( $data['all_day_function'] ) ) {
			$event_functions_arr['all_day_function'] = $data['all_day_function'];
		}

		// Get the active plugins.
		$active_plugins_arr = get_option( 'active_plugins' );

		// We do some guessing here for Tzolkin
		if ( in_array( 'tzolkin/tzolkin.php', $active_plugins_arr ) ) {

			$event_functions_arr['start_date_function'] = 'tz_get_event_start_date';
			$event_functions_arr['end_date_function']   = 'tz_get_event_end_date';
			$event_functions_arr['all_day_function']    = 'tz_is_all_day';
		}

		// Some more guessing for The Events Calendar
		// (Add more as necessary/prudent)
		if ( in_array( 'the-events-calendar/the-events-calendar.php', $active_plugins_arr ) ) {

			$event_functions_arr['start_date_function']     = 'tribe_get_start_date';
			$event_functions_arr['end_date_function']       = 'tribe_get_end_date';
			$event_functions_arr['date_function_arguments'] = [ $this->post_object->ID, false, 'U' ];
			$event_functions_arr['all_day_function']        = 'tribe_event_is_all_day';
		}

		/**
		 * event_date_functions.
		 *
		 * A generic filter for site-wide use. Use it if you need to supply custom event date functions on a site-wide
		 * basis, rather than on a per-atom or per-organism basis.
		 *
		 * @since 0.5.0
		 *
		 * @param array $event_functions_arr An array of event functions for start date, end date, and all day checks.
		 */
		$event_functions_arr = apply_filters( 'event_date_functions', $event_functions_arr );
		Atom::add_debug_entry( 'Filter', 'event_date_functions' );

		/**
		 * $this->name_event_date_functions.
		 *
		 * An atom-specific event date functions filter
		 *
		 * @since 0.5.0
		 *
		 * @param array $event_functions_arr An array of event functions for start date, end date, and all day checks.
		 */
		$event_date_functions_filter = $this->name . '_event_date_functions';
		$this->event_functions       = apply_filters( $event_date_functions_filter, $event_functions_arr );
		Atom::add_debug_entry( 'Filter', $event_date_functions_filter );

	}

	private function setDateType() {

		$timezone_string = get_option( 'timezone_string' );

		// Temporary fix-- TODO: figure out a bulletproof way of getting the current time
		if ( '' === $timezone_string ) {
			$timezone_string = 'America/New_York';
		}

		$now = new \DateTime( current_time( 'mysql' ), new \DateTimeZone( $timezone_string ) );

		$today    = false;
		$same_day = false;

		if ( $this->event_start_date < $now && $this->event_end_date > $now ) {
			$today = true;
		}

		if ( date( 'Ymd', $this->event_start_date ) === date( 'Ymd', $this->event_end_date ) ) {
			$same_day = true;
		}

		if ( true === $today && true === $same_day ) {
			$event_date_type = 'now';
		}
		if ( true === $this->event_all_day && true === $same_day ) {
			$event_date_type = 'allday-single';
		}
		if ( true === $this->event_all_day && false === $same_day ) {
			$event_date_type = 'allday-multiple';
		}
		if ( true === $same_day && false === $this->event_all_day ) {
			$event_date_type = 'single-day';
		}
		if ( '' == $event_date_type ) {
			$event_date_type = 'uncategorized';
		}

		$this->event_date_type = $event_date_type;

	}

	private function setDateFormat() {

		switch ( $this->event_date_type ) {

			case 'now':

				// Now - 1:45 PM
				$event_date_formatted = sprintf(
					'Now - %s',
					date( 'g:i A', $this->event_end_date )
				);

				break;

			case 'allday-single':

				// Jan 1, 2016 - All Day
				$event_date_formatted = date( 'M j, Y', $this->event_start_date ) . ' - All Day';

				break;


			case 'allday-multiple':

				// Mon, Jan 13 - Fri, Jan 18
				$event_date_formatted = sprintf(
					'%s - %s',
					date( 'D, M j, Y', $this->event_start_date ),
					date( 'D, M j, Y', $this->event_end_date )
				);

				break;

			case 'single-day':

				// 11:05 AM - 1:45 PM
				$event_date_formatted = sprintf(
					'%s - %s',
					date( 'g:i A', $this->event_start_date ),
					date( 'g:i A', $this->event_end_date )
				);

				break;

			default:

				// Mon, Jan 13 @ 11:05 AM - Fri, Jan 18 @ 1:45 PM
				$event_date_formatted = sprintf(
					'%s - %s',
					date( 'D, M j, Y @ g:i A', $this->event_start_date ),
					date( 'D, M j, Y @ g:i A', $this->event_end_date )
				);

				break;
		}

		/**
		 * event_date_format.
		 *
		 * A site-wide filter for adjusting the event date format.
		 *
		 * @since 0.5.0
		 *
		 * @param string $var The formatted event date.
		 * @param string $this ->event_date_type Include the event date type so that making an intelligent adjustment is easier.
		 */
		$event_date_formatted = apply_filters( 'event_date_format', $event_date_formatted, $this );
		Atom::add_debug_entry( 'Filter', 'event_date_format' );

		/**
		 * $this->name_event_date_format.
		 *
		 * An atom-specific event date format filter.
		 *
		 * @since 0.5.0
		 *
		 * @param string $var The formatted event date.
		 * @param string $this ->event_date_type Include the event date type so that making an intelligent adjustment is easier.
		 */
		$event_date_format_filter   = $this->name . '_event_date_format';
		$this->event_date_formatted = apply_filters( $event_date_format_filter, $event_date_formatted, $this );
		Atom::add_debug_entry( 'Filter', $event_date_format_filter );

	}
}
