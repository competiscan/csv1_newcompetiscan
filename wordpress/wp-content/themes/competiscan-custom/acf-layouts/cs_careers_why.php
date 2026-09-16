<?php
/**
 * Careers — Why Join + Benefits grid (flexible-content layout).
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_sub_field( 'heading' ) ?: 'Why join';
$body1   = get_sub_field( 'body1' ) ?: "We are the trusted insights engine providing key competitive intelligence to the marketplace's largest brands, earning a 90%+ renewal rate by acting as an indispensable extension of our clients' teams. When you work here, you get unmatched industry exposure and the pride of knowing we aren't just another service provider: we are consistently celebrated as our clients' favorite partner.";
$body2   = get_sub_field( 'body2' ) ?: 'Behind this success is an organizational structure designed for the brightest talent to build, create, and drive true innovation. We are looking for curious, collaborative individuals who are excited to bring their creativity to the table and shape the future of Competiscan.';

$benefits = get_sub_field( 'benefits' );
$list     = ! empty( $benefits ) ? wp_list_pluck( $benefits, 'label' ) : array(
	'Affordable health plans', '401(k) plan', 'Profit-sharing', 'Year-end bonus',
	'Competitive salary', 'Generous PTO', 'Remote work flexibility', 'Downtown Chicago office',
);
$check = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgb(0,171,171)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"></path></svg>';
?>
<section class="cs-2col cs-x154" id="why">
  <div>
    <h2 class="cs-x155"><?php echo esc_html( $heading ); ?></h2>
    <p class="cs-x156"><?php echo esc_html( $body1 ); ?></p>
    <p class="cs-x43"><?php echo esc_html( $body2 ); ?></p>
  </div>
  <div class="cs-x157">
    <?php foreach ( $list as $benefit ) : ?>
    <div class="cs-x158">
      <span class="cs-x159"><?php echo $check; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
      <span class="cs-x160"><?php echo esc_html( $benefit ); ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</section>
