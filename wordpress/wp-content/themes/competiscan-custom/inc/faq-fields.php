<?php
/**
 * Page-level FAQ fields (ACF Pro).
 *
 * Makes the FAQ content on the Home and Insights pages fully editable from the
 * admin, per page, so each page keeps its own questions/answers. The frontend
 * still renders through the single shared accordion (acf-layouts/cs_faq_accordion.php)
 * — no new FAQ design and no duplicated markup. The other pages (About, MID and the
 * flexible-content pages) already manage their FAQ through their own ACF fields and
 * are intentionally left untouched.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Insights page ID (slug "insights"), used for the field-group location + seed.
 *
 * @return int
 */
function competiscan_insights_page_id() {
	$page = get_page_by_path( 'insights' );
	return $page ? (int) $page->ID : 0;
}

/**
 * Register the "FAQ Section" field group on the front page + the Insights page.
 */
function competiscan_register_faq_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$enable_shown = array(
		array(
			array(
				'field'    => 'field_faq_enable',
				'operator' => '==',
				'value'    => '1',
			),
		),
	);

	$location = array(
		array(
			array(
				'param'    => 'page_type',
				'operator' => '==',
				'value'    => 'front_page',
			),
		),
	);

	$insights_id = competiscan_insights_page_id();
	if ( $insights_id ) {
		$location[] = array(
			array(
				'param'    => 'page',
				'operator' => '==',
				'value'    => (string) $insights_id,
			),
		);
	}

	acf_add_local_field_group(
		array(
			'key'      => 'group_competiscan_page_faq',
			'title'    => 'FAQ Section',
			'fields'   => array(
				array(
					'key'           => 'field_faq_enable',
					'label'         => 'Enable FAQ section',
					'name'          => 'faq_enable',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
					'instructions'  => 'Turn the FAQ section on this page on or off.',
				),
				array(
					'key'               => 'field_faq_title',
					'label'             => 'FAQ Title',
					'name'              => 'faq_title',
					'type'              => 'text',
					'placeholder'       => 'Got Questions?',
					'conditional_logic' => $enable_shown,
				),
				array(
					'key'               => 'field_faq_description',
					'label'             => 'FAQ Intro / Description',
					'name'              => 'faq_description',
					'type'              => 'textarea',
					'rows'              => 3,
					'new_lines'         => '',
					'conditional_logic' => $enable_shown,
				),
				array(
					'key'               => 'field_faq_items',
					'label'             => 'FAQ Items',
					'name'              => 'faq_items',
					'type'              => 'repeater',
					'layout'            => 'block',
					'button_label'      => 'Add FAQ',
					'conditional_logic' => $enable_shown,
					'sub_fields'        => array(
						array(
							'key'     => 'field_faq_question',
							'label'   => 'Question',
							'name'    => 'question',
							'type'    => 'text',
							'wrapper' => array( 'width' => '70' ),
						),
						array(
							'key'          => 'field_faq_order',
							'label'        => 'Order',
							'name'         => 'order',
							'type'         => 'number',
							'wrapper'      => array( 'width' => '30' ),
							'instructions' => 'Lower first. Leave blank to keep the manual (drag) order.',
						),
						array(
							'key'       => 'field_faq_answer',
							'label'     => 'Answer',
							'name'      => 'answer',
							'type'      => 'textarea',
							'rows'      => 3,
							'new_lines' => '',
						),
					),
				),
			),
			'location' => $location,
			'menu_order' => 5,
		)
	);
}
add_action( 'acf/init', 'competiscan_register_faq_fields' );

/**
 * Seed the current FAQ content into the Home + Insights pages once, so the frontend
 * is unchanged after activation. Only fills fields that are still empty, and each
 * page gets its own independent copy.
 */
function competiscan_seed_page_faq() {
	if ( '1' === get_option( 'competiscan_faq_seeded' ) ) {
		return;
	}
	if ( ! function_exists( 'update_field' ) || ! function_exists( 'get_field' ) ) {
		return;
	}
	if ( false === add_option( 'competiscan_faq_seed_claim', time(), '', 'no' ) ) {
		return;
	}

	$title = 'Got Questions?';
	$desc  = 'Find answers to common questions about Competiscan and how we work.';
	$items = array(
		array( 'question' => 'Who is Competiscan?', 'answer' => 'Competiscan is a leading-edge market intelligence company, providing clients with best-in-class service.', 'order' => 1 ),
		array( 'question' => 'What services does Competiscan provide?', 'answer' => 'Competiscan delivers market and competitive intelligence that helps organizations understand how competitors engage with different audiences across channels. Our capabilities include value proposition tracking, creative trends, customer journeys and experiences, behind-the-login insights, marketing strategies, targeting insights, AI-driven analysis of marketing campaigns, and more. With access to the largest consumer panels in the market, we deliver real-time insight into competitors activities.', 'order' => 2 ),
		array( 'question' => 'What channels does Competiscan monitor?', 'answer' => 'We monitor a variety of media channels for a comprehensive view of how competitors communicate with customers and prospects, including direct mail, email, digital, social media, and print.', 'order' => 3 ),
		array( 'question' => 'What industries does Competiscan cover?', 'answer' => 'Competiscan covers key sectors, including Automotive, Banking, Payment Cards, Energy, Insurance, Investments, Mortgage & Loan, Retail, Travel & Leisure, and Telecom.', 'order' => 4 ),
		array( 'question' => 'What audiences does Competiscan cover?', 'answer' => 'Our panel includes a diverse range of audiences: Consumers, Business Owners, Insurance Producers, Financial Advisors, Mortgage Brokers, and Healthcare Providers.', 'order' => 5 ),
		array( 'question' => 'What parts of the customer journey does Competiscan capture?', 'answer' => 'We provide a 360-degree view of customer-journey communications, including acquisition, follow-up, loyalty, retention, win-back, statements, and upgrade & cross-sell.', 'order' => 6 ),
	);

	$page_ids = array( (int) get_option( 'page_on_front' ), competiscan_insights_page_id() );
	foreach ( $page_ids as $pid ) {
		if ( ! $pid ) {
			continue;
		}
		update_field( 'field_faq_enable', 1, $pid );
		if ( empty( get_field( 'faq_items', $pid ) ) ) {
			update_field( 'field_faq_title', $title, $pid );
			update_field( 'field_faq_description', $desc, $pid );
			update_field( 'field_faq_items', $items, $pid );
		}
	}

	update_option( 'competiscan_faq_seeded', '1' );
}
add_action( 'wp_loaded', 'competiscan_seed_page_faq', 47 );
