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

// Each cell is either a stat (num/colour/label) or a logo image, in source order.
$cells = array(
	array( 'num' => '8,000+', 'color' => 'yellow', 'label' => 'projects completed for clients in 2025' ),
	array( 'img' => 'fortune-1.png' ),
	array( 'num' => '2,000+', 'color' => 'orange', 'label' => 'hours of custom research per year' ),
	array( 'img' => 'fortune-2.png' ),

	array( 'img' => 'fortune-3.png' ),
	array( 'num' => '~20,000', 'color' => 'blue', 'label' => 'hours per year supporting client research' ),
	array( 'img' => 'fortune-4.png' ),
	array( 'num' => '24/7', 'color' => 'yellow', 'label' => 'monitoring coverage' ),

	array( 'num' => '90%+', 'color' => 'orange', 'label' => 'average annual renewal rate among subscribers' ),
	array( 'img' => 'fortune-5.png' ),
	array( 'num' => '#1', 'color' => 'blue', 'label' => 'longitudinal consumer panel' ),
	array( 'img' => 'fortune-6.png' ),
);
?>
<!-- ============ STATS ============ -->
<section class="section stats">
  <div class="container">
    <h2><span class="accent">Most Fortune 500 firms rely on us to stay ahead.</span> Shouldn't you?</h2>
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
        <div class="stat-cell img-cell"><img src="<?php echo esc_url( $img . $cell['img'] ); ?>" alt=""></div>
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
