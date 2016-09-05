<?php
namespace CNP;

/**
 * PostAuthorLink.
 *
 * Returns a post author link.
 *
 * @since 0.2.0
 */
class PostAuthorUrl extends AtomTemplate {

	private $prefix;
	private $suffix;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'postauthorlink';
		}

		$author_id   = $data['post']->post_author;
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$author_url  = get_author_posts_url( $author_id );

		$this->tag                = 'a';
		$this->attributes['href'] = $author_url;
		$this->content            = $author_name;
	}
}
