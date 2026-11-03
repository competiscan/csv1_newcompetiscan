<?php
/**
 * Webinars — custom post type + data helpers for the Insights page "Webinars" section.
 *
 * Every webinar card on the Insights page is a "Webinar" post managed from
 * WP Admin → Webinars. There is no limit on how many can be published; the grid
 * simply wraps onto new rows using the existing .webinars-grid styles.
 *
 * Fields are an ACF Pro field group ("Webinar Details", key group_competiscan_webinar).
 * It lives in the database, so it can be edited in WP Admin → ACF → Field Groups, and
 * ACF mirrors it to acf-json/group_competiscan_webinar.json (version control / deploy:
 * on a new site the JSON is loaded automatically; use ACF → Field Groups → Sync to make
 * it editable there).
 *
 * Fields per webinar (field name → use):
 *  - post title            → Webinar title
 *  - _thumbnail_id         → Featured Image / Thumbnail (ACF image field = native featured image)
 *  - webinar_date          → date shown in the MON / DAY box (required)
 *  - webinar_time          → optional start time, shown above the title
 *  - webinar_timezone      → optional label after the time ("CT", "ET" …)
 *  - webinar_description   → optional short description under the title
 *  - webinar_tag           → small label at the top of the card ("Webinars")
 *  - webinar_button_label  → button text ("Register")
 *  - webinar_button_url    → button link (https://…, /path/ or #anchor)
 *  - webinar_new_tab       → open the button link in a new tab
 *  - webinar_status        → Auto (closes after the date/time) / Open / Closed
 *  - webinar_closed_text   → text shown instead of the button once closed
 *  - webinar_order         → optional manual ordering
 *
 * Empty optional values hide their element on the front end.
 *
 * Front-end order: open/upcoming webinars first (soonest first), then closed ones
 * (most recent first). A non-zero "Display Order" overrides that within each group.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Webinar post type.
 */
function competiscan_register_webinar_cpt() {
	if ( post_type_exists( 'webinar' ) ) {
		return; // Never register twice (e.g. if a plugin already provides it).
	}

	$labels = array(
		'name'                  => __( 'Webinars', 'competiscan-custom' ),
		'singular_name'         => __( 'Webinar', 'competiscan-custom' ),
		'menu_name'             => __( 'Webinars', 'competiscan-custom' ),
		'add_new'               => __( 'Add Webinar', 'competiscan-custom' ),
		'add_new_item'          => __( 'Add New Webinar', 'competiscan-custom' ),
		'edit_item'             => __( 'Edit Webinar', 'competiscan-custom' ),
		'new_item'              => __( 'New Webinar', 'competiscan-custom' ),
		'view_item'             => __( 'View Webinar', 'competiscan-custom' ),
		'search_items'          => __( 'Search Webinars', 'competiscan-custom' ),
		'not_found'             => __( 'No webinars found', 'competiscan-custom' ),
		'not_found_in_trash'    => __( 'No webinars found in Trash', 'competiscan-custom' ),
		'all_items'             => __( 'All Webinars', 'competiscan-custom' ),
		'featured_image'        => __( 'Webinar Image', 'competiscan-custom' ),
		'set_featured_image'    => __( 'Set webinar image', 'competiscan-custom' ),
		'remove_featured_image' => __( 'Remove webinar image', 'competiscan-custom' ),
		'use_featured_image'    => __( 'Use as webinar image', 'competiscan-custom' ),
	);

	register_post_type(
		'webinar',
		array(
			'labels'        => $labels,
			'description'   => __( 'Webinars listed in the Webinars section of the Insights page.', 'competiscan-custom' ),
			'public'        => false, // Cards link to the registration URL; no single pages.
			'show_ui'       => true,
			'show_in_menu'  => true,
			'show_in_rest'  => true,
			'menu_position' => 23,
			'menu_icon'     => 'dashicons-video-alt2',
			'hierarchical'  => false,
			'has_archive'   => false,
			'rewrite'       => false,
			'query_var'     => false,
			'supports'      => array( 'title', 'thumbnail', 'revisions' ),
		)
	);
}
add_action( 'init', 'competiscan_register_webinar_cpt' );

/**
 * The ACF "Featured Image / Thumbnail" field writes the native featured image
 * (_thumbnail_id), so the core Featured Image box is hidden to keep a single control.
 * If ACF is ever deactivated the core box comes back automatically.
 */
function competiscan_webinar_meta_boxes() {
	if ( function_exists( 'acf' ) ) {
		remove_meta_box( 'postimagediv', 'webinar', 'side' );
	}
}
add_action( 'add_meta_boxes_webinar', 'competiscan_webinar_meta_boxes', 20 );

/**
 * Button link: accept full URLs, site-relative paths, in-page anchors, mailto: and tel:.
 *
 * @param bool|string $valid Current validity.
 * @param mixed       $value Submitted value.
 * @return bool|string
 */
function competiscan_validate_webinar_button_url( $valid, $value ) {
	if ( true !== $valid || ! is_string( $value ) || '' === trim( $value ) ) {
		return $valid;
	}
	$value = trim( $value );
	if ( preg_match( '#^(https?://|//|/|\#|mailto:|tel:)#i', $value ) && ! preg_match( '/\s/', $value ) ) {
		return $valid;
	}
	return __( 'Enter a full URL (https://…), a site path (/contact-us/) or an anchor (#section).', 'competiscan-custom' );
}
add_filter( 'acf/validate_value/key=field_webinar_button_url', 'competiscan_validate_webinar_button_url', 10, 2 );

/**
 * Field value with a default for webinars saved before the field existed.
 *
 * A field that was saved empty stays empty (so the front end can hide it); only a
 * field that has never been saved falls back to $default.
 *
 * @param int    $post_id Post ID.
 * @param string $name    Meta key / ACF field name.
 * @param mixed  $default Default when the field has never been saved.
 * @return mixed
 */
function competiscan_webinar_meta( $post_id, $name, $default = '' ) {
	if ( ! metadata_exists( 'post', $post_id, $name ) ) {
		return $default;
	}
	$value = get_post_meta( $post_id, $name, true );
	return is_string( $value ) ? trim( $value ) : $value;
}

/**
 * Normalised data for one webinar, ready for the template.
 *
 * @param WP_Post|int $post Webinar post.
 * @return array|null
 */
function competiscan_get_webinar_data( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return null;
	}
	$id   = $post->ID;
	$tz   = wp_timezone();
	$date = preg_replace( '/\D/', '', (string) competiscan_webinar_meta( $id, 'webinar_date' ) );
	$time = (string) competiscan_webinar_meta( $id, 'webinar_time' );

	$start = null;
	if ( 8 === strlen( $date ) ) {
		$start = DateTimeImmutable::createFromFormat( 'Ymd H:i:s', $date . ' ' . ( $time ? $time : '00:00:00' ), $tz );
		$start = $start ? $start : null;
	}
	// Without a start time the webinar counts as running until the end of its day.
	$ends = $start ? ( $time ? $start : $start->setTime( 23, 59, 59 ) ) : null;

	$status = (string) competiscan_webinar_meta( $id, 'webinar_status', 'auto' );
	if ( ! in_array( $status, array( 'auto', 'open', 'closed' ), true ) ) {
		$status = 'auto';
	}
	if ( 'auto' === $status ) {
		$closed = $ends ? ( $ends < current_datetime() ) : false;
	} else {
		$closed = ( 'closed' === $status );
	}

	$time_txt = '';
	if ( $start && $time ) {
		$time_txt = wp_date( get_option( 'time_format', 'g:i a' ), $start->getTimestamp(), $tz );
		$zone     = (string) competiscan_webinar_meta( $id, 'webinar_timezone' );
		if ( '' !== $zone ) {
			$time_txt .= ' ' . $zone;
		}
	}

	$image_id  = (int) get_post_thumbnail_id( $id );
	$image_alt = $image_id ? trim( (string) get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) : '';

	$button_url = (string) competiscan_webinar_meta( $id, 'webinar_button_url' );
	$new_tab    = '0' !== (string) competiscan_webinar_meta( $id, 'webinar_new_tab', '1' );

	return array(
		'id'           => $id,
		'title'        => get_the_title( $id ),
		'description'  => (string) competiscan_webinar_meta( $id, 'webinar_description' ),
		'tag'          => (string) competiscan_webinar_meta( $id, 'webinar_tag', __( 'Webinars', 'competiscan-custom' ) ),
		'image_id'     => $image_id && wp_attachment_is_image( $image_id ) ? $image_id : 0,
		'image_alt'    => '' !== $image_alt ? $image_alt : wp_strip_all_tags( get_the_title( $id ) ),
		'month'        => $start ? strtoupper( wp_date( 'M', $start->getTimestamp(), $tz ) ) : '',
		'day'          => $start ? wp_date( 'j', $start->getTimestamp(), $tz ) : '',
		'time'         => $time_txt,
		'timestamp'    => $start ? $start->getTimestamp() : 0,
		'closed'       => $closed,
		'closed_text'  => (string) competiscan_webinar_meta( $id, 'webinar_closed_text', __( 'This webinar has closed.', 'competiscan-custom' ) ),
		'button_label' => (string) competiscan_webinar_meta( $id, 'webinar_button_label', __( 'Register', 'competiscan-custom' ) ),
		'button_url'   => $button_url,
		// In-page anchors (#…) never open a new tab.
		'new_tab'      => $new_tab && '' !== $button_url && 0 !== strpos( $button_url, '#' ),
		'order'        => (int) competiscan_webinar_meta( $id, 'webinar_order', 0 ),
	);
}

/**
 * All published webinars, in front-end display order. No limit.
 *
 * @return array[] List of competiscan_get_webinar_data() arrays.
 */
function competiscan_get_webinars() {
	$posts = get_posts(
		array(
			'post_type'        => 'webinar',
			'post_status'      => 'publish',
			'numberposts'      => -1,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'suppress_filters' => false,
		)
	);

	$items = array_values( array_filter( array_map( 'competiscan_get_webinar_data', $posts ) ) );

	usort(
		$items,
		static function ( $a, $b ) {
			// 1) Open/upcoming before closed.
			if ( $a['closed'] !== $b['closed'] ) {
				return $a['closed'] ? 1 : -1;
			}
			// 2) Manual Display Order (non-zero values first, ascending).
			$ao = $a['order'] ? $a['order'] : PHP_INT_MAX;
			$bo = $b['order'] ? $b['order'] : PHP_INT_MAX;
			if ( $ao !== $bo ) {
				return $ao <=> $bo;
			}
			// 3) Date: upcoming soonest first; closed most recent first.
			if ( $a['timestamp'] !== $b['timestamp'] ) {
				return $a['closed'] ? ( $b['timestamp'] <=> $a['timestamp'] ) : ( $a['timestamp'] <=> $b['timestamp'] );
			}
			return strcasecmp( $a['title'], $b['title'] );
		}
	);

	return $items;
}

/**
 * Small stylesheet for the optional card parts (image, time, description).
 * Loaded only on the Insights page; the existing card CSS is untouched.
 */
function competiscan_webinar_assets() {
	if ( ! function_exists( 'competiscan_is_insights_page' ) || ! competiscan_is_insights_page() ) {
		return;
	}
	$file = '/assets/css/webinars.css';
	wp_enqueue_style(
		'competiscan-webinars',
		get_template_directory_uri() . $file,
		array( 'competiscan-responsive' ),
		competiscan_asset_version( get_template_directory() . $file )
	);
}
add_action( 'wp_enqueue_scripts', 'competiscan_webinar_assets', 25 );

/* ------------------------------------------------------------------------
 * Admin list: useful columns + sort by webinar date.
 * --------------------------------------------------------------------- */

/**
 * @param array $columns Columns.
 * @return array
 */
function competiscan_webinar_admin_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		if ( 'date' === $key ) {
			$new['webinar_when']   = __( 'Webinar Date', 'competiscan-custom' );
			$new['webinar_status'] = __( 'Status', 'competiscan-custom' );
			$new['webinar_link']   = __( 'Button Link', 'competiscan-custom' );
			$new['webinar_image']  = __( 'Image', 'competiscan-custom' );
		}
		$new[ $key ] = $label;
	}
	return $new;
}
add_filter( 'manage_webinar_posts_columns', 'competiscan_webinar_admin_columns' );

/**
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function competiscan_webinar_admin_column_content( $column, $post_id ) {
	$w = competiscan_get_webinar_data( $post_id );
	if ( ! $w ) {
		return;
	}
	switch ( $column ) {
		case 'webinar_when':
			echo $w['timestamp'] ? esc_html( wp_date( get_option( 'date_format', 'F j, Y' ), $w['timestamp'], wp_timezone() ) . ( $w['time'] ? ' — ' . $w['time'] : '' ) ) : '—';
			break;
		case 'webinar_status':
			echo $w['closed'] ? '<span style="color:#b32d2e;">' . esc_html__( 'Closed', 'competiscan-custom' ) . '</span>' : '<span style="color:#008a20;">' . esc_html__( 'Open', 'competiscan-custom' ) . '</span>';
			$mode = (string) competiscan_webinar_meta( $post_id, 'webinar_status', 'auto' );
			echo ' <small style="color:#787c82;">(' . esc_html( ucfirst( $mode ? $mode : 'auto' ) ) . ')</small>';
			break;
		case 'webinar_link':
			if ( '' !== $w['button_url'] && '' !== $w['button_label'] ) {
				echo '<a href="' . esc_url( $w['button_url'] ) . '" target="_blank" rel="noopener">' . esc_html( $w['button_label'] ) . '</a> <small style="color:#787c82;">' . esc_html( $w['button_url'] ) . '</small>';
			} else {
				echo '<span style="color:#787c82;">' . esc_html__( '— (button hidden)', 'competiscan-custom' ) . '</span>';
			}
			break;
		case 'webinar_image':
			echo $w['image_id'] ? wp_get_attachment_image( $w['image_id'], array( 60, 40 ) ) : '—';
			break;
	}
}
add_action( 'manage_webinar_posts_custom_column', 'competiscan_webinar_admin_column_content', 10, 2 );

/**
 * @param array $columns Sortable columns.
 * @return array
 */
function competiscan_webinar_sortable_columns( $columns ) {
	$columns['webinar_when'] = 'webinar_date';
	return $columns;
}
add_filter( 'manage_edit-webinar_sortable_columns', 'competiscan_webinar_sortable_columns' );

/**
 * @param WP_Query $query Admin list query.
 */
function competiscan_webinar_admin_orderby( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'webinar' !== $query->get( 'post_type' ) ) {
		return;
	}
	if ( 'webinar_date' !== $query->get( 'orderby' ) ) {
		return;
	}
	// Clause pair so webinars without a date (e.g. drafts) stay listed.
	$query->set(
		'meta_query',
		array(
			'relation'     => 'OR',
			'webinar_date' => array(
				'key'     => 'webinar_date',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => 'webinar_date',
				'compare' => 'NOT EXISTS',
			),
		)
	);
	$query->set( 'orderby', array( 'webinar_date' => ( 'ASC' === strtoupper( (string) $query->get( 'order' ) ) ? 'ASC' : 'DESC' ) ) );
}
add_action( 'pre_get_posts', 'competiscan_webinar_admin_orderby' );

/* ------------------------------------------------------------------------
 * One-time import of the three webinars that were previously hardcoded in
 * template-insights.php (same content as the live page), so the section looks
 * exactly the same after switching to the post type. Runs once per site and
 * never when any webinar already exists (e.g. on deploy to another environment).
 * --------------------------------------------------------------------- */
function competiscan_seed_webinars() {
	if ( get_option( 'competiscan_webinar_cpt_seeded' ) ) {
		return;
	}
	if ( ! post_type_exists( 'webinar' ) ) {
		return;
	}
	// Claim the seed first so concurrent requests can't import twice.
	if ( ! add_option( 'competiscan_webinar_cpt_seeded', 'running', '', true ) ) {
		return;
	}

	$existing = get_posts(
		array(
			'post_type'   => 'webinar',
			'post_status' => 'any',
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);

	if ( ! $existing ) {
		// Title, date, status, button link ("#" = placeholder, as on the live page).
		$seed = array(
			array( 'Direct Mail Reimagined: How USPS incentives are fueling a new wave of creative innovation', '20260318', 'open', '#' ),
			array( 'Launching New ETFs', '20260408', 'open', '#' ),
			array( 'Competiscan Capabilities and Market Trends in Medicare Advantage.', '20260123', 'closed', '' ),
		);
		foreach ( $seed as $row ) {
			$id = wp_insert_post(
				array(
					'post_type'   => 'webinar',
					'post_status' => 'publish',
					'post_title'  => $row[0],
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				continue;
			}
			$meta = array(
				'webinar_date'         => $row[1],
				'webinar_time'         => '',
				'webinar_timezone'     => '',
				'webinar_description'  => '',
				'webinar_tag'          => 'Webinars',
				'webinar_button_label' => 'Register',
				'webinar_button_url'   => $row[3],
				'webinar_new_tab'      => '0',
				'webinar_status'       => $row[2],
				'webinar_closed_text'  => 'This webinar has closed.',
				'webinar_order'        => '0',
			);
			foreach ( $meta as $key => $value ) {
				update_post_meta( $id, $key, $value );
				update_post_meta( $id, '_' . $key, 'field_' . $key ); // ACF field reference.
			}
		}
	}

	update_option( 'competiscan_webinar_cpt_seeded', '1', true );
}
add_action( 'init', 'competiscan_seed_webinars', 30 );
