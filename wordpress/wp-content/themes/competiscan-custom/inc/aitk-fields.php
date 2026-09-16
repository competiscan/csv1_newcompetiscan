<?php
/**
 * ACF field group for the AI Toolkit page (template-aitk.php).
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function competiscan_register_aitk_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group(
		array(
			'key'      => 'group_competiscan_aitk',
			'title'    => 'AI Toolkit — Content',
			'fields'   => array(
				array( 'key' => 'field_aitk_tab_hero', 'label' => 'Hero', 'type' => 'tab' ),
				array( 'key' => 'field_aitk_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'aitk_hero_eyebrow', 'type' => 'text' ),
				array( 'key' => 'field_aitk_hero_badge', 'label' => 'Eyebrow badge', 'name' => 'aitk_hero_badge', 'type' => 'text' ),
				array( 'key' => 'field_aitk_hero_title', 'label' => 'Title', 'name' => 'aitk_hero_title', 'type' => 'text' ),
				array( 'key' => 'field_aitk_hero_text', 'label' => 'Intro', 'name' => 'aitk_hero_text', 'type' => 'textarea', 'rows' => 4 ),
				array( 'key' => 'field_aitk_hero_btn1', 'label' => 'Primary button', 'name' => 'aitk_hero_btn1', 'type' => 'text' ),
				array( 'key' => 'field_aitk_hero_btn2', 'label' => 'Secondary button', 'name' => 'aitk_hero_btn2', 'type' => 'text' ),

				array( 'key' => 'field_aitk_tab_mod', 'label' => 'Analysis Modules', 'type' => 'tab' ),
				array(
					'key' => 'field_aitk_mods', 'label' => 'Modules', 'name' => 'aitk_mods', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add module',
					'sub_fields' => array(
						array( 'key' => 'field_aitk_mod_num', 'label' => 'Number', 'name' => 'num', 'type' => 'text' ),
						array( 'key' => 'field_aitk_mod_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_aitk_mod_text', 'label' => 'Text', 'name' => 'text', 'type' => 'textarea', 'rows' => 3 ),
					),
				),

				array( 'key' => 'field_aitk_tab_caps', 'label' => 'Key Capabilities', 'type' => 'tab' ),
				array( 'key' => 'field_aitk_caps_title', 'label' => 'Section heading', 'name' => 'aitk_caps_title', 'type' => 'text' ),
				array(
					'key' => 'field_aitk_caps', 'label' => 'Capabilities', 'name' => 'aitk_caps', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add',
					'sub_fields' => array(
						array( 'key' => 'field_aitk_cap_title', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_aitk_cap_text', 'label' => 'Text', 'name' => 'text', 'type' => 'text' ),
					),
				),

				array( 'key' => 'field_aitk_tab_del', 'label' => 'Deliverables', 'type' => 'tab' ),
				array( 'key' => 'field_aitk_del_title', 'label' => 'Section title', 'name' => 'aitk_del_title', 'type' => 'text' ),
				array( 'key' => 'field_aitk_del_intro', 'label' => 'Section intro', 'name' => 'aitk_del_intro', 'type' => 'textarea', 'rows' => 2 ),
				array( 'key' => 'field_aitk_del_caption', 'label' => 'Caption under carousel', 'name' => 'aitk_del_caption', 'type' => 'text' ),
				array(
					'key' => 'field_aitk_dels', 'label' => 'Slides', 'name' => 'aitk_dels', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add slide',
					'sub_fields' => array(
						array( 'key' => 'field_aitk_del_img', 'label' => 'Image', 'name' => 'image', 'type' => 'image', 'return_format' => 'url' ),
						array( 'key' => 'field_aitk_del_h', 'label' => 'Title', 'name' => 'title', 'type' => 'text' ),
						array( 'key' => 'field_aitk_del_sub', 'label' => 'Subtitle', 'name' => 'subtitle', 'type' => 'text' ),
						array( 'key' => 'field_aitk_del_lead', 'label' => 'Lead paragraph', 'name' => 'lead', 'type' => 'textarea', 'rows' => 2 ),
						array( 'key' => 'field_aitk_del_bullets', 'label' => 'Bullets (one per line, HTML allowed)', 'name' => 'bullets', 'type' => 'textarea', 'rows' => 5 ),
					),
				),

				array( 'key' => 'field_aitk_tab_wp', 'label' => 'White Paper', 'type' => 'tab' ),
				array( 'key' => 'field_aitk_wp_eyebrow', 'label' => 'Eyebrow', 'name' => 'aitk_wp_eyebrow', 'type' => 'text' ),
				array( 'key' => 'field_aitk_wp_title', 'label' => 'Title', 'name' => 'aitk_wp_title', 'type' => 'text' ),
				array( 'key' => 'field_aitk_wp_desc', 'label' => 'Description', 'name' => 'aitk_wp_desc', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_aitk_wp_pdf', 'label' => 'PDF file', 'name' => 'aitk_wp_pdf', 'type' => 'file', 'return_format' => 'url' ),
				array( 'key' => 'field_aitk_wp_btn', 'label' => 'Button label', 'name' => 'aitk_wp_btn', 'type' => 'text' ),

				array( 'key' => 'field_aitk_tab_cta', 'label' => 'CTA', 'type' => 'tab' ),
				array( 'key' => 'field_aitk_cta_title', 'label' => 'Title', 'name' => 'aitk_cta_title', 'type' => 'text' ),
				array( 'key' => 'field_aitk_cta_text', 'label' => 'Text', 'name' => 'aitk_cta_text', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_aitk_cta_btn', 'label' => 'Button label', 'name' => 'aitk_cta_btn', 'type' => 'text' ),

				array( 'key' => 'field_aitk_tab_faq', 'label' => 'FAQ', 'type' => 'tab' ),
				array( 'key' => 'field_aitk_faq_title', 'label' => 'FAQ title (line 1)', 'name' => 'aitk_faq_title', 'type' => 'text' ),
				array( 'key' => 'field_aitk_faq_title2', 'label' => 'FAQ title (line 2)', 'name' => 'aitk_faq_title2', 'type' => 'text' ),
				array( 'key' => 'field_aitk_faq_intro', 'label' => 'FAQ intro', 'name' => 'aitk_faq_intro', 'type' => 'textarea', 'rows' => 2 ),
				array(
					'key' => 'field_aitk_faq', 'label' => 'Questions', 'name' => 'aitk_faq', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Add question',
					'sub_fields' => array(
						array( 'key' => 'field_aitk_faq_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text' ),
						array( 'key' => 'field_aitk_faq_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'textarea', 'rows' => 3 ),
					),
				),
			),
			'location' => array(
				array(
					array( 'param' => 'page_template', 'operator' => '==', 'value' => 'template-aitk.php' ),
				),
			),
			'active'   => true,
		)
	);
}
add_action( 'acf/init', 'competiscan_register_aitk_fields' );
