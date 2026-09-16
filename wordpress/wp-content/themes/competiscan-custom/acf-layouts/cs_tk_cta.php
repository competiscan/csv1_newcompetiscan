<?php
/**
 * AI Toolkit — CTA band (flexible-content layout).
 *
 * Fields: title, description, btn_label, btn_url. Falls back to source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = get_sub_field( 'title' ) ?: 'Concept to market-ready in under 30 minutes';
$desc  = get_sub_field( 'description' ) ?: 'See the AI Toolkit score your campaign with forensic certainty. Book a demo and our team will walk you through it end to end.';
$bl    = get_sub_field( 'btn_label' ) ?: 'See it in action';
$bu    = get_sub_field( 'btn_url' ) ?: '#learn';
?>
<section class="cs-x64" id="learn">
  <div class="cs-x76">
    <h2 class="cs-x77"><?php echo esc_html( $title ); ?></h2>
    <p class="cs-x78"><?php echo esc_html( $desc ); ?></p>
    <a class="cs-btn-primary cs-x79" data-cs-calendly href="<?php echo esc_url( $bu ); ?>"><?php echo esc_html( $bl ); ?> <span class="cs-x23">&rarr;</span></a>
  </div>
</section>
