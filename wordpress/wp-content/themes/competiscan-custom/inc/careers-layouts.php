<?php
/**
 * Careers page — flexible-content layouts + page bootstrap.
 *
 * Adds the Careers section layouts to the existing "Page Sections" flexible
 * field (via filter, no edits to the shared acf-json), assigns template-cms.php
 * to the Careers page and seeds the section order. The Open Roles section is
 * dynamic (Career CPT); the FAQ reuses cs_faq_accordion. Every field is editable
 * from the admin; render files fall back to the source copy when empty.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function competiscan_register_careers_layouts( $field ) {
	if ( empty( $field['layouts'] ) || ! is_array( $field['layouts'] ) ) {
		return $field;
	}

	$t = function ( $key, $name, $label, $type = 'text', $extra = array() ) {
		$base = array( 'key' => $key, 'label' => $label, 'name' => $name, '_name' => $name, 'type' => $type );
		if ( 'textarea' === $type ) {
			$base['new_lines'] = '';
		}
		return array_merge( $base, $extra );
	};

	$layouts = array();

	$layouts['layout_cs_careers_hero'] = array(
		'key'        => 'layout_cs_careers_hero',
		'name'       => 'cs_careers_hero',
		'label'      => '💼 Careers – Hero',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_ch_eyebrow', 'eyebrow', 'Eyebrow' ),
			$t( 'field_ch_title', 'title', 'Title' ),
			$t( 'field_ch_desc', 'description', 'Description', 'textarea', array( 'rows' => 3 ) ),
			$t( 'field_ch_b1l', 'btn1_label', 'Primary button label' ),
			$t( 'field_ch_b1u', 'btn1_url', 'Primary button URL' ),
			$t( 'field_ch_b2l', 'btn2_label', 'Secondary button label' ),
			$t( 'field_ch_b2u', 'btn2_url', 'Secondary button URL' ),
		),
	);

	$layouts['layout_cs_careers_why'] = array(
		'key'        => 'layout_cs_careers_why',
		'name'       => 'cs_careers_why',
		'label'      => '💼 Careers – Why Join + Benefits',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_cw_heading', 'heading', 'Heading' ),
			$t( 'field_cw_body1', 'body1', 'Paragraph 1', 'textarea', array( 'rows' => 4 ) ),
			$t( 'field_cw_body2', 'body2', 'Paragraph 2', 'textarea', array( 'rows' => 4 ) ),
			$t(
				'field_cw_benefits', 'benefits', 'Benefits', 'repeater',
				array(
					'layout'       => 'table',
					'button_label' => 'Add benefit',
					'sub_fields'   => array( $t( 'field_cw_benefit', 'label', 'Label' ) ),
				)
			),
		),
	);

	$layouts['layout_cs_careers_roles'] = array(
		'key'        => 'layout_cs_careers_roles',
		'name'       => 'cs_careers_roles',
		'label'      => '💼 Careers – Open Roles (dynamic)',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_cr_heading', 'heading', 'Heading' ),
			$t( 'field_cr_intro', 'intro', 'Intro', 'textarea', array( 'rows' => 2 ) ),
			$t( 'field_cr_note', 'footer_note', 'Footer note (HTML allowed)', 'textarea', array( 'rows' => 2 ) ),
		),
	);

	$layouts['layout_cs_careers_cta'] = array(
		'key'        => 'layout_cs_careers_cta',
		'name'       => 'cs_careers_cta',
		'label'      => '💼 Careers – CTA',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_cc_title', 'title', 'Title' ),
			$t( 'field_cc_desc', 'description', 'Description', 'textarea', array( 'rows' => 3 ) ),
			$t( 'field_cc_bl', 'btn_label', 'Button label' ),
			$t( 'field_cc_bu', 'btn_url', 'Button URL' ),
		),
	);

	foreach ( $layouts as $key => $layout ) {
		$field['layouts'][ $key ] = $layout;
	}
	return $field;
}
add_filter( 'acf/load_field/key=field_cs_flexible_content', 'competiscan_register_careers_layouts' );

/**
 * One-time: put the Careers page on template-cms.php and seed its sections.
 */
function competiscan_bootstrap_careers_page() {
	if ( get_option( 'competiscan_careers_page_seeded_v2' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}
	$page = get_page_by_path( 'careers' );
	if ( ! $page ) {
		return;
	}
	$id = $page->ID;

	update_post_meta( $id, '_wp_page_template', 'template-cms.php' );

	$rows = array(
		array( 'acf_fc_layout' => 'cs_careers_hero' ),
		array( 'acf_fc_layout' => 'cs_careers_why' ),
		array( 'acf_fc_layout' => 'cs_careers_roles' ),
		array( 'acf_fc_layout' => 'cs_careers_cta' ),
		array( 'acf_fc_layout' => 'cs_faq_accordion' ),
	);
	$ok = update_field( 'field_cs_flexible_content', $rows, $id );

	if ( false !== $ok ) {
		update_option( 'competiscan_careers_page_seeded_v2', '1' );
	}
}
add_action( 'wp_loaded', 'competiscan_bootstrap_careers_page', 27 );
