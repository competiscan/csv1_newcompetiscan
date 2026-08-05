<?php
/**
 * Careers — CTA band (flexible-content layout).
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = get_sub_field( 'title' ) ?: 'Ready to thrive at Competiscan?';
$desc  = get_sub_field( 'description' ) ?: "We offer a competitive salary, strong benefits, and real opportunities for growth. Send us your resume and let's talk.";
$bl    = get_sub_field( 'btn_label' ) ?: 'Apply today';
$bu    = get_sub_field( 'btn_url' ) ?: 'mailto:contactus@competiscan.com?subject=Application';
$logomark = get_template_directory_uri() . '/assets/images/logomark-white.png';
?>
<section class="cs-x64" id="apply">
  <div class="cs-x180">
    <img class="cs-x144" src="<?php echo esc_url( $logomark ); ?>" alt="" aria-hidden="true">
    <div class="cs-x181">
      <h2 class="cs-x182"><?php echo esc_html( $title ); ?></h2>
      <p class="cs-x183"><?php echo esc_html( $desc ); ?></p>
      <a class="cs-btn-primary cs-x79" href="<?php echo esc_url( $bu ); ?>"><?php echo esc_html( $bl ); ?> <span class="cs-x23">&rarr;</span></a>
    </div>
  </div>
</section>
