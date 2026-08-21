<?php
/**
 * Partner logo marquee.
 *
 * main.js duplicates .marquee-row's innerHTML on load to make the loop seamless,
 * so only the single set is printed here — same as the HTML source.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$img = get_template_directory_uri() . '/assets/images/';

// Editable from admin (ACF "Home — Partners"); hardcoded logos are the fallback.
$pid     = (int) get_option( 'page_on_front' );
$eyebrow = function_exists( 'get_field' ) ? get_field( 'partners_eyebrow', $pid ) : '';
if ( ! $eyebrow ) {
	$eyebrow = 'Our Trusted Partners';
}

$logo_rows = function_exists( 'get_field' ) ? get_field( 'partners_logos', $pid ) : array();
$logos     = array();
if ( ! empty( $logo_rows ) && is_array( $logo_rows ) ) {
	foreach ( $logo_rows as $r ) {
		if ( ! empty( $r['logo'] ) ) {
			$logos[] = $r['logo'];
		}
	}
}
if ( empty( $logos ) ) {
	foreach ( competiscan_home_partner_logos() as $fn ) {
		$logos[] = $img . 'companylogos/' . $fn;
	}
}
?>
<!-- ============ PARTNERS ============ -->
<section class="partners">
  <div class="container">
    <span class="eyebrow-pill"><?php echo esc_html( $eyebrow ); ?></span>
    </div>
    <div class="partners-track container">
      <div class="marquee-row">
        <?php foreach ( $logos as $logo ) : ?>
        <span class="logo-chip"><img src="<?php echo esc_url( $logo ); ?>" alt="logo"></span>
        <?php endforeach; ?>
        <!-- duplicate set for seamless infinite loop -->
      </div>
    </div>

</section>
