<?php
/**
 * Pattern Library
 *
 * A package for defining commonly-used atoms and organisms, which expedites the front-end development process.
 *
 * @version 0.2.0
 */

/*——————————————————————————————————————————————————————————————————————————————
/  Templates: what we base all the other classes on.
——————————————————————————————————————————————————————————————————————————————*/

include_once( 'AtomTemplate.php' );
include_once( 'OrganismTemplate.php' );


/*——————————————————————————————————————————————————————————————————————————————
/  Atoms: order matters. Make sure you include the source class before extending classes.
——————————————————————————————————————————————————————————————————————————————*/

$atoms_dir = 'atoms/';

include_once( $atoms_dir . 'Link.php' );
include_once( $atoms_dir . 'PostLink.php' );
include_once( $atoms_dir . 'FrontPageLink.php' );
include_once( $atoms_dir . 'SiteTitleFrontPageLink.php' );
include_once( $atoms_dir . 'PostsPageLink.php' );
include_once( $atoms_dir . 'Excerpt.php' );
include_once( $atoms_dir . 'ForceExcerpt.php' );
include_once( $atoms_dir . 'PostClass.php' );
include_once( $atoms_dir . 'PostThumbnail.php' );
include_once( $atoms_dir . 'PostTitle.php' );
include_once( $atoms_dir . 'PostTitleLink.php' );
include_once( $atoms_dir . 'CategoryList.php' );
include_once( $atoms_dir . 'Loop.php' );
include_once( $atoms_dir . 'SchemaAddress.php' );
