<?php
/**
 * Custom Research — Study Types grid (flexible-content layout).
 *
 * Fields: studies (repeater: title, text) + a highlight "Need something else?"
 * card (special_heading, special_text, special_btn_label, special_btn_url). The
 * per-card icons are kept in the layout (by index) and match the source. Falls
 * back to the source copy.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icons = array(
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="14" rx="2"></rect><path d="M8 21h8"></path><path d="M12 18v3"></path><path d="M7 9h6"></path><path d="M7 13h10"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="m7 15 4-4 3 3 5-6"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"></path><path d="M18 20V4"></path><path d="M6 20v-6"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v4"></path><path d="M12 18v4"></path><circle cx="12" cy="12" r="4"></circle><path d="m4.9 4.9 2.8 2.8"></path><path d="m16.3 16.3 2.8 2.8"></path><path d="M2 12h4"></path><path d="M18 12h4"></path></svg>',
	'<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgb(0,75,129)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 8 8 10 4.5-2 8-5 8-10V5l-8-3Z"></path><path d="m9 12 2 2 4-4"></path></svg>',
);

$studies = get_sub_field( 'studies' );
if ( empty( $studies ) ) {
	$studies = array(
		array( 'title' => 'Experiential Journeys', 'text' => "Live, end-to-end capture of what customers actually experience across a competitor's channels and touchpoints." ),
		array( 'title' => 'Custom Online Studies', 'text' => 'Tailored online research designed around your specific questions, audiences, and competitive set.' ),
		array( 'title' => 'Secret Shopping', 'text' => 'Anonymous, real-world evaluation of competitor sales, service, and onboarding as a prospect sees them.' ),
		array( 'title' => 'Engagement Strategies Analysis', 'text' => 'Deep analysis of how competitors engage, nurture, and convert audiences across the customer journey.' ),
		array( 'title' => 'Competitive Benchmarking', 'text' => 'Side-by-side benchmarking of your performance against competitors on the metrics that matter most.' ),
		array( 'title' => 'Ongoing Monitoring', 'text' => 'Continuous tracking of competitor activity so you see shifts in strategy the moment they happen.' ),
		array( 'title' => 'Brand Protection & Claims Validation', 'text' => 'Verification of competitor claims and monitoring to protect your brand from misleading messaging.' ),
	);
}

$sp_h  = get_sub_field( 'special_heading' ) ?: 'Need something else?';
$sp_t  = get_sub_field( 'special_text' ) ?: "Tell us your question and we'll design a study around it.";
$sp_bl = get_sub_field( 'special_btn_label' ) ?: 'Get in touch';
$sp_bu = get_sub_field( 'special_btn_url' ) ?: '#learn';
?>
<section class="cs-x184" id="studies">
  <div class="cs-x26">
    <?php foreach ( $studies as $i => $s ) : ?>
    <div class="cs-x27">
      <span class="cs-x185"><?php echo isset( $icons[ $i ] ) ? $icons[ $i ] : $icons[0]; // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
      <h2 class="cs-x31"><?php echo esc_html( $s['title'] ); ?></h2>
      <p class="cs-x32"><?php echo esc_html( $s['text'] ); ?></p>
    </div>
    <?php endforeach; ?>
    <div class="cs-x186">
      <h2 class="cs-x187"><?php echo esc_html( $sp_h ); ?></h2>
      <p class="cs-x188"><?php echo esc_html( $sp_t ); ?></p>
      <a class="cs-btn-white cs-x189" data-cs-calendly href="<?php echo esc_url( $sp_bu ); ?>"><?php echo esc_html( $sp_bl ); ?> <span class="cs-x23">&rarr;</span></a>
    </div>
  </div>
</section>
