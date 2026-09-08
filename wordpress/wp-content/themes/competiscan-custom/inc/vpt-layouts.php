<?php
/**
 * Value Proposition Trackers page — flexible-content wiring.
 *
 * Reuses shared layouts for the CTA (cs_careers_cta) and FAQ (cs_faq_accordion)
 * and registers three new layouts unique to this page: the text intro
 * (cs_vpt_intro), the feature tile with the dashboard mock (cs_vpt_tile) and the
 * "Trackers by category" grid (cs_vpt_categories). Assigns template-cms.php and
 * seeds the sections. Every field is editable from the admin.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function competiscan_register_vpt_layouts( $field ) {
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
	$target = array( 'choices' => array( '_self' => 'Same tab (_self)', '_blank' => 'New tab (_blank)' ), 'default_value' => '_self', 'ui' => 0, 'ajax' => 0, 'allow_null' => 0, 'multiple' => 0, 'return_format' => 'value', 'placeholder' => '' );

	// 1. Text intro.
	$field['layouts']['layout_cs_vpt_intro'] = array(
		'key'        => 'layout_cs_vpt_intro',
		'name'       => 'cs_vpt_intro',
		'label'      => '📈 VPT – Text Intro',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_vpt_intro_eyebrow', 'eyebrow', 'Eyebrow' ),
			$t( 'field_vpt_intro_title', 'title', 'Title' ),
			$t( 'field_vpt_intro_desc', 'description', 'Description (HTML allowed)', 'textarea', array( 'rows' => 4 ) ),
		),
	);

	// 2. Feature tile with dashboard mock.
	$field['layouts']['layout_cs_vpt_tile'] = array(
		'key'        => 'layout_cs_vpt_tile',
		'name'       => 'cs_vpt_tile',
		'label'      => '📈 VPT – Feature Tile + Dashboard',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_vpt_tile_badge', 'badge', 'Badge label' ),
			$t( 'field_vpt_tile_title', 'title', 'Title' ),
			$t( 'field_vpt_tile_desc', 'description', 'Description', 'textarea', array( 'rows' => 3 ) ),
			$t(
				'field_vpt_tile_bullets', 'bullets', 'Bullets (HTML allowed)', 'repeater',
				array( 'layout' => 'block', 'button_label' => 'Add bullet', 'sub_fields' => array( $t( 'field_vpt_tile_bullet', 'text', 'Text', 'textarea', array( 'rows' => 2 ) ) ) )
			),
			$t( 'field_vpt_tile_bl', 'btn_label', 'Button label' ),
			$t( 'field_vpt_tile_bu', 'btn_url', 'Button URL' ),
			$t( 'field_vpt_tile_bt', 'btn_target', 'Button target', 'select', $target ),
			$t( 'field_vpt_tile_br', 'btn_rel', 'Button rel (optional)' ),
			$t( 'field_vpt_tile_foot', 'footnote', 'Footnote (HTML allowed)', 'textarea', array( 'rows' => 2 ) ),
			$t( 'field_vpt_tile_tablelabel', 'table_label', 'Dashboard title' ),
			$t(
				'field_vpt_tile_filters', 'filters', 'Dashboard filters', 'repeater',
				array( 'layout' => 'table', 'button_label' => 'Add filter', 'sub_fields' => array( $t( 'field_vpt_tile_filter', 'label', 'Label' ) ) )
			),
			$t(
				'field_vpt_tile_rows', 'rows', 'Dashboard table rows', 'repeater',
				array(
					'layout' => 'block', 'button_label' => 'Add row',
					'sub_fields' => array(
						$t( 'field_vpt_row_name', 'name', 'Card name' ),
						$t( 'field_vpt_row_issuer', 'issuer', 'Issuer' ),
						$t( 'field_vpt_row_type', 'type', 'Type' ),
						$t( 'field_vpt_row_apr', 'apr', 'Reg. APR' ),
						$t( 'field_vpt_row_fee', 'fee', 'Annual fee' ),
					),
				)
			),
		),
	);

	// 3. Trackers by category grid.
	$field['layouts']['layout_cs_vpt_categories'] = array(
		'key'        => 'layout_cs_vpt_categories',
		'name'       => 'cs_vpt_categories',
		'label'      => '📈 VPT – Trackers by Category',
		'display'    => 'block',
		'sub_fields' => array(
			$t( 'field_vpt_cat_heading', 'heading', 'Heading' ),
			$t(
				'field_vpt_cat_items', 'items', 'Categories', 'repeater',
				array(
					'layout' => 'block', 'button_label' => 'Add category',
					'sub_fields' => array(
						$t( 'field_vpt_cat_title', 'title', 'Title' ),
						$t( 'field_vpt_cat_desc', 'description', 'Description', 'textarea', array( 'rows' => 2 ) ),
					),
				)
			),
		),
	);

	return $field;
}
add_filter( 'acf/load_field/key=field_cs_flexible_content', 'competiscan_register_vpt_layouts' );

/**
 * One-time: put the Value Prop Trackers page on template-cms.php and seed sections.
 */
function competiscan_bootstrap_vpt_page() {
	if ( get_option( 'competiscan_vpt_page_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}
	$page = get_page_by_path( 'value-proposition-trackers' );
	if ( ! $page ) {
		return;
	}
	$id = $page->ID;

	update_post_meta( $id, '_wp_page_template', 'template-cms.php' );

	$rows = array(
		array( 'acf_fc_layout' => 'cs_vpt_intro' ),
		array( 'acf_fc_layout' => 'cs_vpt_tile' ),
		array( 'acf_fc_layout' => 'cs_vpt_categories' ),
		array(
			'acf_fc_layout' => 'cs_careers_cta',
			'title'         => 'See how your competitive landscape is changing',
			'description'   => 'Request a walkthrough of the Value Prop Trackers and see the live dashboard for your category.',
			'btn_label'     => 'See it in action',
			'btn_url'       => 'mailto:contactus@competiscan.com?subject=Value Prop Tracker demo',
		),
		array(
			'acf_fc_layout' => 'cs_faq_accordion',
			'title'         => 'Got Questions?',
			'faqs'          => array(
				array( 'question' => 'Who is Competiscan?', 'answer' => 'Competiscan is a leading-edge market intelligence company, providing clients with best-in-class service.' ),
				array( 'question' => 'What are Value Prop Trackers?', 'answer' => "Value Prop Trackers supplement Competiscan's database of direct and digital marketing with website tracking, incorporating public/web offers, promotions, and fees so you can see how competitors' strategies change across media channels over time." ),
				array( 'question' => 'What data does the credit card tracker include?', 'answer' => 'Dozens of data points across 600+ credit cards, including regular and intro APR, annual fees, and rewards, filterable by issuer, card type, industry, and risk group, with offer trends across direct mail, email, and web in one dashboard.' ),
				array( 'question' => 'What channels does Competiscan monitor?', 'answer' => 'We monitor a variety of media channels for a comprehensive view of how competitors communicate with customers and prospects, including direct mail, email, digital, social media, and print.' ),
				array( 'question' => 'Which categories are available?', 'answer' => 'Value Prop Trackers are available for credit cards, deposits, retail, and travel loyalty programs.' ),
				array( 'question' => 'Is there an AI feature?', 'answer' => 'Yes, an AI-powered chat feature lets you ask questions, set up custom tables, and more, directly within the dashboard.' ),
			),
		),
	);
	$ok = update_field( 'field_cs_flexible_content', $rows, $id );

	if ( false !== $ok ) {
		update_option( 'competiscan_vpt_page_seeded', '1' );
	}
}
add_action( 'wp_loaded', 'competiscan_bootstrap_vpt_page', 31 );
