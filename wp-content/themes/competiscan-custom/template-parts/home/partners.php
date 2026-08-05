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

$logos = array(
	'amsive-logo.jpg',
	'amazon-web-services.png',
	'deluxe.png',
	'njm.png',
	'prosper-logo.png',
	'publix.png',
	'rbc.png',
	'sir.png',
	'snap.png',
);
?>
<!-- ============ PARTNERS ============ -->
<section class="partners">
  <div class="container">
    <span class="eyebrow-pill">Our Trusted Partners</span>
    </div>
    <div class="partners-track">
      <div class="marquee-row">
        <?php foreach ( $logos as $logo ) : ?>
        <span class="logo-chip"><img src="<?php echo esc_url( $img . $logo ); ?>" alt="logo"></span>
        <?php endforeach; ?>
        <!-- duplicate set for seamless infinite loop -->
      </div>
    </div>

</section>
