<?php
/**
 * Pattern Library
 *
 * A package for defining commonly-used atoms and organisms, which expedites the front-end development process.
 *
 * @version 0.5.0
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
include_once( $atoms_dir . 'ExcerptForce.php' );
include_once( $atoms_dir . 'ExcerptSearch.php' );
include_once( $atoms_dir . 'PostClass.php' );
include_once( $atoms_dir . 'PostThumbnail.php' );
include_once( $atoms_dir . 'PostTitle.php' );
include_once( $atoms_dir . 'PostTitleLink.php' );
include_once( $atoms_dir . 'CategoryList.php' );
include_once( $atoms_dir . 'Loop.php' );
include_once( $atoms_dir . 'SchemaAddress.php' );
include_once( $atoms_dir . 'PostDate.php' );
include_once( $atoms_dir . 'EventDate.php' );
include_once( $atoms_dir . 'EventBadge.php' );
include_once( $atoms_dir . 'TaxonomyList.php' );
include_once( $atoms_dir . 'ListTerms.php' );
include_once( $atoms_dir . 'ListPages.php' );
include_once( $atoms_dir . 'PostAuthor.php' );
include_once( $atoms_dir . 'Menu.php' );
include_once( $atoms_dir . 'Image.php' );

/*——————————————————————————————————————————————————————————————————————————————
/  Organisms
——————————————————————————————————————————————————————————————————————————————*/

$organisms_dir = 'organisms/';

include_once( $organisms_dir . 'PostList.php' );
include_once( $organisms_dir . 'EventList.php' );
include_once( $organisms_dir . 'Subnav.php' );
include_once( $organisms_dir . 'PostHeaderSingular.php' );
include_once( $organisms_dir . 'PostHeaderArchive.php' );
include_once( $organisms_dir . 'SectionHeader.php' );