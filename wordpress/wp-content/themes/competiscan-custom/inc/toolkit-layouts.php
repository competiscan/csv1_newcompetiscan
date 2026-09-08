<?php
/**
 * AI Toolkit page — flexible-content layouts.
 *
 * The AI Toolkit page is built with the existing ACF Flexible Content system
 * (field group group_cs_flexible_content, rendered by template-cms.php). Its
 * sections are added here as individual reusable layouts — registered on the
 * existing "Page Sections" flexible field via a filter (so the shared
 * acf-json group file is never edited) and rendered by matching files in
 * acf-layouts/. The FAQ reuses the existing cs_faq_accordion layout.
 *
 * Every field is editable from the WordPress admin; render files fall back to
 * the original HTML copy when a field is empty so the page is pixel-perfect out
 * of the box.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Append the AI Toolkit section layouts to the existing flexible field.
 *
 * @param array $field The cms_content flexible-content field.
 * @return array
 */
function competiscan_register_toolkit_layouts( $field ) {
	if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
		return $field;
	}

	$t = function ( $key, $name, $label, $type = 'text', $extra = array() ) {
		$base = array(
			'key'   => $key,
			'label' => $label,
			'name'  => $name,
			'_name' => $name,
			'type'  => $type,
		);
		if ( 'textarea' === $type ) {
			$base['new_lines'] = '';
		}
		return array_merge( $base, $extra );
	};

	$layouts = array();

	// 1. Hero.
	$layouts['layout_cs_tk_hero'] = array(
		'key'        => 'layout_cs_tk_hero',
		'name'       => 'cs_tk_hero',
		'label'      => '🧰 Toolkit – Hero',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_tk_hero_eyebrow', 'eyebrow', 'Eyebrow' ),
			$t( 'field_tk_hero_badge', 'badge', 'Badge (e.g. NEW)' ),
			$t( 'field_tk_hero_title', 'title', 'Title' ),
			$t( 'field_tk_hero_desc', 'description', 'Description', 'textarea', array( 'rows' => 3 ) ),
			$t( 'field_tk_hero_b1l', 'btn1_label', 'Primary button label' ),
			$t( 'field_tk_hero_b1u', 'btn1_url', 'Primary button URL' ),
			$t( 'field_tk_hero_b2l', 'btn2_label', 'Secondary button label' ),
			$t( 'field_tk_hero_b2u', 'btn2_url', 'Secondary button URL' ),
		),
	);

	// 2. Analysis modules.
	$layouts['layout_cs_tk_modules'] = array(
		'key'        => 'layout_cs_tk_modules',
		'name'       => 'cs_tk_modules',
		'label'      => '🧰 Toolkit – Analysis Modules',
		'display'    => 'block',
		'sub_fields' => array(
			$t(
				'field_tk_modules_items', 'modules', 'Modules', 'repeater',
				array(
					'layout'       => 'block',
					'button_label' => 'Add module',
					'sub_fields'   => array(
						$t( 'field_tk_mod_num', 'number', 'Number' ),
						$t( 'field_tk_mod_title', 'title', 'Title' ),
						$t( 'field_tk_mod_text', 'text', 'Text', 'textarea', array( 'rows' => 3 ) ),
					),
				)
			),
		),
	);

	// 3. Key capabilities.
	$layouts['layout_cs_tk_capabilities'] = array(
		'key'        => 'layout_cs_tk_capabilities',
		'name'       => 'cs_tk_capabilities',
		'label'      => '🧰 Toolkit – Key Capabilities',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_tk_cap_heading', 'heading', 'Section heading' ),
			$t(
				'field_tk_cap_items', 'items', 'Capabilities', 'repeater',
				array(
					'layout'       => 'table',
					'button_label' => 'Add capability',
					'sub_fields'   => array(
						$t( 'field_tk_cap_t', 'title', 'Title' ),
						$t( 'field_tk_cap_x', 'text', 'Text' ),
					),
				)
			),
		),
	);

	// 4. Deliverables carousel.
	$layouts['layout_cs_tk_deliverables'] = array(
		'key'        => 'layout_cs_tk_deliverables',
		'name'       => 'cs_tk_deliverables',
		'label'      => '🧰 Toolkit – Deliverables Carousel',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_tk_del_title', 'title', 'Section title' ),
			$t( 'field_tk_del_intro', 'intro', 'Section intro', 'textarea', array( 'rows' => 2 ) ),
			$t( 'field_tk_del_caption', 'caption', 'Caption under carousel' ),
			$t(
				'field_tk_del_slides', 'slides', 'Slides', 'repeater',
				array(
					'layout'       => 'block',
					'button_label' => 'Add slide',
					'sub_fields'   => array(
						$t( 'field_tk_del_img', 'image', 'Image', 'image', array( 'return_format' => 'url' ) ),
						$t( 'field_tk_del_h', 'title', 'Title' ),
						$t( 'field_tk_del_sub', 'subtitle', 'Subtitle' ),
						$t( 'field_tk_del_lead', 'lead', 'Lead paragraph', 'textarea', array( 'rows' => 2 ) ),
						$t( 'field_tk_del_bul', 'bullets', 'Bullets (one per line, HTML allowed)', 'textarea', array( 'rows' => 5 ) ),
					),
				)
			),
		),
	);

	// 5. White paper (gated CF7 form).
	$layouts['layout_cs_tk_whitepaper'] = array(
		'key'        => 'layout_cs_tk_whitepaper',
		'name'       => 'cs_tk_whitepaper',
		'label'      => '🧰 Toolkit – White Paper / Case Study',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_tk_wp_eyebrow', 'eyebrow', 'Eyebrow' ),
			$t( 'field_tk_wp_title', 'title', 'Title' ),
			$t( 'field_tk_wp_desc', 'description', 'Description', 'textarea', array( 'rows' => 3 ) ),
			$t( 'field_tk_wp_form', 'cf7_form_id', 'CF7 form ID or slug', 'text', array( 'instructions' => 'Leave blank to use the "Turning Credit Card Onboarding into Continuous Growth" form.' ) ),
		),
	);

	// 6. CTA band.
	$layouts['layout_cs_tk_cta'] = array(
		'key'        => 'layout_cs_tk_cta',
		'name'       => 'cs_tk_cta',
		'label'      => '🧰 Toolkit – CTA',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_tk_cta_title', 'title', 'Title' ),
			$t( 'field_tk_cta_desc', 'description', 'Description', 'textarea', array( 'rows' => 3 ) ),
			$t( 'field_tk_cta_bl', 'btn_label', 'Button label' ),
			$t( 'field_tk_cta_bu', 'btn_url', 'Button URL' ),
		),
	);

	foreach ( $layouts as $key => $layout ) {
		$field['layouts'][ $key ] = $layout;
	}
	return $field;
}
add_filter( 'acf/load_field/key=field_cs_flexible_content', 'competiscan_register_toolkit_layouts' );

/**
 * One-time: put the AI Toolkit page on template-cms.php and seed its sections.
 *
 * Only the layout order is seeded; each layout renders the original copy as a
 * fallback, so the page is complete immediately and every field is editable.
 */
function competiscan_bootstrap_toolkit_page() {
	if ( get_option( 'competiscan_toolkit_seeded_v3' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}
	$page = get_page_by_path( 'ai-toolkit' );
	if ( ! $page ) {
		return;
	}
	$id = $page->ID;

	update_post_meta( $id, '_wp_page_template', 'template-cms.php' );

	$rows = array(
		array( 'acf_fc_layout' => 'cs_tk_hero' ),
		array( 'acf_fc_layout' => 'cs_tk_modules' ),
		array( 'acf_fc_layout' => 'cs_tk_capabilities' ),
		array( 'acf_fc_layout' => 'cs_tk_deliverables' ),
		array( 'acf_fc_layout' => 'cs_tk_whitepaper' ),
		array( 'acf_fc_layout' => 'cs_tk_cta' ),
		array( 'acf_fc_layout' => 'cs_faq_accordion' ),
	);

	// Use the field KEY (reliable for flexible content) rather than the name.
	$ok = update_field( 'field_cs_flexible_content', $rows, $id );

	if ( false !== $ok ) {
		update_option( 'competiscan_toolkit_seeded_v3', '1' );
	}
}
add_action( 'wp_loaded', 'competiscan_bootstrap_toolkit_page', 25 );

/**
 * Helper: does the flexible field already have rows for this post?
 *
 * @param string $field Field name.
 * @param int    $id    Post ID.
 * @return bool
 */
function have_rows_exists( $field, $id ) {
	$val = get_field( $field, $id, false );
	return ! empty( $val );
}
