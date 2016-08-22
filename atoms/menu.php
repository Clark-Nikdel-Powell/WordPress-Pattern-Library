<?php
namespace CNP;

/**
 * Menu.
 *
 * Returns a WordPress Menu.
 *
 * @since 0.2.0
 */
class Menu extends AtomTemplate {

	private $menu;

	public function __construct( $data ) {

		parent::__construct( $data );

		if ( '' == $this->name ) {
			$this->name = 'menu';
		}
		$this->tag = isset( $data['tag'] ) ? $data['tag'] : 'nav';

		if ( isset( $data['menu'] ) ) {
			$this->menu = $data['menu'];
		} elseif ( isset( $data['menu-args']['menu'] ) ) {
			$this->menu = $data['menu-args']['menu'];
		}

		$menu_args = [
			'menu'      => $this->menu,
			'echo'      => false,
			'container' => '',
		];

		if ( isset( $data['menu-args'] ) ) {
			$menu_args = wp_parse_args( $menu_args, $data['menu-args'] );
		}

		$this->content = wp_nav_menu( $menu_args );
	}
}
