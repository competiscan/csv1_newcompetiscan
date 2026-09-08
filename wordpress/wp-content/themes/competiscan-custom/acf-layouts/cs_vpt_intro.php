<?php
/**
 * Value Prop Trackers — Text intro (flexible-content layout). Source fallback.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'eyebrow' ) ?: 'Solution 03';
$title   = get_sub_field( 'title' ) ?: 'Value Proposition Trackers';
$desc    = get_sub_field( 'description' );
if ( '' === $desc || null === $desc || false === $desc ) {
	$desc = "Get a pulse check of the competitive landscape. Supplement Competiscan's unsurpassed database of direct and digital marketing with website tracking. Incorporate public/web offers, promotions, and fees, and see how competitors' strategies change across media channels. Competiscan's Value Prop Trackers answer the question: <strong class=\"cs-x179\">how is my competitive landscape changing over time?</strong>";
}
?>
<section class="cs-x358" id="top">
  <span class="cs-x113"><?php echo esc_html( $eyebrow ); ?></span>
  <h1 class="cs-x19"><?php echo esc_html( $title ); ?></h1>
  <p class="cs-x20"><?php echo wp_kses_post( $desc ); ?></p>
</section>
