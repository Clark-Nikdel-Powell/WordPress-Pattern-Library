<?php
/**
 * Pattern Library
 *
 * A package for defining commonly-used atoms and organisms, which expedites the front-end development process.
 *
 * @version 0.7.0
 */

/*——————————————————————————————————————————————————————————————————————————————
/  Templates: what we base all the other classes on.
——————————————————————————————————————————————————————————————————————————————*/

include_once( 'class.helpers.php' );
include_once( 'class.atom-template.php' );
include_once( 'class.organism-template.php' );

/*——————————————————————————————————————————————————————————————————————————————
/  Atoms: order matters. Make sure you include the source class before extending classes.
——————————————————————————————————————————————————————————————————————————————*/

$atoms_dir = 'atoms/';

include_once( $atoms_dir . 'link.php' );
include_once( $atoms_dir . 'post-link.php' );
include_once( $atoms_dir . 'post-parent-link.php' );
include_once( $atoms_dir . 'front-page-link.php' );
include_once( $atoms_dir . 'site-title-front-page-link.php' );
include_once( $atoms_dir . 'posts-page-link.php' );
include_once( $atoms_dir . 'facebook-share.php' );
include_once( $atoms_dir . 'twitter-share.php' );
include_once( $atoms_dir . 'email-share.php' );
include_once( $atoms_dir . 'excerpt.php' );
include_once( $atoms_dir . 'excerpt-force.php' );
include_once( $atoms_dir . 'excerpt-search.php' );
include_once( $atoms_dir . 'post-class.php' );
include_once( $atoms_dir . 'post-thumbnail.php' );
include_once( $atoms_dir . 'post-title.php' );
include_once( $atoms_dir . 'post-title-link.php' );
include_once( $atoms_dir . 'category-list.php' );
include_once( $atoms_dir . 'loop.php' );
include_once( $atoms_dir . 'schema-address.php' );
include_once( $atoms_dir . 'post-date.php' );
include_once( $atoms_dir . 'event-date.php' );
include_once( $atoms_dir . 'event-badge.php' );
include_once( $atoms_dir . 'taxonomy-list.php' );
include_once( $atoms_dir . 'list-terms.php' );
include_once( $atoms_dir . 'list-pages.php' );
include_once( $atoms_dir . 'post-author.php' );
include_once( $atoms_dir . 'post-author-link.php' );
include_once( $atoms_dir . 'menu.php' );
include_once( $atoms_dir . 'image.php' );
include_once( $atoms_dir . 'image-background.php' );
include_once( $atoms_dir . 'content-source-link.php' );
include_once( $atoms_dir . 'background-video.php' );
include_once( $atoms_dir . 'post-term-link-single.php' );
include_once( $atoms_dir . 'post-term-single.php' );
include_once( $atoms_dir . 'comment-number.php' );


/*——————————————————————————————————————————————————————————————————————————————
/  Organisms
——————————————————————————————————————————————————————————————————————————————*/

$organisms_dir = 'organisms/';

include_once( $organisms_dir . 'post-list.php' );
include_once( $organisms_dir . 'event-list.php' );
include_once( $organisms_dir . 'subnav.php' );
include_once( $organisms_dir . 'post-header-singular.php' );
include_once( $organisms_dir . 'post-header-archive.php' );
include_once( $organisms_dir . 'section-header.php' );
