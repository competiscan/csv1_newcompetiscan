<?php
/**
 * Value Prop Trackers — Trackers by category grid (flexible-content layout).
 *
 * Field: heading + items (repeater: title, description). Card accent colours are
 * applied by index to match the source. Falls back to the source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_sub_field( 'heading' ) ?: 'Trackers by category';
$items   = get_sub_field( 'items' );
if ( empty( $items ) ) {
	$items = array(
		array( 'title' => 'Credit Cards', 'description' => '600+ cards tracked across issuers, card types, and media channels.' ),
		array( 'title' => 'Deposits', 'description' => 'Rates, fees, and promotions across checking, savings, and CD products.' ),
		array( 'title' => 'Retail', 'description' => 'Offers, pricing, and incentive trends across retail card programs.' ),
		array( 'title' => 'Travel Loyalty', 'description' => 'Points, tiers, and benefits tracked across travel loyalty programs.' ),
	);
}
$colors = array( 'cs-x403', 'cs-x406', 'cs-x407', 'cs-x408' );
?>
<section class="cs-x184">
  <h2 class="cs-x399"><?php echo esc_html( $heading ); ?></h2>
  <div class="cs-x400">
    <?php foreach ( $items as $i => $item ) : ?>
    <div class="cs-x401">
      <div class="cs-x402">
        <span class="<?php echo esc_attr( $colors[ $i % count( $colors ) ] ); ?>"></span>
      </div>
      <h3 class="cs-x404"><?php echo esc_html( $item['title'] ); ?></h3>
      <p class="cs-x405"><?php echo esc_html( $item['description'] ); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>
