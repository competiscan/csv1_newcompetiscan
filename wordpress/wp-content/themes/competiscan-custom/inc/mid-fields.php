<?php
/**
 * ACF field group for the Market Intelligence Database page.
 *
 * Bound to the template-mid.php template. Every section is editable; the
 * template falls back to the original copy when a field is empty so the page
 * renders correctly out of the box.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function competiscan_register_mid_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group(
		array(
			'key'      => 'group_competiscan_mid',
			'title'    => 'Market Intelligence Database — Content',
			'fields'   => array(
				array( 'key' => 'field_mid_tab_hero', 'label' => 'Hero', 'type' => 'tab' ),
				array( 'key' => 'field_mid_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'mid_hero_eyebrow', 'type' => 'text' ),
				array( 'key' => 'field_mid_hero_title', 'label' => 'Title', 'name' => 'mid_hero_title', 'type' => 'text' ),
				array( 'key' => 'field_mid_hero_text', 'label' => 'Intro', 'name' => 'mid_hero_text', 'type' => 'textarea', 'rows' => 4 ),
				array( 'key' => 'field_mid_hero_btn1', 'label' => 'Primary button', 'name' => 'mid_hero_btn1', 'type' => 'text' ),
				array( 'key' => 'field_mid_hero_btn2', 'label' => 'Secondary button', 'name' => 'mid_hero_btn2', 'type' => 'text' ),

				array( 'key' => 'field_mid_tab_caps', 'label' => 'Capabilities', 'type' => 'tab' ),
				array(
					'key' => 'field_mid_caps', 'label' => 'Capability cards', 'name' => 'mid_caps', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add card',
					'sub_fields' => array(
						array( 'key' => 'field_mid_cap_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_mid_cap_text', 'label' => 'Text', 'name' => 'text', 'type' => 'textarea', 'rows' => 3 ),
					),
				),

				array( 'key' => 'field_mid_tab_wp', 'label' => 'White Paper', 'type' => 'tab' ),
				array( 'key' => 'field_mid_wp_eyebrow', 'label' => 'Eyebrow', 'name' => 'mid_wp_eyebrow', 'type' => 'text' ),
				array( 'key' => 'field_mid_wp_title', 'label' => 'Title', 'name' => 'mid_wp_title', 'type' => 'text' ),
				array( 'key' => 'field_mid_wp_desc', 'label' => 'Description', 'name' => 'mid_wp_desc', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_mid_wp_pdf', 'label' => 'PDF file', 'name' => 'mid_wp_pdf', 'type' => 'file', 'return_format' => 'url' ),
				array( 'key' => 'field_mid_wp_btn', 'label' => 'Download button label', 'name' => 'mid_wp_btn', 'type' => 'text' ),

				array( 'key' => 'field_mid_tab_cov', 'label' => 'Coverage', 'type' => 'tab' ),
				array( 'key' => 'field_mid_channels', 'label' => 'Channels', 'name' => 'mid_channels', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add', 'sub_fields' => array( array( 'key' => 'field_mid_channel', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ) ) ),
				array( 'key' => 'field_mid_industries', 'label' => 'Industries', 'name' => 'mid_industries', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add', 'sub_fields' => array( array( 'key' => 'field_mid_industry', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ) ) ),
				array( 'key' => 'field_mid_audiences', 'label' => 'Audiences', 'name' => 'mid_audiences', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add', 'sub_fields' => array( array( 'key' => 'field_mid_audience', 'label' => 'Label', 'name' => 'label', 'type' => 'text' ) ) ),

				array( 'key' => 'field_mid_tab_cta', 'label' => 'CTA', 'type' => 'tab' ),
				array( 'key' => 'field_mid_cta_title', 'label' => 'Title', 'name' => 'mid_cta_title', 'type' => 'text' ),
				array( 'key' => 'field_mid_cta_text', 'label' => 'Text', 'name' => 'mid_cta_text', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_mid_cta_btn', 'label' => 'Button label', 'name' => 'mid_cta_btn', 'type' => 'text' ),

				array( 'key' => 'field_mid_tab_faq', 'label' => 'FAQ', 'type' => 'tab' ),
				array( 'key' => 'field_mid_faq_title', 'label' => 'FAQ title (line 1)', 'name' => 'mid_faq_title', 'type' => 'text' ),
				array( 'key' => 'field_mid_faq_title2', 'label' => 'FAQ title (line 2)', 'name' => 'mid_faq_title2', 'type' => 'text' ),
				array( 'key' => 'field_mid_faq_intro', 'label' => 'FAQ intro', 'name' => 'mid_faq_intro', 'type' => 'textarea', 'rows' => 2 ),
				array(
					'key' => 'field_mid_faq', 'label' => 'Questions', 'name' => 'mid_faq', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add question',
					'sub_fields' => array(
						array( 'key' => 'field_mid_faq_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text' ),
						array( 'key' => 'field_mid_faq_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'textarea', 'rows' => 3 ),
					),
				),
			),
			'location' => array(
				array(
					array( 'param' => 'page_template', 'operator' => '==', 'value' => 'template-mid.php' ),
				),
			),
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'competiscan_register_mid_fields' );
