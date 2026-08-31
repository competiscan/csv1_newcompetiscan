<?php
/**
 * Fortune 500 stats grid.
 *
 * The grid interleaves stat cells and logo cells in a fixed order — that ordering is
 * what produces the checkerboard layout, so the sequence below must not be re-sorted.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$img = get_template_directory_uri() . '/assets/images/';

// Editable from admin (ACF "Home — Stats"); hardcoded content is the fallback. The
// grid interleaves stat cells and logo cells — repeater order = display order.
$pid          = (int) get_option( 'page_on_front' );
$stats_accent = function_exists( 'get_field' ) ? get_field( 'stats_accent', $pid ) : '';
if ( ! $stats_accent ) {
	$stats_accent = 'Most Fortune 500 firms rely on us to stay ahead.';
}
$stats_tail = function_exists( 'get_field' ) ? get_field( 'stats_tail', $pid ) : '';
if ( '' === (string) $stats_tail ) {
	$stats_tail = " Shouldn't you?";
}

$stats_rows = function_exists( 'get_field' ) ? get_field( 'stats_cells', $pid ) : array();
$cells      = array();
if ( ! empty( $stats_rows ) && is_array( $stats_rows ) ) {
	foreach ( $stats_rows as $r ) {
		if ( isset( $r['cell_type'] ) && 'logo' === $r['cell_type'] ) {
			if ( ! empty( $r['logo_image'] ) ) {
				$cells[] = array( 'img' => $r['logo_image'] );
			}
		} else {
			$cells[] = array(
				'num'   => isset( $r['number'] ) ? $r['number'] : '',
				'color' => isset( $r['color'] ) ? $r['color'] : '',
				'label' => isset( $r['label'] ) ? $r['label'] : '',
			);
		}
	}
}
if ( empty( $cells ) ) {
	$cells = array(
		array( 'num' => '8,000+', 'color' => 'yellow', 'label' => 'projects completed for clients annually' ),
		array( 'img' => $img . 'fortune-1.png' ),
		array( 'num' => '2,000+', 'color' => 'orange', 'label' => 'hours of custom research per year' ),
		array( 'img' => $img . 'fortune-2.png' ),
		array( 'img' => $img . 'fortune-3.png' ),
		array( 'num' => '~20,000', 'color' => 'blue', 'label' => 'hours per year supporting client research' ),
		array( 'img' => $img . 'fortune-4.png' ),
		array( 'num' => '24/7', 'color' => 'yellow', 'label' => 'monitoring coverage' ),
		array( 'num' => '90%+', 'color' => 'orange', 'label' => 'average annual renewal rate among subscribers' ),
		array( 'img' => $img . 'fortune-5.png' ),
		array( 'num' => '#1', 'color' => 'blue', 'label' => 'longitudinal consumer panel' ),
		array( 'img' => $img . 'fortune-6.png' ),
	);
}
?>
<!-- ============ STATS ============ -->
<section class="section stats">
  <div class="container">
    <h2><span class="accent"><?php echo esc_html( $stats_accent ); ?></span><?php echo esc_html( $stats_tail ); ?></h2>
    </div>
  <div class="pattern-white">
    <div class="stats-wrap">
      <span class="line-1"></span>
      <span class="line-2"></span>
      <span class="line-3"></span>
    <div class="container">

      <div class="stats-grid">

        <?php foreach ( $cells as $cell ) : ?>
          <?php if ( isset( $cell['img'] ) ) : ?>
        <div class="stat-cell img-cell"><img src="<?php echo esc_url( $cell['img'] ); ?>" alt=""></div>
          <?php else : ?>
        <div class="stat-cell"><div class="num <?php echo esc_attr( $cell['color'] ); ?>"><?php echo esc_html( $cell['num'] ); ?></div><div class="label"><?php echo esc_html( $cell['label'] ); ?></div></div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
   <div class="mobil-img w-100">
     <img src="<?php echo esc_url( $img . 'fortune-3.png' ); ?>" alt="">
   </div>
  </div>

</section>
