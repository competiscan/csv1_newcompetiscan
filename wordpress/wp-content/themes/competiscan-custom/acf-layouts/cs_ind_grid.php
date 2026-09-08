<?php
/**
 * Industries — Other industries icon grid (flexible-content layout).
 *
 * Field: items (repeater: label, icon [optional image]) + and_more_label. Icons
 * default to the source inline SVGs by index. Falls back to the source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icons = array(
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l1.4-4.2A2 2 0 0 1 8.3 7.4h7.4a2 2 0 0 1 1.9 1.4L19 13"></path><path d="M4 13h16v4a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1H7a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-4Z"></path><path d="M7 16h.01M17 16h.01"></path></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h7l-1 8 10-12h-7l1-8Z"></path></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-4.5-9.5-9A5 5 0 0 1 12 6a5 5 0 0 1 9.5 6C19 16.5 12 21 12 21Z"></path></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.1A4 4 0 0 1 16 11"></path></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path><path d="M3 6h18"></path><path d="M16 10a4 4 0 0 1-8 0"></path></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"></path><path d="M22 2 15 22l-4-9-9-4 20-7Z"></path></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h.01"></path><path d="M2 8.8a15 15 0 0 1 20 0"></path><path d="M5 12.5a10 10 0 0 1 14 0"></path><path d="M8.5 16a5 5 0 0 1 7 0"></path></svg>',
);

$items = get_sub_field( 'items' );
if ( empty( $items ) ) {
	$items = array(
		array( 'label' => 'Automotive', 'icon' => '' ),
		array( 'label' => 'Energy', 'icon' => '' ),
		array( 'label' => 'Non-Profit', 'icon' => '' ),
		array( 'label' => 'Consumer Services', 'icon' => '' ),
		array( 'label' => 'Retail', 'icon' => '' ),
		array( 'label' => 'Travel & Leisure', 'icon' => '' ),
		array( 'label' => 'Telecom', 'icon' => '' ),
	);
}
$and_more  = get_sub_field( 'and_more_label' ) ?: 'And more';
$more_show = get_sub_field( 'and_more_show' );
$more_show = ( null === $more_show || '' === $more_show ) ? true : (bool) $more_show; // Default: shown (backward compatible).
$more_icon = get_sub_field( 'and_more_icon' );
$more_url  = get_sub_field( 'and_more_url' );
?>
<section class="cs-x283">
  <div class="cs-x284">
    <?php foreach ( $items as $i => $item ) : ?>
    <div class="cs-x285">
      <span class="cs-x286">
        <?php
        if ( ! empty( $item['icon'] ) ) {
          echo '<img src="' . esc_url( $item['icon'] ) . '" alt="" width="24" height="24" style="object-fit:contain">';
        } else {
          echo isset( $icons[ $i ] ) ? $icons[ $i ] : $icons[0]; // phpcs:ignore WordPress.Security.EscapeOutput
        }
        ?>
      </span>
      <span class="cs-x287"><?php echo esc_html( $item['label'] ); ?></span>
    </div>
    <?php endforeach; ?>
    <?php
    if ( $more_show ) :
      $more_tag  = $more_url ? 'a' : 'div';
      $more_href = $more_url ? ' href="' . esc_url( $more_url ) . '"' : '';
      ?>
    <<?php echo esc_html( $more_tag ) . $more_href; // phpcs:ignore WordPress.Security.EscapeOutput -- tag + pre-escaped href ?> class="cs-x288">
      <span class="cs-x289">
        <?php
        if ( ! empty( $more_icon ) ) {
          echo '<img src="' . esc_url( $more_icon ) . '" alt="" width="24" height="24" style="object-fit:contain">';
        } else {
          ?>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="rgb(255,255,255)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"></path><path d="M5 12h14"></path></svg>
          <?php
        }
        ?>
      </span>
      <span class="cs-x290"><?php echo esc_html( $and_more ); ?></span>
    </<?php echo esc_html( $more_tag ); ?>>
    <?php endif; ?>
  </div>
</section>
