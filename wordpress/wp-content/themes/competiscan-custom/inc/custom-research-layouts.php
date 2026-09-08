<?php
/**
 * Custom Research & Analysis page — flexible-content wiring.
 *
 * Reuses existing shared layouts for the common sections (hero → cs_careers_hero,
 * CTA band → cs_careers_cta, FAQ → cs_faq_accordion) and registers ONE new
 * layout for the section unique to this page: the study-types grid
 * (cs_cr_studies). Assigns template-cms.php to the page and seeds the sections,
 * populating the reused hero/CTA with this page's copy. Everything stays editable.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the unique "Study Types Grid" layout on the shared flexible field.
 */
function competiscan_register_custom_research_layouts( $field ) {
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

	$field['layouts']['layout_cs_cr_studies'] = array(
		'key'        => 'layout_cs_cr_studies',
		'name'       => 'cs_cr_studies',
		'label'      => '🔬 Custom Research – Study Types Grid',
		'display'    => 'block',
		'sub_fields' => array(
			$t(
				'field_cr_studies', 'studies', 'Study Types', 'repeater',
				array(
					'layout'       => 'block',
					'button_label' => 'Add study type',
					'sub_fields'   => array(
						$t( 'field_cr_study_title', 'title', 'Title' ),
						$t( 'field_cr_study_text', 'text', 'Text', 'textarea', array( 'rows' => 3 ) ),
					),
				)
			),
			$t( 'field_cr_special_h', 'special_heading', 'Highlight card — heading' ),
			$t( 'field_cr_special_t', 'special_text', 'Highlight card — text', 'textarea', array( 'rows' => 2 ) ),
			$t( 'field_cr_special_bl', 'special_btn_label', 'Highlight card — button label' ),
			$t( 'field_cr_special_bu', 'special_btn_url', 'Highlight card — button URL' ),
		),
	);
	return $field;
}
add_filter( 'acf/load_field/key=field_cs_flexible_content', 'competiscan_register_custom_research_layouts' );

/**
 * One-time: put the Custom Research page on template-cms.php and seed sections.
 */
function competiscan_bootstrap_custom_research_page() {
	if ( get_option( 'competiscan_cr_page_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}
	$page = get_page_by_path( 'custom-research-analysis' );
	if ( ! $page ) {
		return;
	}
	$id = $page->ID;

	update_post_meta( $id, '_wp_page_template', 'template-cms.php' );

	$rows = array(
		array(
			'acf_fc_layout' => 'cs_careers_hero',
			'eyebrow'       => 'Solution 04',
			'title'         => 'Custom Research & Analysis',
			'description'   => 'Custom research capabilities and services designed to deepen your competitive and market knowledge.',
			'btn1_label'    => 'Explore study types',
			'btn1_url'      => '#studies',
			'btn2_label'    => 'Talk to us',
			'btn2_url'      => '#learn',
		),
		array( 'acf_fc_layout' => 'cs_cr_studies' ),
		array(
			'acf_fc_layout' => 'cs_careers_cta',
			'title'         => 'Have a research question in mind?',
			'description'   => 'Tell us what you need to know and our team will design the right study to deepen your competitive and market knowledge.',
			'btn_label'     => 'Get answers',
			'btn_url'       => 'mailto:contactus@competiscan.com?subject=Custom Research & Analysis',
		),
		array( 'acf_fc_layout' => 'cs_faq_accordion' ),
	);
	$ok = update_field( 'field_cs_flexible_content', $rows, $id );

	if ( false !== $ok ) {
		update_option( 'competiscan_cr_page_seeded', '1' );
	}
}
add_action( 'wp_loaded', 'competiscan_bootstrap_custom_research_page', 28 );
