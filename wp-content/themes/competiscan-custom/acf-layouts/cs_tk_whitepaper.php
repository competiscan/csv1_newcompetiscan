<?php
/**
 * AI Toolkit — White Paper / Case Study with gated CF7 form (flexible-content).
 *
 * Fields: eyebrow, title, description, cf7_form_id. Uses the existing CF7 form
 * (defaults to "Turning Credit Card Onboarding into Continuous Growth"). Falls
 * back to the source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow  = get_sub_field( 'eyebrow' ) ?: 'Case study · Explore a use case';
$title    = get_sub_field( 'title' ) ?: "Design best practices from Spring 2026's highest-volume mailers";
$desc     = get_sub_field( 'description' ) ?: "See exactly what the highest-scoring direct mail campaigns in Competiscan's database did right: five instructive blueprints across banking, insurance, telecom, retail, and more, drawn from a real Competiscan Compass DME run.";
$logomark = get_template_directory_uri() . '/assets/images/logomark-white.png';

// Resolve the CF7 form: field (id or slug) → white-paper form → nothing.
$form_ref = trim( (string) get_sub_field( 'cf7_form_id' ) );
$form_id  = 0;
if ( '' !== $form_ref ) {
	$form_id = is_numeric( $form_ref ) ? (int) $form_ref : ( function_exists( 'competiscan_cf7_id_by_slug' ) ? competiscan_cf7_id_by_slug( $form_ref ) : 0 );
}
if ( ! $form_id && function_exists( 'competiscan_whitepaper_form_id' ) ) {
	$form_id = competiscan_whitepaper_form_id();
}
?>
<section class="cs-x64" id="whitepaper">
  <div class="cs-x65">
    <img class="cs-x66" src="<?php echo esc_url( $logomark ); ?>" alt="" aria-hidden="true">
    <div class="cs-x67">
      <div>
        <span class="cs-x68"><?php echo esc_html( $eyebrow ); ?></span>
        <h2 class="cs-x69"><?php echo esc_html( $title ); ?></h2>
        <p class="cs-x70"><?php echo esc_html( $desc ); ?></p>
      </div>
      <div class="cs-x71">
        <?php
        if ( $form_id ) {
          $pdf = home_url( '/competiscan-html/assets/whitepapers/competiscan-compass-case-study.pdf' );
          echo '<div class="cs-cf7" data-cs-pdf="' . esc_url( $pdf ) . '" data-cs-btn="Access the white paper">' . do_shortcode( '[contact-form-7 id="' . $form_id . '"]' ) . '</div>';
        }
        ?>
      </div>
    </div>
  </div>
</section>
