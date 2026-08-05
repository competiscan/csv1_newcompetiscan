<?php
/**
 * AI Toolkit — Key Capabilities (flexible-content layout).
 *
 * Fields: heading, items (repeater: title, text). Falls back to source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_sub_field( 'heading' ) ?: 'Key capabilities';
$items   = get_sub_field( 'items' );
if ( empty( $items ) ) {
	$items = array(
		array( 'title' => 'Quantified Metrics', 'text' => 'Scores grounded in engagement elements and two decades of historical data.' ),
		array( 'title' => 'Explained Scoring', 'text' => 'Detailed scoring with clear explanations for how to improve.' ),
		array( 'title' => 'Competitive Comparisons', 'text' => 'Benchmark your campaign directly against competitor campaigns.' ),
		array( 'title' => 'Data-Driven Recommendations', 'text' => 'Actionable recommendations for test campaigns before you go to market.' ),
	);
}
?>
<section class="cs-x33">
  <div class="cs-x34">
    <h3 class="cs-x35"><?php echo esc_html( $heading ); ?></h3>
    <div class="cs-x36">
      <?php foreach ( $items as $c ) : ?>
      <div class="cs-x37">
        <svg class="cs-x38" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgb(0,171,171)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>
        <div><h4 class="cs-x39"><?php echo esc_html( $c['title'] ); ?></h4><p class="cs-x40"><?php echo esc_html( $c['text'] ); ?></p></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
