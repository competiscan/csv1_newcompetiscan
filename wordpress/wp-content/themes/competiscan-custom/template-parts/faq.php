<?php
/**
 * FAQ section for the Home and Insights pages.
 *
 * Content is fully editable per page from the admin (ACF "FAQ Section" fields —
 * see inc/faq-fields.php). This part only reads those page fields and delegates the
 * actual rendering to the single shared accordion, acf-layouts/cs_faq_accordion.php,
 * so there is no duplicated markup and the design/behaviour are unchanged.
 *
 * Pass 'variant' => 'insights' for the Insights markup variant; defaults to 'home'.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$variant = isset( $args['variant'] ) ? $args['variant'] : 'home';

// Resolve the current page (front page has no queried object id in some contexts).
$faq_page_id = get_queried_object_id();
if ( ! $faq_page_id && is_front_page() ) {
	$faq_page_id = (int) get_option( 'page_on_front' );
}

$faq_has_acf = function_exists( 'get_field' ) && $faq_page_id;

// Enable/disable — default on when the field has never been set.
$faq_enabled = $faq_has_acf ? get_field( 'faq_enable', $faq_page_id ) : true;
if ( false === $faq_enabled || 0 === $faq_enabled || '0' === $faq_enabled ) {
	return; // Section disabled for this page.
}

$faq_title = $faq_has_acf ? get_field( 'faq_title', $faq_page_id ) : '';
$faq_desc  = $faq_has_acf ? get_field( 'faq_description', $faq_page_id ) : '';
$faq_rows  = $faq_has_acf ? get_field( 'faq_items', $faq_page_id ) : array();

// Normalise the repeater rows and apply the optional "Order" field.
$faq_items = array();
if ( is_array( $faq_rows ) ) {
	foreach ( $faq_rows as $index => $row ) {
		$faq_items[] = array(
			'question' => isset( $row['question'] ) ? $row['question'] : '',
			'answer'   => isset( $row['answer'] ) ? $row['answer'] : '',
			'order'    => ( isset( $row['order'] ) && '' !== $row['order'] ) ? (int) $row['order'] : null,
			'_i'       => $index,
		);
	}

	$faq_has_order = false;
	foreach ( $faq_items as $row ) {
		if ( null !== $row['order'] ) {
			$faq_has_order = true;
			break;
		}
	}
	if ( $faq_has_order ) {
		usort(
			$faq_items,
			static function ( $a, $b ) {
				$ao = null === $a['order'] ? PHP_INT_MAX : $a['order'];
				$bo = null === $b['order'] ? PHP_INT_MAX : $b['order'];
				return ( $ao === $bo ) ? ( $a['_i'] <=> $b['_i'] ) : ( $ao <=> $bo );
			}
		);
	}
}

// Hand off to the single shared accordion (design + behaviour unchanged).
get_template_part(
	'acf-layouts/cs_faq_accordion',
	null,
	array(
		'variant'     => $variant,
		'title'       => $faq_title,
		'description' => $faq_desc,
		'faqs'        => $faq_items,
	)
);
