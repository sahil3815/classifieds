<?php
/**
 * @author  RadiusTheme
 * @since   1.0.0
 * @version 1.0.0
 */

namespace RadiusTheme\ClassifiedLite;

use stdClass;
use Walker_Nav_Menu;
use WP_Post;

class Menu_Walker extends Walker_Nav_Menu {
	/**
	 * Starts the list before the elements are added.
	 *
	 * @param  string   $output  Used to append additional content (passed by reference).
	 * @param  int      $depth  Depth of menu item. Used for padding.
	 * @param  stdClass $args  An object of wp_nav_menu() arguments.
	 *
	 * @see Walker::start_lvl()
	 *
	 * @since 3.0.0
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="sub-menu">';
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @param  string   $output  Used to append additional content (passed by reference).
	 * @param  int      $depth  Depth of menu item. Used for padding.
	 * @param  stdClass $args  An object of wp_nav_menu() arguments.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 3.0.0
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}

	/**
	 * Starts the element output.
	 *
	 * @param  string   $output  Used to append additional content (passed by reference).
	 * @param  WP_Post  $data_object  Menu item data object.
	 * @param  int      $depth  Depth of menu item. Used for padding.
	 * @param  stdClass $args  An object of wp_nav_menu() arguments.
	 * @param  int      $id  Optional. ID of the current menu item. Default 0.
	 *
	 * @since 3.0.0
	 * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 * @since 5.9.0 Renamed `$item` to `$data_object` and `$id` to `$current_object_id`
	 *              to match parent class for PHP 8 named parameter support.
	 * @since 6.7.0 Removed redundant title attributes.
	 *
	 * @see Walker::start_el()
	 */
	public function start_el( &$output, $data_object, $depth = 0, $args = null, $id = 0 ) {
		$has_children = in_array( 'menu-item-has-children', $data_object->classes, true );

		$output .= '<li class="' . implode( ' ', $data_object->classes ) . '">';

		// Main link
		$output .= '<a href="' . esc_url( $data_object->url ) . '">' . esc_html( $data_object->title ) . '</a>';

		// Add toggle button if item has children
		if ( $has_children ) {
			$output .= '<button class="rt-submenu-toggle" aria-expanded="false" aria-label="Toggle submenu">';
			$output .= '<i class="fa-solid fa-plus" aria-hidden="true"></i>';
			$output .= '</button>';
		}
	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @param  string   $output  Used to append additional content (passed by reference).
	 * @param  WP_Post  $data_object  Menu item data object. Not used.
	 * @param  int      $depth  Depth of page. Not Used.
	 * @param  stdClass $args  An object of wp_nav_menu() arguments.
	 *
	 * @since 5.9.0 Renamed `$item` to `$data_object` to match parent class for PHP 8 named parameter support.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 3.0.0
	 */
	public function end_el( &$output, $data_object, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}
