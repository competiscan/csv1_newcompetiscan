<?php
/**
 * AI Toolkit — Analysis Modules (flexible-content layout).
 *
 * Field: modules (repeater: number, title, text). Two module icons are kept in
 * the layout (by index) and match the source. Falls back to the source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icons = array(
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-6"></path></svg>',
);

$modules = get_sub_field( 'modules' );
if ( empty( $modules ) ) {
	$modules = array(
		array( 'number' => '01', 'title' => 'DME (Direct Marketing Engagement)', 'text' => 'Get pre-flight feedback on direct mail, email, or video ads tailored to the media channel and sector combination of your asset, scored on categories like branding, narrative, visual appeal, and more.' ),
		array( 'number' => '02', 'title' => 'CRE (Commercial Reasoning Engine)', 'text' => 'Compare your campaign against competitor products, promotional terms, and fees. Know where your offer wins, or falls short, against real, in-market direct and digital offers targeting your prospects.' ),
	);
}
?>
<section class="cs-x25" id="analysis">
  <div class="cs-x26">
    <?php foreach ( $modules as $i => $m ) : ?>
    <div class="cs-x27">
      <div class="cs-x28">
        <span class="cs-x29"><?php echo esc_html( $m['number'] ); ?></span>
        <span class="cs-x30"><?php echo isset( $icons[ $i ] ) ? $icons[ $i ] : $icons[0]; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
      </div>
      <h2 class="cs-x31"><?php echo esc_html( $m['title'] ); ?></h2>
      <p class="cs-x32"><?php echo esc_html( $m['text'] ); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>
