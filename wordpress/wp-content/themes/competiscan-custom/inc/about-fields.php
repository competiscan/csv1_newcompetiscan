<?php
/**
 * ACF field group for the About Us page content.
 *
 * Every text block on the About page (hero, Our Story, Superior Service, the
 * testimonials band, the Connect CTA and the FAQ) is editable here so the whole
 * page can be maintained from the admin without touching code. The template
 * falls back to the original copy when a field is empty, so the page renders
 * correctly out of the box before anything is edited.
 *
 * Fields are shown only on a page using the "About" template.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function competiscan_register_about_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_competiscan_about',
			'title'    => 'About Page Content',
			'fields'   => array(

				// --- Hero -------------------------------------------------.
				array(
					'key'   => 'field_about_tab_hero',
					'label' => 'Hero',
					'type'  => 'tab',
				),
				array( 'key' => 'field_about_hero_eyebrow', 'label' => 'Eyebrow', 'name' => 'about_hero_eyebrow', 'type' => 'text' ),
				array( 'key' => 'field_about_hero_title', 'label' => 'Title', 'name' => 'about_hero_title', 'type' => 'text' ),
				array( 'key' => 'field_about_hero_text', 'label' => 'Intro paragraph', 'name' => 'about_hero_text', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_about_hero_btn1', 'label' => 'Primary button label', 'name' => 'about_hero_btn1', 'type' => 'text' ),
				array( 'key' => 'field_about_hero_btn2', 'label' => 'Secondary button label', 'name' => 'about_hero_btn2', 'type' => 'text' ),

				// --- Story + Service -------------------------------------.
				array(
					'key'   => 'field_about_tab_story',
					'label' => 'Story & Service',
					'type'  => 'tab',
				),
				array( 'key' => 'field_about_story_title', 'label' => 'Our Story — title', 'name' => 'about_story_title', 'type' => 'text' ),
				array( 'key' => 'field_about_story_body', 'label' => 'Our Story — body', 'name' => 'about_story_body', 'type' => 'textarea', 'rows' => 4 ),
				array( 'key' => 'field_about_service_title', 'label' => 'Superior Service — title', 'name' => 'about_service_title', 'type' => 'text' ),
				array( 'key' => 'field_about_service_body', 'label' => 'Superior Service — body', 'name' => 'about_service_body', 'type' => 'textarea', 'rows' => 4 ),

				// --- Testimonials ----------------------------------------.
				array(
					'key'   => 'field_about_tab_testi',
					'label' => 'Testimonials',
					'type'  => 'tab',
				),
				array( 'key' => 'field_about_testi_title', 'label' => 'Section title', 'name' => 'about_testi_title', 'type' => 'text' ),
				array(
					'key'          => 'field_about_testi_items',
					'label'        => 'Quotes',
					'name'         => 'about_testi_items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add quote',
					'sub_fields'   => array(
						array( 'key' => 'field_about_testi_quote', 'label' => 'Quote', 'name' => 'quote', 'type' => 'textarea', 'rows' => 3 ),
						array( 'key' => 'field_about_testi_author', 'label' => 'Attribution', 'name' => 'author', 'type' => 'text' ),
					),
				),

				// --- Team heading ----------------------------------------.
				array(
					'key'   => 'field_about_tab_team',
					'label' => 'Team',
					'type'  => 'tab',
				),
				array( 'key' => 'field_about_team_title', 'label' => 'Team — title', 'name' => 'about_team_title', 'type' => 'text' ),
				array( 'key' => 'field_about_team_text', 'label' => 'Team — intro', 'name' => 'about_team_text', 'type' => 'textarea', 'rows' => 3 ),
				array(
					'key'     => 'field_about_team_note',
					'label'   => '',
					'type'    => 'message',
					'message' => 'Team members are managed under <strong>Team</strong> in the admin menu. Add, edit, remove or reorder them there.',
				),

				// --- Connect CTA -----------------------------------------.
				array(
					'key'   => 'field_about_tab_cta',
					'label' => 'Connect CTA',
					'type'  => 'tab',
				),
				array( 'key' => 'field_about_cta_title', 'label' => 'CTA title (plain part)', 'name' => 'about_cta_title', 'type' => 'text' ),
				array( 'key' => 'field_about_cta_title_accent', 'label' => 'CTA title (accent part)', 'name' => 'about_cta_title_accent', 'type' => 'text' ),
				array( 'key' => 'field_about_cta_text', 'label' => 'CTA body', 'name' => 'about_cta_text', 'type' => 'textarea', 'rows' => 3 ),
				array( 'key' => 'field_about_cta_btn', 'label' => 'CTA button label', 'name' => 'about_cta_btn', 'type' => 'text' ),
				array( 'key' => 'field_about_cta_email', 'label' => 'Contact email', 'name' => 'about_cta_email', 'type' => 'text' ),

				// --- FAQ --------------------------------------------------.
				array(
					'key'   => 'field_about_tab_faq',
					'label' => 'FAQ',
					'type'  => 'tab',
				),
				array( 'key' => 'field_about_faq_title', 'label' => 'FAQ title (line 1)', 'name' => 'about_faq_title', 'type' => 'text' ),
				array( 'key' => 'field_about_faq_title2', 'label' => 'FAQ title (line 2, accent)', 'name' => 'about_faq_title2', 'type' => 'text' ),
				array( 'key' => 'field_about_faq_intro', 'label' => 'FAQ intro', 'name' => 'about_faq_intro', 'type' => 'textarea', 'rows' => 2 ),
				array(
					'key'          => 'field_about_faq_items',
					'label'        => 'Questions',
					'name'         => 'about_faq_items',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add question',
					'sub_fields'   => array(
						array( 'key' => 'field_about_faq_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text' ),
						array( 'key' => 'field_about_faq_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'textarea', 'rows' => 3 ),
					),
				),
			),
			'location'     => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'template-about.php',
					),
				),
			),
			'active'       => true,
			'show_in_rest' => 1,
		)
	);
}
add_action( 'acf/init', 'competiscan_register_about_fields' );
