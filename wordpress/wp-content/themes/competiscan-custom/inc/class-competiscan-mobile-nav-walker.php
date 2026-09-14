<?php
/**
 * Mobile navigation walker.
 *
 * Reproduces the .mobile-nav-list markup from the HTML source:
 *
 *   <li class="mobile-nav-item has-sub">
 *     <a href="#" class="mobile-nav-link">Solutions <svg/></a>
 *     <ul class="mobile-submenu">
 *       <li><a href="#">Market Intelligence Database</a></li>
 *     </ul>
 *   </li>
 *   <li class="mobile-nav-item"><a href="#" class="mobile-nav-link">Industries</a></li>
 *
 * The outer <ul class="mobile-nav-list"> comes from the wp_nav_menu() items_wrap.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Competiscan_Mobile_Nav_Walker extends Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 !== $depth ) {
			return;
		}
		$output .= '<ul class="mobile-submenu">';
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( 0 !== $depth ) {
			return;
		}
		$output .= '</ul>';
	}

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

		// Same WordPress-provided context classes as the desktop walker.
		$is_current  = in_array( 'current-menu-item', $classes, true );
		$is_ancestor = in_array( 'current-menu-ancestor', $classes, true ) || in_array( 'current-menu-parent', $classes, true );
		$aria        = $is_current ? ' aria-current="page"' : '';

		if ( 0 === $depth ) {
			$link_class = 'mobile-nav-link' . ( ( $is_current || $is_ancestor ) ? ' active' : '' );

			if ( $has_children ) {
				// Open the group that contains the current page so the active
				// submenu item is visible without an extra tap.
				$item_class = 'mobile-nav-item has-sub' . ( $is_ancestor ? ' open active' : '' );
				$output    .= '<li class="' . $item_class . '">';
				$output    .= '<a' . $href . $atts . $aria . ' class="' . $link_class . '">' . $title . "\n" . competiscan_chevron_svg( 'mobile' ) . '</a>';
			} else {
				$item_class = 'mobile-nav-item' . ( $is_current ? ' active' : '' );
				$output    .= '<li class="' . $item_class . '">';
				$output    .= '<a' . $href . $atts . $aria . ' class="' . $link_class . '">' . $title . '</a>';
			}

			return;
		}

		$badge      = in_array( 'menu-item-new', $classes, true ) ? ' <span class="badge-new">NEW</span>' : '';
		$link_class = $is_current ? ' class="active"' : '';
		$output    .= '<li><a' . $href . $atts . $aria . $link_class . '>' . $title . $badge . '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}
