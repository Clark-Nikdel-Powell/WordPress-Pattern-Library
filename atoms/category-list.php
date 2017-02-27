<?php
namespace CNP;

/**
 * CategoryList.
 *
 * Uses get_the_category_list() to return a comma-delimited list of categories in a paragraph.
 *
 * @since 0.1.0
 *
 * @param string $delimiter The separator for the category links.
 */
class CategoryList extends AtomTemplate {

	private $prefix;
	private $suffix;
	public $separator;
	public $parents;
	public $parents_order;
	public $include_links;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' === $this->name ) {
			$this->name = 'category-list';
		}

		$this->separator = isset( $data['separator'] ) ? $data['separator'] : ', ';
		$this->tag       = isset( $data['tag'] ) ? $data['tag'] : 'p';

		$this->prefix = isset( $data['prefix'] ) ? $data['prefix'] : '<strong>Categories:</strong> ';
		$this->suffix = isset( $data['suffix'] ) ? $data['suffix'] : '';

		if ( ! isset( $this->include_links ) ) {
			$this->include_links = isset( $data['include-links'] ) ? $data['include-links'] : true;
		}
		if ( ! isset( $this->parents ) ) {
			$this->parents = isset( $data['parents'] ) && ( 'multiple' === $data['parents'] || 'single' === $data['parents'] ) ? $data['parents'] : '';
		}
		if ( ! isset( $this->parents_order ) ) {
			$this->parents_order = isset( $data['parents-order'] ) && ( 'first' === $data['parents-order'] || 'last' === $data['parents-order'] ) ? $data['parents-order'] : 'last';
		}

		if ( true === $this->include_links ) {
			$this->content = $this->prefix . get_the_category_list( $this->separator, $this->parents, $this->post_object ) . $this->suffix;
		}
		if ( false === $this->include_links ) {
			$this->content = $this->prefix . self::get_the_category_list_without_links( $this->separator, $this->parents, $this->post_object ) . $this->suffix;
		}
	}


	/**
	 * Retrieve category list in either HTML list or custom format.
	 *
	 * @since 1.5.1
	 *
	 * @global WP_Rewrite $wp_rewrite
	 *
	 * @param string $separator Optional, default is empty string. Separator for between the categories.
	 * @param string $parents Optional. How to display the parents.
	 * @param int $post_id Optional. Post ID to retrieve categories.
	 *
	 * @return string
	 */
	private function get_the_category_list_without_links( $separator = '', $parents = '', $post_id = false ) {
		global $wp_rewrite;
		if ( ! is_object_in_taxonomy( get_post_type( $post_id ), 'category' ) ) {
			/** This filter is documented in wp-includes/category-template.php */
			return apply_filters( 'the_category', '', $separator, $parents );
		}

		/**
		 * Filters the categories before building the category list.
		 *
		 * @since 4.4.0
		 *
		 * @param array $categories An array of the post's categories.
		 * @param int|bool $post_id ID of the post we're retrieving categories for. When `false`, we assume the
		 *                             current post in the loop.
		 */
		$categories = apply_filters( 'the_category_list', get_the_category( $post_id ), $post_id );

		if ( empty( $categories ) ) {
			/** This filter is documented in wp-includes/category-template.php */
			return apply_filters( 'the_category', __( 'Uncategorized' ), $separator, $parents );
		}

		$rel = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

		$thelist = '';
		if ( '' == $separator ) {
			$thelist .= '<ul class="post-categories">';
			foreach ( $categories as $category ) {
				$thelist .= "\n\t<li>";
				switch ( strtolower( $parents ) ) {
					case 'multiple':
						if ( $category->parent ) {
							$thelist .= get_category_parents( $category->parent, true, $separator );
						}
						$thelist .= $category->name . '</li>';
						break;
					case 'single':
						if ( $category->parent ) {
							$thelist .= get_category_parents( $category->parent, false, $separator );
						}
						$thelist .= $category->name . '</li>';
						break;
					case '':
					default:
						$thelist .= $category->name . '</li>';
				}
			}
			$thelist .= '</ul>';
		} else {
			$i = 0;
			foreach ( $categories as $category ) {
				if ( 0 < $i ) {
					$thelist .= $separator;
				}
				switch ( strtolower( $parents ) ) {
					case 'multiple':

						#region CNP MODIFICATIONS
						$parents_string = '';
						$item_string    = '<span class="category">' . $category->name . '</span>';

						if ( $category->parent ) {

							if ( 'last' === $this->parents_order ) {
								$parents_string = self::get_category_parents_without_links( $category->parent, false, '' );
								$thelist .= $item_string . $separator . $parents_string;
							}
							if ( 'first' === $this->parents_order ) {
								$parents_string = self::get_category_parents_without_links( $category->parent, false, $separator );
								$thelist .= $parents_string . $item_string;
							}
						} else {
							$thelist .= $item_string;
						}
						#endregion

						break;
					case 'single':
						$thelist .= '<span class="category">';
						if ( $category->parent ) {
							$thelist .= self::get_category_parents_without_links( $category->parent, false, $separator );
						}
						$thelist .= "$category->name</span>";
						break;
					case '':
					default:
						$thelist .= '<span class="category">' . $category->name . '</span>';
				}
				++ $i;
			}
		}

		/**
		 * Filters the category or list of categories.
		 *
		 * @since 1.2.0
		 *
		 * @param array $thelist List of categories for the current post.
		 * @param string $separator Separator used between the categories.
		 * @param string $parents How to display the category parents. Accepts 'multiple',
		 *                          'single', or empty.
		 */
		return apply_filters( 'the_category', $thelist, $separator, $parents );
	}


	/**
	 * Retrieve category parents with separator.
	 *
	 * @since 1.2.0
	 *
	 * @param int $id Category ID.
	 * @param bool $link Optional, default is false. Whether to format with link.
	 * @param string $separator Optional, default is '/'. How to separate categories.
	 * @param bool $nicename Optional, default is false. Whether to use nice name for display.
	 * @param array $visited Optional. Already linked to categories to prevent duplicates.
	 *
	 * @return string|WP_Error A list of category parents on success, WP_Error on failure.
	 */
	private function get_category_parents_without_links( $id, $link = false, $separator = '/', $nicename = false, $visited = array() ) {

		$chain  = '';
		$parent = get_term( $id, 'category' );
		if ( is_wp_error( $parent ) ) {
			return $parent;
		}

		if ( $nicename ) {
			$name = $parent->slug;
		} else {
			$name = $parent->name;
		}

		if ( $parent->parent && ( $parent->parent != $parent->term_id ) && ! in_array( $parent->parent, $visited ) ) {
			$visited[] = $parent->parent;
			$chain .= get_category_parents( $parent->parent, $link, $separator, $nicename, $visited );
		}

		$chain .= '<span class="category">' . $name . '</span>' . $separator;

		return $chain;
	}
}
