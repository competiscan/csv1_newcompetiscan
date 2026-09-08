<?php
/**
 * Primary navigation bootstrap.
 *
 * The header/mobile nav was falling back to the static markup in nav-fallbacks.php
 * because no menu was assigned to the `primary` theme location — so every link was
 * a dead "#". This one-time bootstrap fills the EXISTING "Primary Menu" with items
 * linked to the real published pages (WordPress generates the permalinks, so no URL
 * is hard-coded) and assigns that menu to the `primary` location. Once assigned,
 * wp_nav_menu() renders the menu through the walkers and the fallback never fires.
 *
 * Guarded by an option so it runs once; bump COMPETISCAN_PRIMARY_MENU_VERSION to
 * rebuild after a structure change.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'COMPETISCAN_PRIMARY_MENU_VERSION', '6' );

/**
 * Ensure the primary menu exists, is page-linked, and is assigned to its location.
 */
function competiscan_bootstrap_primary_menu() {
	if ( get_option( 'competiscan_primary_menu_built' ) === COMPETISCAN_PRIMARY_MENU_VERSION ) {
		return;
	}

	// This runs on wp_loaded, which fires on EVERY request (front-end and REST).
	// Atomically claim the build so exactly one request ever rebuilds: add_option()
	// performs an INSERT that fails (returns false) if the row already exists, so
	// concurrent/subsequent requests bail here instead of double-building.
	if ( false === add_option( 'competiscan_primary_menu_claim_' . COMPETISCAN_PRIMARY_MENU_VERSION, time(), '', 'no' ) ) {
		return;
	}

	// Resolve pages by slug so URLs stay dynamic (no hard-coded links).
	$id = static function ( $slug ) {
		$page = get_page_by_path( $slug );
		return $page ? (int) $page->ID : 0;
	};

	$pages = array(
		'mid'      => $id( 'market-intelligence-database' ),
		'toolkit'  => $id( 'ai-toolkit' ),
		'vpt'      => $id( 'value-proposition-trackers' ),
		'custom'   => $id( 'custom-research-analysis' ),
		'ind'      => $id( 'industries' ),
		'insights' => $id( 'insights' ),
		'about'    => $id( 'about-us' ),
		'careers'  => $id( 'careers' ),
		'login'    => $id( 'client-login' ),
	);

	// If the core pages aren't there yet, try again on the next load.
	if ( ! $pages['mid'] || ! $pages['ind'] || ! $pages['about'] ) {
		return;
	}

	// Reuse the existing "Primary Menu" if present; otherwise create it.
	$menu     = wp_get_nav_menu_object( 'Primary Menu' );
	$menu_id  = $menu ? (int) $menu->term_id : 0;
	if ( ! $menu_id ) {
		$created = wp_create_nav_menu( 'Primary Menu' );
		if ( is_wp_error( $created ) ) {
			return;
		}
		$menu_id = (int) $created;
	}

	// Clear existing items so a rebuild is deterministic.
	$existing = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );
	if ( $existing ) {
		foreach ( $existing as $item ) {
			wp_delete_post( $item->ID, true );
		}
	}

	$add = static function ( array $args, $parent = 0 ) use ( $menu_id ) {
		$new = wp_update_nav_menu_item(
			$menu_id,
			0,
			array_merge(
				array(
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => $parent,
				),
				$args
			)
		);
		return is_wp_error( $new ) ? 0 : (int) $new;
	};

	$page_item = static function ( $title, $object_id, $description = '', $classes = '' ) {
		return array(
			'menu-item-title'       => $title,
			'menu-item-type'        => 'post_type',
			'menu-item-object'      => 'page',
			'menu-item-object-id'   => $object_id,
			'menu-item-description' => $description,
			'menu-item-classes'     => $classes,
		);
	};

	// Add an item to any menu by id (the $add closure above is bound to the primary
	// menu; footer menus need their own target).
	$add_to = static function ( $target_menu_id, array $args, $parent = 0 ) {
		$new = wp_update_nav_menu_item(
			$target_menu_id,
			0,
			array_merge(
				array(
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => $parent,
				),
				$args
			)
		);
		return is_wp_error( $new ) ? 0 : (int) $new;
	};

	// Find-or-create a menu by name and clear its items so the rebuild is clean.
	$ensure_menu = static function ( $name ) {
		$menu = wp_get_nav_menu_object( $name );
		if ( $menu ) {
			$menu_id  = (int) $menu->term_id;
			$existing = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );
			if ( $existing ) {
				foreach ( $existing as $item ) {
					wp_delete_post( $item->ID, true );
				}
			}
			return $menu_id;
		}
		$created = wp_create_nav_menu( $name );
		return is_wp_error( $created ) ? 0 : (int) $created;
	};

	// --- Solutions (toggle-only parent, mirrors the source dropdown) ----------
	$solutions = $add(
		array(
			'menu-item-title' => 'Solutions',
			'menu-item-type'  => 'custom',
			'menu-item-url'   => '#',
		)
	);
	$add( $page_item( 'Market Intelligence Database', $pages['mid'], '24/7 access to 200M+ omnichannel competitor communications, updated daily.' ), $solutions );
	// Badge markup can't live in a menu title (WP strips it), so flag the item
	// with a class and let the walkers render the NEW badge.
	$add( $page_item( 'AI Toolkit', $pages['toolkit'], 'Score and benchmark campaigns with AI backed by two decades of data.', 'menu-item-new' ), $solutions );
	$add( $page_item( 'Value Proposition Trackers', $pages['vpt'], "Track competitors' public offers, promotions, and fees as they evolve." ), $solutions );
	$add( $page_item( 'Custom Research &amp; Analysis', $pages['custom'], 'Tailored research built around your questions, audiences, and competitive set.' ), $solutions );

	// --- Top-level links ------------------------------------------------------
	$add( $page_item( 'Industries', $pages['ind'] ) );
	$add( $page_item( 'Insights', $pages['insights'] ) );

	// --- About Us (narrow toggle dropdown) ------------------------------------
	$about = $add(
		array(
			'menu-item-title'   => 'About Us',
			'menu-item-type'    => 'custom',
			'menu-item-url'     => '#',
			'menu-item-classes' => 'mega-drop-narrow',
		)
	);
	$add( $page_item( 'About Us', $pages['about'] ), $about );
	$add( $page_item( 'Careers', $pages['careers'] ), $about );

	// --- Footer: Solutions column --------------------------------------------
	$footer_solutions = $ensure_menu( 'Footer Solutions' );
	if ( $footer_solutions ) {
		$add_to( $footer_solutions, $page_item( 'Database', $pages['mid'] ) );
		$add_to( $footer_solutions, $page_item( 'AI Toolkit', $pages['toolkit'], '', 'menu-item-new' ) );
		$add_to( $footer_solutions, $page_item( 'Tracker', $pages['vpt'] ) );
		$add_to( $footer_solutions, $page_item( 'Custom', $pages['custom'] ) );
	}

	// --- Footer: Company column ----------------------------------------------
	$footer_company = $ensure_menu( 'Footer Company' );
	if ( $footer_company ) {
		$add_to( $footer_company, $page_item( 'About Us', $pages['about'] ) );
		$add_to( $footer_company, $page_item( 'Insights', $pages['insights'] ) );
		if ( $pages['login'] ) {
			$add_to( $footer_company, $page_item( 'Client Login', $pages['login'] ) );
		}
		// Contact Us is an action (opens the contact modal via contact.js), not a page.
		$add_to(
			$footer_company,
			array(
				'menu-item-title' => 'Contact Us',
				'menu-item-type'  => 'custom',
				'menu-item-url'   => '#contact',
			)
		);
	}

	// Assign each menu to its theme location (header/mobile + the two footer columns).
	$locations = get_theme_mod( 'nav_menu_locations' );
	if ( ! is_array( $locations ) ) {
		$locations = array();
	}
	$locations['primary'] = $menu_id;
	if ( $footer_solutions ) {
		$locations['footer_solutions'] = $footer_solutions;
	}
	if ( $footer_company ) {
		$locations['footer_company'] = $footer_company;
	}
	set_theme_mod( 'nav_menu_locations', $locations );

	update_option( 'competiscan_primary_menu_built', COMPETISCAN_PRIMARY_MENU_VERSION );
}
add_action( 'wp_loaded', 'competiscan_bootstrap_primary_menu', 40 );
