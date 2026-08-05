<?php
/**
 * AI Toolkit — Hero section (flexible-content layout).
 *
 * Fields: eyebrow, badge, title, description, btn1_label, btn1_url,
 * btn2_label, btn2_url. Falls back to the source HTML copy when empty.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'eyebrow' ) ?: 'Solution 02';
$badge   = get_sub_field( 'badge' ) ?: 'NEW';
$title   = get_sub_field( 'title' ) ?: 'AI Toolkit: Competiscan Compass';
$desc    = get_sub_field( 'description' ) ?: 'Evaluate direct marketing campaigns with generative AI and computer vision, backed by two decades of data. Go from a creative concept to a market-ready campaign with forensic certainty in under 30 minutes.';
$b1l     = get_sub_field( 'btn1_label' ) ?: 'See it in action';
$b1u     = get_sub_field( 'btn1_url' ) ?: '#learn';
$b2l     = get_sub_field( 'btn2_label' ) ?: 'See how it works';
$b2u     = get_sub_field( 'btn2_url' ) ?: '#analysis';
?>
<section class="cs-x15" id="top">
  <div class="cs-x16">
    <span class="cs-x17"><?php echo esc_html( $eyebrow ); ?> <?php if ( $badge ) : ?><span class="cs-x18"><?php echo esc_html( $badge ); ?></span><?php endif; ?></span>
    <h1 class="cs-x19"><?php echo esc_html( $title ); ?></h1>
    <p class="cs-x20"><?php echo esc_html( $desc ); ?></p>
    <div class="cs-x21">
      <a class="cs-btn-primary cs-x22" data-cs-calendly href="<?php echo esc_url( $b1u ); ?>"><?php echo esc_html( $b1l ); ?> <span class="cs-x23">&rarr;</span></a>
      <a class="cs-btn-outline cs-x24" href="<?php echo esc_url( $b2u ); ?>"><?php echo esc_html( $b2l ); ?></a>
    </div>
  </div>
</section>
