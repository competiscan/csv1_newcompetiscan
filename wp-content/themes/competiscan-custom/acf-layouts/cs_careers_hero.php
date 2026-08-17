<?php
/**
 * Careers — Hero (flexible-content layout). Falls back to the source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = get_sub_field( 'eyebrow' ) ?: 'Careers';
$title   = get_sub_field( 'title' ) ?: 'Join the Competiscan team.';
$desc    = get_sub_field( 'description' ) ?: 'Do meaningful work, learn new skills, and grow your career alongside a growing company.';
$b1l     = get_sub_field( 'btn1_label' ) ?: 'See open roles';
$b1u     = get_sub_field( 'btn1_url' ) ?: '#roles';
$b2l     = get_sub_field( 'btn2_label' ) ?: 'Why join us';
$b2u     = get_sub_field( 'btn2_url' ) ?: '#why';

// Site convention: a "#learn" (or Calendly) URL opens the Calendly popup, handled
// by assets/js/cs-site.js via the data-cs-calendly attribute.
$cs_is_calendly = static function ( $url ) {
	return ( '#learn' === $url || false !== strpos( (string) $url, 'calendly.com' ) );
};
$b1_cal = $cs_is_calendly( $b1u ) ? ' data-cs-calendly' : '';
$b2_cal = $cs_is_calendly( $b2u ) ? ' data-cs-calendly' : '';
?>
<section class="cs-x151" id="top">
  <div class="cs-x152">
    <span class="cs-x113"><?php echo esc_html( $eyebrow ); ?></span>
    <h1 class="cs-x19"><?php echo esc_html( $title ); ?></h1>
    <p class="cs-x153"><?php echo esc_html( $desc ); ?></p>
    <div class="cs-x21">
      <a class="cs-btn-primary cs-x22"<?php echo $b1_cal; // phpcs:ignore WordPress.Security.EscapeOutput ?> href="<?php echo esc_url( $b1u ); ?>"><?php echo esc_html( $b1l ); ?> <span class="cs-x23">&rarr;</span></a>
      <a class="cs-btn-outline cs-x24"<?php echo $b2_cal; // phpcs:ignore WordPress.Security.EscapeOutput ?> href="<?php echo esc_url( $b2u ); ?>"><?php echo esc_html( $b2l ); ?></a>
    </div>
  </div>
</section>
