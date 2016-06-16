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

___

#### PostLink

##### Extends: Link

A link to a post.

##### Parameters: 

- None

##### Uses: 

- `get_permalink()`

___

#### FrontPageLink 

##### Extends: Link

A link to the front page.

##### Parameters: 

- None

##### Uses: 

- `home_url()`

___

#### SiteTitleFrontPageLink 

A link to the front page with the site title as its content.

##### Parameters: 

- None

##### Uses: 

- FrontPageLink
- get_bloginfo( 'site_title' )

___

#### PostsPageLink 

##### Extends: Link

A link to the posts page.

##### Parameters: 

- None

##### Uses: 

- get_permalink( get_option( 'page_for_posts' ) )

___

#### Excerpt 

Uses the manual post excerpt from the post object.

##### Parameters: 

- None

##### Uses:

- None

#### ExcerptForce 

##### Extends: Excerpt

Either the post excerpt, or the beginning of the post.

##### Parameters: 

- None

##### Uses:

- `get_the_excerpt()`

___

#### ExcerptSearch 

##### Extends: Excerpt

A specialized excerpt for search result pages that highlights the searched-for term.

##### Parameters: 

- None

##### Uses:

- `get_query_var( 's' )`
- `get_the_content()`

___

#### PostClass 

Returns an `<article>` with the post classes.

##### Parameters: 

- None

##### Uses:

- `get_post_class()`

___

#### PostThumbnail 

Returns a post thumbnail.

##### Parameters: 

- **size**: The post thumbnail size.
- **attr**: Attributes passed in to the `get_the_post_thumbnail` function.

##### Uses:

- `get_the_post_thumbnail()`

___

#### PostTitle 

Returns an h2 with the title.

##### Parameters: 

- None

##### Uses:

- `get_the_title()`

___

#### PostTitleLink 

##### Extends: PostTitle

Returns a PostTitle with a PostLink.

##### Parameters: 

- None

##### Uses:

- None

___

#### CategoryList 

Returns a list of post categories.

##### Parameters: 

- None

##### Uses:

- `get_the_category_list()`

___

#### Loop 

Uses `vsprintf` to run through a simple loop.

##### Parameters: 

- **array**: the data array to loop through.
- **format**: the format string.

##### Uses:

- `vsprintf()`

___

#### SchemaAddress 

##### Parameters: 

- **address_data**: array of address data. The keys are:
   - street_address
   - city
   - state
   - zip_code
   - country

##### Uses:

- None

___

#### PostDate 

Returns a formatted post date

##### Parameters: 

- **date_format**: The PHP date format. Default: "F j, Y"
- **prefix**: String output before the date. Default: "`<strong>Published:</strong> `".
- **suffix**: String output after the date. Default: "".

##### Uses:

- `get_the_date()`

___

#### EventDate 

A complicated atom that returns an event date. It works for both Tzolkin or The Events Calendar.

##### Parameters: 

- **start_date_function**: the function for getting the event's start date.
- **end_date_function**: the function for getting the event's end date.
- **all_day_function**: the function for checking whether the event is an all-day event.

##### Uses:

- `self::SetEventFunctions`: sets the event start date, end date and all day functions.
- `self::setDateType`: sets the date type based on the current time in relation to the event start date **and** end date. Possible values are: "now," "allday-single," "allday-multiple," "single-day," or "uncategorized" if all other checks fail.
- `self::setDateFormat`: uses `date` and `sprintf` to set the event date format string.

##### Filters: 

- **event_date_functions/`{$name}`_event_date_functions**: A global or organism-specific filter to adjust the event date functions.
- **event_date_format/`{$name}`_event_date_format**: A global or organism-specific filter to set the date format.

___

#### EventBadge 

##### Extends: EventDate

Returns an event "badge," i.e., a month/day block that displays the event's start date.

##### Parameters:

- **badge_pieces** (array): the sub-elements of the badge. Default: `[ 'month' => date( 'F', $this->event_start_date ), 'day' => date( 'd', $this->event_start_date ) ]` 

##### Uses:

- None

##### Filters:

- **`{$name}`_badge_pieces_arr**: a organism-specific filter for adjusting the badge pieces.

___

#### TaxonomyList 

Returns a list of taxonomy terms assigned to a specific post. Use for custom taxonomies.

##### Parameters: 

- **taxonomy**: the taxonomy to pull from. Default: first result of `get_object_taxonomies()` if not provided.
- **separator**: the delimiter for list items. Default: ",".
- **before**: String to output before the list.
- **after**: String to output after the list.


##### Uses:

- `get_object_taxonomies()`
- `get_the_term_list()`

___

#### ListTerms 

Returns an unordered list of taxonomy terms.

##### Parameters:

- **taxonomy**: Which taxonomy's terms to display. Default: "Category," if the post_type is "post," first taxonomy returned from `get_object_taxonomies()` if not provided.
- **list_args**: List args for `wp_list_categories()`. Default: `[ 'taxonomy' => $this->taxonomy, 'echo' => 0, 'title_li' => '' ]`.

##### Uses:

- `get_object_taxonomies()`
- `wp_parse_args()`
- `wp_list_categories()`

##### Filters:

- **list_terms_list_args/`{$name}`_list_terms_list_args**: Global or organism-specific filter for the `wp_list_categories()` args.

___

#### ListPages 

Returns an unordered list of pages.

##### Parameters:

- **list_args**: List args for `wp_list_pages()`. Default: `[ 'post_type' => 'page', 'echo' => 0, 'title_li' => '' ]`.

##### Uses:

- `wp_list_pages()`

##### Filters:

- **list_pages_list_args/`{$name}`_list_pages_list_args**: Global or organism-specific filter for the `wp_list_pages()` args.

___

#### PostAuthor 

Displays the post author's name.

##### Parameters:

- **prefix** (string): String before the post author. Default: "By: ".
- **suffix** (string): String after the post author. Default: ".".

##### Uses:

- `get_the_author()`

___

#### Menu 

Returns a wp_nav_menu.

##### Parameters:

- **menu_args** (array): The args passed in to `wp_nav_menu()`. Default: `[ 'menu' => $this->menu, 'echo' => false, 'container' => '' ]`.

##### Uses:

- `wp_parse_args()`
- `wp_nav_menu()`

___

#### Image
 
 Returns a responsive image.

##### Parameters:

- **image_object/attachment_id**: Either the image object or attachment ID can be supplied from ACF. However, we only need the ID, so **attachment ID is preferred**.
- **size**: the WordPress image size.
- **icon**: The icon. 

##### Uses:

- `wp_get_attachment_image()`

___

#### ContentSourceLink 

A link that is output on the front-end to provide quicker editing access for content areas.

##### Parameters:

- **href**: A relative WordPress admin link to where the content can be edited. Uses `site_url()` to build the full URL.
- **type**: There are 3 types of content: "h" for "Hardcoded," "d" for "Dynamic" and "e" for "Editable." Only Editable content source links should have an href.
- **parent**: CSS selector of the element that the content source link labels.
- **title**: shorthand for the title attribute.

##### Uses:

- `site_url()`

##### Filters:

- 

___

#### BackgroundVideo 

##### Parameters:

- ****:

##### Uses:

- None

##### Filters:

- 

___

#### PostTermLinkSingle 

##### Parameters:

- ****:

##### Uses:

- None

##### Filters:

- 

___


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
