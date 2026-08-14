<?php
/**
 * Home page content fields (ACF Pro).
 *
 * Makes the hardcoded Home page sections editable from the admin, per this page,
 * without changing the design. Templates (template-parts/home/*.php) keep control of
 * layout/markup and read their text/images/links from these fields. Content is
 * seeded once to match the current design exactly.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Home page field groups (front page only).
 */
function competiscan_register_home_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$front = array(
		array(
			array(
				'param'    => 'page_type',
				'operator' => '==',
				'value'    => 'front_page',
			),
		),
	);

	// ---- Hero -------------------------------------------------------------
	acf_add_local_field_group(
		array(
			'key'        => 'group_competiscan_home_hero',
			'title'      => 'Home — Hero',
			'menu_order' => 1,
			'location'   => $front,
			'fields'     => array(
				array(
					'key'          => 'field_home_hero_heading',
					'label'        => 'Heading',
					'name'         => 'hero_heading',
					'type'         => 'textarea',
					'rows'         => 3,
					'new_lines'    => '',
					'instructions' => 'Line breaks are preserved.',
				),
				array(
					'key'       => 'field_home_hero_text',
					'label'     => 'Sub-text',
					'name'      => 'hero_text',
					'type'      => 'textarea',
					'rows'      => 4,
					'new_lines' => '',
				),
				array(
					'key'     => 'field_home_hero_placeholder',
					'label'   => 'Email field placeholder',
					'name'    => 'hero_email_placeholder',
					'type'    => 'text',
					'wrapper' => array( 'width' => '34' ),
				),
				array(
					'key'     => 'field_home_hero_btn_text',
					'label'   => 'Button text',
					'name'    => 'hero_button_text',
					'type'    => 'text',
					'wrapper' => array( 'width' => '33' ),
				),
				array(
					'key'          => 'field_home_hero_btn_url',
					'label'        => 'Button URL',
					'name'         => 'hero_button_url',
					'type'         => 'url',
					'wrapper'      => array( 'width' => '33' ),
					'instructions' => 'Optional. Where the demo form submits.',
				),
				array(
					'key'           => 'field_home_hero_image',
					'label'         => 'Hero image',
					'name'          => 'hero_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'medium',
				),
				array(
					'key'   => 'field_home_hero_image_alt',
					'label' => 'Hero image alt text',
					'name'  => 'hero_image_alt',
					'type'  => 'text',
				),

				// Floating decorative cards (text only — positions are fixed by design).
				array(
					'key'     => 'field_home_hero_media_prefix',
					'label'   => 'Card · Media label',
					'name'    => 'hero_media_prefix',
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_home_hero_media_value',
					'label'   => 'Card · Media value',
					'name'    => 'hero_media_value',
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_home_hero_c1_title',
					'label'   => 'Card 1 · Title',
					'name'    => 'hero_card1_title',
					'type'    => 'text',
					'wrapper' => array( 'width' => '30' ),
				),
				array(
					'key'     => 'field_home_hero_c1_text',
					'label'   => 'Card 1 · Text',
					'name'    => 'hero_card1_text',
					'type'    => 'textarea',
					'rows'    => 2,
					'new_lines' => '',
					'wrapper' => array( 'width' => '40' ),
				),
				array(
					'key'          => 'field_home_hero_c1_tags',
					'label'        => 'Card 1 · Tags',
					'name'         => 'hero_card1_tags',
					'type'         => 'text',
					'wrapper'      => array( 'width' => '30' ),
					'instructions' => 'Comma-separated.',
				),
				array(
					'key'     => 'field_home_hero_c2_title',
					'label'   => 'Card 2 · Title',
					'name'    => 'hero_card2_title',
					'type'    => 'text',
					'wrapper' => array( 'width' => '30' ),
				),
				array(
					'key'     => 'field_home_hero_c2_text',
					'label'   => 'Card 2 · Text',
					'name'    => 'hero_card2_text',
					'type'    => 'textarea',
					'rows'    => 2,
					'new_lines' => '',
					'wrapper' => array( 'width' => '40' ),
				),
				array(
					'key'          => 'field_home_hero_c2_tags',
					'label'        => 'Card 2 · Tags',
					'name'         => 'hero_card2_tags',
					'type'         => 'text',
					'wrapper'      => array( 'width' => '30' ),
					'instructions' => 'Comma-separated.',
				),
				array(
					'key'     => 'field_home_hero_c3_title',
					'label'   => 'Card 3 · Title',
					'name'    => 'hero_card3_title',
					'type'    => 'text',
					'wrapper' => array( 'width' => '30' ),
				),
				array(
					'key'     => 'field_home_hero_c3_text',
					'label'   => 'Card 3 · Text',
					'name'    => 'hero_card3_text',
					'type'    => 'textarea',
					'rows'    => 2,
					'new_lines' => '',
					'wrapper' => array( 'width' => '40' ),
				),
				array(
					'key'     => 'field_home_hero_c3_link',
					'label'   => 'Card 3 · Link label',
					'name'    => 'hero_card3_link_label',
					'type'    => 'text',
					'wrapper' => array( 'width' => '30' ),
				),
				array(
					'key'     => 'field_home_hero_channel_strong',
					'label'   => 'Card · Channel (bold)',
					'name'    => 'hero_channel_strong',
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_home_hero_channel_text',
					'label'   => 'Card · Channel (text)',
					'name'    => 'hero_channel_text',
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'           => 'field_home_hero_donut',
					'label'         => 'Card · Channel chart image',
					'name'          => 'hero_donut_image',
					'type'          => 'image',
					'return_format' => 'url',
					'preview_size'  => 'thumbnail',
				),
				array(
					'key'     => 'field_home_hero_aud_prefix',
					'label'   => 'Card · Audience label',
					'name'    => 'hero_audience_prefix',
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_home_hero_aud_value',
					'label'   => 'Card · Audience value',
					'name'    => 'hero_audience_value',
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
			),
		)
	);

	// ---- Testimonials -----------------------------------------------------
	acf_add_local_field_group(
		array(
			'key'        => 'group_competiscan_home_testimonials',
			'title'      => 'Home — Testimonials',
			'menu_order' => 6,
			'location'   => $front,
			'fields'     => array(
				array(
					'key'   => 'field_home_testi_heading',
					'label' => 'Section heading',
					'name'  => 'testi_heading',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_home_testi_items',
					'label'        => 'Testimonials',
					'name'         => 'testi_items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add testimonial',
					'sub_fields'   => array(
						array(
							'key'           => 'field_home_testi_image',
							'label'         => 'Photo',
							'name'          => 'person_image',
							'type'          => 'image',
							'return_format' => 'url',
							'preview_size'  => 'thumbnail',
							'wrapper'       => array( 'width' => '50' ),
						),
						array(
							'key'           => 'field_home_testi_avatar',
							'label'         => 'Avatar / icon',
							'name'          => 'avatar_image',
							'type'          => 'image',
							'return_format' => 'url',
							'preview_size'  => 'thumbnail',
							'wrapper'       => array( 'width' => '50' ),
						),
						array(
							'key'       => 'field_home_testi_quote',
							'label'     => 'Quote',
							'name'      => 'quote',
							'type'      => 'textarea',
							'rows'      => 4,
							'new_lines' => '',
						),
						array(
							'key'     => 'field_home_testi_name',
							'label'   => 'Name / title',
							'name'    => 'name',
							'type'    => 'text',
							'wrapper' => array( 'width' => '50' ),
						),
						array(
							'key'     => 'field_home_testi_role',
							'label'   => 'Role / company',
							'name'    => 'role',
							'type'    => 'text',
							'wrapper' => array( 'width' => '50' ),
						),
					),
				),
			),
		)
	);

	// ---- Partners ---------------------------------------------------------
	acf_add_local_field_group(
		array(
			'key'        => 'group_competiscan_home_partners',
			'title'      => 'Home — Partners',
			'menu_order' => 2,
			'location'   => $front,
			'fields'     => array(
				array(
					'key'   => 'field_home_partners_eyebrow',
					'label' => 'Eyebrow label',
					'name'  => 'partners_eyebrow',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_home_partners_logos',
					'label'        => 'Logos',
					'name'         => 'partners_logos',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Add logo',
					'sub_fields'   => array(
						array(
							'key'           => 'field_home_partner_logo',
							'label'         => 'Logo',
							'name'          => 'logo',
							'type'          => 'image',
							'return_format' => 'url',
							'preview_size'  => 'thumbnail',
						),
					),
				),
			),
		)
	);

	// ---- Omnichannel Tracking --------------------------------------------
	acf_add_local_field_group(
		array(
			'key'        => 'group_competiscan_home_tracking',
			'title'      => 'Home — Omnichannel Tracking',
			'menu_order' => 3,
			'location'   => $front,
			'fields'     => array(
				array(
					'key'   => 'field_home_tracking_heading',
					'label' => 'Heading',
					'name'  => 'tracking_heading',
					'type'  => 'text',
				),
				array(
					'key'       => 'field_home_tracking_desc',
					'label'     => 'Description',
					'name'      => 'tracking_desc',
					'type'      => 'textarea',
					'rows'      => 4,
					'new_lines' => '',
				),
				array(
					'key'          => 'field_home_tracking_cards',
					'label'        => 'Channel cards',
					'name'         => 'tracking_cards',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add channel',
					'sub_fields'   => array(
						array(
							'key'           => 'field_home_track_icon',
							'label'         => 'Icon (optional — keeps the built-in icon if empty)',
							'name'          => 'icon',
							'type'          => 'image',
							'return_format' => 'url',
							'preview_size'  => 'thumbnail',
							'wrapper'       => array( 'width' => '30' ),
						),
						array(
							'key'     => 'field_home_track_title',
							'label'   => 'Title',
							'name'    => 'title',
							'type'    => 'text',
							'wrapper' => array( 'width' => '30' ),
						),
						array(
							'key'       => 'field_home_track_text',
							'label'     => 'Text',
							'name'      => 'text',
							'type'      => 'textarea',
							'rows'      => 2,
							'new_lines' => '',
							'wrapper'   => array( 'width' => '40' ),
						),
					),
				),
			),
		)
	);

	// ---- Fortune 500 Stats -----------------------------------------------
	acf_add_local_field_group(
		array(
			'key'        => 'group_competiscan_home_stats',
			'title'      => 'Home — Stats',
			'menu_order' => 4,
			'location'   => $front,
			'fields'     => array(
				array(
					'key'   => 'field_home_stats_accent',
					'label' => 'Heading (highlighted part)',
					'name'  => 'stats_accent',
					'type'  => 'text',
				),
				array(
					'key'   => 'field_home_stats_tail',
					'label' => 'Heading (rest)',
					'name'  => 'stats_tail',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_home_stats_cells',
					'label'        => 'Grid cells (stats + images, in display order)',
					'name'         => 'stats_cells',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add cell',
					'sub_fields'   => array(
						array(
							'key'           => 'field_home_stat_type',
							'label'         => 'Cell type',
							'name'          => 'cell_type',
							'type'          => 'select',
							'choices'       => array( 'stat' => 'Stat', 'logo' => 'Image' ),
							'default_value' => 'stat',
							'wrapper'       => array( 'width' => '25' ),
						),
						array(
							'key'               => 'field_home_stat_num',
							'label'             => 'Number',
							'name'              => 'number',
							'type'              => 'text',
							'wrapper'           => array( 'width' => '25' ),
							'conditional_logic' => array( array( array( 'field' => 'field_home_stat_type', 'operator' => '==', 'value' => 'stat' ) ) ),
						),
						array(
							'key'               => 'field_home_stat_color',
							'label'             => 'Colour',
							'name'              => 'color',
							'type'              => 'select',
							'choices'           => array( 'yellow' => 'Yellow', 'orange' => 'Orange', 'blue' => 'Blue' ),
							'wrapper'           => array( 'width' => '25' ),
							'conditional_logic' => array( array( array( 'field' => 'field_home_stat_type', 'operator' => '==', 'value' => 'stat' ) ) ),
						),
						array(
							'key'               => 'field_home_stat_label',
							'label'             => 'Label',
							'name'              => 'label',
							'type'              => 'text',
							'wrapper'           => array( 'width' => '25' ),
							'conditional_logic' => array( array( array( 'field' => 'field_home_stat_type', 'operator' => '==', 'value' => 'stat' ) ) ),
						),
						array(
							'key'               => 'field_home_stat_img',
							'label'             => 'Image',
							'name'              => 'logo_image',
							'type'              => 'image',
							'return_format'     => 'url',
							'preview_size'      => 'thumbnail',
							'conditional_logic' => array( array( array( 'field' => 'field_home_stat_type', 'operator' => '==', 'value' => 'logo' ) ) ),
						),
					),
				),
			),
		)
	);

	// ---- Industries We Serve ---------------------------------------------
	acf_add_local_field_group(
		array(
			'key'        => 'group_competiscan_home_industries',
			'title'      => 'Home — Industries',
			'menu_order' => 5,
			'location'   => $front,
			'fields'     => array(
				array(
					'key'          => 'field_home_ind_heading',
					'label'        => 'Heading',
					'name'         => 'ind_heading',
					'type'         => 'textarea',
					'rows'         => 2,
					'new_lines'    => '',
					'instructions' => 'Line breaks are preserved.',
				),
				array(
					'key'       => 'field_home_ind_desc',
					'label'     => 'Description',
					'name'      => 'ind_desc',
					'type'      => 'textarea',
					'rows'      => 3,
					'new_lines' => '',
				),
				array(
					'key'     => 'field_home_ind_btn_label',
					'label'   => 'Button label',
					'name'    => 'ind_btn_label',
					'type'    => 'text',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'     => 'field_home_ind_btn_url',
					'label'   => 'Button URL',
					'name'    => 'ind_btn_url',
					'type'    => 'url',
					'wrapper' => array( 'width' => '50' ),
				),
				array(
					'key'          => 'field_home_ind_items',
					'label'        => 'Industries',
					'name'         => 'ind_items',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Add industry',
					'sub_fields'   => array(
						array(
							'key'           => 'field_home_ind_icon',
							'label'         => 'Icon (optional — keeps the built-in icon if empty)',
							'name'          => 'icon',
							'type'          => 'image',
							'return_format' => 'url',
							'preview_size'  => 'thumbnail',
							'wrapper'       => array( 'width' => '40' ),
						),
						array(
							'key'     => 'field_home_ind_label',
							'label'   => 'Label',
							'name'    => 'label',
							'type'    => 'text',
							'wrapper' => array( 'width' => '60' ),
						),
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'competiscan_register_home_fields' );

/**
 * Seed the Home hero with the current design content once (fills empty fields only).
 */
function competiscan_seed_home_hero() {
	if ( '1' === get_option( 'competiscan_home_hero_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
		return;
	}
	if ( false === add_option( 'competiscan_home_hero_seed_claim', time(), '', 'no' ) ) {
		return;
	}

	$pid = (int) get_option( 'page_on_front' );
	if ( ! $pid ) {
		update_option( 'competiscan_home_hero_seeded', '1' );
		return;
	}

	$img = get_template_directory_uri() . '/assets/images/';

	$defaults = array(
		'hero_heading'           => "Your Single Source\nfor Market and\nCompetitive\nInsights",
		'hero_text'              => 'Competiscan transforms direct mail and digital marketing into actionable insights. Our best-in-class service leverages the largest longitudinal, omni-channel panels of consumers, business owners, insurance producers, financial advisors, and mortgage brokers in the marketplace.

',
		'hero_email_placeholder' => 'Enter work email',
		'hero_button_text'       => 'Request a demo',
		'hero_image_alt'         => 'Financial advisor reviewing insights dashboard',
		'hero_media_prefix'      => 'Media:',
		'hero_media_value'       => 'Direct Mail',
		'hero_card1_title'       => 'Banking',
		'hero_card1_text'        => 'Increase loyalty from acquisition through onboarding and retention',
		'hero_card1_tags'        => 'Acquisition, Loyalty',
		'hero_card2_title'       => 'Insurance',
		'hero_card2_text'        => 'Monitor for new policies, rate changes, and channel preference.',
		'hero_card2_tags'        => 'Product Updates',
		'hero_card3_title'       => 'Consumer Services',
		'hero_card3_text'        => 'Discover marketing volume, digital impressions and campaign learning.',
		'hero_card3_link_label'  => 'My Email',
		'hero_channel_strong'    => 'Channel Utilization:',
		'hero_channel_text'      => 'Direct Mail vs. Digital',
		'hero_audience_prefix'   => 'Audience:',
		'hero_audience_value'    => 'Financial Advisors',
	);
	foreach ( $defaults as $name => $value ) {
		if ( '' === (string) get_field( $name, $pid ) ) {
			update_field( $name, $value, $pid );
		}
	}

	// Images — reuse the bundled theme assets via the media library.
	if ( empty( get_field( 'hero_image', $pid ) ) ) {
		$att = competiscan_import_theme_image( 'hero-image.jpg', 'Home hero image' );
		if ( $att ) {
			update_field( 'field_home_hero_image', $att, $pid );
		}
	}
	if ( empty( get_field( 'hero_donut_image', $pid ) ) ) {
		$att = competiscan_import_theme_image( 'donut-chart.svg', 'Home hero channel chart' );
		if ( $att ) {
			update_field( 'field_home_hero_donut', $att, $pid );
		}
	}

	update_option( 'competiscan_home_hero_seeded', '1' );
}
add_action( 'wp_loaded', 'competiscan_seed_home_hero', 48 );

/**
 * Seed the Home testimonials repeater with the current design content. Versioned so
 * a corrected seed can overwrite a previous one; bump COMPETISCAN_HOME_TESTI_SEED to
 * re-seed.
 */
define( 'COMPETISCAN_HOME_TESTI_SEED', '2' );
function competiscan_seed_home_testimonials() {
	if ( COMPETISCAN_HOME_TESTI_SEED === get_option( 'competiscan_home_testi_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
		return;
	}
	if ( false === add_option( 'competiscan_home_testi_seed_claim_' . COMPETISCAN_HOME_TESTI_SEED, time(), '', 'no' ) ) {
		return;
	}

	$pid = (int) get_option( 'page_on_front' );
	if ( ! $pid ) {
		update_option( 'competiscan_home_testi_seeded', COMPETISCAN_HOME_TESTI_SEED );
		return;
	}

	update_field( 'field_home_testi_heading', 'Market Leaders Trust Competiscan', $pid );

	$data = array(
		array( 'img' => 'leaders-1.png', 'icon' => 'icon1.svg', 'quote' => '“Competiscan is a reliable source to receive timely competitor intelligence with national and local perspectives. We are very pleased with the results and relationship with Competiscan.”', 'name' => 'VP Strategic Marketing', 'role' => 'Health Insurance Carrier' ),
		array( 'img' => 'leaders-2.png', 'icon' => 'icon2.svg', 'quote' => '“Competiscan’s database is thorough and easy to search, but what really stands out is their research. The insights team goes the extra mile to understand and respond to our needs.”', 'name' => 'Creative Director', 'role' => 'Direct Marketing Agency' ),
		array( 'img' => 'leaders-3.png', 'icon' => 'icon3.svg', 'quote' => '"The team has been phenomenal – they are super responsive to queries and are willing to work with us on our requests outside of the self-servicing platform. We also appreciate the fast turnaround as well. The Competiscan team are stars!!”', 'name' => 'Research Analyst', 'role' => 'Investment Management Firm' ),
		array( 'img' => 'leaders-4.png', 'icon' => 'icon4.svg', 'quote' => '“The Competiscan team is amazing to work with. From helping solve problems and account coordination to presenting, the team is nailing it with me and my business partners. I appreciate all of Competiscan’s support!”', 'name' => 'Competitive Intelligence', 'role' => 'Financial Services' ),
	);

	$rows = array();
	foreach ( $data as $d ) {
		$person = competiscan_import_theme_image( $d['img'], 'Testimonial photo' );
		$avatar = competiscan_import_theme_image( $d['icon'], 'Testimonial avatar' );
		$rows[] = array(
			'person_image' => $person ? $person : '',
			'avatar_image' => $avatar ? $avatar : '',
			'quote'        => $d['quote'],
			'name'         => $d['name'],
			'role'         => $d['role'],
		);
	}
	update_field( 'field_home_testi_items', $rows, $pid );

	update_option( 'competiscan_home_testi_seeded', COMPETISCAN_HOME_TESTI_SEED );
}
add_action( 'wp_loaded', 'competiscan_seed_home_testimonials', 49 );

/**
 * Seed the remaining Home sections (Partners, Tracking, Stats, Industries) with the
 * current design content once. Fills empty fields only; images are imported into the
 * media library (icons are left empty so the built-in artwork shows as fallback).
 */
function competiscan_seed_home_sections() {
	if ( '1' === get_option( 'competiscan_home_sections_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
		return;
	}
	if ( false === add_option( 'competiscan_home_sections_seed_claim', time(), '', 'no' ) ) {
		return;
	}

	$pid = (int) get_option( 'page_on_front' );
	if ( ! $pid ) {
		update_option( 'competiscan_home_sections_seeded', '1' );
		return;
	}

	// Partners.
	if ( '' === (string) get_field( 'partners_eyebrow', $pid ) ) {
		update_field( 'field_home_partners_eyebrow', 'Our Trusted Partners', $pid );
	}
	if ( empty( get_field( 'partners_logos', $pid ) ) ) {
		$logos = array( 'amsive-logo.jpg', 'amazon-web-services.png', 'deluxe.png', 'njm.png', 'prosper-logo.png', 'publix.png', 'rbc.png', 'sir.png', 'snap.png' );
		$rows  = array();
		foreach ( $logos as $fn ) {
			$att   = competiscan_import_theme_image( $fn, 'Partner logo' );
			$rows[] = array( 'logo' => $att ? $att : '' );
		}
		update_field( 'field_home_partners_logos', $rows, $pid );
	}

	// Omnichannel tracking.
	if ( '' === (string) get_field( 'tracking_heading', $pid ) ) {
		update_field( 'field_home_tracking_heading', 'Our Comprehensive Omnichannel Tracking', $pid );
	}
	if ( '' === (string) get_field( 'tracking_desc', $pid ) ) {
		update_field( 'field_home_tracking_desc', 'Competiscan captures competitor activity across every major channel, giving you one clear, comparable view of how strategies shift over time and where the market is heading next.', $pid );
	}
	if ( empty( get_field( 'tracking_cards', $pid ) ) ) {
		$tp    = 'Track creative trends, mail volumes, and segmentation strategy.';
		$rows  = array();
		foreach ( array( 'Direct Mail', 'Email', 'Digital', 'Social Media', 'Print' ) as $title ) {
			$rows[] = array( 'icon' => '', 'title' => $title, 'text' => $tp );
		}
		update_field( 'field_home_tracking_cards', $rows, $pid );
	}

	// Stats.
	if ( '' === (string) get_field( 'stats_accent', $pid ) ) {
		update_field( 'field_home_stats_accent', 'Most Fortune 500 firms rely on us to stay ahead.', $pid );
	}
	if ( '' === (string) get_field( 'stats_tail', $pid ) ) {
		update_field( 'field_home_stats_tail', " Shouldn't you?", $pid );
	}
	if ( empty( get_field( 'stats_cells', $pid ) ) ) {
		$cells_src = array(
			array( 'type' => 'stat', 'number' => '8,000+', 'color' => 'yellow', 'label' => 'projects completed for clients annually' ),
			array( 'type' => 'logo', 'img' => 'fortune-1.png' ),
			array( 'type' => 'stat', 'number' => '2,000+', 'color' => 'orange', 'label' => 'hours of custom research per year' ),
			array( 'type' => 'logo', 'img' => 'fortune-2.png' ),
			array( 'type' => 'logo', 'img' => 'fortune-3.png' ),
			array( 'type' => 'stat', 'number' => '~20,000', 'color' => 'blue', 'label' => 'hours per year supporting client research' ),
			array( 'type' => 'logo', 'img' => 'fortune-4.png' ),
			array( 'type' => 'stat', 'number' => '24/7', 'color' => 'yellow', 'label' => 'monitoring coverage' ),
			array( 'type' => 'stat', 'number' => '90%+', 'color' => 'orange', 'label' => 'average annual renewal rate among subscribers' ),
			array( 'type' => 'logo', 'img' => 'fortune-5.png' ),
			array( 'type' => 'stat', 'number' => '#1', 'color' => 'blue', 'label' => 'longitudinal consumer panel' ),
			array( 'type' => 'logo', 'img' => 'fortune-6.png' ),
		);
		$rows = array();
		foreach ( $cells_src as $c ) {
			if ( 'logo' === $c['type'] ) {
				$att   = competiscan_import_theme_image( $c['img'], 'Fortune logo' );
				$rows[] = array( 'cell_type' => 'logo', 'logo_image' => $att ? $att : '', 'number' => '', 'color' => '', 'label' => '' );
			} else {
				$rows[] = array( 'cell_type' => 'stat', 'number' => $c['number'], 'color' => $c['color'], 'label' => $c['label'], 'logo_image' => '' );
			}
		}
		update_field( 'field_home_stats_cells', $rows, $pid );
	}

	// Industries.
	if ( '' === (string) get_field( 'ind_heading', $pid ) ) {
		update_field( 'field_home_ind_heading', "Industries\nWe Serve", $pid );
	}
	if ( '' === (string) get_field( 'ind_desc', $pid ) ) {
		update_field( 'field_home_ind_desc', 'Etiam accumsan urna a mauris dapibus, nec aliquet nunc convallis. Phasellus eget justo et libero ultrices posuere.', $pid );
	}
	if ( '' === (string) get_field( 'ind_btn_label', $pid ) ) {
		update_field( 'field_home_ind_btn_label', 'Learn More', $pid );
	}
	if ( empty( get_field( 'ind_items', $pid ) ) ) {
		$items = array( 'Banking', 'Mortgage & Loans', 'Credit Cards', 'Retail', 'Insurance', 'Telecoms', 'Investment & Wealth', 'And more...' );
		$rows  = array();
		foreach ( $items as $label ) {
			$rows[] = array( 'icon' => '', 'label' => $label );
		}
		update_field( 'field_home_ind_items', $rows, $pid );
	}

	update_option( 'competiscan_home_sections_seeded', '1' );
}
add_action( 'wp_loaded', 'competiscan_seed_home_sections', 50 );

/**
 * Import a bundled theme image (assets/images/<file>) into the media library once,
 * returning its attachment ID (reused via a marker meta). Returns 0 on failure.
 *
 * @param string $file  File name inside assets/images/.
 * @param string $title Attachment title.
 * @return int
 */
function competiscan_import_theme_image( $file, $title = '' ) {
	$marker  = '_competiscan_asset_' . sanitize_key( $file );
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => $marker,
			'meta_value'     => '1',
		)
	);
	if ( $existing ) {
		return (int) $existing[0];
	}

	$path = get_template_directory() . '/assets/images/' . $file;
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$tmp = wp_tempnam( $file );
	if ( ! $tmp || ! @copy( $path, $tmp ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return 0;
	}

	$att_id = media_handle_sideload(
		array(
			'name'     => $file,
			'tmp_name' => $tmp,
		),
		0,
		$title
	);
	if ( is_wp_error( $att_id ) ) {
		@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		return 0;
	}

	update_post_meta( $att_id, $marker, '1' );
	return (int) $att_id;
}
