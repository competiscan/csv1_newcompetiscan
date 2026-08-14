<?php
/**
 * Industries we serve.
 *
 * Note: the first item is a <div class="industry-item" href="#"> in the HTML source,
 * not an <a>. That is reproduced verbatim rather than corrected, so the rendered DOM
 * matches the original exactly.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$img = get_template_directory_uri() . '/assets/images/';

// Editable from admin (ACF "Home — Industries"); hardcoded content is the fallback.
$pid          = (int) get_option( 'page_on_front' );
$ind_heading  = function_exists( 'get_field' ) ? get_field( 'ind_heading', $pid ) : '';
if ( ! $ind_heading ) {
	$ind_heading = "Industries\nWe Serve";
}
$ind_desc = function_exists( 'get_field' ) ? get_field( 'ind_desc', $pid ) : '';
if ( ! $ind_desc ) {
	$ind_desc = 'Etiam accumsan urna a mauris dapibus, nec aliquet nunc convallis. Phasellus eget justo et libero ultrices posuere.';
}
$ind_btn_label = function_exists( 'get_field' ) ? get_field( 'ind_btn_label', $pid ) : '';
if ( ! $ind_btn_label ) {
	$ind_btn_label = 'Learn More';
}
$ind_btn_url = function_exists( 'get_field' ) ? get_field( 'ind_btn_url', $pid ) : '';
if ( ! $ind_btn_url ) {
	$ind_btn_url = '#';
}

// Built-in icon files (fallback, in the original order).
$ind_icons = array( 'banking.svg', 'house.svg', 'credit-card.svg', 'retail.svg', 'insurance.svg', 'telecoms.svg', 'plane.svg', 'more.svg' );

$ind_rows  = function_exists( 'get_field' ) ? get_field( 'ind_items', $pid ) : array();
$industries = array();
if ( ! empty( $ind_rows ) && is_array( $ind_rows ) ) {
	$k = 0;
	foreach ( $ind_rows as $r ) {
		$icon = ! empty( $r['icon'] ) ? $r['icon'] : ( isset( $ind_icons[ $k ] ) ? $img . $ind_icons[ $k ] : '' );
		$industries[] = array(
			'icon'  => $icon,
			'label' => isset( $r['label'] ) ? $r['label'] : '',
		);
		$k++;
	}
} else {
	$labels = array( 'Banking', 'Mortgage & Loans', 'Credit Cards', 'Retail', 'Insurance', 'Telecoms', 'Investment & Wealth', 'And more...' );
	foreach ( $labels as $k => $label ) {
		$industries[] = array( 'icon' => $img . $ind_icons[ $k ], 'label' => $label );
	}
}
?>
<!-- ============ INDUSTRIES ============ -->
<section class="section">
  <div class="container industries-grid">
    <div class="industries-copy">
      <h2><?php echo nl2br( esc_html( $ind_heading ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- nl2br over esc_html. ?></h2>
      <p><?php echo esc_html( $ind_desc ); ?></p>
      <a href="<?php echo esc_url( $ind_btn_url ); ?>" class="btn btn-primary for_desktop"><?php echo esc_html( $ind_btn_label ); ?></a>
    </div>
    <div class="industry-list">
      <?php foreach ( $industries as $industry ) : ?>
      <a class="industry-item" href="#">
        <div class="ic">
          <?php if ( ! empty( $industry['icon'] ) ) : ?>
          <img src="<?php echo esc_url( $industry['icon'] ); ?>" alt="icon">
          <?php endif; ?>
        </div>
        <span><?php echo esc_html( $industry['label'] ); ?></span>
      </a>
      <?php endforeach; ?>
      <a href="<?php echo esc_url( $ind_btn_url ); ?>" class="btn btn-primary for_mobile"><?php echo esc_html( $ind_btn_label ); ?></a>

    </div>
  </div>
</section>
