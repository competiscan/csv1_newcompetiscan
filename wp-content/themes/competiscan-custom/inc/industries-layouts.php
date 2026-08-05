<?php
/**
 * Industries page — flexible-content wiring.
 *
 * Reuses shared layouts for common sections (hero → cs_careers_hero, CTA band →
 * cs_careers_cta, FAQ → cs_faq_accordion) and registers TWO new layouts unique
 * to this page: the featured-industries cards (cs_ind_featured) and the other-
 * industries grid (cs_ind_grid). Both listings are dynamic ACF repeaters — every
 * item's icon, title, sub-categories/label are editable from the admin.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function competiscan_register_industries_layouts( $field ) {
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

	// Featured industries (cards with sub-category chips).
	$field['layouts']['layout_cs_ind_featured'] = array(
		'key'        => 'layout_cs_ind_featured',
		'name'       => 'cs_ind_featured',
		'label'      => '🏢 Industries – Featured Cards',
		'display'    => 'block',
		'sub_fields' => array(
			$t(
				'field_ind_feat_items', 'items', 'Featured industries', 'repeater',
				array(
					'layout'       => 'block',
					'button_label' => 'Add industry',
					'sub_fields'   => array(
						$t( 'field_ind_feat_title', 'title', 'Title' ),
						$t( 'field_ind_feat_subs', 'subcategories', 'Sub-categories (comma separated)' ),
						$t( 'field_ind_feat_icon', 'icon', 'Custom icon (optional)', 'image', array( 'return_format' => 'url', 'preview_size' => 'thumbnail' ) ),
					),
				)
			),
		),
	);

	// Other industries grid (icon + label items).
	$field['layouts']['layout_cs_ind_grid'] = array(
		'key'        => 'layout_cs_ind_grid',
		'name'       => 'cs_ind_grid',
		'label'      => '🏢 Industries – Icon Grid',
		'display'    => 'block',
		'sub_fields' => array(
			$t(
				'field_ind_grid_items', 'items', 'Industries', 'repeater',
				array(
					'layout'       => 'table',
					'button_label' => 'Add industry',
					'sub_fields'   => array(
						$t( 'field_ind_grid_label', 'label', 'Label' ),
						$t( 'field_ind_grid_icon', 'icon', 'Custom icon (optional)', 'image', array( 'return_format' => 'url', 'preview_size' => 'thumbnail' ) ),
					),
				)
			),
			$t( 'field_ind_grid_more_show', 'and_more_show', 'Show the "And more" card', 'true_false', array( 'ui' => 1, 'message' => '', 'default_value' => 1 ) ),
			$t( 'field_ind_grid_more', 'and_more_label', '"And more" label' ),
			$t( 'field_ind_grid_more_icon', 'and_more_icon', '"And more" custom icon (optional)', 'image', array( 'return_format' => 'url', 'preview_size' => 'thumbnail' ) ),
			$t( 'field_ind_grid_more_url', 'and_more_url', '"And more" link URL (optional)', 'text' ),
		),
	);

	return $field;
}
add_filter( 'acf/load_field/key=field_cs_flexible_content', 'competiscan_register_industries_layouts' );

/**
 * One-time: put the Industries page on template-cms.php and seed sections.
 */
function competiscan_bootstrap_industries_page() {
	if ( get_option( 'competiscan_industries_page_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}
	$page = get_page_by_path( 'industries' );
	if ( ! $page ) {
		return;
	}
	$id = $page->ID;

	update_post_meta( $id, '_wp_page_template', 'template-cms.php' );

	$rows = array(
		array(
			'acf_fc_layout' => 'cs_careers_hero',
			'eyebrow'       => 'Industries',
			'title'         => 'Actionable insights for your industry',
			'description'   => 'Providing you with the data and expertise you need to stay a step ahead of your industry competitors.',
			'btn1_label'    => 'See industries covered',
			'btn1_url'      => '#industries',
			'btn2_label'    => 'Talk to us',
			'btn2_url'      => '#learn',
		),
		array( 'acf_fc_layout' => 'cs_ind_featured' ),
		array( 'acf_fc_layout' => 'cs_ind_grid' ),
		array(
			'acf_fc_layout' => 'cs_careers_cta',
			'title'         => "Don't see your industry? We likely cover it.",
			'description'   => 'Tell us the market you compete in and our team will show you exactly what Competiscan tracks for it.',
			'btn_label'     => 'See what we track',
			'btn_url'       => 'mailto:contactus@competiscan.com?subject=Industry coverage',
		),
		array( 'acf_fc_layout' => 'cs_faq_accordion' ),
	);
	$ok = update_field( 'field_cs_flexible_content', $rows, $id );

	if ( false !== $ok ) {
		update_option( 'competiscan_industries_page_seeded', '1' );
	}
}
add_action( 'wp_loaded', 'competiscan_bootstrap_industries_page', 29 );
