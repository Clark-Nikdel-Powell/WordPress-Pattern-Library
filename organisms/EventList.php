<?php
namespace CNP;

class EventList extends OrganismTemplate {

	public function __construct( $data=[] ) {

		parent::__construct( $data );

		if ( ! isset($data['name'])) {
			$this->name = 'eventlist';
		}

		if ( ! isset( $data['posts'] ) ) {

			$event_args = [];

			// Get the active plugins.
			$active_plugins     = get_option( 'active_plugins' );

			// We do some guessing here for Tzolkin
			if ( in_array( 'tzolkin/tzolkin.php', $active_plugins ) ) {

				$event_args = [
					'post_type' => 'tz_events'
				];
			}

			// Some more guessing for The Events Calendar
			if ( in_array( 'the-events-calendar/the-events-calendar.php', $active_plugins ) ) {

				$event_args = [
					'post_type'            => \Tribe__Events__Main::POSTTYPE,
					'orderby'              => 'event_date',
					'order'                => 'ASC',
					'posts_per_page'       => tribe_get_option( 'postsPerPage', 10 ),
					'tribe_render_context' => 'default'
				];
			}

			$eventlist_event_args_filter = $this->name . '_event_args';
			$event_args = apply_filters( $eventlist_event_args_filter, $event_args );
			Atom::AddDebugEntry( 'Filter', $eventlist_event_args_filter );

			$this->posts = new \WP_Query($event_args);

		}

		if ( ! isset( $data['posts-structure'] ) ) {

			$posts_structure = [
				'PostClass' => [
					'children' => [ 'image', 'text' ],
				],
				'image'     => [
					'parts' => [
						'PostThumbnail'
					]
				],
				'text'      => [
					'parts' => [
						'EventBadge',
						'PostTitleLink',
						'EventDate',
						'ForceExcerpt',
						'PostLink' => 'Read More'
					]
				]
			];

			$postlist_posts_structure_filter = $this->name . '_posts_structure';
			$this->posts_structure = apply_filters( $postlist_posts_structure_filter, $posts_structure);
			Atom::AddDebugEntry( 'Filter', $postlist_posts_structure_filter );
		}
	}
}