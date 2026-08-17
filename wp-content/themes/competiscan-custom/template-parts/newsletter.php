<?php
/**
 * Newsletter signup section (Noptin) — single shared component.
 *
 * Used by the Insights archive and single Insight pages (and reusable anywhere).
 * The heading and sub-text are editable globally from Site Options (ACF Options);
 * the form is the shared Noptin form — no duplicated markup and one place to change
 * the design. Markup/classes/behaviour are unchanged.
 *
 * @package Competiscan_Custom
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nl_heading = function_exists( 'get_field' ) ? get_field( 'newsletter_heading', 'option' ) : '';
if ( ! $nl_heading ) {
	$nl_heading = 'Subscribe to our <br> free newsletter';
}
$nl_subtext = function_exists( 'get_field' ) ? get_field( 'newsletter_subtext', 'option' ) : '';
if ( ! $nl_subtext ) {
	$nl_subtext = 'Get the latest insights straight to your inbox every month';
}
?>
<!-- ============ NEWSLETTER ============ -->
<section class="free-subscribe">
  <div class="container">
    <div class="newsletter">
      <div>
        <h2><?php echo wp_kses_post( $nl_heading ); ?></h2>
        <p><?php echo esc_html( $nl_subtext ); ?></p>
      </div>
      <?php
      // Shared Noptin form — do not create a new form.
      echo do_shortcode( '[noptin fields="first_name,last_name,email" labels="hide" styles="none" template="normal" submit="Submit" first_name_placeholder="First name" last_name_placeholder="Last name" email_placeholder="Enter work email" html_class="newsletter-form cs-noptin-newsletter"]' ); // phpcs:ignore WordPress.Security.EscapeOutput
      ?>
    </div>
  </div>
</section>
