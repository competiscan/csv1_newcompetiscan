<?php
/**
 * Desktop navigation walker.
 *
 * The header nav in the HTML source is not a <ul>/<li> tree — it is a flat run of
 * <a class="nav-link"> and <div class="nav-item has-dropdown"> wrappers. This walker
 * reproduces that DOM exactly so the existing CSS applies unchanged.
 *
 * Target markup:
 *
 *   <div class="nav-item has-dropdown">
 *     <a href="#" class="nav-link">Solutions <svg/></a>
 *     <div class="mega-drop">
 *       <a href="#" class="mega-drop-link">
 *         <div class="title">Market Intelligence Database</div>
 *         <p>Description from the menu item's Description field.</p>
 *       </a>
 *     </div>
 *   </div>
 *   <a href="#" class="nav-link">Industries</a>
 *
 * Add the CSS class `mega-drop-narrow` to a parent menu item to get the single-column
 * dropdown the "About Us" menu uses in the source.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Competiscan_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * CSS classes on the top-level item currently being walked, so start_lvl()
	 * can tell whether its dropdown should be the narrow variant.
	 *
	 * @var array
	 */
	protected $current_parent_classes = array();

	/**
	 * Open a dropdown. Only depth 0 has one in this design.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 !== $depth ) {
			return;
		}

		// Mirrors the inline style on the "About Us" dropdown in the HTML source.
		$style = in_array( 'mega-drop-narrow', $this->current_parent_classes, true )
			? ' style="grid-template-columns:1fr;width:16rem;"'
			: '';

		$output .= '<div class="mega-drop"' . $style . '>';
	}

	/**
	 * Close a dropdown.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 !== $depth ) {
			return;
		}
		$output .= '</div>';
	}

	/**
	 * Render one item.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes      = empty( $item->classes ) ? array() : (array) $item->classes;
		$has_children = in_array( 'menu-item-has-children', $classes, true );
		$title        = apply_filters( 'nav_menu_item_title', $item->title, $item, $args, $depth );
		$url          = ! empty( $item->url ) ? $item->url : '#';
		$href         = ' href="' . esc_url( $url ) . '"';

		$atts = '';
		if ( ! empty( $item->target ) ) {
			$atts .= ' target="' . esc_attr( $item->target ) . '"';
		}
		if ( ! empty( $item->xfn ) ) {
			$atts .= ' rel="' . esc_attr( $item->xfn ) . '"';
		}

		// WordPress adds these context classes automatically to the item that maps
		// to the current page (and to its ancestors), so the active state is derived
		// from the queried object — never hard-coded.
		$is_current  = in_array( 'current-menu-item', $classes, true );
		$is_ancestor = in_array( 'current-menu-ancestor', $classes, true ) || in_array( 'current-menu-parent', $classes, true );
		$aria        = $is_current ? ' aria-current="page"' : '';

		if ( 0 === $depth ) {
			$this->current_parent_classes = $classes;

			// A top-level item is active when it is the current page, or (for a
			// dropdown parent) when one of its children is the current page.
			$link_class = 'nav-link' . ( ( $is_current || $is_ancestor ) ? ' active' : '' );

			if ( $has_children ) {
				$output .= '<div class="nav-item has-dropdown">';
				$output .= '<a' . $href . $atts . $aria . ' class="' . $link_class . '">' . $title . "\n" . competiscan_chevron_svg( 'desktop' ) . '</a>';
			} else {
				$output .= '<a' . $href . $atts . $aria . ' class="' . $link_class . '">' . $title . '</a>';
			}

			return;
		}

		// Depth 1: a card inside the mega dropdown.
		$description = '';
		if ( ! empty( $item->description ) ) {
			$description = '<p>' . wp_kses_post( $item->description ) . '</p>';
		}

		$badge      = in_array( 'menu-item-new', $classes, true ) ? ' <span class="badge-new">NEW</span>' : '';
		$link_class = 'mega-drop-link' . ( $is_current ? ' active' : '' );

		$output .= '<a' . $href . $atts . $aria . ' class="' . $link_class . '">';
		$output .= '<div class="title">' . $title . $badge . '</div>';
		$output .= $description;
		$output .= '</a>';
	}

	/**
	 * Close the item. Only the has-dropdown wrapper needs closing; anchors are
	 * already self-contained in start_el().
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

		if ( 0 === $depth && in_array( 'menu-item-has-children', $classes, true ) ) {
			$output .= '</div>';
		}
	}
}
