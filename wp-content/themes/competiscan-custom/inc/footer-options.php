<?php
/**
 * Footer functionality: editable social links (ACF options page) and the active
 * state for the footer nav menus.
 *
 * Design/markup are unchanged — this only makes the social icons manageable from
 * the admin and drives the active state from the queried object.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the "Site Options" options page (Appearance-level capability).
 */
function competiscan_register_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}
	acf_add_options_page(
		array(
			'page_title' => __( 'Site Options', 'competiscan-custom' ),
			'menu_title' => __( 'Site Options', 'competiscan-custom' ),
			'menu_slug'  => 'competiscan-site-options',
			'capability' => 'edit_theme_options',
			'redirect'   => false,
			'icon_url'   => 'dashicons-share',
			'position'   => 60,
		)
	);
}
add_action( 'acf/init', 'competiscan_register_options_page' );

/**
 * Register the editable social-media URL fields on the options page.
 */
function competiscan_register_social_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$url = function ( $key, $name, $label, $default = '' ) {
		return array(
			'key'           => $key,
			'label'         => $label,
			'name'          => $name,
			'type'          => 'url',
			'default_value' => $default,
			'placeholder'   => 'https://',
			'wrapper'       => array( 'width' => '50' ),
		);
	};

	acf_add_local_field_group(
		array(
			'key'      => 'group_competiscan_social',
			'title'    => 'Social Media Links',
			'fields'   => array(
				$url( 'field_social_linkedin', 'social_linkedin', 'LinkedIn URL', 'https://www.linkedin.com/company/competiscan' ),
				$url( 'field_social_x', 'social_x', 'X (Twitter) URL', 'https://x.com/competiscan' ),
				$url( 'field_social_facebook', 'social_facebook', 'Facebook URL', '' ),
				$url( 'field_social_instagram', 'social_instagram', 'Instagram URL', 'https://www.instagram.com/competiscan/' ),
				$url( 'field_social_youtube', 'social_youtube', 'YouTube URL', '' ),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'competiscan-site-options',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'competiscan_register_social_fields' );

/**
 * Register the editable "Footer Company Information" fields on the options page.
 */
function competiscan_register_footer_company_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_competiscan_footer_company',
			'title'    => 'Footer Company Information',
			'fields'   => array(
				array(
					'key'           => 'field_footer_logo',
					'label'         => 'Footer Logo',
					'name'          => 'footer_logo',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'mime_types'    => 'jpg,jpeg,png,gif,svg,webp',
				),
				array(
					'key'          => 'field_footer_logo_link',
					'label'        => 'Footer Logo Link',
					'name'         => 'footer_logo_link',
					'type'         => 'url',
					'instructions' => 'Where the footer logo links to. Defaults to the site home page if left empty.',
				),
				array(
					'key'          => 'field_footer_address',
					'label'        => 'Company Address',
					'name'         => 'footer_address',
					'type'         => 'textarea',
					'rows'         => 3,
					'new_lines'    => '',
					'instructions' => 'Line breaks are preserved in the footer.',
				),
				array(
					'key'     => 'field_footer_phone',
					'label'   => 'Phone Number',
					'name'    => 'footer_phone',
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_footer_phone_link',
					'label'        => 'Phone Link (tel:)',
					'name'         => 'footer_phone_link',
					'type'         => 'text',
					'wrapper'      => array( 'width' => '50' ),
					'instructions' => 'Digits for the tel: link, e.g. +13125463489. Falls back to the phone number if empty.',
				),
				array(
					'key'   => 'field_footer_email',
					'label' => 'Email Address',
					'name'  => 'footer_email',
					'type'  => 'email',
				),
			),
			'location' => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'competiscan-site-options',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'competiscan_register_footer_company_fields' );

/**
 * Seed the footer company information already present in the design as real, saved
 * option values (once) so the footer keeps rendering after activation. Values stay
 * fully editable/clearable in admin; seeding only fills fields that are empty.
 */
function competiscan_seed_footer_company() {
	if ( '1' === get_option( 'competiscan_footer_company_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
		return;
	}
	if ( false === add_option( 'competiscan_footer_company_seed_claim', time(), '', 'no' ) ) {
		return;
	}

	$defaults = array(
		'footer_address'    => '205 West Wacker Drive, Ste 1900, Chicago, IL 60606',
		'footer_phone'      => '+1 312.546.3489',
		'footer_phone_link' => '+13125463489',
		'footer_email'      => 'contactus@competiscan.com',
	);
	foreach ( $defaults as $name => $value ) {
		if ( empty( get_field( $name, 'option' ) ) ) {
			update_field( $name, $value, 'option' );
		}
	}

	// Seed the logo as a real media-library attachment so it's editable/clearable.
	if ( empty( get_field( 'footer_logo', 'option' ) ) ) {
		$att_id = competiscan_import_theme_logo();
		if ( $att_id ) {
			update_field( 'footer_logo', $att_id, 'option' );
		}
	}

	update_option( 'competiscan_footer_company_seeded', '1' );
}
add_action( 'wp_loaded', 'competiscan_seed_footer_company', 46 );

/**
 * Import the bundled theme logo into the media library once and return its
 * attachment ID (reused on later runs via a marker meta).
 *
 * @return int Attachment ID, or 0 on failure.
 */
function competiscan_import_theme_logo() {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_competiscan_footer_logo',
			'meta_value'     => '1',
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$path = get_template_directory() . '/assets/images/logo.png';
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( 'competiscan-footer-logo.png' );
	if ( ! $tmp || ! @copy( $path, $tmp ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return 0;
	}

	$file_array = array(
		'name'     => 'competiscan-footer-logo.png',
		'tmp_name' => $tmp,
	);
	$att_id = media_handle_sideload( $file_array, 0, 'Competiscan footer logo' );
	if ( is_wp_error( $att_id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return 0;
	}

	update_post_meta( $att_id, '_competiscan_footer_logo', '1' );
	return (int) $att_id;
}

/**
 * Seed the social URLs already present in the design as real, saved option values
 * (once). This guarantees the icons render after activation without relying on ACF
 * default_value behaviour, and the values stay fully editable/clearable in admin —
 * seeding only fills a field that is currently empty, and only on the first run.
 */
function competiscan_seed_social_links() {
	if ( '1' === get_option( 'competiscan_social_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
		return;
	}
	// Claim once so concurrent requests don't all seed.
	if ( false === add_option( 'competiscan_social_seed_claim', time(), '', 'no' ) ) {
		return;
	}

	$defaults = array(
		'social_linkedin'  => 'https://www.linkedin.com/company/competiscan',
		'social_x'         => 'https://x.com/competiscan',
		'social_instagram' => 'https://www.instagram.com/competiscan/',
	);
	foreach ( $defaults as $name => $url ) {
		if ( empty( get_field( $name, 'option' ) ) ) {
			update_field( $name, $url, 'option' );
		}
	}

	update_option( 'competiscan_social_seeded', '1' );
}
add_action( 'wp_loaded', 'competiscan_seed_social_links', 45 );

/**
 * SVG markup for a social network, matching the teal icons in the footer design.
 *
 * @param string $key Network key.
 * @return string
 */
function competiscan_social_svg( $key ) {
	$svgs = array(
		'linkedin'  => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.625 0C15.2227 0 15.75 0.527344 15.75 1.16016V14.625C15.75 15.2578 15.2227 15.75 14.625 15.75H1.08984C0.492188 15.75 0 15.2578 0 14.625V1.16016C0 0.527344 0.492188 0 1.08984 0H14.625ZM4.74609 13.5V6.01172H2.42578V13.5H4.74609ZM3.58594 4.95703C4.32422 4.95703 4.92188 4.35938 4.92188 3.62109C4.92188 2.88281 4.32422 2.25 3.58594 2.25C2.8125 2.25 2.21484 2.88281 2.21484 3.62109C2.21484 4.35938 2.8125 4.95703 3.58594 4.95703ZM13.5 13.5V9.38672C13.5 7.38281 13.043 5.80078 10.6875 5.80078C9.5625 5.80078 8.78906 6.43359 8.47266 7.03125H8.4375V6.01172H6.22266V13.5H8.54297V9.80859C8.54297 8.82422 8.71875 7.875 9.94922 7.875C11.1445 7.875 11.1445 9 11.1445 9.84375V13.5H13.5Z" fill="#00ABAB"/></svg>',
		'instagram' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.91016 3.83203C10.125 3.83203 11.9531 5.66016 11.9531 7.875C11.9531 10.125 10.125 11.918 7.91016 11.918C5.66016 11.918 3.86719 10.125 3.86719 7.875C3.86719 5.66016 5.66016 3.83203 7.91016 3.83203ZM7.91016 10.5117C9.35156 10.5117 10.5117 9.35156 10.5117 7.875C10.5117 6.43359 9.35156 5.27344 7.91016 5.27344C6.43359 5.27344 5.27344 6.43359 5.27344 7.875C5.27344 9.35156 6.46875 10.5117 7.91016 10.5117ZM13.043 3.69141C13.043 3.16406 12.6211 2.74219 12.0938 2.74219C11.5664 2.74219 11.1445 3.16406 11.1445 3.69141C11.1445 4.21875 11.5664 4.64062 12.0938 4.64062C12.6211 4.64062 13.043 4.21875 13.043 3.69141ZM15.7148 4.64062C15.7852 5.94141 15.7852 9.84375 15.7148 11.1445C15.6445 12.4102 15.3633 13.5 14.4492 14.4492C13.5352 15.3633 12.4102 15.6445 11.1445 15.7148C9.84375 15.7852 5.94141 15.7852 4.64062 15.7148C3.375 15.6445 2.28516 15.3633 1.33594 14.4492C0.421875 13.5 0.140625 12.4102 0.0703125 11.1445C0 9.84375 0 5.94141 0.0703125 4.64062C0.140625 3.375 0.421875 2.25 1.33594 1.33594C2.28516 0.421875 3.375 0.140625 4.64062 0.0703125C5.94141 0 9.84375 0 11.1445 0.0703125C12.4102 0.140625 13.5352 0.421875 14.4492 1.33594C15.3633 2.25 15.6445 3.375 15.7148 4.64062ZM14.0273 12.5156C14.4492 11.4961 14.3438 9.03516 14.3438 7.875C14.3438 6.75 14.4492 4.28906 14.0273 3.23438C13.7461 2.56641 13.2188 2.00391 12.5508 1.75781C11.4961 1.33594 9.03516 1.44141 7.91016 1.44141C6.75 1.44141 4.28906 1.33594 3.26953 1.75781C2.56641 2.03906 2.03906 2.56641 1.75781 3.23438C1.33594 4.28906 1.44141 6.75 1.44141 7.875C1.44141 9.03516 1.33594 11.4961 1.75781 12.5156C2.03906 13.2188 2.56641 13.7461 3.26953 14.0273C4.28906 14.4492 6.75 14.3438 7.91016 14.3438C9.03516 14.3438 11.4961 14.4492 12.5508 14.0273C13.2188 13.7461 13.7812 13.2188 14.0273 12.5156Z" fill="#00ABAB"/></svg>',
		'x'         => '<svg width="17" height="15" viewBox="0 0 17 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.7617 0H15.2227L9.80859 6.22266L16.207 14.625H11.2148L7.27734 9.52734L2.8125 14.625H0.316406L6.11719 8.01562L0 0H5.13281L8.64844 4.67578L12.7617 0ZM11.8828 13.1484H13.2539L4.39453 1.40625H2.91797L11.8828 13.1484Z" fill="#00ABAB"/></svg>',
		'facebook'  => '<svg width="16" height="16" viewBox="0 0 16 16" fill="#00ABAB" xmlns="http://www.w3.org/2000/svg"><path d="M16 8.05C16 3.6 12.42 0 8 0S0 3.6 0 8.05c0 4.02 2.93 7.35 6.75 7.95v-5.62H4.72V8.05h2.03V6.28c0-2.01 1.19-3.12 3.01-3.12.87 0 1.79.16 1.79.16v1.98h-1.01c-.99 0-1.3.62-1.3 1.26v1.49h2.22l-.35 2.33H9.25V16c3.82-.6 6.75-3.93 6.75-7.95z"/></svg>',
		'youtube'   => '<svg width="16" height="16" viewBox="0 0 16 16" fill="#00ABAB" xmlns="http://www.w3.org/2000/svg"><path d="M15.66 4.12a2.01 2.01 0 0 0-1.42-1.42C12.98 2.36 8 2.36 8 2.36s-4.98 0-6.24.34A2.01 2.01 0 0 0 .34 4.12C0 5.38 0 8 0 8s0 2.62.34 3.88a2.01 2.01 0 0 0 1.42 1.42c1.26.34 6.24.34 6.24.34s4.98 0 6.24-.34a2.01 2.01 0 0 0 1.42-1.42C16 10.62 16 8 16 8s0-2.62-.34-3.88zM6.4 10.4V5.6L10.53 8 6.4 10.4z"/></svg>',
	);
	return isset( $svgs[ $key ] ) ? $svgs[ $key ] : '';
}

/**
 * Ordered social links that have a URL set. Empty ones are skipped so their icon
 * never renders.
 *
 * @return array<int,array{key:string,label:string,url:string,svg:string}>
 */
function competiscan_social_links() {
	$defs = array(
		'linkedin'  => 'LinkedIn',
		'instagram' => 'Instagram',
		'x'         => 'X',
		'facebook'  => 'Facebook',
		'youtube'   => 'YouTube',
	);

	$get = function_exists( 'get_field' );
	$out = array();
	foreach ( $defs as $key => $label ) {
		$url = $get ? get_field( 'social_' . $key, 'option' ) : '';
		if ( empty( $url ) ) {
			continue;
		}
		$out[] = array(
			'key'   => $key,
			'label' => $label,
			'url'   => $url,
			'svg'   => competiscan_social_svg( $key ),
		);
	}
	return $out;
}

/**
 * Menu locations that should receive the automatic active state.
 *
 * @return string[]
 */
function competiscan_active_menu_locations() {
	return array( 'footer_solutions', 'footer_company' );
}

/**
 * Add the `active` class (and aria-current) to the current footer menu item and
 * its ancestors. Derived from WordPress' own context flags, never hard-coded.
 *
 * @param array    $atts Link attributes.
 * @param WP_Post  $item Menu item.
 * @param stdClass $args wp_nav_menu args.
 * @return array
 */
function competiscan_footer_menu_active_class( $atts, $item, $args ) {
	if ( empty( $args->theme_location ) || ! in_array( $args->theme_location, competiscan_active_menu_locations(), true ) ) {
		return $atts;
	}

	$classes     = (array) $item->classes;
	$is_current  = ! empty( $item->current ) || in_array( 'current-menu-item', $classes, true );
	$is_ancestor = ! empty( $item->current_item_ancestor ) || ! empty( $item->current_item_parent )
		|| in_array( 'current-menu-ancestor', $classes, true ) || in_array( 'current-menu-parent', $classes, true );

	if ( $is_current || $is_ancestor ) {
		$existing         = isset( $atts['class'] ) ? $atts['class'] . ' ' : '';
		$atts['class']    = trim( $existing . 'active' );
	}
	if ( $is_current ) {
		$atts['aria-current'] = 'page';
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'competiscan_footer_menu_active_class', 10, 3 );

/**
 * Render the NEW badge inside footer menu links flagged with the `menu-item-new`
 * CSS class (WordPress strips HTML from menu titles, so it's added here).
 *
 * @param string   $item_output The menu item's HTML.
 * @param WP_Post  $item        Menu item.
 * @param int      $depth       Depth.
 * @param stdClass $args        wp_nav_menu args.
 * @return string
 */
function competiscan_footer_menu_badge( $item_output, $item, $depth, $args ) {
	if ( empty( $args->theme_location ) || 'footer_solutions' !== $args->theme_location ) {
		return $item_output;
	}
	if ( in_array( 'menu-item-new', (array) $item->classes, true ) ) {
		$item_output = preg_replace( '/<\/a>\s*$/', ' <span class="badge-new">NEW</span></a>', $item_output, 1 );
	}
	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'competiscan_footer_menu_badge', 10, 4 );
