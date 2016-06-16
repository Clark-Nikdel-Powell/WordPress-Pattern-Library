# Pattern-Library

A library of pre-made atoms, molecules and organisms intended for rapid front-end development.

## Atoms

The AtomTemplate class is a way of defining common atom arguments. The method **getMarkup** uses the CNP/Atom class to assemble the markup. Named atoms can also create and use atom-specific arguments if necessary (see PostThumbnail).

### Atom Documentation

#### Link

A standard link. 

##### Parameters:

- **href**: the link URL.

##### Uses: 

- None

#### PostLink

A link to a post.

##### Uses: 

- `get_permalink()`

##### Parameters: 

- None

#### FrontPageLink 

A link to the front page.
Uses: `home_url()`
Parameters: none.

#### SiteTitleFrontPageLink 

A link to the front page with the site title as its content.

#### PostsPageLink 

A link to the posts page.

#### Excerpt 

The post excerpt.

#### ForceExcerpt 

Either the post excerpt, or the beginning of the post.

#### ExcerptSearch 

A specialized excerpt for search result pages that highlights the searched-for term.

#### PostClass 

Uses `get_post_class` to return an `<article>` with the post classes.

#### PostThumbnail 

Uses a `thumbnail_args` to pass in arguments for `get_the_post_thumbnail`

#### PostTitle 

Uses `get_the_title` to return an h2 with the title.

#### PostTitleLink 

Extends PostTitle to return a PostTitle with a PostLink.

#### CategoryList 

Uses `get_the_category_list` to return a list of post categories.

#### Loop 

Uses `vsprintf` to run through a simple loop.

#### SchemaAddress 



#### PostDate 



#### EventDate 



#### EventBadge 



#### TaxonomyList 



#### ListTerms 



#### ListPages 



#### PostAuthor 



#### Menu 



#### Image 



#### ContentSourceLink 



#### BackgroundVideo 



#### PostTermLinkSingle 




## Organisms

The OrganismTemplate class is the starting point for multi-atom markup. Using a structure array, we can pass in named or generic atoms that need to be wrapped up in an organism.



Like AtomTemplate, the OrganismTemplate class has a getMarkup method that loops through the markup array and compiles the organism's markup.

The properties available in the OrganismTemplate class are:

- **name**: (required) the organism name. All atoms inside the organism are given the CSS class `$organism_name-$atom_name`.
- **tag**: (optional) the organism tag, used in CNP/Atom. *Default value: 'div'*.
- **tag_type**: (optional) the type of tag, used in CNP/Atom when assembling the organism wrapper. It defaults to split so that the atoms are nested inside properly. *Default value: 'split'*.
- **attributes**: (optional) organism attributes, used in CNP/Atom. *Default value: array*.
- **before_content**: (optional) string of markup to place before atoms.
- **after_content**: (optional) string of markup to place after atoms.
- **structure**: (required) the structure array.
- **posts**: (optional) array of WP Post Objects, which are used in the loopPosts method. Useful if you're building a posts list.
- **posts-structure**: (required if posts is present) the structure for each post object.

### Structure

You must write the structure array in a specific way in order for it to nest correctly. 

- **key**: atom name.
- **value**: string|array. If the value is a string, then it will be used as the content for the atom. If it's an array, then you must supply one of these keys:
    - **children**: use to create another level of nesting.
    - **parts**: use to indicate the final level of nesting in this part of the organism.

In additional to `children` and `parts`, the value is also used to pass in ordinary atom arguments. There is a shorthand for `class` that makes adding a specific CSS class easier.

### Organism Examples

#### Simple

**Input**
```php
$brief_args = [
	'name' => 'brief',
	'structure' => [
		'title' => 'The Brief',
		'description' => 'Here's the Brief!',
	]
];
```

**Output**
```html
<div class="brief">
    <div class="brief-title">The Brief</div>
    <div class="brief-description">Here's the Brief!</div>
</div>
```

#### Nesting using Posts, Children and Parts

**Input**
```php
$post_card_args = array(
	'name'           => 'posts',
	'posts'          => $latest_posts,
	'posts-structure' => [
		'item' => [
			'children' => ['PostClass'],
			'class' => 'column'
		],
		'PostClass' => [
			'children' => ['item-image', 'item-text'],
			'class' => 'posts-item-inside'
		],
		'item-image' => [
			'parts' => [
				'PostThumbnail'
			]
		],
		'item-text' => [
			'parts' => [
				'PostTitleLink',
				'ForceExcerpt'
			]
		]
	]
);
```

**Output**

```html
<div class="posts">
  <div class="posts-item column">
    <article class="PostClass posts-item-inside">
      <div class="posts-item-image">
        <img src="PostThumbnail.jpg" />
      </div>
      <div class="posts-item-text">
        <h2 class="posts-PostTitleLink"><a href="#">Post Title</a></h2>
        <p class="posts-ForceExcerpt">Post excerpt</p>
      </div>
    </article>
  </div>
</div>
```
