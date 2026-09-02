<?php
/**
 * Industries — Featured cards with sub-category chips (flexible-content layout).
 *
 * Field: items (repeater: title, subcategories [comma separated], icon [optional
 * image]). Icons default to the source inline SVGs by index. Falls back to the
 * source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icons = array(
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"></path><path d="M4 10l8-6 8 6"></path><path d="M6 10v8"></path><path d="M10 10v8"></path><path d="M14 10v8"></path><path d="M18 10v8"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 10 4.5-2 8-5 8-10V5l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>',
);

$items = get_sub_field( 'items' );
if ( empty( $items ) ) {
	$items = array(
		array( 'title' => 'Financial Services', 'subcategories' => 'Banking, Payment Cards, Investments, Mortgage & Loans', 'icon' => '' ),
		array( 'title' => 'Insurance', 'subcategories' => 'Health, Property & Casualty, Life, Worksite', 'icon' => '' ),
	);
}
?>
<section class="cs-x277" id="industries">
  <div class="cs-x278">
    <?php foreach ( $items as $i => $item ) : ?>
    <div class="cs-x27">
      <div class="cs-x279">
        <span class="cs-x30">
          <?php
          if ( ! empty( $item['icon'] ) ) {
            echo '<img src="' . esc_url( $item['icon'] ) . '" alt="" width="28" height="28" style="object-fit:contain">';
          } else {
            echo isset( $icons[ $i ] ) ? $icons[ $i ] : $icons[0]; // phpcs:ignore WordPress.Security.EscapeOutput
          }
          ?>
        </span>
        <h2 class="cs-x280"><?php echo esc_html( $item['title'] ); ?></h2>
      </div>
      <div class="cs-x281">
        <?php
        foreach ( array_filter( array_map( 'trim', explode( ',', (string) $item['subcategories'] ) ) ) as $chip ) :
          ?>
        <span class="cs-x282"><?php echo esc_html( $chip ); ?></span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
